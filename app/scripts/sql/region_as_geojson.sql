--
-- using left outer join gets the last heat map created for that neighborhood
--
SELECT (heat_map_point.*) FROM region 
INNER JOIN heat_map hm1 ON hm1.region_id = region.id
LEFT OUTER JOIN heat_map hm2
    ON ( hm2.region_id = region.id AND hm1.id < hm2.id )
INNER JOIN heat_map_point
    ON hm2.id = heat_map_point.heat_map_id
WHERE region.name = 'Philadelphia';