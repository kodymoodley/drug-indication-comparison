select struct_id from omop_relationship_doid_view where ((umls_cui is null) or (doid is null)) 
and (struct_id in (select struct_id from struct2atc where atc_code like 'L01%'))

select struct_id from omop_relationship_doid_view where ((umls_cui is null) and (doid is null)) 
and (struct_id in (select struct_id from struct2atc where atc_code like 'L01%'))

select struct_id, concept_name, umls_cui, doid 
from omop_relationship_doid_view 
where struct_id in (select struct_id from struct2atc where atc_code in (select code from atc where chemical_substance = 'bortezomib')) 
and relationship_name = 'indication'

struct_id in (select struct_id from struct2atc where atc_code like 'L01%')atc 

select code from atc where chemical_substance = 'bortezomib'  





SELECT struct_id FROM active_ingredient WHERE substance_name like '%anagrelide%' LIMIT 1

select * from omop_relationship_doid_view