package nl.unimaas.ids.semanticsimilarity;

import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.StringTokenizer;

import org.apache.commons.csv.CSVFormat;
import org.apache.commons.csv.CSVParser;
import org.apache.commons.csv.CSVPrinter;
import org.apache.commons.csv.CSVRecord;

public class Main {

	public static void main(String[] args) throws IOException {
		String inputFileName = "data\\data_processed.csv";
		String outputFileName = "data\\output.csv";
		CachedSemanticSimilarityProcessor processor = new CachedSemanticSimilarityProcessor();
		CSVParser parser = CSVFormat.RFC4180.withFirstRecordAsHeader().parse(new FileReader(inputFileName));//CSVParser.parse(inputFileName, CSVFormat.DEFAULT);
		FileWriter writer = new FileWriter(outputFileName);
		CSVPrinter printer = new CSVPrinter(writer, CSVFormat.DEFAULT);
		printer.printRecord("atc","name","dc_code","other_code", "dc_name", "other_name", "random", "path", "cdist", "res", "jcn", "lin", "wup", "lch", "nam");

		for(CSVRecord record : parser.getRecords()) {
			String atc = record.get("atc");
			String name = record.get("name");
			String code1 = record.get("dc_code");
			String code2 = record.get("other_code");

			if (!atc.equals("atc")){												
				// Initialise semantic measure values
				/** just random score (baseline) **/
				Double random = 0.0d;
				 
				/** path-based **/
				Double path = 0.0d; 
				Double cdist = 0.0d;				
				
				/** path + information content **/
				Double res = 0.0d; 
				Double jcn = 0.0d;
				Double lin = 0.0d;
				
				/** path +depth **/
				Double wup = 0.0d;		
				Double lch = 0.0d;
				Double nam = 0.0d;
												
				//random = processor.calcSimilarityValue(code1, code2, "random");
				path = processor.calcSimilarityValue(code1, code2, "path");
				cdist = processor.calcSimilarityValue(code1, code2, "cdist");
				res = processor.calcSimilarityValue(code1, code2, "res");
				jcn = processor.calcSimilarityValue(code1, code2, "jcn");
				lin = processor.calcSimilarityValue(code1, code2, "lin");
				wup = processor.calcSimilarityValue(code1, code2, "wup");
				lch = processor.calcSimilarityValue(code1, code2, "lch");
				nam = processor.calcSimilarityValue(code1, code2, "nam");				
				
				// name_map
				String dc_name = "?";
				String other_name = "?";
				String indic_names = "";
				
				if (processor.name_cache.containsKey(code1 + "_" + code2)){
					indic_names = processor.name_cache.get(code1 + "_" + code2);
				}
				else if (processor.name_cache.containsKey(code2 + "_" + code1)){
					indic_names = processor.name_cache.get(code2 + "_" + code1);
				}
				
				if (!indic_names.isEmpty()){
					if (indic_names.contains("_") && indic_names.contains("[a-zA-Z]+")){
						System.out.println(indic_names);
						StringTokenizer st = new StringTokenizer(indic_names, "_");
						dc_name = st.nextToken();
						other_name = st.nextToken();
					}
				}
				
				printer.printRecord(atc, name, code1, code2, dc_name, other_name, random, path, cdist, res, jcn, lin, wup, lch, nam);
			}
		}

		printer.close();
		writer.close();
	}

}
