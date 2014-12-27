--
--  merge_user_polygons will merge all of the user polgyons given by _neighborhood_id
--  and wrap up test_points into a border where those test points are dominated by _neighborhood_id
--

--
-- drop the function if it exists
--
DROP FUNCTION IF EXISTS whathood.gather_test_point_counts(
  test_points point[],
  neighborhood_id integer
);

--
-- name: whathood.merge_user_polygons
--
-- description: given an array of test points, return:
--   an array of custom types that
--   a count the number of user polygons identified by _neighborhood_id
--   a count of all the user polygons the test point touches
--
CREATE OR REPLACE FUNCTION  whathood.gather_test_point_counts(
  test_points geometry[],
  _neighborhood_id integer
)
RETURNS SETOF polygon_counts_result
AS
$$
  DECLARE
    _test_point geometry;
    _r polygon_counts_result%rowtype;
    _polygon_count_result_array polygon_counts_result[];
    _polygon_counts_result polygon_counts_result;
  BEGIN

    FOREACH _test_point IN ARRAY test_points LOOP
      SELECT * INTO _r FROM whathood.polygon_counts(_test_point,_neighborhood_id);
      RETURN NEXT _r;
   END LOOP;
  END;
$$
LANGUAGE plpgsql;


DROP FUNCTION IF EXISTS whathood.merge_user_polygons(
  _test_points geometry[],
  _neighborhood_id integer
);


CREATE OR REPLACE FUNCTION whathood.merge_user_polygons(
  _test_points geometry[],
  _neighborhood_id integer
)
RETURNS integer
AS
$$
  DECLARE
    _max integer;
  BEGIN
    SELECT COUNT(a.*) INTO _max FROM (SELECT * FROM whathood.gather_test_point_counts(_test_points,_neighborhood_id)) a;
    RETURN _max;
  END;
$$
LANGUAGE plpgsql;
