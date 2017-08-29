package nl.unimaas.ids.semanticsimilarity;

import java.io.BufferedInputStream;
import java.io.FileInputStream;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.util.StringTokenizer;

import org.apache.commons.csv.CSVFormat;
import org.apache.commons.csv.CSVParser;
import org.apache.commons.csv.CSVPrinter;
import org.apache.commons.csv.CSVRecord;

public class Main {
	
	public static int count(String filename) throws IOException {
	    InputStream is = new BufferedInputStream(new FileInputStream(filename));
	    try {
	    byte[] c = new byte[1024];
	    int count = 0;
	    int readChars = 0;
	    boolean empty = true;
	    while ((readChars = is.read(c)) != -1) {
	        empty = false;
	        for (int i = 0; i < readChars; ++i) {
	            if (c[i] == '\n') {
	                ++count;
	            }
	        }
	    }
	    return (count == 0 && !empty) ? 1 : count;
	    } finally {
	    is.close();
	   }
	}


	public static void main(String[] args) throws IOException {
		String inputFileName = "data\\data_processed.csv";
		//Semantic similarity
		//String outputFileName = "data\\output.csv";
		//Semantic relatedness
		String outputFileName = "data\\relatedness_output.csv";
		//Semantic similarity
		//CachedSemanticSimilarityProcessor processor = new CachedSemanticSimilarityProcessor();
		//Semantic relatedness
		CachedSemanticRelatednessProcessor processor = new CachedSemanticRelatednessProcessor();
		CSVParser parser = CSVFormat.RFC4180.withFirstRecordAsHeader().parse(new FileReader(inputFileName));//CSVParser.parse(inputFileName, CSVFormat.DEFAULT);
		FileWriter writer = new FileWriter(outputFileName);
		CSVPrinter printer = new CSVPrinter(writer, CSVFormat.DEFAULT);
		//Semantic similarity
		//printer.printRecord("atc","name","dc_code","other_code", "dc_name", "other_name", "random", "path", "cdist", "res", "jcn", "lin", "wup", "lch", "nam");
		//Semantic relatedness
		printer.printRecord("atc","name","dc_code","other_code", "dc_name", "other_name", "lesk", "vector");
		int idx = 1;
		int total = count(inputFileName);
		for(CSVRecord record : parser.getRecords()) {
			System.out.print(idx + " / " + total + " ... ");
			String atc = record.get("atc");
			String name = record.get("name");
			String code1 = record.get("dc_code");
			String code2 = record.get("other_code");

			if (!atc.equals("atc")){												
				// Initialise semantic similarity measure values
//				/** just random score (baseline) **/
//				Double random = 0.0d;
//				 
//				/** path-based **/
//				Double path = 0.0d; 
//				Double cdist = 0.0d;				
//				
//				/** path + information content **/
//				Double res = 0.0d; 
//				Double jcn = 0.0d;
//				Double lin = 0.0d;
//				
//				/** path +depth **/
//				Double wup = 0.0d;		
//				Double lch = 0.0d;
//				Double nam = 0.0d;
				
				//Semantic relatedness measures
				Double lesk = 0.0d;
				Double vector = 0.0d;
				
				//Semantic similarity calculations
//				path = processor.calcSimilarityValue(code1, code2, "path");
//				cdist = processor.calcSimilarityValue(code1, code2, "cdist");
//				res = processor.calcSimilarityValue(code1, code2, "res");
//				jcn = processor.calcSimilarityValue(code1, code2, "jcn");
//				lin = processor.calcSimilarityValue(code1, code2, "lin");
//				wup = processor.calcSimilarityValue(code1, code2, "wup");
//				lch = processor.calcSimilarityValue(code1, code2, "lch");
//				nam = processor.calcSimilarityValue(code1, code2, "nam");	
				
				//Semantic relatedness calculations
				lesk = processor.calcRelatednessValue(code1, code2, "lesk");
				vector = processor.calcRelatednessValue(code1, code2, "vector");
				
				// name_map
				String dc_name = processor.getIndicationName(code1, code2, 1);
				String other_name = processor.getIndicationName(code1, code2, 2);
				
				printer.printRecord(atc, name, code1, code2, dc_name, other_name, lesk, vector);
			}
			System.out.println("done.");
			idx++;
		}

		printer.close();
		writer.close();
	}

}
