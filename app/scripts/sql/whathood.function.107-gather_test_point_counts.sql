--
-- description: given an array of test points, return:
--  * an array of custom types that
--  * a count the number of user polygons identified by _neighborhood_id
--  * a count of all the user polygons the test point touches
--

-- drop the function if it exists
DROP FUNCTION IF EXISTS whathood.gather_test_point_counts(test_points geometry[],neighborhood_id integer);

--
-- function definition
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
      SELECT * INTO _r
      FROM whathood.polygon_counts(_test_point,_neighborhood_id);
      RETURN NEXT _r;
   END LOOP;
  END;
$$
LANGUAGE plpgsql;
