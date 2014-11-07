DROP FUNCTION IF EXISTS create_neighborhood_polygons(test_point_set_num integer);
DROP TYPE IF EXISTS create_neighborhood_polygons_return_type;

CREATE TYPE create_neighborhood_polygons_return_type AS (
  neighborhood_id integer,
  polygon geometry,
  set_number integer,
  timestamp timestamp with time zone
);



CREATE OR REPLACE FUNCTION create_neighborhood_polygons(test_point_set_num integer)
RETURNS SETOF create_neighborhood_polygons_return_type
AS
$BODY$
DECLARE
  _r record;
  polygon geometry;
BEGIN
  
  FOR _r IN EXECUTE 'SELECT * FROM neighborhood' LOOP

    SELECT ST_SetSRID( ST_ConcaveHull( ST_Collect( test_point.point ),.99 ),4326) INTO polygon
      FROM my_temp_table x
        JOIN ( SELECT 
                 test_point_id, 
                 MAX(strength_of_identity) as strength_of_identity,
                 num_polygons_for_name 
               FROM neighborhood_point_strength_of_identity 
               GROUP BY test_point_id ) y
        ON x.test_point_id = y.test_point_id AND x.strength_of_identity = y.strength_of_identity
        JOIN test_point ON x.test_point_id = test_point.id
        WHERE 
          test_point.set_num = test_point_set_num
          AND neighborhood_id = _r.id 
    ;
    
    RETURN QUERY SELECT _r.id::integer, polygon::geometry, test_point_set_num::integer, current_timestamp;
  END LOOP;
END;
$BODY$
LANGUAGE plpgsql;

INSERT INTO neighborhood_polygon(neighborhood_id,polygon,set_number,timestamp) SELECT * FROM create_neighborhood_polygons(100);
--SELECT create_neighborhood_polygons(200);
--SELECT ST_AsGeoJSON(create_neighborhood_polygons(200));
--SELECT ST_Collect(create_neighborhood_polygons(200));
--SELECT ST_AsGeoJson( ST_Collect(create_neighborhood_polygons(200)) ) FROM create_neighborhood_polygons();