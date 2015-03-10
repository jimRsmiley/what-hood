--
-- given a test point and the neighborhood id, return info about that point
-- return type polygon_counts_result
--
DROP TYPE IF EXISTS polygon_counts_result CASCADE;
CREATE TYPE polygon_counts_result AS (
  point_as_text text,
  test_neighborhood_name text,
  point geometry,
  neighborhood_names text,
  num_in_neighborhood integer,
  total_user_polygons integer,
  strength_of_identity double precision,
  dominant_neighborhood_id integer
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
  _test_neighborhood_name text;
  _ret_val polygon_counts_result%rowtype;
  _neighborhood_name_arr text[];
  _neighborhood_names text;
  _num_in_neighborhood integer;
  _total_user_polygons integer;
  _dominant_neighborhood_id integer;
BEGIN

  SELECT name INTO _ret_val.test_neighborhood_name FROM neighborhood WHERE id = _neighborhood_id;

  --
  --
  --
  SELECT COUNT(*) INTO _num_in_neighborhood
  FROM user_polygon up
  WHERE
    neighborhood_id = _neighborhood_id
    AND ST_Contains(up.polygon,_test_point) = 'true';

  --
  -- get the names of the neighborhoods this test point is in
  --
  SELECT
    array_agg(neighborhood_name) INTO _neighborhood_name_arr
  FROM
    whathood.user_polygon_test_point
  WHERE
    ST_Contains(polygon,_test_point);

  --
  --
  --
  SELECT COUNT(*) INTO _total_user_polygons
  FROM user_polygon up
  WHERE
    ST_Contains(up.polygon,_test_point) = 'true';

  --
  --
  --
  SELECT ST_AsText(_test_point) INTO _point_as_text;

  --
  --
  --
  SELECT whathood.get_dominant_neighborhood(_test_point)
  INTO _dominant_neighborhood_id;

  IF _total_user_polygons = 0 THEN
    _ret_val.strength_of_identity := 0;
  ELSE
    _ret_val.strength_of_identity := cast(_num_in_neighborhood as double precision)/cast(_total_user_polygons as double precision);
  END IF;

  _ret_val.point_as_text        := _point_as_text;
  _ret_val.point                := _test_point;
  _ret_val.num_in_neighborhood  := _num_in_neighborhood;
  _ret_val.total_user_polygons  := _total_user_polygons;
  _ret_val.neighborhood_names   := array_to_string(_neighborhood_name_arr,';');
  _ret_val.dominant_neighborhood_id := _dominant_neighborhood_id;

  RETURN _ret_val;
END;
$$
LANGUAGE 'plpgsql';
