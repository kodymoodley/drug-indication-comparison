select struct_id from struct2atc where atc_code like 'L01%'
 
/** This is the query to get the SET ID(s) of a particular drug, given its name, from DrugCentral itself!!!
* This might not be the best way to get it because we are interested in pulling annotations from DailyMed,
* and therefore if we have SET ID on DailyMed which does NOT appear in DrugCentral then we MAY miss annotations
in DailyMed.
*/
select label_id from prd2label where prd2label.ndc_product_code in (select product.ndc_product_code 
from product where product_name like '%cisplatin%' or product_name like '%Cisplatin%')

select struct_id from struct2atc where atc_code like 'L01%'

select code from atc where chemical_substance = 'metformin'

select * from active_ingredient where struct_id = 4392

select struct_id from active_ingredient where substance_name like '%cetuximab%' limit 1

select * from doid_xref where doid = 'DOID:2394'

select * from identifier where struct_id = 4392

Select * from omop_relationship where struct_id = 4392

SELECT omop_relationship.id,
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
			 
	WHERE omop_relationship.struct_id = 1725 and omop_relationship.relationship_name = 'indication';

