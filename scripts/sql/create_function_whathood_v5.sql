--
--
-- we need to associate test_points with user_polygons
--



DROP FUNCTION IF EXISTS whathood( test_point_set_num integer );
CREATE OR REPLACE FUNCTION whathood ( test_point_set_num integer ) 
RETURNS VOID
AS
$BODY$
DECLARE
  _r record;
  test_point_id integer;
  user_polygon_id integer;
  _sum_of_polygons_per_point integer;
BEGIN

  -- delete anything in with the current set number
  DELETE FROM neighborhood_point_strength_of_identity;

  
  FOR _r IN EXECUTE 'SELECT test_point.id as test_point_id, user_polygon.id as user_polygon_id'
			||' FROM user_polygon'
			||' INNER JOIN test_point ON ST_Contains(polygon,test_point.point) = true'
			||' WHERE test_point.set_num = '||test_point_set_num
  LOOP

	test_point_id := _r.test_point_id;
	user_polygon_id := _r.us
	INSERT INTO trans_test_point_user_polygon(test_point_id,user_polygon_id) VALUES (_r.test_point_id,_r.user_polygon_id);
  END LOOP;

END;
$BODY$
LANGUAGE plpgsql;


SELECT whathood( 100 );

DROP VIEW IF EXISTS heat_map_count;
CREATE OR REPLACE VIEW heat_map_count AS
    SELECT 
	test_point.id,
	ST_AsText(test_point.point), 
	neighborhood.name AS neighborhood_name, 
	num_polygons_for_name, 
	num_polygons_for_point,
	strength_of_identity
    FROM test_point
    INNER JOIN neighborhood_point_strength_of_identity on neighborhood_point_strength_of_identity.test_point_id = test_point.id
    INNER JOIN neighborhood ON neighborhood_point_strength_of_identity.neighborhood_id = neighborhood.id;


SELECT * FROM heat_map_count ORDER BY num_polygons_for_point DESC;
