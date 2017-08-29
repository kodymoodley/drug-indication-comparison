package nl.unimaas.ids.semanticsimilarity;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.HashMap;
import java.util.Map;
import java.util.StringTokenizer;

public class CachedSemanticRelatednessProcessor {
	public Map<String, Double> lesk_cache = new HashMap<String, Double>();
	public Map<String, Double> vector_cache = new HashMap<String, Double>();
	
	public Map<String, String> name_cache = new HashMap<String, String>();

	public Double calcRelatednessValue(final String code1, final String code2, final String measure) throws IOException {
		if(code1.compareTo(code2)>0)
			return calcRelatednessValueSorted(code2, code1, measure);
		else
			return calcRelatednessValueSorted(code1, code2, measure);
	}

	private Double calcRelatednessValueSorted(final String code1, final String code2, final String measure) throws IOException {
		String key = code1 + "_" + code2;

		Double val = 0.0;
		switch (measure) {
			case "lesk" : 	if(!lesk_cache.containsKey(key)){
				lesk_cache.put(key, doCalculation(code1, code2, measure));
			}
			val = lesk_cache.get(key);
			break;

			case "vector" : 	if(!vector_cache.containsKey(key)){
				vector_cache.put(key, doCalculation(code1, code2, measure));
			}
			val = vector_cache.get(key);
			break;	
		}

		return val;
	}

	private Double doCalculation(final String c1, final String c2, final String measure) throws IOException {			
		String aCmdArgs = "C:/Strawberry/perl/bin/perl.exe UMLS-Similarity-1.47.tar\\UMLS-Similarity-1.47\\utils\\query-umls-similarity-webinterface.pl" +
				" --measure " + measure + " --url http://maraca.d.umn.edu " + c1 + " " + c2;

		//System.out.println(aCmdArgs);

		Runtime oRuntime = Runtime.getRuntime();
		Process oProcess = null;
		String sLine = null;
		String score = "";
		String dc_name = "";
		String other_name = "";

		try {
			oProcess = oRuntime.exec(aCmdArgs);            
			oProcess.waitFor();
		} catch (Exception e) {
			System.out.println("error executing " + aCmdArgs);
		}

		BufferedReader is = new BufferedReader( new InputStreamReader(oProcess.getInputStream()));

		while ((sLine = is.readLine()) != null) {
			//System.out.println(sLine);
			StringTokenizer st = new StringTokenizer(sLine, "<>");
			int i = 0;						        							        
			while (st.hasMoreTokens()){
				String tmp = st.nextToken();
				if (i == 0){
					if (tmp.isEmpty())
						score = "-2.0";
					else
						score = tmp;						
				}
				else if (i == 1){
					dc_name = tmp;					
					dc_name = dc_name.replace(",", "");
					int nosIdx = dc_name.indexOf("NOS");
					if (nosIdx >= 0){
						dc_name = dc_name.substring(0, nosIdx);
						if (dc_name.length() == 0)
							dc_name = tmp;
					}
					else{
						int bracketIdx = dc_name.indexOf("(");
						dc_name = dc_name.substring(0, bracketIdx);
						if (dc_name.length() == 0)
							dc_name = tmp;
					}					
				}
				else if (i == 2){
					other_name = tmp;
					other_name = other_name.replace(",", "");
					int nosIdx = other_name.indexOf("NOS");
					if (nosIdx >= 0){
						other_name = other_name.substring(0, nosIdx);
						if (other_name.length() == 0)
							other_name = tmp;
					}
					else{
						int bracketIdx = other_name.indexOf("(");
						other_name = other_name.substring(0, bracketIdx);
						if (other_name.length() == 0)
							other_name = tmp;
					}							
				}

				i++;
			}

			if (dc_name.matches(".*[a-z].*") && other_name.matches(".*[a-z].*")){
				name_cache.put(c1 + "_" + c2, dc_name + "_" + other_name);
				name_cache.put(c2 + "_" + c1, other_name + "_" + dc_name);
			}
		}

		System.out.flush();

		if (score.length() == 0 || score.equals("()"))
			score = "-2.0";
		//System.err.println("Exit status=" + oProcess.exitValue());
		return Double.parseDouble(score);		
	}
	
	public String getIndicationName(String code1, String code2, int part){
		if (part == 1){
			if (name_cache.containsKey(code1 + "_" + code2)){
				String fullStr = name_cache.get(code1 + "_" + code2);
				return fullStr.substring(0, fullStr.indexOf("_"));
			}
			else if (name_cache.containsKey(code2 + "_" + code1)){
				String fullStr = name_cache.get(code2 + "_" + code1);
				return fullStr.substring(0, fullStr.indexOf("_"));
			}
			else{
				return "?";
			}
		}
		else{
			if (name_cache.containsKey(code1 + "_" + code2)){
				String fullStr = name_cache.get(code1 + "_" + code2);
				return fullStr.substring(fullStr.indexOf("_")+1);
			}
			else if (name_cache.containsKey(code2 + "_" + code1)){
				String fullStr = name_cache.get(code2 + "_" + code1);
				return fullStr.substring(fullStr.indexOf("_")+1);
			}
			else{
				return "?";
			}
		}
	}
}
