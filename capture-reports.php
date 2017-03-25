<!DOCTYPE html>
<?php
    include("conn_dev.php");

    $drugname=@$_POST['drugname'];
    	
    $candisplaystuff = 0;
    if(isset($_POST['drugname']) && $drugname <> ""){
        $candisplaystuff = 2;
        $getStructIDQueryStr = "SELECT struct_id FROM active_ingredient WHERE substance_name like '%".$drugname."%' LIMIT 1";
        $getStructIDQueryResult = pg_query($conn, $getStructIDQueryStr) or die("Could not execute query");
        $structIDCell = pg_fetch_object($getStructIDQueryResult);
        $struct_id = $structIDCell->struct_id;
        $getATCQueryStr = "SELECT atc_code FROM struct2atc WHERE struct_id = $struct_id";
        $getATCQueryResult = pg_query($conn, $getATCQueryStr) or die("Could not execute query");
        $atcCell = pg_fetch_object($getATCQueryResult);
        $atc = $atcCell->atc_code;
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
        $getIndicationCodesQueryResult = pg_query($conn, $getIndicationCodesQueryStr) or die("Could not execute query");
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
        
                <form action="capture-reports.php" method="post">
            
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
              <table class="table table-hover">
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
                        <td><?php echo $grow['doid']; ?></td>
                        <td><?php echo $grow['snomed_conceptid']; ?></td>
                        <td><?php echo $grow['snomed_full_name']; ?></td>
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