--
--  merge_user_polygons will merge all of the user polgyons given by _neighborhood_id 
--  and wrap up test_points into a border where those test points are dominated by _neighborhood_id
--

--
-- drop the function if it exists
--
DROP FUNCTION IF EXISTS whathood.merge_user_polygons( 
  test_points geometry[],
  neighborhood_id integer
);

--
-- create the function whathood.merge_user_polygons
--
CREATE OR REPLACE FUNCTION  whathood.merge_user_polygons( 
  test_points geometry[],
  _neighborhood_id integer 
)
RETURNS polygon_counts_result[]
AS
$$
  DECLARE
    _test_point geometry;
    r merge_user_polygon_res_holder%rowtype;
    _ret_val polygon_counts_result[];
    _polygon_counts_result polygon_counts_result;
  BEGIN
  
    FOREACH _test_point IN ARRAY test_points LOOP
      SELECT whathood.polygon_counts(_test_point,_neighborhood_id) INTO _polygon_counts_result;
      _ret_val := _polygon_counts_result;
--      END LOOP;
--FOR r IN 
--        SELECT
--          ST_AsText(_test_point),
--          cast(_test_point as geometry),
--          COUNT(*) as count1
--        FROM user_polygon 
--        WHERE 
--          ST_Contains(polygon,_test_point) = 'true'
--          AND neighborhood_id = _neighborhood_id 
--      LOOP
      --FOR r IN SELECT ST_Point(1,1), 12 as num1 LOOP
--        IF r.num_user_polygons > 0 THEN
--          RETURN NEXT r;
--        END IF;
--      END LOOP;
    END LOOP;

    RETURN _ret_val;
  END;
$$
LANGUAGE plpgsql;
