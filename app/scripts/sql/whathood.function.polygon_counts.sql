--
-- return type polygon_counts_result
--
DROP TYPE IF EXISTS polygon_counts_result CASCADE;
CREATE TYPE polygon_counts_result AS (num_in_neighborhood integer, total_user_polygons integer);

--
-- function whathood.polygon_counts
--
CREATE OR REPLACE FUNCTION whathood.polygon_counts ( 
  _test_point geometry, 
  _neighborhood_id integer
)
RETURNS polygon_counts_result
AS
$$
DECLARE
  _ret_val polygon_counts_result;
BEGIN
  SELECT COUNT(*) INTO _ret_val.num_in_neighborhood 
  FROM user_polygon up
  WHERE 
    neighborhood_id = _neighborhood_id 
    AND ST_Contains(up.polygon,_test_point) = 'true';

  SELECT COUNT(*) INTO _ret_val.total_user_polygons
  FROM user_polygon up
  WHERE
    ST_Contains(up.polygon,_test_point) = 'true';

  RETURN _ret_val;
END;
$$
LANGUAGE plpgsql;
