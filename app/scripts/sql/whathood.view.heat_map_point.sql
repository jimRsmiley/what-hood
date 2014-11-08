DROP VIEW heat_map_count;
CREATE OR REPLACE VIEW heat_map_count AS
    SELECT 
	test_point.id AS test_point_id,
	test_point.set_num,
	ST_AsText(test_point.point), 
	neighborhood.name AS neighborhood_name, 
	num_polygons_for_name, 
	num_polygons_for_point,
	strength_of_identity
    FROM test_point
    INNER JOIN my_temp_table on my_temp_table.test_point_id = test_point.id
    INNER JOIN neighborhood ON my_temp_table.neighborhood_id = neighborhood.id;
