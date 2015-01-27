--
-- call gather_test_point_counts on all test points inside all user_polygons
-- matching _neighbirhood_id and wrap them in concave_hull
--

DROP FUNCTION IF EXISTS whathood.create_neighborhood_border(_neighborhood_id integer,_grid_resolution numeric);

CREATE OR REPLACE FUNCTION whathood.create_neighborhood_border(
  _neighborhood_id integer,
  _grid_resolution numeric
)
RETURNS SETOF polygon_counts_result AS
$BODY$
DECLARE
BEGIN
  RETURN QUERY SELECT
    (whathood.gather_test_point_counts(
      whathood.makegrid_2d(
        ST_Collect(up.polygon),
        _grid_resolution
      ),
      _neighborhood_id
    )).*
  FROM
    user_polygon up
  WHERE
    up.neighborhood_id = _neighborhood_id;
END;
$BODY$
LANGUAGE 'plpgsql';
