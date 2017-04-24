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

    // Hypothes.is API TOKEN
    $token = '6879-ef-QplA3MslmdVLEuDjP0pQrHmkAxhweWPjAZYOlH_M';

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

    // Input error flag
    $error = 0;

    // If the drugname is specified (textbox is not empty) and the drug class checkbox is not ticked
    if(isset($_POST['drugname']) && $drugname <> "" && $isdrugclass == ""){
        
        // Can display results because textbox is not empty
        $candisplaystuff = 2;

        // -------- DRUGCENTRAL STUFF ---------- //
        // Query string to get drug central id for the given drug name
        $atcCodeQueryStr = "SELECT code from atc where chemical_substance = '".$drugname."' limit 1";
        // Actual query to get the drug central id
        $atcCodeQueryResult = pg_query($conn, $atcCodeQueryStr) or die("Could not execute query");
        
        // If there is exactly one result
        if (pg_num_rows($atcCodeQueryResult) == 1){
            //echo "Rows: ".pg_num_rows($atcCodeQueryResult)." <br>";
            // Get the single cell in the result of the query
            $atcCell = pg_fetch_object($atcCodeQueryResult);
            // Get the single value inside the cell
            $atc = $atcCell->code;

            // Get the struct id of the drug on the drug central database. The struct id is needed so we can 
            // pull up the indication information about the drug from the drug central database table:
            // omop_relationship
            // First the query string
            $getStructIDQueryStr = "SELECT struct_id from struct2atc where atc_code ='".$atc."' limit 1";
            // The actual query
            $getStructIDQueryResult = pg_query($conn, $getStructIDQueryStr) or die("Could not execute query");

            // If there is exactly one result
            if (pg_num_rows($getStructIDQueryResult) == 1){
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

                // If there is at least one indication for this drug
                if (pg_num_rows($getIndicationCodesQueryResult) > 0){

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
                    else{
                        echo "No SPL for: ".$drugname."<br>";
                    }                  
                }
                else{
                    $candisplaystuff = 999;
                }
            }
            else{
                $candisplaystuff = 999;
            }
        }
        else{
            $candisplaystuff = 999;
        }
    }
    else if(isset($_POST['drugname']) && $drugname <> "" && $isdrugclass == "true"){    // If we are searching for all drugs in a particular drug class
        
        // Different output flag value (we have to display a table with numbers representing indication matches per drug)
        $candisplaystuff = 3;
        $data = array();
        // First get all ATC codes and Struct_IDs for given drug class
        // Query string formulation
        $getStructIDsAndATCCodesQueryStr = "SELECT struct_id, atc_code FROM struct2atc WHERE atc_code LIKE '".$drugname."%' AND atc_code NOT IN (SELECT atc FROM dailymed_comparison WHERE atc LIKE '".$drugname."%');";

        //$getStructIDsAndATCCodesQueryStr = "select distinct a.struct_id, b.atc from struct2atc a, cardiovascular_drugs b where a.atc_code like '".$drugname."%' and a.atc_code = b.atc and (b.atc NOT IN (SELECT distinct c.atc FROM dailymed_comparison c WHERE c.atc LIKE 'C%'));";
        
        // Actual query directive to get the struct_id and ATC codes for the given drug class
        $getStructIDsAndATCCodesQueryResult = pg_query($conn, $getStructIDsAndATCCodesQueryStr) or die("Could not execute query");

        // If there is at least one atc / struct code for this drug in DrugCentrals DB
        if (pg_num_rows($getStructIDsAndATCCodesQueryResult) > 0){
            //echo "at least one atc";

            // Initialise atc struct and setid codes for current drug
            $currentATC = "";
            $struct_id = 0;
            $set_id = "";

            // Initialise row number for results table
            $counter = 1;

            // For each drug, get its indications and store to results array
            while($grow = pg_fetch_array($getStructIDsAndATCCodesQueryResult)){

                // Initialise totals
                $numIndicationsDC_coded = 0;    // Num indications for this drug on DRUGCENTRAL which actually have UMLS and / or DOID terms linked
                $numIndicationsDM_coded = 0;    // Num indications for this drug on DailyMed which actually have UMLS and / or DOID terms linked (should be all)
                $numIndicationsDC = 0;          // Num indications for this drug on DRUGCENTRAL ($numDOIDtermsDC + $numUMLStermsDC)
                $numIndicationsDM = 0;          // Num indications for this drug on DailyMed ($numDOIDtermsDM + $numUMLStermsDM)
                $numUMLStermsDC = 0;            // Num UMLS terms for this drug on DrugCentral
                $numUMLStermsDM = 0;            // Num UMLS terms for this drug on DailyMed
                $numDOIDtermsDC = 0;            // Num DOID terms for this drug on DrugCentral
                $numDOIDtermsDM = 0;            // Num DOID terms for this drug on DailyMed
                $numUMLSDOIDPairsDC = 0;        // Num UMLS-DOID pairs for this drug on DrugCentral
                $numUMLSDOIDPairsDM = 0;        // Num UMLS-DOID pairs for this drug on DailyMed
                $numContextAnnotations = 0;     // Num context annotations for this drug (obviously can only be from DailyMed)

                // Initialise totals for exact matches
                $numExactMatchesUMLS = 0;       // Num exact matches for UMLS terms between DailyMed and DrugCentral for this drug
                $numExactMatchesDOID = 0;       // Num exact matches for DOID terms between DailyMed and DrugCentral for this drug
                $numExactMatchesTotal = 0;      // Num exact matches in total for ontology terms between DailyMed and DrugCentral, for this drug

                // Initialise arrays for DrugCentral indications
                $umlsIndicationsDC = array();
                $doidIndicationsDC = array();
                $UMLSDOIDPairsDC = array();

                // Initialise arrays for DailyMed indications
                $umlsIndicationsDM = array();
                $doidIndicationsDM = array();
                $UMLSDOIDPairsDM = array();

                // Initialise current row data in the table
                $tmpRow = array();
                
                // keep reference to ATC code for this drug                
                $currentATC = $grow['atc'];

                // Get the name of the drug as well:
                // (a) Query string to get drug name            
                $getDrugNameQueryStr = "SELECT chemical_substance FROM atc WHERE code = '".$grow['atc']."' limit 1";
                // (b) Actual query directive to get drug name
                $getDrugNameQueryResult = pg_query($conn, $getDrugNameQueryStr) or die("Could not execute query");

                // If there is exactly one drug name
                if (pg_num_rows($getDrugNameQueryResult) == 1){
                    // Get the single cell in the result of the query
                    $drugNameCell = pg_fetch_object($getDrugNameQueryResult);
                    // Get the single value inside the cell
                    $drug_name = $drugNameCell->chemical_substance;

                    //echo "name: ".$drug_name;
                    
                    // Start populating the row with data we already have (1. row number, 2. atc code, 3. drugname, 4. Struct ID)
                    array_push($tmpRow, $counter);
                    array_push($tmpRow, $grow['atc']);
                    array_push($tmpRow, $drug_name);
                    array_push($tmpRow, $grow['struct_id']);

                    // keep reference to struct id
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

                    // If there is at least one indication for this drug
                    if (pg_num_rows($getIndicationCodesQueryResult) > 0){                        
                        // For each row of indications
                        while($grow = pg_fetch_array($getIndicationCodesQueryResult)){
                            // Initialise current UMLS-DOID pair array
                            $currentUMLSDOIDPairDC = array();
                            // If there is an indication then we can increment the number of indications for this drug
                            if ($grow['concept_name'] <> ""){
                                $numIndicationsDC++;
                            }

                            // there is at least a UMLS CUI or DOID code for this indication, for this drug
                            if (($grow['umls_cui'] <> "") || ($grow['doid'] <> "")){
                                // If there is a UMLS CUI
                                if ($grow['umls_cui'] <> ""){
                                    // add this CUI to the UMLS-DOID pair array
                                    array_push($currentUMLSDOIDPairDC, $grow['umls_cui']);
                                    // add this CUI to the UMLS code array
                                    array_push($umlsIndicationsDC, $grow['umls_cui']);   
                                    // increment the number of UMLS terms for this drug
                                    $numUMLStermsDC++;
                                }
                                // If there is a DOID code
                                if ($grow['doid'] <> ""){
                                    // add this DOID code to the UMLS-DOID pair array                        
                                    array_push($currentUMLSDOIDPairDC, substr($grow['doid'],5));
                                    // add this DOID code to the DOID code array
                                    array_push($doidIndicationsDC, substr($grow['doid'],5));    
                                    // increment the number of DOID terms for this drug
                                    $numDOIDtermsDC++;
                                }
                                // increment the number of coded indications for this drug
                                $numIndicationsDC_coded++;
                            }
                            // If there is both a DOID and UMLS term for this indication on DrugCentral
                            if (count($currentUMLSDOIDPairDC) == 2){
                                // Increment the number of UMLS-DOID pairs on DrugCentral, for this drug
                                $numUMLSDOIDPairsDC++;
                            }

                            // add the current UMLS-DOID pair to the UMLS-DOID pairs array
                            array_push($UMLSDOIDPairsDC, $currentUMLSDOIDPairDC);
                        }
                    }

                    // DAILY MED
                    // DailyMed API e.g. https://dailymed.nlm.nih.gov/dailymed/services/v2/spls.json?drug_name=cisplatin
                    // Get SPL (structured product label) information from DailyMed about the drug(s) that have the given name.
                    // I.e., returns a JSON object which holds an array of products (product labels) on DailyMed which have 
                    // the given drug name as the main ingredient
                    // echo "name: ".$drug_name." <br>";
                    $splDailyMed = $dailyMed->getSPLInfo($drug_name); 
                    //echo "labels: ".count($splDailyMed->data)." <br>";
                    // We should have at least one product label for the drug on DailyMed (count the size of the returned array)
                    if (count($splDailyMed->data) > 0){

                        // There may be more than one product containing the same drug (from other manufacturers etc.)
                        // We are only interested in the one which is annotated by Linda / our DailyMed annotation team!
                        // We have to loop through each label until we find it.

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

                            // Append the SET ID so we can isolate the actual web page on DailyMed for the given drug
                            $dailyMedDrugUrl = $dailyMedBaseUrl.$set_id;

                            // Actually get all the Hypothes.is annotations.
                            // Part 1: Get annotations pertaining to this drugs indications. 
                            // (a) first we pull annotations from the dailymed private group that have tag "indication"
                            $result = $hypothesis->search(array('uri' => $dailyMedDrugUrl, 'group' => 'pGv4X7iV',  'tag' => 'indication'), $token);
                            // (b) and then we pull the public annotations
                            $result_public = $hypothesis->search(array('uri' => $dailyMedDrugUrl, 'tag' => 'indication'), $token);
                            // (c) add all public annotations to the dailymed private array (collect all indications in one array)
                            foreach ($result_public as $value) {
                                array_push($result, $value);
                            }
                            // Part 2: Get "other" annotations for this drug (i.e. the context info)
                            // (a) first we pull annotations from the dailymed private group (regardless of tag)
                            $context_result = $hypothesis->search(array('uri' => $dailyMedDrugUrl, 'group' => 'pGv4X7iV'), $token);
                            // (b) and then we pull the public annotations (regardless of tag)
                            $context_result_public = $hypothesis->search(array('uri' => $dailyMedDrugUrl), $token);
                            // (c) add all public annotations to the dailymed private array (collect all context info in one array)
                            foreach ($context_result_public as $value) {
                                array_push($context_result, $value);
                            }
                            // (d) now we have to remove the indication annotations (above arrays will still contain the indication annotations)
                            // Iterate through each annotation (Note: each annotation is a set of tags i.e. an array of tags)
                            // Initialise new annotations array which will keep our context info
                            $new_context_result = array();                            
                            foreach ($context_result as $value){
                                // Keep a reference to the current annotation (array of tags)
                                $current_annotation_tags = $value->tags;                                
                                // Flag to check if current annotation is an indication or not
                                $this_is_an_indication = false;
                                // Iterate through these tags to see if one of them is "indication"                                
                                for ($x = 0; $x < count($current_annotation_tags); $x++){
                                    // Check if current tag is "indication"
                                    if ($current_annotation_tags[$x] == "indication" 
                                        || $current_annotation_tags[$x] == "Indication"
                                        || $current_annotation_tags[$x] == "indications"){

                                        // this confirms that this annotation is an indication
                                        $this_is_an_indication = true;
                                    }
                                }

                                // Add this to our context annotations array if it is not an indication annotation
                                if (!$this_is_an_indication){
                                    array_push($new_context_result, $value);
                                }
                            }                                                

                            // If there is at least one annotation (either indication or context) for this drug
                            if (count($result) > 0 || count($context_result) > 0){
                                // set the total context annotations for this drug (from DailyMed obviously)
                                $numContextAnnotations = count($new_context_result);
                                    
                                // Set the number of indication annotations for this drug on DailyMed
                                $numIndicationsDM = count($result);

                                // We are done searching for the product label that has been annotated in the set                                
                                // NB. the protocol only annotates one product label per drug so there will be only 
                                // one product label with annotations on it
                                $done = true;

                                // Table display values
                                array_push($tmpRow, $set_id);
                                array_push($tmpRow, $numIndicationsDC);
                                array_push($tmpRow, $numIndicationsDM);

                                // debugging
                                echo $drug_name." result: ";
                                // For each indication for this drug
                                foreach ($result as $value) {
                                    // debugging
                                    print_r($value->tags);echo ", ";
                                    
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
                                            // which is 9 characters long. Add this code to the current UMLS-DOID pair array
                                            array_push($currentUMLSDOIDPairDM, substr($annotations[$x],9));
                                            // add this code to the set of UMLS codes for this drug in DailyMed
                                            array_push($umlsIndicationsDM, substr($annotations[$x],9));
                                            // Increment the number of UMLS codes for this drug on DailyMed
                                            $numUMLStermsDM++;
                                        }
                                        if ($firstFourChars == "DOID"){
                                            // Here we take all characters after the 5th. I.e., all characters after the expression "DOID_" 
                                            // which is 5 characters long. Add this code to the current UMLS-DOID pair array
                                            array_push($currentUMLSDOIDPairDM, substr($annotations[$x],5));
                                            // add this code to the set of DOID codes for this drug in DailyMed
                                            array_push($doidIndicationsDM, substr($annotations[$x],5));
                                            // Increment the number of DOID codes for this drug on DailyMed
                                            $numDOIDtermsDM++;   
                                        }
                                    }

                                    // If there is both a DOID and UMLS term for this indication on DailyMed
                                    if (count($currentUMLSDOIDPairDM) == 2){
                                        // Increment the number of UMLS-DOID pairs on DailyMed, for this drug
                                        $numUMLSDOIDPairsDM++;
                                    }

                                    // Store the UMLS-DOID pairs for the indications for this drug
                                    array_push($UMLSDOIDPairsDM, $currentUMLSDOIDPairDM);
                                }
                                // debugging
                                echo "<br>";                                        
                            }
                            
                            // Increment product label index (go to next product label in the SPL JSON array)
                            $idx++;
                        }

                        // If we actually found a product label that has Hypothes.is annotations on its page
                        if ($done){
                            // Calculate the the number of exact matches for UMLS codes between DrugCentral and DailyMed
                            // 15
                            $numExactMatchesUMLS = count(array_intersect($umlsIndicationsDC, $umlsIndicationsDM));
                            // Calculate the the number of exact matches for DOID codes between DrugCentral and DailyMed
                            // 16
                            $numExactMatchesDOID = count(array_intersect($doidIndicationsDC, $doidIndicationsDM));
                            // Get the UMLS codes common to both DrugCentral and DailyMed (this will also tell us which are unique to DC and DM)
                            $commonUMLS = array_map("unserialize", array_intersect(serialize_array_values($umlsIndicationsDC),serialize_array_values($umlsIndicationsDM)));                        
                            $uniqueUMLSDC = arrayRecursiveDiff($umlsIndicationsDC, $commonUMLS);
                            $uniqueUMLSDM = arrayRecursiveDiff($umlsIndicationsDM, $commonUMLS);
                            //20
                            $numUniqueUMLSDC = count($uniqueUMLSDC);
                            //23
                            $numUniqueUMLSDM = count($uniqueUMLSDM);
                            // Get the DOID codes common to both DrugCentral and DailyMed (this will also tell us which are unique to DC and DM)
                            $commonDOID = array_map("unserialize", array_intersect(serialize_array_values($doidIndicationsDC),serialize_array_values($doidIndicationsDM)));                        
                            $uniqueDOIDDC = arrayRecursiveDiff($doidIndicationsDC, $commonDOID);
                            $uniqueDOIDDM = arrayRecursiveDiff($doidIndicationsDM, $commonDOID);
                            //21
                            $numUniqueDOIDDC = count($uniqueDOIDDC);
                            //24
                            $numUniqueDOIDDM = count($uniqueDOIDDM);
                            //18
                            $numExactMatchesTotal = $numExactMatchesUMLS + $numExactMatchesDOID;
                            //22
                            $totalUniqueIndicationCodesDC = $numUniqueUMLSDC + $numUniqueDOIDDC;
                            //25
                            $totalUniqueIndicationCodesDM = $numUniqueUMLSDM + $numUniqueDOIDDM;
                            //26
                            $totalUniqueCodesDM = $numUniqueUMLSDM + $numUniqueDOIDDM + $numContextAnnotations;
                            //14
                            $totalCodesDM = $numUMLStermsDM + $numDOIDtermsDM + $numContextAnnotations;
                            //12
                            $totalIndicationCodesDM = $numUMLStermsDM + $numDOIDtermsDM;
                            //8
                            $totalIndicationCodesDC = $numUMLStermsDC + $numDOIDtermsDC;
                            //9
                            $totalCodesDC = $totalIndicationCodesDC;

                            // Display values to the table data array
                            array_push($tmpRow, $numExactMatchesUMLS);
                            array_push($tmpRow, $numExactMatchesDOID);
                            array_push($tmpRow, $numExactMatchesTotal);
                            array_push($tmpRow, 0);
                            array_push($tmpRow, 0);

                            // Give variable names to the counts for the sets calculated above
                            $numUMLSIndicationsDC = count($umlsIndicationsDC);
                            $numDOIDIndicationsDC = count($doidIndicationsDC);
                            $numUMLSIndicationsDM = count($umlsIndicationsDM);
                            $numDOIDIndicationsDM = count($doidIndicationsDM);

                            // SQL command string to write comparison results for this drug into DrugCentral DB
                            $insertRowString = "INSERT INTO dailymed_comparison 
                            (atc, name, dc_id, dm_id, umls_ind_codes_dc, 
                            doid_ind_codes_dc, total_ind_codes_dc, total_codes_dc, umls_ind_codes_dm, doid_ind_codes_dm,
                            total_ind_codes_dm, context_codes_dm, total_codes_dm, umls_exact_matches, doid_exact_matches,
                            umls_doid_pair_exact_matches, total_separate_exact_matches, total_pair_exact_matches, unique_umls_codes_dc, unique_doid_codes_dc,
                            total_unique_ind_codes_dc, unique_umls_codes_dm, unique_doid_codes_dm, total_unique_ind_codes_dm, total_unique_codes_dm) 

                            VALUES ('$currentATC', '$drug_name', '$struct_id', '$set_id', '$numUMLSIndicationsDC',
                            '$numDOIDIndicationsDC', '$totalIndicationCodesDC', '$totalCodesDC', '$numUMLStermsDM', '$numDOIDtermsDM',
                            '$totalIndicationCodesDM', '$numContextAnnotations', '$totalCodesDM', '$numExactMatchesUMLS', '$numExactMatchesDOID',
                            '0', '$numExactMatchesTotal', '0', '$numUniqueUMLSDC', '$numUniqueDOIDDC',
                            '$totalUniqueIndicationCodesDC', '$numUniqueUMLSDM', '$numUniqueDOIDDM', '$totalUniqueIndicationCodesDM', '$totalUniqueCodesDM')";
                            // Execute SQL command to write comparison results for this drug to DrugCentral DB
                            $insertRowResult = pg_query($conn, $insertRowString) or die("Could not execute query");

                            // if the insertion was successful 
                            //if ($insertRowResult){
                                //echo "successful!<br>";
                            //}
                            
                            // Only increment the row number, and add the row data to the table, if the current drug actually has indications
                            if ($numIndicationsDM > 0){
                                $counter++;
                                array_push($data, $tmpRow);
                            }
                        }
                    }
                }
                /*else{
                    echo "More than one name.";
                }*/


            }

            /*$fp = fopen('php://output', 'w');
            foreach ( $data as $line ) {
                $val = explode(",", $line);
                fputcsv($fp, $val);
            }

            fclose($fp);*/
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