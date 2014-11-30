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
    _r polygon_counts_result%rowtype;
    _polygon_count_result_array polygon_counts_result[];
    _polygon_counts_result polygon_counts_result;
  BEGIN
  
    FOREACH _test_point IN ARRAY test_points LOOP
      SELECT * INTO _r FROM whathood.polygon_counts(_test_point,_neighborhood_id);
      _polygon_count_result_array := array_append( _polygon_count_result_array, _r);
    END LOOP;

    RETURN _polygon_count_result_array;
  END;
$$
LANGUAGE plpgsql;
