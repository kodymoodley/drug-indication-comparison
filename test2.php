<!-- BEGINNING OF DOCUMENT -->
<!DOCTYPE html>

<?php
    //header('Content-Type: text/csv');
    //header('Content-Disposition: attachment; filename="sample.csv"');
    // Hypothes.is API TOKEN: (just a test) 6879-ef-QplA3MslmdVLEuDjP0pQrHmkAxhweWPjAZYOlH_M
    // DailyMed group id on Hypothes.is: pGv4X7iV
    // List of usernames on the dailymed group that actually have annotations:
    /*amrapaliz 66
    jonquet 2
    liuwenqiangcs 37
    lrieswijk 4
    micheldumontier 3
    rcelebi 98
    sandeepayyar19 17
    shima 11
    Tudorzinho 4
    yashvyas 11
    Yijia_Zhang 61*/

    // DrugCentral DB connection pot
    include("conn_dev.php");            

    // Hypothes.is API wrapper (I also wrapped the DailyMed API call that we use here, in there as well)
    require_once('Hypothesis.inc.php'); 

    // Don't time out script if it takes long
    set_time_limit(0);

     /**
     * Find the position of the Xth occurrence of a substring in a string
     * @param $haystack
     * @param $needle
     * @param $number integer > 0
     * @return int
     */
    function strposX($haystack, $needle, $number){
        if($number == '1'){
            return strpos($haystack, $needle);
        }elseif($number > '1'){
            return strpos($haystack, $needle, strposX($haystack, $needle, $number - 1) + strlen($needle));
        }else{
            return error_log('Error: Value for parameter $number is out of range');
        }
    }

    // Function to calculate the difference between two arrays
    function arrayRecursiveDiff($a1, $a2) {
        $result = array();

        foreach($a1 as $va1) {
            $found = false;
            foreach($a2 as $va2) {
              try{
                    //echo "va1: ".$va1." va2: ".$va2."<br>";
                    // if its a single value we need to convert to a singleton array 
                    if (!is_array($va1)){
                        $va1 = compact($va1);
                    }
                    if (!is_array($va2)){
                        $va2 = compact($va2);
                    } 

                    $x = array_diff($va1, $va2);
                    if (empty($x)) {
                        $found = true;
                    }   
                }
                catch (Exception $e){
                    echo "va1: ".$va1." va2: ".$va2.", Exception Line 45: ".$e->getMessage();
                }
            }
            if (!$found) { 
              $result[] = $va1;
            }
        }

        foreach($a2 as $va2) {
           $found = false;
           foreach($a1 as $va1) {
              try{
                    //echo "va1: ".$va1." va2: ".$va2."<br>";
                    // if its a single value we need to convert to a singleton array 
                    if (!is_array($va1)){
                        $va1 = compact($va1);
                    }
                    if (!is_array($va2)){
                        $va2 = compact($va2);
                    } 

                    $x = array_diff($va2, $va1);
                    if (empty($x)) {
                        $found = true;
                    }   
                }
                catch (Exception $e){
                    echo "va1: ".$va1." va2: ".$va2.", Exception Line 63: ".$e->getMessage();
                }
           }
           if (!$found) { 
              $result[] = $va2;
           }
        }
    
        return $result;
    } 

    // Function to avoid the "string expected" array found problem
    function serialize_array_values($arr){
        foreach($arr as $key=>$val){
            try{
                echo "val: ".$val."<br>";
                // if sub-array $val is one value it will not be an array
                if (is_array($val)){
                    sort($val);
                }
            }
            catch (Exception $e){
                echo "val: ".$val.", Exception Line 83: ".$e->getMessage();
            }

            $arr[$key]=serialize($val);
        }

        return $arr;
    }

    // Function to avoid the "string expected" array found problem
    function serialize_array_values2($arr){
        foreach($arr as $key=>$val){
            sort($val);
            $arr[$key]=json_encode($val);
        }

        return $arr;
    }

    function contains($str, $c){
        for ($x = 0; $x < strlen($str);$x++){
            if ($str[$x] == $c){
                return true;
            }
        }
        return false;
    }

    // Hypothes.is API TOKEN
    $token = '6879-2oY7ox3Ka1n2H9x_vimd8sVIZJZWEaDLSprWamIvWlA';

    // Hypothes.is and DailyMed PHP Wrapper instances 
    $hypothesis = new HypothesisAPI();

    // Drugname typed in input textbox
    $drugname=@$_POST['drugname'];

    // Whether the checkbox to indicate drug class has been ticked or not
    $isdrugclass=@$_POST['isdrugclass'];
    	
    // Just a input validation flag to check if we entered entered anything or if its a single drug or class of drugs
    // that we are reporting on
    $candisplaystuff = 0;

    // Input error flag
    $error = 0;

    $matchingSETIDs = array();

    $setIDToIndicMap = array();
    
    $setIDToUMLSIndicMap = array();

    $setIDToSNOMEDIndicMap = array();

    $drugnames = array();

    $dcids = array();

    // If the drugname is specified (textbox is not empty) and the drug class checkbox is not ticked
    if(isset($_POST['drugname']) && $drugname <> "" && $isdrugclass == ""){
        //do nothing
    }
    else if(isset($_POST['drugname']) && $drugname <> "" && $isdrugclass == "true"){    // If we are searching for all drugs in a particular drug class
        
        // Different output flag value (we have to display a table with numbers representing indication matches per drug)
        $candisplaystuff = 3;
        $data = array();
        // First get all ATC codes and Struct_IDs for given drug class
        // Query string formulation
        $getSetIDsQueryStr = "SELECT name, dm_id, dc_id FROM dailymed_comparison WHERE atc LIKE '".$drugname."%'";

        //$getStructIDsAndATCCodesQueryStr = "select distinct a.struct_id, b.atc from struct2atc a, cardiovascular_drugs b where a.atc_code like '".$drugname."%' and a.atc_code = b.atc and (b.atc NOT IN (SELECT distinct c.atc FROM dailymed_comparison c WHERE c.atc LIKE 'C%'));";
        
        // Actual query directive to get the Struct_IDs and ATC codes for the given drug class
        $getSetIDsQueryResult = pg_query($conn, $getSetIDsQueryStr) or die("Could not execute query");

        // If there is at least one atc / set id pair for this drug class in DrugCentrals DB
        if (pg_num_rows($getSetIDsQueryResult) > 0){
            // Initialise array of SET IDs (drugs) that will match our condition: that they have co-prescribed medication tags in them

            //Inaccurate indication codes overall (UMLS)
            $umlsCodes = array();
            //Inaccurate indication codes overall (DOID)
            $doidCodes = array();

            // For each drug, check if it has "role" prefixed tags in its annotations and store these to array
            while($grow = pg_fetch_array($getSetIDsQueryResult)){                        
                // add name to array
                array_push($drugnames, $grow['name']);
                // add dcid to array
                array_push($dcids, $grow['dc_id']);
                
                // Set the base URL on DailyMed where we are going to search for drug annotations 
                $dailyMedBaseUrl = "https://dailymed.nlm.nih.gov/dailymed/drugInfo.cfm?setid=";

                // Append the SET ID so we can isolate the actual web page on DailyMed for the given drug
                $dailyMedDrugUrl = trim($dailyMedBaseUrl.$grow['dm_id']);

                //echo $dailyMedDrugUrl." ";

                //echo "<a href='".$dailyMedDrugUrl."'>".$grow['dm_id']."</a><br>";

                // Actually get all the Hypothes.is annotations.
                // (a) first we pull annotations from the dailymed private group
                $pvtGroupAnnotations = $hypothesis->search(array('uri' => $dailyMedDrugUrl, 'group' => 'pGv4X7iV'), $token);
                //print_r($pvtGroupAnnotations);
                // (b) and then we pull the public annotations
                $annotations = $hypothesis->search(array('uri' => $dailyMedDrugUrl), $token);
                //print_r($annotations);
                // (c) add all private annotations to the public annotations array (collect all indications in one array)
                foreach ($pvtGroupAnnotations as $value) {
                    array_push($annotations, $value);
                }

                $numAnno = 0;
                foreach ($annotations as $value) {
                    $numAnno++;
                }                

                // Set of inaccurate indication codes (UMLS)
                $umlsCodesPerDrug = array();
                $umlsCodesPerDrugPrint = array();
                // Set of inaccurate indication codes (UMLS)
                $doidCodesPerDrug = array();
                $doidCodesPerDrugPrint = array();
                // Flag to stop when we find an annotation with "role" prefixed tag
                $globalDone = false;
                // Index for annotations
                $globalIndex = 0;
                // While we didn't find a "role" prefixed annotation tag and we haven't run out of annotations
                //echo "num annos: ".$numAnno."<br>";
                while (!$globalDone && ($globalIndex < $numAnno)){
                    // Keep a reference to the current array of tags in this annotation
                    $current_annotation_tags = $annotations[$globalIndex]->tags;
                    //echo "num tags in anno ".($globalIndex+1).": ".count($current_annotation_tags)."<br>";

                    // Flag to stop when we find a tag with "role" as prefix
                    $done = false;
                    // Index for tags
                    $index = 0;
                    // Iterate through these tags to see if one of them has "role" as prefix
                    // While we didn't find a "role" prefixed tag and we haven't run out of tags in this annotation
                    while (!$done && ($index < count($current_annotation_tags))){
                        //echo substr($current_annotation_tags[$index], 0, 4).", ";
                        // If the first four or five characters of this tag are "role" or "roles" respectively OR the tag is a "rejected" one OR the tag is "AT" (adjunctive therapy)
                        $type = "";
                        if ((substr($current_annotation_tags[$index], 0, 4) == "role") || (substr($current_annotation_tags[$index], 0, 5) == "roles") || ($current_annotation_tags[$index] == "rejected") || ($current_annotation_tags[$index] == "AT")){


                            if (substr($current_annotation_tags[$index], 0, 4) == "role"){
                                $type = "role";
                            }
                            if (substr($current_annotation_tags[$index], 0, 5) == "roles"){
                                $type = "roles";
                            }
                            if ($current_annotation_tags[$index] == "rejected"){
                                $type = "rejected";
                            }
                            if ($current_annotation_tags[$index] == "AT"){
                                $type = "AT";
                            }


                            // This drug does have role / rejected tags so add it to the list and stop
                            //$done = true;
                            //$globalDone = true;
                            array_push($matchingSETIDs, $grow['dm_id']);

                            for ($x = 0; $x < count($current_annotation_tags); $x++){
                                // Get the first four characters of the tag (the annotation protocol dictates that DOID codes
                                // and UMLS codes start with either "DOID" or "UMLS" respectively)
                                $firstFourChars = substr($current_annotation_tags[$x], 0, 4);

                                if ($firstFourChars == "targ"){
                                    //UMLS
                                    $pos = strpos($current_annotation_tags[$x], "target:UMLS_CUI:");
                                    if ($pos === false){
                                        //DOID
                                        $pos = strpos($current_annotation_tags[$x], "target:DOID_");
                                        if ($pos === false){
                                        }
                                        else{
                                            $pos2 = strpos($current_annotation_tags[$x], '_');
                                            array_push($doidCodesPerDrug, substr($current_annotation_tags[$x],$pos2+1));
                                            array_push($doidCodesPerDrugPrint, substr($current_annotation_tags[$x],$pos2+1)."_".$type);
                                        }
                                    }
                                    else{
                                        $pos2 = strposX($current_annotation_tags[$x], ':', 2);
                                        array_push($umlsCodesPerDrug, substr($current_annotation_tags[$x],$pos2+1));
                                        array_push($umlsCodesPerDrugPrint, substr($current_annotation_tags[$x],$pos2+1)."_".$type);
                                    }
                                }

                                if ($firstFourChars == "UMLS"){
                                    $pos = strpos($current_annotation_tags[$x], ':');
                                    array_push($umlsCodesPerDrug, substr($current_annotation_tags[$x],$pos+1));
                                    array_push($umlsCodesPerDrugPrint, substr($current_annotation_tags[$x],$pos+1)."_".$type);
                                }

                                if ($firstFourChars == "DOID"){
                                    $colon = false;
                                    $colon = contains($current_annotation_tags[$x], ':');
                                    if ($colon){
                                        $pos = strpos($current_annotation_tags[$x], ':');
                                        array_push($doidCodesPerDrug, substr($current_annotation_tags[$x],$pos+1));
                                        array_push($doidCodesPerDrugPrint, substr($current_annotation_tags[$x],$pos+1)."_".$type);
                                    }
                                    else{
                                        $pos = strpos($current_annotation_tags[$x], '_');
                                        array_push($doidCodesPerDrug, substr($current_annotation_tags[$x],$pos+1));
                                        array_push($doidCodesPerDrugPrint, substr($current_annotation_tags[$x],$pos+1)."_".$type);
                                    }
                                }



                            }

                            

                        }

                        
                        // Next tag
                        $index++;
                    }
                    // Next annotation
                    $globalIndex++;
                }

                $umlsCodesPerDrug = array_unique($umlsCodesPerDrug);
                $umlsCodesPerDrug = array_values($umlsCodesPerDrug);
                $umlsCodesStr = "'" . implode( "','", $umlsCodesPerDrug ) . "'";
                //echo "<br>";
                //echo $umlsCodesStr."<br>";
                $getDCIndicationsQueryStr = "SELECT umls_cui, snomed_conceptid from omop_relationship where struct_id = '".$grow['dc_id']."' and relationship_name = 'indication' and umls_cui in ($umlsCodesStr) ";

                $umlsCodesPerDrugPrint = array_unique($umlsCodesPerDrugPrint);
                $umlsCodesPerDrugPrint = array_values($umlsCodesPerDrugPrint);
                
                //echo $getDCIndicationsQueryStr."<br>";
                //echo "<br>";
                $getDCIndicationsQueryResult = pg_query($conn, $getDCIndicationsQueryStr) or die("Could not execute query");
                
                $snomedCodesDC = array();
                $umlsCodesDC = array();
                if (pg_num_rows($getDCIndicationsQueryResult) > 0){
                    while($grow2 = pg_fetch_array($getDCIndicationsQueryResult)){
                        array_push($snomedCodesDC, $grow2['snomed_conceptid']);
                        array_push($umlsCodesDC, $grow2['umls_cui']);
                    }
                }     

                $setIDToNameMap[$grow['dm_id']] = $grow['name'];
                $setIDToDCIDMap[$grow['dm_id']] = $grow['dc_id'];
                $setIDToUMLSIndicMap[$grow['dm_id']] = implode(", ",$umlsCodesDC);
                $setIDToSNOMEDIndicMap[$grow['dm_id']] = implode(", ",$snomedCodesDC);
                $setIDToIndicMap[$grow['dm_id']] = implode(", ",$umlsCodesPerDrugPrint);
            }

            $matchingSETIDs = array_unique($matchingSETIDs);
            $matchingSETIDs = array_values($matchingSETIDs);

            // Now print our results to HTML page  
        }
    }
?>

<!-- DISPLAY TABLE OF RESULTS -->
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
                .back 
{                    width: 100%;
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
        
                <form action="test2.php" method="post">
            
                    <div align="center">
                        <div style="color: #747f8c; font-size: 20px;"> Drug name or class: </div> <br> <input type="text" id="drugname" name="drugname"> 
                    </div>
                    <br>
                    <div align="center">
                        <input type="checkbox" name="isdrugclass" value="true">Class<br>
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
                        <td><?php echo str_replace("DOID", "", $grow['doid']); ?></td>
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
                        <td><?php echo $dailyMedIndications[$x][2]; ?></td>
                        <td><?php echo $dailyMedIndications[$x][3]; ?></td>
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
                else if ($candisplaystuff == 999){
                    echo '<script language="javascript">';
                    echo 'alert("Could not find information for this drug or class.")';
                    echo '</script>';
                    exit;
                } 
                else if ($candisplaystuff == 3){ ?>
                    <h1><?php echo "Drug Class: ".$drugname; ?></h1>
                    <div class="container">
                      <table class="table table-hover" style="table-layout:fixed">
                        <thead>
                          <tr bgcolor="#51ede4">
                            <th>#</th>
                            <th>DrugCentral Link</th>
                            <th>IDS Annotations Link</th>
                            <th>Inaccurate Indications (IIs)</th>
                            <th>IIs On DrugCentral (SNOMED)</th>
                            <th>IIs On DrugCentral (UMLS)</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php for($x = 0; $x < count($matchingSETIDs); $x++) { ?>
                          <tr>
                                <td><?php echo $x+1; ?></td>
                                <td><?php echo "<a href='http://drugcentral.org/drugcard/".$setIDToDCIDMap[$matchingSETIDs[$x]]."'>".$setIDToNameMap[$matchingSETIDs[$x]]."</a>"; ?></td>
                                <td><?php echo "<a href='https://dailymed.nlm.nih.gov/dailymed/drugInfo.cfm?setid=".$matchingSETIDs[$x]."'>".$setIDToNameMap[$matchingSETIDs[$x]]."</a>"; ?></td>           
                                <td><?php echo $setIDToIndicMap[$matchingSETIDs[$x]]; ?></td>
                                <td><?php echo $setIDToSNOMEDIndicMap[$matchingSETIDs[$x]]; ?></td>
                                <td><?php echo $setIDToUMLSIndicMap[$matchingSETIDs[$x]]; ?></td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
        <?php }
            } ?>
    
        </section>
        
    </body>
    
</html> 
<!-- END OF DOCUMENT -->