--
--  makegrid_2d
--
-- returns a fishnet grid of points inside the public geometry


DROP FUNCTION whathood.makegrid_2d(
  bound_polygon public.geometry,
  grid_step numeric
);
CREATE OR REPLACE FUNCTION whathood.makegrid_2d (
  bound_polygon public.geometry,
  grid_step numeric
)
RETURNS geometry[] AS
$body$
DECLARE
  BoundM public.geometry; --Bound polygon transformed to metric projection (with metric_srid SRID)
  Xmin DOUBLE PRECISION;
  Xmax DOUBLE PRECISION;
  Ymax DOUBLE PRECISION;
  X DOUBLE PRECISION;
  Y DOUBLE PRECISION;
  point public.geometry;
  points geometry[];
  i INTEGER;
  j INTEGER;
  count INTEGER = 0;
BEGIN
  BoundM := $1;
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
      IF( SELECT ST_Contains( bound_polygon, ST_PointFromText('POINT('||X||' '||Y||')', 4326)) = true ) THEN
        point := ST_PointFromText('POINT('||X||' '||Y||')', 4326);
        points := array_append(points,point);
        count := count + 1;
      END IF;
      X := X + grid_step;
    END LOOP xloop;
    Y := Y + grid_step;
  END LOOP yloop;

  RETURN points;
END;
$body$
LANGUAGE 'plpgsql';
