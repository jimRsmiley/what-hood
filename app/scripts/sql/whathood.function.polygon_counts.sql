--
-- given a test point and the neighborhood id, return info about that point
-- return type polygon_counts_result
--
DROP TYPE IF EXISTS polygon_counts_result CASCADE;
CREATE TYPE polygon_counts_result AS (
  point_as_text text,
  point geometry,
  num_in_neighborhood integer,
  total_user_polygons integer,
  strength_of_identity double precision
);

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
  _point_as_text text;
  _ret_val polygon_counts_result%rowtype;
  _num_in_neighborhood integer;
  _total_user_polygons integer;
BEGIN
  SELECT COUNT(*) INTO _num_in_neighborhood
  FROM user_polygon up
  WHERE
    neighborhood_id = _neighborhood_id
    AND ST_Contains(up.polygon,_test_point) = 'true';

  SELECT COUNT(*) INTO _total_user_polygons
  FROM user_polygon up
  WHERE
    ST_Contains(up.polygon,_test_point) = 'true';

  SELECT ST_AsText(_test_point) INTO _point_as_text;

  _ret_val.point_as_text       := _point_as_text;
  _ret_val.point               := _test_point;
  _ret_val.num_in_neighborhood := _num_in_neighborhood;
  _ret_val.total_user_polygons := _total_user_polygons;
  _ret_val.strength_of_identity := _num_in_neighborhood/_total_user_polygons;

  RETURN _ret_val;
END;
$$
LANGUAGE 'plpgsql';
