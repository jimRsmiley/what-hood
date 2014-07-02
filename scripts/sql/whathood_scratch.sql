UPDATE neighborhood SET name = 'Fishtown' WHERE id = 87


-- 20140316
SELECT COUNT(*) FROM heat_map_test_point INNER JOIN region ON ST_Contains(region.polygon,point) AND region.id = 1;

-- delete all test points outside of Philadelphia's polygon
DELETE FROM test_point USING region WHERE ST_Contains( region.polygon, test_point.point ) = false WHERE region.name = 'Philadelphia';

-- select only latest neighborhood_polygons
SELECT * FROM neighborhood_polygon b 
JOIN (
  SELECT neighborhood_id, MAX( id ) as id 
  FROM neighborhood_polygon 
  GROUP BY neighborhood_id 
  ORDER BY neighborhood_id
) as a
ON a.id = b.id

-- alternatively
SELECT * FROM neighborhood_polygon a 
WHERE NOT EXISTS (
	SELECT * FROM neighborhood_polygon b INNER JOIN neighborhood ON b.neighborhood_id = neighborhood.id WHERE a.neighborhood_id = b.neighborhood_id AND a.timestamp < b.timestamp AND neighborhood.region_id = test_region_id
);