package nl.unimaas.ids.semanticsimilarity;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.HashMap;
import java.util.Map;
import java.util.StringTokenizer;

public class CachedSemanticSimilarityProcessor {

	public Map<String, Double> path_cache = new HashMap<String, Double>();
	public Map<String, Double> cdist_cache = new HashMap<String, Double>();
	public Map<String, Double> res_cache = new HashMap<String, Double>();
	public Map<String, Double> jcn_cache = new HashMap<String, Double>();
	public Map<String, Double> lin_cache = new HashMap<String, Double>();
	public Map<String, Double> wup_cache = new HashMap<String, Double>();
	public Map<String, Double> lch_cache = new HashMap<String, Double>();
	public Map<String, Double> nam_cache = new HashMap<String, Double>();

	public Map<String, String> name_cache = new HashMap<String, String>();

	public Double calcSimilarityValue(final String code1, final String code2, final String measure) throws IOException {
		if(code1.compareTo(code2)>0)
			return calcSimilarityValueSorted(code2, code1, measure);
		else
			return calcSimilarityValueSorted(code1, code2, measure);
	}

	private Double calcSimilarityValueSorted(final String code1, final String code2, final String measure) throws IOException {
		String key = code1 + "_" + code2;

		Double val = 0.0;
		switch (measure) {
		case "path" : 	if(!path_cache.containsKey(key)){
			path_cache.put(key, doCalculation(code1, code2, measure));
		}
		val = path_cache.get(key);
		break;

		case "cdist" : 	if(!cdist_cache.containsKey(key)){
			cdist_cache.put(key, doCalculation(code1, code2, measure));
		}
		val = cdist_cache.get(key);
		break;

		case "res" : 	if(!res_cache.containsKey(key)){
			res_cache.put(key, doCalculation(code1, code2, measure));
		}
		val = res_cache.get(key);
		break;

		case "jcn" : 	if(!jcn_cache.containsKey(key)){
			jcn_cache.put(key, doCalculation(code1, code2, measure));
		}
		val = jcn_cache.get(key);
		break;

		case "lin" : 	if(!lin_cache.containsKey(key)){
			lin_cache.put(key, doCalculation(code1, code2, measure));
		}
		val = lin_cache.get(key);
		break;

		case "wup" : 	if(!wup_cache.containsKey(key)){
			wup_cache.put(key, doCalculation(code1, code2, measure));
		}
		val = wup_cache.get(key);
		break;

		case "lch" : 	if(!lch_cache.containsKey(key)){
			lch_cache.put(key, doCalculation(code1, code2, measure));
		}
		val = lch_cache.get(key);
		break;

		case "nam" : 	if(!nam_cache.containsKey(key)){
			nam_cache.put(key, doCalculation(code1, code2, measure));
		}
		val = nam_cache.get(key);
		break;

		}

		return val;
	}

	private Double doCalculation(final String c1, final String c2, final String measure) throws IOException {			
		String aCmdArgs = "C:/Strawberry/perl/bin/perl.exe UMLS-Similarity-1.47.tar\\UMLS-Similarity-1.47\\utils\\query-umls-similarity-webinterface.pl" +
				" --measure " + measure + " --url http://maraca.d.umn.edu " + c1 + " " + c2;

		System.out.println(aCmdArgs);

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

			name_cache.put(c1 + "_" + c2, dc_name + "_" + other_name);
			name_cache.put(c2 + "_" + c1, other_name + "_" + dc_name);
		}

		System.out.flush();

		if (score.length() == 0 || score.equals("()"))
			score = "-2.0";
		//System.err.println("Exit status=" + oProcess.exitValue());
		return Double.parseDouble(score);		
	}

}
