select struct_id from struct2atc where atc_code = 'L01XA01'

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

