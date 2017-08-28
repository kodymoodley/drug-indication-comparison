package nl.unimaas.ids.semanticsimilarity;

import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.HashSet;
import java.util.Set;
import java.util.StringTokenizer;
import org.apache.commons.csv.CSVFormat;
import org.apache.commons.csv.CSVParser;
import org.apache.commons.csv.CSVPrinter;
import org.apache.commons.csv.CSVRecord;

public class DataProcessor {
	
	private static Set<String> getCodes(String commaSepCodes){
		Set<String> result = new HashSet<String>();					
		String str = commaSepCodes.replace(";", ","); 				// replace ; with ,
		String str_final = str.replace("-", ","); 					// replace - with ,
		StringTokenizer st = new StringTokenizer(str_final, ",");	// split string into tokens seperated by ,
		while (st.hasMoreTokens()){
			String tmp = st.nextToken();							// Get next token
			String tmp_final = tmp.replaceAll("\\s+","");			// remove whitespace
			result.add(tmp_final);								
		}		
		return result;	
	}
	
	public static void main(String [] args) throws IOException{
		String inputFileName = "data\\data.csv";
		String outputFileName = "data\\data_processed.csv";		
						
	
		CSVParser parser = CSVFormat.RFC4180.withFirstRecordAsHeader().parse(new FileReader(inputFileName));//CSVParser.parse(inputFileName, CSVFormat.EXCEL);
		FileWriter writer = new FileWriter(outputFileName);
		CSVPrinter printer = new CSVPrinter(writer, CSVFormat.DEFAULT);		
		printer.printRecord("atc", "name", "dc_code", "other_code");
		
		for(CSVRecord record : parser.getRecords()) {
			String atc = record.get("atc");
			String name = record.get("name");			
			
			// skip header row
			if (!atc.equals("atc")){	 				
				// Got some dc codes and at least some unique or overlapping codes to work with
				if (!record.get("dc_unique_umls_actualcodes").replaceAll("\\s+","").equals("") && 
						(
								!record.get("dm_unique_umls_actualcodes").replaceAll("\\s+","").equals("") 
								|| 	
								!record.get("umls_matches_codes").replaceAll("\\s+","").equals("")
						)
					)
				{ 					
					// Combine unique, overlapping and other dc codes
					Set<String> dcCodes = getCodes(record.get("dc_unique_umls_actualcodes").replaceAll("\\s+",""));
					Set<String> otherCodes = getCodes(record.get("dm_unique_umls_actualcodes").replaceAll("\\s+","") + "," + record.get("umls_matches_codes").replaceAll("\\s+",""));
					Set<String> allCodes = new HashSet<String>();
					allCodes.addAll(dcCodes);
					allCodes.addAll(otherCodes);

					for (String dcCode: dcCodes){			// for each drugcentral CUI for this drug
						for (String allCode: allCodes){		// for each other CUI for this drug		
							if (!dcCode.equals(allCode)){	// Don't compare CUI to itself AND don't compare previously compared CUIs												
								printer.printRecord(atc, name, dcCode, allCode);
							}
						}
					}													            	        
				}				
			}									 										
		}
		
		printer.close();
		writer.close();				
	}
	
}
