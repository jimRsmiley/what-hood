DROP FUNCTION whathood.makegrid_2d(bound_polygon public.geometry,
  grid_step integer,
  metric_srid integer
);
--
--  makegrid_2d
--
-- returns a fishnet grid of points inside the public geometry
CREATE OR REPLACE FUNCTION whathood.makegrid_2d (
  bound_polygon public.geometry,
  grid_step integer,
  metric_srid integer = 2251 --metric SRID optimal for the PA in state plane
)
RETURNS BOOLEAN AS
$body$
DECLARE
  BoundM public.geometry; --Bound polygon transformed to metric projection (with metric_srid SRID)
  Xmin DOUBLE PRECISION;
  Xmax DOUBLE PRECISION;
  Ymax DOUBLE PRECISION;
  X DOUBLE PRECISION;
  Y DOUBLE PRECISION;
  point public.geometry;
  i INTEGER;
  j INTEGER;
BEGIN
  BoundM := ST_Transform($1, $3); --From WGS84 (SRID 4326) to metric projection, to operate with step in meters
  Xmin := ST_XMin(BoundM);
  Xmax := ST_XMax(BoundM);
  Ymax := ST_YMax(BoundM);

  Y := ST_YMin(BoundM); --current sector's corner coordinate
  <<yloop>>
  LOOP
    IF (Y > Ymax) THEN  --Better if generating polygons exceeds bound for one step. You always can crop the result. But if not you may get not quite correct data for outbound polygons (if you calculate frequency per a sector  e.g.)
        EXIT;
    END IF;

    X := Xmin;
    <<xloop>>
    LOOP
      IF (X > Xmax) THEN
          EXIT;
      END IF;

      -- we only want points that are inside the bound_polygon
      IF( SELECT ST_Contains( bound_polygon, ST_Transform( ST_PointFromText('POINT('||X||' '||Y||')', $3), ST_SRID($1)) ) = true ) THEN
        point := ST_PointFromText('POINT('||X||' '||Y||')', $3);
        INSERT INTO test_point (point,set_num) VALUES( point, grid_step );
      END IF;

      X := X + $2;
    END LOOP xloop;
    Y := Y + $2;
  END LOOP yloop;

  RETURN True;
END;
$body$
LANGUAGE 'plpgsql';