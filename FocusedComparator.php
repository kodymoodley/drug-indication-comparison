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

    function possibleUMLS($text){
        $result = false;
        if (substr($text, 0, 1) == "C"){
            $possibleNumberPortion = substr($text, 1);
            $result = is_numeric($possibleNumberPortion);
        }
        return $result;
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

                            $dailyMedIndications = array();

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


                                    $umlsCodes = array();
                                    $doidCodes = array();

                                    // Iterate through the tags to find UMLS and DOID codes
                                    for ($x = 0; $x < count($annotations); $x++){
                                        // Get the first four characters of the tag (the annotation protocol dictates that DOID codes
                                        // and UMLS codes start with either "DOID" or "UMLS" respectively)
                                        $firstFourChars = substr($annotations[$x], 0, 4);

                                        if ($firstFourChars == "UMLS"){
                                            $pos = strpos($annotations[$x], ':');
                                            array_push($umlsCodes, substr($annotations[$x],$pos+1));
                                            // ?
                                            if(($key = array_search(2, $arr)) !== false) {
                                                unset($arr[$key]);
                                            }
                                        }

                                        if ($firstFourChars == "DOID"){
                                            $colon = false;
                                            $colon = contains($annotations[$x], ':');
                                            if ($colon){
                                                $pos = strpos($annotations[$x], ':');
                                                array_push($doidCodes, substr($annotations[$x],$pos+1));
                                            }
                                            else{
                                                $pos = strpos($annotations[$x], '_');
                                                array_push($doidCodes, substr($annotations[$x],$pos+1));
                                            }
                                            // ?
                                            if(($key = array_search(3, $arr)) !== false) {
                                                unset($arr[$key]);
                                            }
                                        }
                                    }

                                    $currentRow[2] = implode(',', $umlsCodes);
                                    $currentRow[3] = implode(',', $doidCodes);

                                    // ?
                                    $currentRow[4] = "";
                                    $currentRow[5] = "";
                                    
                                    // Rows of table
                                    array_push($dailyMedIndications, $currentRow);

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

        // column names: id, atc, name, dc_id, dm_id, IDSannotations, DrugCentralAnnotations        
        // Different output flag value (we have to display a table with numbers representing indication matches per drug)
        $candisplaystuff = 3;
        $data = array();
        // First get all ATC codes and Struct_IDs for given drug class
        // Query string formulation
        $getStructIDsAndATCCodesQueryStr = "SELECT name, dc_id, dm_id, atc FROM dailymed_comparison WHERE atc LIKE '".$drugname."%'";
        
        // Actual query directive to get the struct_id and ATC codes for the given drug class
        $getStructIDsAndATCCodesQueryResult = pg_query($conn, $getStructIDsAndATCCodesQueryStr) or die("Could not execute query");

        // If there is at least one atc / struct code for this drug in DrugCentrals DB
        if (pg_num_rows($getStructIDsAndATCCodesQueryResult) > 0){

            // Initialise columns
            $currentATC = "";
            $drug_name = "";
            $struct_id = 0;
            $set_id = "";
            $ids_annotations = "";
            $dc_annotations = "";

            // UMLS codes
            $ids_annotations_UMLS = "";
            $dc_annotations_UMLS = "";            
            $overlapping_UMLS = "";
            $overlapping_context = "";
            $ids_annotations_context = "";

            // Initialise drug counter
            $counter = 1;

            // For each drug, get its indications and store to results array
            while($grow = pg_fetch_array($getStructIDsAndATCCodesQueryResult)){                
                
                // Set columns that can be set
                $currentATC = $grow['atc'];
                $drug_name = $grow['name'];
                $struct_id = $grow['dc_id'];
                $set_id = $grow['dm_id'];
                $ids_annotations = "";
                $dc_annotations = "";

                // UMLS codes
                $ids_annotations_UMLS = "";
                $dc_annotations_UMLS = "";            
                $overlapping_UMLS = "";
                $overlapping_context = "";
                $ids_annotations_context = "";

                // Initialise counts
                $terms_dc = 0;
                $cuis_dc = 0;
                $cuis_ids = 0;
                $cuis_overlap = 0;
                $cuis_overlap_c = 0;

                // Initialise cui arrays
                $cuis_dc_arr = array();
                $cuis_ids_arr = array();             
                $cuis_ids_indications_arr = array();
                $cuis_ids_context_arr = array();

                // Initialise new cui arrays
                $ids_annotations_UMLS_arr = array();
                $dc_annotations_UMLS_arr = array();
                $overlapping_UMLS_arr = array();
                $overlapping_context_arr = array();
                $ids_annotations_context_arr = array();



                // Initialise cui overlap arrays - these will be calculated at the end
                $cuis_overlap_arr = array();
                $cuis_overlap_c_arr = array();

                // DrugCentral indications
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
                        // collect everything as text
                        $dc_annotations .= $grow['concept_name'].",".$grow['umls_cui'].",".$grow['doid']."|";
                        // count indications
                        if ($grow['concept_name'] <> ""){
                            $terms_dc++;
                        }
                        // count cuis
                        if ($grow['umls_cui'] <> ""){
                            array_push($cuis_dc_arr, $grow['umls_cui']);                                                    
                        }
                    }
                }

                // DAILY MED indications
                // Set the base URL on DailyMed where we are going to search for drug annotations 
                $dailyMedBaseUrl = "https://dailymed.nlm.nih.gov/dailymed/drugInfo.cfm?setid=";

                // Append the SET ID so we can isolate the actual web page on DailyMed for the given drug
                $dailyMedDrugUrl = trim($dailyMedBaseUrl.$set_id);

                // Actually get all the Hypothes.is annotations
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

                // Get number of annotations
                $numAnno = 0;
                foreach ($annotations as $value) {
                    $numAnno++;
                }     

                // Annotation index
                $annoIndex = 0;
                $currentAnnotations = array();
                // Iterate through annotations
                while ($annoIndex < $numAnno){
                    // Keep a reference to the current array of tags in this annotation
                    $current_annotation_tags = $annotations[$annoIndex]->tags;                    
                    array_push($currentAnnotations, implode(",", $current_annotation_tags));
                    // Iterate through these tags to see if one of them has "role" as prefix
                    // While we didn't find a "role" prefixed tag and we haven't run out of tags in this annotation
                    $tagIndex = 0;
                    $this_is_an_indication = false;
                    $currentCuis = array();
                    
                    while ($tagIndex < count($current_annotation_tags)){
                        // collect everything as text
                       
                        //$ids_annotations .= $current_annotation_tags[$tagIndex].",";

                        // check if it is an indication (so we can differentiate between context and actual indications)
                        if ($current_annotation_tags[$tagIndex] == "indication" || $current_annotation_tags[$tagIndex] == "Indication" || $current_annotation_tags[$tagIndex] == "indications")
                        {
                            $this_is_an_indication = true;
                        }

                        // get first four characters of this tag
                        $firstFourChars = substr($current_annotation_tags[$tagIndex], 0, 4);

                        // if this tag specifies a UMLS CUI
                        if ($firstFourChars == "UMLS" || possibleUMLS($firstFourChars)){
                            // Here we take all characters after the 9th. I.e., all characters after the expression "UMLS_CUI:" 
                            // which is 9 characters long. 
                            if ($firstFourChars == "UMLS"){
                                $pos = strpos($current_annotation_tags[$tagIndex], ':');
                                $tmp1 = substr($current_annotation_tags[$tagIndex], $pos+1);
                                array_push($cuis_ids_arr, $tmp1);                            
                                array_push($currentCuis, $tmp1);  
                            }   
                            else{
                                array_push($cuis_ids_arr, $current_annotation_tags[$tagIndex]);                            
                                array_push($currentCuis, $current_annotation_tags[$tagIndex]);                                                                  
                            }
                            //$cuis_ids++;
                        }

                        $tagIndex++;
                    }
                
                    if ($this_is_an_indication && (count($currentCuis) > 0)){
                        $cuis_ids_indications_arr = array_merge($cuis_ids_indications_arr, $currentCuis);
                    }
                    else if (!$this_is_an_indication && (count($currentCuis) > 0)){
                        $cuis_ids_context_arr = array_merge($cuis_ids_context_arr, $currentCuis);   
                    }                

                    // Increment annotation index
                    $annoIndex++;
                }

                $currentAnnotations = array_unique($currentAnnotations);
                $currentAnnotations = array_values($currentAnnotations);
                $ids_annotations = implode("|", $currentAnnotations);

                // Remove last comma
                $ids_annotations_UMLS = substr($ids_annotations_UMLS, 0, -1);

                // Do some calculations of overlap
                // first remove duplicates from arrays and key value pairs
                // DrugCentral
                if (count($cuis_dc_arr) > 0){
                    $cuis_dc_arr = array_unique($cuis_dc_arr);
                    $cuis_dc_arr = array_values($cuis_dc_arr);
                }

                // IDS
                if (count($cuis_ids_arr) > 0){
                    $cuis_ids_arr = array_unique($cuis_ids_arr);
                    $cuis_ids_arr = array_values($cuis_ids_arr);
                }

                // IDS Indications
                if (count($cuis_ids_indications_arr) > 0){
                    $cuis_ids_indications_arr = array_unique($cuis_ids_indications_arr);
                    $cuis_ids_indications_arr = array_values($cuis_ids_indications_arr);
                }

                // IDS Context
                if (count($cuis_ids_context_arr) > 0){
                    $cuis_ids_context_arr = array_unique($cuis_ids_context_arr);
                    $cuis_ids_context_arr = array_values($cuis_ids_context_arr);
                }

                $ids_annotations_context = implode(",", $cuis_ids_context_arr);            
                $dc_annotations_UMLS = implode(",", $cuis_dc_arr);
                $ids_annotations_UMLS = implode(",", $cuis_ids_arr);

                // Record the cuis in DC and IDS
                $cuis_dc = count($cuis_dc_arr);
                $cuis_ids = count($cuis_ids_arr);
                $cuis_ids_context = count($cuis_ids_context_arr);
                $cuis_ids_indications = count($cuis_ids_indications_arr);

                // Calculate overlap cuis and the size of this array
                $cuis_overlap_arr = array_intersect($cuis_dc_arr, $cuis_ids_arr);
                $cuis_overlap = count($cuis_overlap_arr);
                $overlapping_UMLS = implode(",", $cuis_overlap_arr);

                // How many of these are contextual cuis?
                // Calculate intersection of overlap array and context cui array and get the size of this array
                $cuis_overlap_c_arr = array_intersect($cuis_overlap_arr, $cuis_ids_context_arr);
                $cuis_overlap_c = count($cuis_overlap_c_arr);
                $overlapping_context = implode(",", $cuis_overlap_c_arr);            

                $cuis_overlap_ind_arr = array_intersect($cuis_overlap_arr, $cuis_ids_indications_arr);
                $cuis_overlap_ind = count($cuis_overlap_ind_arr);

                // Those indication CUIs that also appear in the context (which likely make them not indications anymore)
                $cuis_ids_indications_nic_arr = array_diff($cuis_ids_indications_arr, $cuis_ids_context_arr);
                $cuis_ids_indications_nic = count($cuis_ids_indications_nic_arr);

                $dc_annotations = str_replace("'","",$dc_annotations);                
                $ids_annotations = str_replace("'","",$ids_annotations);

                //echo $ids_annotations."<br>";
                //echo "<br>";
                $sqlCheckIfExistingRow = "SELECT * FROM ids_curation_comparison WHERE atc = '$currentATC' limit 1";

                $duplicateResult = pg_query($conn, $sqlCheckIfExistingRow) or die("Could not execute query");

                // If this drug has already been analysed
                if (pg_num_rows($duplicateResult) == 0){  
                    // SQL command string to write comparison results for this drug into DrugCentral DB
                    $insertRowString = "INSERT INTO ids_curation_comparison
                                        (atc, name, dc_id, dm_id, ids_annotations, dc_annotations, terms_dc, cuis_dc, cuis_ids, cuis_ids_indications, cuis_ids_context, cuis_overlap, cuis_overlap_c, cuis_overlap_ind, cuis_ids_indications_nic, ids_umls, dc_umls, overlap_umls, ids_context, overlap_umls_context) 
                                        VALUES 
                                        ('$currentATC', '$drug_name', '$struct_id', '$set_id', '$ids_annotations', '$dc_annotations', '$terms_dc', '$cuis_dc', '$cuis_ids', '$cuis_ids_indications', '$cuis_ids_context', '$cuis_overlap', '$cuis_overlap_c', '$cuis_overlap_ind', '$cuis_ids_indications_nic', '$ids_annotations_UMLS', '$dc_annotations_UMLS', '$overlapping_UMLS', '$ids_annotations_context', '$overlapping_context')";

                    // Execute SQL command to write comparison results for this drug to DrugCentral DB
                    $insertRowResult = pg_query($conn, $insertRowString) or die("Could not execute query");
                }
            
                // if the insertion was successful 
                //if ($insertRowResult){
                   // echo $counter." successful!<br>";
                //}
                   
                // Increment drug counter                                 
                $counter++;
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
        
                <form action="FocusedComparator.php" method="post">
            
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