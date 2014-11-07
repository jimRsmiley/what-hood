DISCARD TEMP;

DROP VIEW IF EXISTS heat_map_count;

DROP FUNCTION IF EXISTS whathood( test_point_set_num integer );

DROP TYPE IF EXISTS my_return_thing;

DROP TABLE IF EXISTS public.my_temp_table;

CREATE TABLE public.my_temp_table (
  test_point_id integer,
  point geometry,
  neighborhood_name varchar,
  num_polygons_for_name integer, 
  num_polygons_for_point integer
);

CREATE INDEX test_point_id_idx ON my_temp_table(test_point_id);
CREATE INDEX neighborhood_name_idx ON my_temp_table(neighborhood_name);
CREATE UNIQUE INDEX neighborhood_name_test_point_id_idx ON my_temp_table(test_point_id,neighborhood_name);
  
CREATE OR REPLACE FUNCTION whathood ( test_point_set_num integer ) 
RETURNS VOID
AS
$BODY$
DECLARE
  _r record;
  _count integer;
  i integer;
BEGIN
  
  FOR _r IN EXECUTE 'SELECT heat_map_test_point.id as heat_map_test_point_id, ST_AsText(heat_map_test_point.point) as point_text, *'
			||' FROM user_polygon'
			||' INNER JOIN neighborhood ON user_polygon.neighborhood_id = neighborhood.id'
			||' INNER JOIN heat_map_test_point ON ST_Contains(polygon,heat_map_test_point.point) = true'
			||' WHERE heat_map_test_point.set_num = '||test_point_set_num
  LOOP

	-- if the text point ID already exists in table, increment 
	IF EXISTS ( SELECT 1 FROM my_temp_table WHERE test_point_id = _r.heat_map_test_point_id AND neighborhood_name = _r.name ) THEN
		UPDATE my_temp_table SET num_polygons_for_name = num_polygons_for_name + 1 WHERE test_point_id = _r.heat_map_test_point_id AND neighborhood_name = _r.name;
	ELSE
		INSERT INTO my_temp_table VALUES( _r.heat_map_test_point_id, _r.point, _r.name, 1,0);
	END IF;

	-- get a count of how many user polygons total there are for the heat map point, and update the table
	SELECT SUM(num_polygons_for_name) INTO _count FROM my_temp_table WHERE test_point_id = _r.heat_map_test_point_id;
	UPDATE my_temp_table SET num_polygons_for_point = _count WHERE test_point_id = _r.heat_map_test_point_id;
  END LOOP;


END;
$BODY$
LANGUAGE plpgsql;

SELECT whathood( 1 );

CREATE OR REPLACE VIEW heat_map_count AS
    SELECT ST_AsText(heat_map_test_point.point), neighborhood_name, num_polygons_for_name
    FROM heat_map_test_point
    INNER JOIN my_temp_table on my_temp_table.test_point_id = heat_map_test_point.id
    INNER JOIN neighborhood ON my_temp_table.neighborhood_name = neighborhood.name;


SELECT * FROM heat_map_count;
