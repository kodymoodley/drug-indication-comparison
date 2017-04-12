<!-- BEGINNING OF DOCUMENT -->
<!DOCTYPE html>

<?php
    // Hypothes.is API TOKEN: (just a test) 6879-A0VNmaeMeE7HZuaehDrRcGWooSRY68dXNbawDjVwJxY

    // DrugCentral DB connection pot
    include("conn_dev.php");            

    // Hypothes.is API wrapper (I also wrapped the DailyMed API call that we use here, in there as well)
    require_once('Hypothesis.inc.php'); 

    // Don't time out script if it takes long
    set_time_limit(0);

    // Function to calculate the difference between two arrays
    function arrayRecursiveDiff($a1, $a2) {
        $result = array();
        foreach($a1 as $va1) {
            $found = false;
            foreach($a2 as $va2) {
              $x = array_diff($va1, $va2);
              if (empty($x)) {
                 $found = true;
              }
            }
            if (!$found) { 
              $result[] = $va1;
            }
        }

        foreach($a2 as $va2) {
           $found = false;
           foreach($a1 as $va1) {
              $x = array_diff($va2, $va1);
              if (empty($x)) {
                 $found = true;
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
            sort($val);
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

    // Hypothes.is API TOKEN
    $token = '6879-A0VNmaeMeE7HZuaehDrRcGWooSRY68dXNbawDjVwJxY';

    // Hypothes.is and DailyMed PHP Wrapper instances 
    $hypothesis = new HypothesisAPI();
    $dailyMed = new DailyMedAPI();

    // Drugname typed in input textbox
    $drugname=@$_POST['drugname'];

    // Whether the checkbox to indicate drug class has been ticked or not
    $isdrugclass=@$_POST['isdrugclass'];
    	
    // Just a input validation flag to check if we entered entered anything or if its a single drug or class of drugs
    // that we are reporting on
    $candisplaystuff = 0;

    // If the drugname is specified (textbox is not empty) and the drug class checkbox is not ticked
    if(isset($_POST['drugname']) && $drugname <> "" && $isdrugclass == ""){
        
        // Can display results because textbox is not empty
        $candisplaystuff = 2;

        // -------- DRUGCENTRAL STUFF ---------- //
        // Query string to get drug central id for the given drug name
        $atcCodeQueryStr = "SELECT code from atc where chemical_substance = '".$drugname."'";
        // Actual query to get the drug central id
        $atcCodeQueryResult = pg_query($conn, $atcCodeQueryStr) or die("Could not execute query");
        // Get the single cell in the result of the query
        $atcCell = pg_fetch_object($atcCodeQueryResult);
        // Get the single value inside the cell
        $atc = $atcCell->code;

        // Get the struct id of the drug on the drug central database. The struct id is needed so we can 
        // pull up the indication information about the drug from the drug central database table:
        // omop_relationship
        // First the query string
        $getStructIDQueryStr = "SELECT struct_id from struct2atc where atc_code ='".$atc."'";
        // The actual query
        $getStructIDQueryResult = pg_query($conn, $getStructIDQueryStr) or die("Could not execute query");
        // Get the single cell in the result of the query
        $structIDCell = pg_fetch_object($getStructIDQueryResult);
        // Get the single value inside the cell
        $struct_id = $structIDCell->struct_id;

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
        // DailyMed API e.g. https://dailymed.nlm.nih.gov/dailymed/services/v2/spls.json?drug_name=cisplatin
        // Get SPL (structured product label) information from DailyMed about the drug(s) that have the name given.
        // I.e., returns a JSON object which holds an array of products (product labels) on DailyMed which have 
        // the given drug name as the main ingredient
        $splDailyMed = $dailyMed->getSPLInfo($drugname);

        // We should have at least one product label for the drug on DailyMed (count the size of the returned array)
        if (count($splDailyMed->data) > 0){

            // There may be more than one product containing the same drug (from other manufacturers etc.)
            // We are only interested in the one which is annotated by Linda! We have to loop through
            // each label until we find it.

            // Get the array / list of drugs from the JSON object
            $d = $splDailyMed->data;

            // Get the number of products in the array (size of array)
            $numDMEntries = count($d);

            // Now we will loop through each of them until we find the one with the annotations
            $done = false;
            $idx = 0;
            while (!$done && ($idx < $numDMEntries)){

                // Not "first drug". A better name for this variable would be "current drug"
                // Get current drug in the array
                $firstDrug = $d[$idx]; 

                // Get the SET ID (DailyMed ID) for the drug
                $set_id = $firstDrug->setid;

                // Set the base URL on DailyMed where we are going to search for drug annotations 
                $dailyMedBaseUrl = "https://dailymed.nlm.nih.gov/dailymed/drugInfo.cfm?setid=";

                // Add the SET ID so we can isolate the actual web page on DailyMed for the given drug
                $dailyMedDrugUrl = $dailyMedBaseUrl.$set_id;

                // Actually get all the hypothesis annotations. This calls the Hypothes.is API command for getting all the annotations on this page
                // I.e., all annotations at this URL which are by lrieswijk and have a tag in it called "indication" (the indications) 
                // NB. each annotation is actually a set / array of tags, so $result is a set of arrays, one for each indication
                $result = $hypothesis->search(array('uri' => $dailyMedDrugUrl, 'user' => 'lrieswijk', 'tag' => 'indication'), $token);
                //$result = $hypothesis->search(array('uri' => $dailyMedDrugUrl, 'user' => 'lrieswijk'), $token);
                //$result = $hypothesis->search(array('uri' => $dailyMedDrugUrl, 'group' => 'dailymed'), $token);

                $dailyMedIndications = array(count($result));

                // If there is at least one indication for the drug
                if (count($result) > 0){
                    
                    $str = "";
                    $idx2 = 0;

                    // For each indication listed (NEED TO FIX THIS FOREACH AND HOW I STORE AND DISPLAY THE INFO IN THE TABLE!!!)
                    foreach ($result as $value) {

                        // Initialise current row (array) of the indications table
                        $currentRow = array(6);

                        // Add the first column value (SET ID)
                        $currentRow[0] = $set_id;
                        
                        // Drill down to the actual text that we annotating
                        $t = $value->target;

                        // ?
                        $s = $t[0]->selector;
                        
                        // Now add the second column value (indication name or text)
                        $currentRow[1] = $s[3]->exact;

                        // Get the tags for this indication as an array
                        $annotations = $value->tags;

                        // ?
                        $arr = array();
                        array_push($arr, 2);
                        array_push($arr, 3);

                        // Iterate through the tags to find UMLS and DOID codes
                        for ($x = 0; $x < count($annotations); $x++){

                            // Get the first four characters of the tag (the annotation protocol dictates that DOID codes
                            // and UMLS codes start with either "DOID" or "UMLS" respectively)
                            $firstFourChars = substr($annotations[$x], 0, 4);

                            if ($firstFourChars == "UMLS"){
                                // Third column of row (UMLS Code)
                                $currentRow[2] = $annotations[$x];

                                // ?
                                if(($key = array_search(2, $arr)) !== false) {
                                    unset($arr[$key]);
                                }
                            }

                            if ($firstFourChars == "DOID"){
                                // Fourth column of row (DOID Code)
                                $currentRow[3] = $annotations[$x];

                                // ?
                                if(($key = array_search(3, $arr)) !== false) {
                                    unset($arr[$key]);
                                }
                            }
                        }

                        // Way to mark cells in the table that don't have values. HORRIBLE hack!! Need to fix this section
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

                        // ?
                        $currentRow[4] = "";
                        $currentRow[5] = "";
                        
                        // Rows of table
                        $dailyMedIndications[$idx2] = $currentRow;

                        // Increment row for this indication
                        $idx2++;
                    }

                    $done = true;
                }

                // increment product label index (go to next product label)
                $idx++;
            }
        }
    }
    else if(isset($_POST['drugname']) && $drugname <> "" && $isdrugclass == "true"){
        $candisplaystuff = 3;

        //$resultData = array();
        // First get all ATC codes and Struct_IDs for given drug class
        // Query string formulation
        $getStructIDsAndATCCodesQueryStr = "SELECT struct_id, atc_code FROM struct2atc WHERE atc_code like '".$drugname."%'";

        // Actual query directive to get the struct_id and ATC codes for the given drug class
        $getStructIDsAndATCCodesQueryResult = pg_query($conn, $getStructIDsAndATCCodesQueryStr) or die("Could not execute query");

        // COUNTERS
        // INDICATIONS

        $currentATC = "";
       
        $data = array();

        $counter = 1;

        $totalExactMatchesUMLS = 0;
        $totalExactMatchesDOID = 0;
        $totalExactMatchesTotal = 0;

        // For each drug get its indications and store to results array
        while($grow = pg_fetch_array($getStructIDsAndATCCodesQueryResult)){
            $numIndicationsDC_coded = 0;        // DRUG CENTRAL has UMLS and / or DOID
            $numIndicationsDM_coded = 0;        // DAILY MED has UMLS and / or DOID
            $numIndicationsDC = 0;              // DRUG CENTRAL TOTAL INDICATIONS
            $numIndicationsDM = 0;              // DAILY MED TOTAL INDICATIONS

            // EXACT MATCHES
            $numExactMatchesUMLS = 0;
            $numExactMatchesDOID = 0;
            $numExactMatchesTotal = 0;

            $tmpRow = array();
            
            
            // Get the name of the drug as well
            // Query string to get drug name

            $currentATC = $grow['atc_code'];

            $getDrugNameQueryStr = "SELECT chemical_substance FROM atc WHERE code = '".$grow['atc_code']."'";
            // Actual query directive to get drug name
            $getDrugNameQueryResult = pg_query($conn, $getDrugNameQueryStr) or die("Could not execute query");
            // Get the single cell in the result of the query
            $drugNameCell = pg_fetch_object($getDrugNameQueryResult);
            // Get the single value inside the cell
            $drug_name = $drugNameCell->chemical_substance;
            
            array_push($tmpRow, $counter);
            array_push($tmpRow, $grow['atc_code']);
            array_push($tmpRow, $drug_name);
            array_push($tmpRow, $grow['struct_id']);
            // DRUG CENTRAL
            $struct_id = $grow['struct_id'];
            // Query string to get all the drug central indications for current drug
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

            $umlsIndicationsDC = array();
            $doidIndicationsDC = array();
            $UMLSDOIDPairsDC = array();

            
            while($grow = pg_fetch_array($getIndicationCodesQueryResult)){
                $currentUMLSDOIDPairDC = array();
                if ($grow['concept_name'] <> ""){
                    $numIndicationsDC++;
                }
                if (($grow['umls_cui'] <> "") || ($grow['doid'] <> "")){
                    if ($grow['umls_cui'] <> ""){
                        array_push($currentUMLSDOIDPairDC, $grow['umls_cui']);
                        array_push($umlsIndicationsDC, $grow['umls_cui']);    
                    }
                    if ($grow['doid'] <> ""){
                        //$currentUMLSDOIDPairDC.= substr($grow['doid'],5);
                        array_push($currentUMLSDOIDPairDC, substr($grow['doid'],5));
                        array_push($doidIndicationsDC, substr($grow['doid'],5));    
                    }
                    $numIndicationsDC_coded++;
                }
                array_push($UMLSDOIDPairsDC, $currentUMLSDOIDPairDC);
            }

            // DAILY MED
            // DailyMed API e.g. https://dailymed.nlm.nih.gov/dailymed/services/v2/spls.json?drug_name=cisplatin
            // Get SPL (structured product label) information from DailyMed about the drug(s) that have the name given.
            // I.e., returns a JSON object which holds an array of products (product labels) on DailyMed which have 
            // the given drug name as the main ingredient

            $splDailyMed = $dailyMed->getSPLInfo($drug_name); 

            // We should have at least one product label for the drug on DailyMed (count the size of the returned array)
            if (count($splDailyMed->data) > 0){

                // There may be more than one product containing the same drug (from other manufacturers etc.)
                // We are only interested in the one which is annotated by Linda! We have to loop through
                // each label until we find it.

                // Get the array / list of drugs from the JSON object
                $d = $splDailyMed->data;

                // Get the number of products in the array (size of array)
                $numDMEntries = count($d);

                // Now we will loop through each of them until we find the one with the annotations
                $done = false;
                $idx = 0;
                while (!$done && ($idx < $numDMEntries)){
 
                    // Not "first drug". A better name for this variable would be "current drug"
                    // Get current drug in the array
                    $firstDrug = $d[$idx]; 

                    // Get the SET ID (DailyMed ID) for the drug
                    $set_id = $firstDrug->setid;

                    // Set the base URL on DailyMed where we are going to search for drug annotations 
                    $dailyMedBaseUrl = "https://dailymed.nlm.nih.gov/dailymed/drugInfo.cfm?setid=";

                    // Add the SET ID so we can isolate the actual web page on DailyMed for the given drug
                    $dailyMedDrugUrl = $dailyMedBaseUrl.$set_id;

                    // Actually get all the hypothesis annotations. This calls the Hypothes.is API command for getting all the annotations on this page
                    // I.e., all annotations at this URL which are by lrieswijk and have a tag in it called "indication" (the indications) 
                    // NB. each annotation is actually a set / array of tags, so $result is a set of arrays, one for each indication
                    $result = $hypothesis->search(array('uri' => $dailyMedDrugUrl, 'user' => 'lrieswijk', 'tag' => 'indication'), $token);
                    //$result = $hypothesis->search(array('uri' => $dailyMedDrugUrl, 'user' => 'lrieswijk'), $token);
                    //$result = $hypothesis->search(array('uri' => $dailyMedDrugUrl, 'group' => 'dailymed'), $token);

                    // Count the number of items in the set (number of indications)
                    $numIndicationsDM = count($result);

                    // Table display values  
                    $umlsIndicationsDM = array();
                    $doidIndicationsDM = array();
                    $UMLSDOIDPairsDM = array();

                    // If there is at least one indication for the drug
                    if (count($result) > 0){

                        // Table display values
                        array_push($tmpRow, $set_id);
                        array_push($tmpRow, $numIndicationsDC);
                        array_push($tmpRow, $numIndicationsDM);

                        // For each indication
                        foreach ($result as $value) {

                            // Get the set of tags for this specific indication as an array
                            $annotations = $value->tags;

                            // We are going to store UMLS and DOID codes as pairs (so that we can associate / map the two codes to each other)
                            $currentUMLSDOIDPairDM = array();

                            // Iterate through each tag to find UMLS and DOID codes
                            for ($x = 0; $x < count($annotations); $x++){
                                
                                // Get the first four characters of the tag (the annotation protocol dictates that DOID codes
                                // and UMLS codes start with either "DOID" or "UMLS" respectively)
                                $firstFourChars = substr($annotations[$x], 0, 4);

                                // Store the codes based on their category
                                if ($firstFourChars == "UMLS"){
                                    // Here we take all characters after the 9th. I.e., all characters after the expression "UMLS_CUI:" 
                                    // which is 9 characters long.
                                    array_push($currentUMLSDOIDPairDM, substr($annotations[$x],9));
                                    array_push($umlsIndicationsDM, substr($annotations[$x],9));
                                }
                                if ($firstFourChars == "DOID"){
                                    // Here we take all characters after the 5th. I.e., all characters after the expression "DOID_" 
                                    // which is 5 characters long.
                                    array_push($currentUMLSDOIDPairDM, substr($annotations[$x],5));
                                    array_push($doidIndicationsDM, substr($annotations[$x],5));
                                }
                            }

                            // Store the UMLS-DOID pairs for the indications for this drug
                            array_push($UMLSDOIDPairsDM, $currentUMLSDOIDPairDM);
                        }

                        // We have at least one annotation (indication) for this drug so we can exit the loop
                        // NB. the protocol only annotates one product label per drug so there will be only 
                        // one product label with annotations on it
                        $done = true;
                    }

                    // increment product label index (go to next product label)
                    $idx++;
                }

                // Calculate the the number of exact matches for UMLS codes between DrugCentral and DailyMed
                $numExactMatchesUMLS = count(array_intersect($umlsIndicationsDC, $umlsIndicationsDM));

                // Calculate the the number of exact matches for DOID codes between DrugCentral and DailyMed
                $numExactMatchesDOID = count(array_intersect($doidIndicationsDC, $doidIndicationsDM));

                // Get the UMLS-DOID pairs that are common to both DrugCentral and DailyMed
                $result = array_map("unserialize", array_intersect(serialize_array_values($UMLSDOIDPairsDC),serialize_array_values($UMLSDOIDPairsDM)));

                // Use the above $result to calculate the unique ones in DrugCentral
                $uniqueDC = arrayRecursiveDiff($UMLSDOIDPairsDC, $result);

                // Use the above $result to calculate the unique ones in DailyMed
                $uniqueDM = array_map("unserialize", array_diff(serialize_array_values($UMLSDOIDPairsDM),serialize_array_values($result)));;
                
                // Tally up the counts for the above metrics
                $uniqueDCCount = count($uniqueDC);
                $uniqueDMCount = count($uniqueDM);
                $numExactMatchesTotal = count($result);

                // Display values to the table data array
                array_push($tmpRow, $numExactMatchesUMLS);
                array_push($tmpRow, $numExactMatchesDOID);
                array_push($tmpRow, $numExactMatchesTotal);
                array_push($tmpRow, $uniqueDCCount);
                array_push($tmpRow, $uniqueDMCount);
                
                // Only increment the row number, and add the row data to the table, if the current drug actually has indications
                if ($numIndicationsDM > 0){
                    $counter++;
                    array_push($data, $tmpRow);
                }
            }
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
                else if ($candisplaystuff == 3){ ?>
                    <h1><?php echo "Drug Class: ".$drugname; ?></h1>
                    <div class="container">
                      <table class="table table-hover" style="table-layout:fixed">
                        <thead>
                          <tr bgcolor="#51ede4">
                            <th>#</th>
                            <th>ATC</th>
                            <th>Name</th>
                            <th>DC ID</th>
                            <th>DM ID</th>
                            <th># Indic. (DC)</th>
                            <th># Indic. (DM)</th>
                            <th>UMLS Exact</th>
                            <th>DOID Exact</th>
                            <th>Total Exact</th>
                            <th>Unique (DC)</th>
                            <th>Unique (DM)</th>
                            
                            
                            <!--<th># Unique Indications (DC)</th>
                            <th># Unique Indications (DM)</th>-->
                          </tr>
                        </thead>
                        <tbody>
                        <?php for($x = 0; $x < count($data); $x++) { ?>
                          <tr>
                                <td><?php echo $data[$x][0]; ?></td>
                                <td><?php echo $data[$x][1]; ?></td>
                                <td><?php echo $data[$x][2]; ?></td>
                                <td><?php echo $data[$x][3]; ?></td>
                                <td><?php echo $data[$x][4]; ?></td>
                                <td><?php echo $data[$x][5]; ?></td>
                                <td><?php echo $data[$x][6]; ?></td>
                                <td><?php echo $data[$x][7]; ?></td>
                                <td><?php echo $data[$x][8]; ?></td>
                                <td><?php echo $data[$x][9]; ?></td>
                                <td><?php echo $data[$x][10]; ?></td>
                                <td><?php echo $data[$x][11]; ?></td>
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