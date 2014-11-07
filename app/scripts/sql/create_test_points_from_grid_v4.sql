CREATE OR REPLACE FUNCTION public.makegrid_2d (
  bound_polygon public.geometry,
  grid_step integer,
  metric_srid integer = 2251 --metric SRID optimal for the PA in state plane
)
RETURNS public.geometry AS
$body$
DECLARE
  BoundM public.geometry; --Bound polygon transformed to metric projection (with metric_srid SRID)
  Xmin DOUBLE PRECISION;
  Xmax DOUBLE PRECISION;
  Ymax DOUBLE PRECISION;
  X DOUBLE PRECISION;
  Y DOUBLE PRECISION;
  points public.geometry[];
  i INTEGER;
BEGIN
  BoundM := ST_Transform($1, $3); --From WGS84 (SRID 4326) to metric projection, to operate with step in meters
  Xmin := ST_XMin(BoundM);
  Xmax := ST_XMax(BoundM);
  Ymax := ST_YMax(BoundM);

  Y := ST_YMin(BoundM); --current sector's corner coordinate
  i := -1;
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

      i := i + 1;
      points[i] := ST_PointFromText('POINT('||X||' '||Y||')', $3);

      X := X + $2;
    END LOOP xloop;
    Y := Y + $2;
  END LOOP yloop;

  RETURN ST_Transform(ST_Collect(points), ST_SRID($1));
END;
$body$
LANGUAGE 'plpgsql';

TRUNCATE table heat_map_test_point;

INSERT INTO heat_map_test_point (set_num,point) (
	SELECT 
	1 as set_num,
	(ST_Dump(makegrid_2d(ST_GeomFromText('POLYGON((-75.30535697937 39.840177132755,-75.30535697937 40.13799199974,-74.955763000017 40.13799199974,-74.955763000017 39.840177132755,-75.30535697937 39.840177132755))',
		 4326), -- WGS84 SRID
		 500) -- cell step in meters, 500 turns into 56088 points, 47 seconds
	)).geom AS point
);


