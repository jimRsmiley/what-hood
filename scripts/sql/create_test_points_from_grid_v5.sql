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
  j INTEGER;
BEGIN
  BoundM := ST_Transform($1, $3); --From WGS84 (SRID 4326) to metric projection, to operate with step in meters
  Xmin := ST_XMin(BoundM);
  Xmax := ST_XMax(BoundM);
  Ymax := ST_YMax(BoundM);

  Y := ST_YMin(BoundM); --current sector's corner coordinate
  i := -1;
  j := 0;
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

      -- we only want points that are inside the bound_polygon
      IF( SELECT ST_Contains( bound_polygon, ST_Transform( ST_PointFromText('POINT('||X||' '||Y||')', $3), ST_SRID($1)) ) = true ) THEN
	points[j] := ST_PointFromText('POINT('||X||' '||Y||')', $3);
	j := j + 1;
      END IF;
      
      X := X + $2;
    END LOOP xloop;
    Y := Y + $2;
  END LOOP yloop;

  RETURN ST_Transform(ST_Collect(points), ST_SRID($1));
END;
$body$
LANGUAGE 'plpgsql';

INSERT INTO test_point (set_num,point) (
  SELECT 
  150 as set_num,
  (ST_Dump(
	-- 500 turns into  16095 points  in  19,132 ms
	-- 400 turns into  25159 points  in  35,201 ms
	-- 300 turns into  44696 points  in  61,698 ms
	-- 200 turns into 100575 points  in 231,979 ms
	-- 180 turns into 124154 poings  in 355,330 ms
	makegrid_2d(region.polygon,150)
  )).geom AS point FROM region WHERE region.name = 'Philadelphia'
);