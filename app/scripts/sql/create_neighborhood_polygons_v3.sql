DROP FUNCTION IF EXISTS create_neighborhood_polygons(test_point_set_num integer);

CREATE OR REPLACE FUNCTION create_neighborhood_polygons(test_point_set_num integer)
RETURNS SETOF record
AS
$BODY$
DECLARE
  _r record;
  polygon geometry;
BEGIN
  
  FOR _r IN EXECUTE 'SELECT * FROM neighborhood' LOOP

    SELECT ST_SetSRID( ST_ConcaveHull( ST_Collect( test_point.point ),.99 ),4326) INTO polygon
      FROM my_temp_table x
        JOIN ( SELECT test_point_id, MAX(strength_of_identity) as strength_of_identity FROM my_temp_table GROUP BY test_point_id ) y
        ON x.test_point_id = y.test_point_id AND x.strength_of_identity = y.strength_of_identity
        JOIN test_point ON x.test_point_id = test_point.id
        WHERE 
          test_point.set_num = test_point_set_num
          AND neighborhood_id = _r.id 
    ;
    
    RETURN QUERY SELECT _r.id::integer, polygon::geometry, test_point_set_num::integer;
  END LOOP;
END;
$BODY$
LANGUAGE plpgsql;

SELECT * FROM create_neighborhood_polygons(100);
--SELECT create_neighborhood_polygons(200);
--SELECT ST_AsGeoJSON(create_neighborhood_polygons(200));
--SELECT ST_Collect(create_neighborhood_polygons(200));
--SELECT ST_AsGeoJson( ST_Collect(create_neighborhood_polygons(200)) ) FROM create_neighborhood_polygons();