<!DOCTYPE html>

<?php
    // Hypothes.is API TOKEN: (test) 6879-A0VNmaeMeE7HZuaehDrRcGWooSRY68dXNbawDjVwJxY
    include("conn_dev.php");
    require_once('Hypothesis.inc.php');

    // Hypothes.is API TOKEN
    $token = '6879-A0VNmaeMeE7HZuaehDrRcGWooSRY68dXNbawDjVwJxY';
    // Hypothes.is PHP Wrapper instance
    $hypothesis = new HypothesisAPI();
    $dailyMed = new DailyMedAPI();

    // drugname typed in input textbox
    $drugname=@$_POST['drugname'];
    	
    $candisplaystuff = 0;
    // If the drugname is specified (textbox is not empty)
    if(isset($_POST['drugname']) && $drugname <> ""){
        // Can display results because textbox is not empty
        $candisplaystuff = 2;

        // -------- DRUGCENTRAL STUFF ---------- //
        // Query string to get drug central id for the drug name
        $getStructIDQueryStr = "SELECT struct_id FROM active_ingredient WHERE substance_name like '%".$drugname."%' LIMIT 1";
        // Actual query directive to get drug central id for the drug name
        $getStructIDQueryResult = pg_query($conn, $getStructIDQueryStr) or die("Could not execute query");
        // Get the single cell in the result of the query
        $structIDCell = pg_fetch_object($getStructIDQueryResult);
        // Get the single value inside the cell
        $struct_id = $structIDCell->struct_id;
        // Query string to get ATC code for the drug name
        $getATCQueryStr = "SELECT atc_code FROM struct2atc WHERE struct_id = $struct_id";
        // Actual query directive to get ATC code for the drug name
        $getATCQueryResult = pg_query($conn, $getATCQueryStr) or die("Could not execute query");
        // Get the single cell in the result of the query
        $atcCell = pg_fetch_object($getATCQueryResult);
        // Get the single value inside the cell
        $atc = $atcCell->atc_code;
        // Query string to get all the drug central indications for this drug
        $getIndicationCodesQueryStr = "SELECT omop_relationship.id,
        omop_relationship.struct_id,
        omop_relationship.concept_id,
        omop_relationship.relationship_name,
        omop_relationship.concept_name,
        omop_relationship.umls_cui,
        omop_relationship.snomed_full_name,
        omop_relationship.cui_semantic_type,
        omop_relationship.snomed_conceptid,
        d.doid
       FROM (omop_relationship
         LEFT JOIN ( SELECT doid_xref.xref,
                string_agg((doid_xref.doid)::text, ','::text) AS doid
               FROM doid_xref
              WHERE ((doid_xref.source)::text ~~ 'SNOMED%'::text)
              GROUP BY doid_xref.xref) d ON ((omop_relationship.snomed_conceptid = (d.xref)::bigint))) 
                 
        WHERE omop_relationship.struct_id = $struct_id and omop_relationship.relationship_name = 'indication'";
        // Actual query directive to get all the drug central indications for this drug
        $getIndicationCodesQueryResult = pg_query($conn, $getIndicationCodesQueryStr) or die("Could not execute query");

        // -------- DAILYMED STUFF ---------- //

        // Get the SET ID (DailyMed ID) for the drug
        // DailyMed API e.g. https://dailymed.nlm.nih.gov/dailymed/services/v2/spls.json?drug_name=cisplatin
        $splDailyMed = $dailyMed->getSETID($drugname);

        // We should have at least one match for the drugname from DailyMed API
        if (count($splDailyMed->data) > 0){
            // There may be more than one drug with same name (from other manufacturers etc.)
            // We are only interested in the one which is annotated by Linda! For now, we just
            // take the first one in the array but we can be more clever about this later.

            // Get list of drugs matching the text given
            $d = $splDailyMed->data;
            // Only interested in the first one (see above paragraph)
            $firstDrug = $d[0]; 
            // Get the SET ID (DailyMed ID)
            $set_id = $firstDrug->setid;

            

            //echo $set_id;

            // Search for the annotations on hypothesis by lrieswijk containing the text "indication"
            $dailyMedBaseUrl = "https://dailymed.nlm.nih.gov/dailymed/drugInfo.cfm?setid=";
            // Add the SET ID so we can isolate the actual web page on DailyMed for the given drug
            $dailyMedDrugUrl = $dailyMedBaseUrl.$set_id;
            // Actually get all the hypothesis annotations
            $result = $hypothesis->search(array('uri' => $dailyMedDrugUrl, 'user' => 'lrieswijk', 'tag' => 'indication'), $token);

            $dailyMedIndications = array(count($result));

            //print_r($result);

            // Print each set of tags on a new line
            $str = "";
            $idx = 0;
            foreach ($result as $value) {
                // Current row (array) of indications table
                $currentRow = array(6);
                // Initialise array

                // Add the first column value (SET ID)
                $currentRow[0] = $set_id;
                
                // Drill down to the actual text that we annotating
                $t = $value->target;
                $s = $t[0]->selector;
                //$str.= $s[3]->exact.": ";
                // Now add the second column value (indication name or text)
                $currentRow[1] = $s[3]->exact;
                // Get the annotations as an array
                $annotations = $value->tags;
                // Iterate through the annotations to find UMLs and DOID codes
                $arr = array();
                array_push($arr, 2);
                array_push($arr, 3);
                for ($x = 0; $x < count($annotations); $x++){
                    $firstFourChars = substr($annotations[$x], 0, 4);
                    if ($firstFourChars == "UMLS"){
                        // Third column of row (UMLS Code)
                        $currentRow[2] = $annotations[$x];
                        if(($key = array_search(2, $arr)) !== false) {
                            unset($arr[$key]);
                        }
                    }
                    if ($firstFourChars == "DOID"){
                        // Fourth column of row (DOID Code)
                        $currentRow[3] = $annotations[$x];
                        if(($key = array_search(3, $arr)) !== false) {
                            unset($arr[$key]);
                        }
                    }
                }

                if (count($arr) == 2){
                    $currentRow[2] = "-";
                    $currentRow[3] = "-";
                }
                else if (count($arr) == 1){
                    $i = (int)implode(",", $arr);
                    if ($i == 2)
                        $currentRow[2] = "-";
                    else
                        $currentRow[3] = "-";
                }

                $currentRow[4] = "";
                $currentRow[5] = "";
                
                //echo $s[3]->exact;
                //print_r($value->updated);
                //echo "<br>";
                //$str.= implode(",", $value->tags);
                //$str.="<br>";
                $dailyMedIndications[$idx] = $currentRow;
                $idx++;
                //$str.= $value->target;
                //$str.="<br>";
            }

            //echo $str;
        }

    }
?>

<html lang="en">  
    <head>
        <meta charset="utf-8" />
        <title>DrugCentral vs DailyMed: indication codes comparison</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />                
        <link href="css/layout.css" rel="stylesheet" type="text/css" />               
		<link rel="stylesheet" href="dist.css"></link>		
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script type="text/javascript" src="js/jquery/asidebar.jquery.js"></script>		
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <style>
            .container{
                margin-top: 100px;
                margin: auto;
            }

            h1{
                /*margin-top: 100px;*/
                text-align: center;
                color: #747f8c;
            }

            @import url('http://fonts.googleapis.com/css?family=Roboto+Condensed');
            .back {
                width: 33%;
                height: 100px;
                background-color: #eeeeee;
                border: 10px;
                border-color: #ffffff;
                border-style: solid;
                box-sizing: border-box;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                counter-increment: bc;
                padding: 0px 5px 5px 5px;
            }

            .back:before {
                content: counter(bc) "_";
                position: absolute;
                padding: 10px;
            }

            @media screen and (max-width: 1260px) {
                .back {
                    width: 50%;
                }
            }

            @media screen and (max-width: 840px) {
                .back {
                    width: 100%;
                }
            }

            .button_base {
                font-size: 18px;
                margin:auto!important;
                border-radius: 10px;
                width: 218px;
                height: 50px;
                text-align: center;
                box-sizing: border-box;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                -webkit-user-select: none;
                cursor: default;
            }

            .button_base:hover {
                cursor: pointer;
            }

            .b01_simple_rollover {
                color: #ffffff;
                border: #747f8c solid 1px;
                background-color: #51ede4;
            }

            .b01_simple_rollover:hover {
                color: #747f8c;
                background-color: #ffffff;
            }

            .formcontainer{
                float: left;
                width:150px;
            }

            p {
                font-size: 14pt;
                padding-left: 0.5cm;
            }

            input[type=submit]{
                text-align: center;
                font-weight: bold;
            }

            .table td {
               text-align: center;   
            }

            .table th {
               text-align: center;   
            }

            table, td, th {
                border: 1px solid #ddd;
                text-align: left;
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            th, td {
                padding: 15px;
            }
        </style>

        <h1 style="margin-top:100px;">DrugCentral vs DailyMed: indication codes comparison</h1> 
    </head>

    <body>
        <br><br>
        <section>
        
            <div id="formcontainer">
        
                <form action="IndicationCodesExtractor.php" method="post">
            
                    <div align="center">
                        <div style="color: #747f8c; font-size: 20px;"> Drug name: </div> <input type="text" id="drugname" name="drugname">
                    </div>
                    <br>
                    <div align="center">
                        <input class="back button_base b01_simple_rollover" type="submit" value="View Report">
                    </div>
                    <br><br>

                </form>

            </div>

            <?php if ($candisplaystuff == 2) { ?>
            <h1><?php echo $drugname." (".$atc.")"; ?></h1>
            <div class="container">
              <table class="table table-hover" style="table-layout:fixed">
                <thead>
                  <tr bgcolor="#51ede4">
                    <th>DrugCentral ID</th>
            		<th>Indication Name</th>
            		<th>UMLS Code</th>
                    <th>DOID Code</th>
                    <th>SNOMED Code</th>
                    <th>SNOMED Name</th>
                  </tr>
                </thead>
                <tbody>
                <?php while($grow = pg_fetch_array($getIndicationCodesQueryResult)) { ?>
                  <tr>
                        <td><?php echo $grow['struct_id']; ?></td>
            			<td><?php echo $grow['concept_name']; ?></td>
            			<td><?php echo $grow['umls_cui']; ?></td>
                        <td><?php echo substr($grow['doid'], 5); ?></td>
                        <td><?php echo $grow['snomed_conceptid']; ?></td>
                        <td><?php echo $grow['snomed_full_name']; ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
               
              </table>
              <br><br>
              <table class="table table-hover" style="table-layout:fixed">
                <thead>
                  <tr bgcolor="#51ede4">
                    <th>DailyMed ID</th>
                    <th>Indication Name</th>
                    <th>UMLS Code</th>
                    <th>DOID Code</th>
                    <th>SNOMED Code</th>
                    <th>SNOMED Name</th>
                  </tr>
                </thead>
                <tbody>
                <?php for($x = 0; $x < count($result); $x++) { ?>
                  <tr>
                        <td><?php echo $dailyMedIndications[$x][0]; ?></td>
                        <td><?php echo $dailyMedIndications[$x][1]; ?></td>
                        <td><?php echo substr($dailyMedIndications[$x][2], 9); ?></td>
                        <td><?php echo substr($dailyMedIndications[$x][3], 5); ?></td>
                        <td><?php echo $dailyMedIndications[$x][4]; ?></td>
                        <td><?php echo $dailyMedIndications[$x][5]; ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
               
              </table>
            </div>

            <?php }

            else{
                if ($candisplaystuff == 1){
                    echo '<script language="javascript">';
                    echo 'alert("Please type in a drug name")';
                    echo '</script>';
                    exit;
                }
            } ?>
    
        </section>
        
    </body>
    
</html> 