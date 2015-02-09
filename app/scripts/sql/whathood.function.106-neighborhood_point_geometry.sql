--
-- 
-- Returns a geometry of neighborhood points that are considered dominant points for the neighborhood

CREATE OR REPLACE FUNCTION whathood.neighborhood_point_geometry(
  _neighborhood_id integer,
  _user_polygon_bound geometry,
  _grid_resolution numeric
)
RETURNS geometry AS
$BODY$
DECLARE
BEGIN
  RETURN ST_Collect(point) FROM whathood.neighborhood_point_info(_neighborhood_id,_user_polygon_bound,_grid_resolution); 
END;
$BODY$
LANGUAGE 'plpgsql';
