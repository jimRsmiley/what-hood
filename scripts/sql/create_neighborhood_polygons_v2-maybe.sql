DROP FUNCTION IF EXISTS create_neighborhood_polygons(test_point_set_num integer);

CREATE OR REPLACE FUNCTION create_neighborhood_polygons(test_point_set_num integer)
RETURNS SETOF geometry
AS
$BODY$
DECLARE
  _r record;
  _sum_of_polygons_per_point integer;
  polygon geometry;
BEGIN
  
  FOR _r IN EXECUTE 'SELECT * FROM neighborhood' LOOP

    SELECT ST_SetSRID( ST_ConcaveHull( ST_Collect( heat_map_test_point.point ),.99 ),4326) INTO polygon
    FROM my_temp_table
    INNER JOIN heat_map_test_point ON my_temp_table.test_point_id = heat_map_test_point.id
      WHERE 
        heat_map_test_point.set_num = test_point_set_num
        AND neighborhood_id = _r.id 
        AND strength_of_identity > .5;

    RETURN NEXT polygon;
  END LOOP;
END;
$BODY$
LANGUAGE plpgsql;

SELECT ST_AsGeoJson( ST_Collect(create_neighborhood_polygons) ) FROM create_neighborhood_polygons(200);
--SELECT create_neighborhood_polygons(200);
--SELECT ST_AsGeoJSON(create_neighborhood_polygons(200));
--SELECT ST_Collect(create_neighborhood_polygons(200));
--SELECT ST_AsGeoJson( ST_Collect(create_neighborhood_polygons(200)) ) FROM create_neighborhood_polygons();