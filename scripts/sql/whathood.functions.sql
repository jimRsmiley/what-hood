--
--  whathood.functions.sql
--
-- sql functions needed to run whathood.sql
--
--





--
--  makegrid_2d
--
-- returns a fishnet grid of points inside the public geometry
CREATE OR REPLACE FUNCTION whathood.makegrid_2d (
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

--
-- function name: associate_test_points_w_user_polygons
--
DROP FUNCTION IF EXISTS whathood.associate_test_points_w_user_polygons( _create_event neighborhood_polygons_create_event );
CREATE OR REPLACE FUNCTION whathood.associate_test_points_w_user_polygons ( _create_event neighborhood_polygons_create_event ) 
RETURNS VOID
AS
$BODY$
DECLARE
  _r record;
BEGIN
  RAISE NOTICE 'in function associate_test_points_w_user_polygons';

  FOR _r IN EXECUTE 'SELECT test_point.id as test_point_id, user_polygon.id as up_id'
			||' FROM user_polygon'
			||' INNER JOIN test_point ON ST_Contains(polygon,test_point.point) = true'
			||' WHERE test_point.set_num = '||_create_event.test_point_meter_width
  LOOP
    INSERT INTO trans_ce_tp_up(create_event_id,test_point_id,user_polygon_id) VALUES (_create_event.id,_r.test_point_id,_r.up_id);
  END LOOP;

  RETURN;
END;
$BODY$
LANGUAGE plpgsql;

--
-- function name: neighborhoods_by_test_point
--
DROP TYPE holder CASCADE;
CREATE TYPE holder AS (test_point_id integer, name varchar, num_polygons bigint );
DROP FUNCTION IF EXISTS neighborhoods_by_test_point( test_set_num integer );
CREATE OR REPLACE FUNCTION neighborhoods_by_test_point( test_set_num integer )
RETURNS SETOF holder
AS
$BODY$
DECLARE
  _r record;
  count integer;
BEGIN

  FOR _r IN EXECUTE 'SELECT test_point_id, name, COUNT( user_polygon.id ) as num_polygons FROM trans_test_point_user_polygon 
INNER JOIN user_polygon ON user_polygon.id = trans_test_point_user_polygon.up_id 
INNER JOIN neighborhood ON user_polygon.neighborhood_id = neighborhood.id 
INNER JOIN test_point ON test_point.id = trans_test_point_user_polygon.test_point_id
WHERE test_point.set_num = '||test_set_num||
' GROUP BY neighborhood.name, test_point_id'
  LOOP
    RETURN NEXT _r;
  END LOOP;
END;
$BODY$
LANGUAGE plpgsql;

--
-- create the strength of identity table for each test point
--
DROP FUNCTION IF EXISTS whathood.create_strength_of_identity( _create_event neighborhood_polygons_create_event );
CREATE OR REPLACE FUNCTION whathood.create_strength_of_identity ( _create_event neighborhood_polygons_create_event ) 
RETURNS VOID
AS
$BODY$
DECLARE
  _r record;
  _sum_of_polygons_per_point integer;
  _test_point_meter_width integer;
  _npsoi_id integer;
BEGIN
  RAISE NOTICE 'in function create_strength_of_identity';
  
  -- delete anything in with the current create_event
  DELETE FROM neighborhood_point_strength_of_identity WHERE create_event_id = _create_event.id;

  --RAISE NOTICE 'done deleting';
  
  SELECT test_point_meter_width INTO _test_point_meter_width FROM neighborhood_polygons_create_event WHERE id = _create_event.id;

  --RAISE NOTICE 'using test point width (%)', _test_point_meter_width;
  
  FOR _r IN EXECUTE 'SELECT test_point.id as test_point_id, neighborhood_id'
			||' FROM user_polygon'
			||' INNER JOIN neighborhood ON user_polygon.neighborhood_id = neighborhood.id'
			||' INNER JOIN test_point ON ST_Contains(polygon,test_point.point) = true'
			||' WHERE test_point.set_num = '||_create_event.test_point_meter_width
  LOOP
    --RAISE NOTICE 'test_point_id(%) create_event_id(%) neighborhood_id(%)',_r.test_point_id,_create_event.id,_r.neighborhood_id;
    
    -- if the text point ID already exists in table, increment
    SELECT id INTO _npsoi_id FROM neighborhood_point_strength_of_identity WHERE create_event_id = _create_event.id AND test_point_id = _r.test_point_id AND neighborhood_id = _r.neighborhood_id;
    IF _npsoi_id IS NOT NULL THEN
      UPDATE neighborhood_point_strength_of_identity SET num_polygons_for_name = num_polygons_for_name + 1 
        WHERE id = _npsoi_id;
    ELSE
      INSERT INTO neighborhood_point_strength_of_identity( create_event_id, test_point_id, neighborhood_id, num_polygons_for_name, num_polygons_for_point ) 
	VALUES( _create_event.id, _r.test_point_id, _r.neighborhood_id, 1,0) RETURNING id INTO _npsoi_id;
    END IF;

    -- get a count of how many user polygons total there are for the test point, and update the table for all test points
    SELECT SUM(num_polygons_for_name) INTO _sum_of_polygons_per_point 
    FROM 
      neighborhood_point_strength_of_identity 
    WHERE 
      test_point_id = _r.test_point_id 
      AND create_event_id = _create_event.id;

    IF _sum_of_polygons_per_point IS NULL THEN
      RAISE EXCEPTION '_sum_of_polygons_per_point should not be null';
    END IF;

    IF NULL IS NULL THEN
      UPDATE neighborhood_point_strength_of_identity SET 
        num_polygons_for_point = _sum_of_polygons_per_point,
        strength_of_identity = num_polygons_for_name::float / _sum_of_polygons_per_point 
      WHERE 
        test_point_id = _r.test_point_id 
        AND create_event_id = _create_event.id;
    END IF;
    
  END LOOP;

END;
$BODY$
LANGUAGE plpgsql;


--
-- return type for select_neighborhood_points
--
DROP TYPE select_neighborhood_points_type CASCADE;
CREATE TYPE select_neighborhood_points_type AS (
  neighborhood_point_strength_of_identity_id integer,
  test_point_id integer,
  point geometry,
  neighborhood_id integer,
  strength_of_identity double precision
);
--
-- select neighborhood_points( _create_event_id integer, _neighborhood_id integer )
--
DROP FUNCTION IF EXISTS whathood.select_neighborhood_points(integer,integer);
CREATE OR REPLACE FUNCTION whathood.select_neighborhood_points( _create_event_id integer, _neighborhood_id integer )
RETURNS SETOF select_neighborhood_points_type 
AS
$BODY$
BEGIN
  RETURN QUERY 
    SELECT 
      x.id,
      test_point.id,
      point,
      neighborhood_id,
      x.strength_of_identity
    FROM 
      neighborhood_point_strength_of_identity x
    INNER JOIN ( 
      -- grab only the highest strength_of_identity_point
      SELECT
        test_point_id, 
        MAX(strength_of_identity) as strength_of_identity
      FROM 
        neighborhood_point_strength_of_identity
      WHERE 
        create_event_id = _create_event_id
          AND neighborhood_id = _neighborhood_id
      GROUP BY test_point_id 
    ) y
      ON x.test_point_id = y.test_point_id 
    INNER JOIN test_point 
      ON test_point.id = x.test_point_id
    WHERE 
        x.neighborhood_id = _neighborhood_id
        AND x.create_event_id = _create_event_id ;
END;
$BODY$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION whathood.select_latest_neighborhood_polygons( _create_event integer, test_region_id integer )
RETURNS SETOF neighborhood_polygon
AS 
$BODY$
BEGIN
  RETURN QUERY 
    SELECT 
      np_a.id as id,
      neighborhood.id as neighborhood_id,
      create_event_id,
      np_a.polygon as polygon,
      COUNT(trans_np_up.up_id) as num_user_polygons,
      current_timestamp as date_time_created
    FROM neighborhood_polygon np_a
    INNER JOIN trans_np_up 
      ON np_a.id = trans_np_up.np_id AND trans_np_up.create_event_id = _create_event.id
    INNER JOIN neighborhood 
      ON np_a.neighborhood_id = neighborhood.id 
    WHERE NOT EXISTS (
	SELECT * FROM neighborhood_polygon b 
	INNER JOIN neighborhood ON b.neighborhood_id = neighborhood.id 
	WHERE 
	  np_a.neighborhood_id = b.neighborhood_id 
	  AND np_a.date_time_created < b.date_time_created
	  AND neighborhood.region_id = test_region_id
    )
  GROUP BY 
    np_a.id,
    neighborhood.name,
    neighborhood.id,
    np_a.polygon,
    np_a.create_event_id
  ;
END;
$BODY$
LANGUAGE plpgsql;

--
-- create_neighborhood_polygons
--
DROP TYPE IF EXISTS create_neighborhood_polygons_return_type CASCADE;
CREATE TYPE create_neighborhood_polygons_return_type AS (neighborhood_id integer,polygon geometry,num_user_polygons integer,set_number integer,date_time_created TIMESTAMP(0));

DROP FUNCTION IF EXISTS whathood.create_neighborhood_polygons( _create_event neighborhood_polygons_create_event );
CREATE OR REPLACE FUNCTION whathood.create_neighborhood_polygons( _create_event neighborhood_polygons_create_event )
RETURNS SETOF create_neighborhood_polygons_return_type
AS
$BODY$
DECLARE
  _neighborhood record;
  _test_point_meter_width integer;
  _polygon geometry;
BEGIN

  RAISE NOTICE 'entered create_neighborhood_polygons';
  -- for each neighborhood
  FOR _neighborhood IN EXECUTE 'SELECT * FROM neighborhood' LOOP

    -- create a polygon and store it in 'polygon'
    SELECT ST_SetSRID( ST_ConcaveHull( ST_Collect( point ), _create_event.concave_hull_target_precision ),4326) INTO _polygon
      FROM whathood.select_neighborhood_points( _create_event.id, _neighborhood.id );

    IF _polygon IS NOT NULL THEN
      -- insert the neighborhood_polygon
      INSERT INTO neighborhood_polygon(neighborhood_id,create_event_id,polygon,date_time_created) 
        VALUES( _neighborhood.id, _create_event.id, _polygon, current_timestamp(0) );
    ELSE
      RAISE NOTICE 'polygon was null with neighborhood_id(%), create_event_id(%)',_neighborhood.id, _create_event.id;
    END IF;
    
  END LOOP;
END;
$BODY$
LANGUAGE plpgsql;

--
-- name: select_np_up
--
-- desc: return the neighborhood_polygon_ids and user_polygons that were used to create them given the create_event_id
--
DROP TYPE IF EXISTS select_np_up_type CASCADE;
CREATE TYPE select_np_up_type AS ( neighborhood_polygon_id integer,user_polygon_id integer );

DROP FUNCTION IF EXISTS whathood.select_np_up( _create_event neighborhood_polygons_create_event );
CREATE OR REPLACE FUNCTION whathood.select_np_up( _create_event neighborhood_polygons_create_event )
RETURNS SETOF select_np_up_type
AS
$BODY$
BEGIN

  RETURN QUERY SELECT 
    np.id as neighborhood_polygon_id, 
    user_polygon.id as up_id 
  FROM neighborhood_point_strength_of_identity
  INNER JOIN test_point 
    ON test_point.id = neighborhood_point_strength_of_identity.test_point_id
  INNER JOIN trans_ce_tp_up 
    ON trans_ce_tp_up.test_point_id = test_point.id AND trans_ce_tp_up.create_event_id = _create_event.id
  INNER JOIN user_polygon 
    ON user_polygon.id = trans_ce_tp_up.user_polygon_id
  INNER JOIN neighborhood 
    ON neighborhood.id = neighborhood_point_strength_of_identity.neighborhood_id
  INNER JOIN neighborhood_polygon np
    ON np.neighborhood_id = neighborhood.id
  WHERE 
    test_point.set_num = _create_event.test_point_meter_width
    AND np.neighborhood_id = user_polygon.neighborhood_id
  GROUP BY up_id, neighborhood_polygon_id;
  
END;
$BODY$
LANGUAGE plpgsql;


--
-- associate_np_w_up
--
DROP FUNCTION IF EXISTS whathood.associate_np_w_up( _create_event neighborhood_polygons_create_event );
CREATE OR REPLACE FUNCTION whathood.associate_np_w_up( _create_event neighborhood_polygons_create_event )
RETURNS VOID
AS
$BODY$
DECLARE
  _create_event_id integer;
BEGIN
  _create_event_id := _create_event.id;
  
  -- associate the neighborhood_polygons with the user polygons
  INSERT INTO trans_np_up(create_event_id,np_id,up_id) 
     SELECT _create_event_id, neighborhood_polygon_id, user_polygon_id
      FROM whathood.select_np_up( _create_event );
END;
$BODY$
LANGUAGE plpgsql;

--
-- find the points that are an area of contention
--
CREATE OR REPLACE FUNCTION whathood.create_contentious_points( _create_event_id integer ) 
RETURNS VOID
AS
$BODY$
BEGIN
  INSERT INTO contentious_point( create_event_id, test_point_id, point, strength_of_identity ) (
    SELECT DISTINCT     
      _create_event_id,
      test_point.id,
      test_point.point,
      a.strength_of_identity
    FROM 
      neighborhood_point_strength_of_identity a, 
      neighborhood_point_strength_of_identity b,
      test_point
    
    WHERE
      test_point.id = a.test_point_id
      AND  a.id <> b.id
      AND a.test_point_id = b.test_point_id
      AND a.strength_of_identity = b.strength_of_identity
      AND a.create_event_id = _create_event_id
      AND a.create_event_id = b.create_event_id
    GROUP BY test_point.id,a.strength_of_identity,test_point.point
  );
END;
$BODY$
LANGUAGE plpgsql;

--
-- create_event
--
DROP FUNCTION IF EXISTS whathood.create_event( set_number integer, concave_hull_target_precision double precision  );

CREATE OR REPLACE FUNCTION whathood.create_event( test_point_meter_width integer, concave_hull_target_precision double precision )
RETURNS VOID
AS 
$BODY$
DECLARE _create_event_id integer;
DECLARE _create_event neighborhood_polygons_create_event;
BEGIN

  _create_event_id := 1;
  
  IF _create_event_id IS NULL THEN
    INSERT INTO neighborhood_polygons_create_event( description, test_point_meter_width, concave_hull_target_precision, date_time_created ) 
      VALUES ( 'initial create', test_point_meter_width, concave_hull_target_precision, current_timestamp(0) );
    SELECT * INTO _create_event FROM neighborhood_polygons_create_event ORDER BY id DESC LIMIT 1;
  ELSE
    SELECT * INTO _create_event FROM neighborhood_polygons_create_event WHERE id = _create_event_id;
  END IF;

  IF _create_event IS NULL THEN
    RAISE EXCEPTION '_create_event is null, chose another id';
  END IF;

  -- 50 seconds
  --PERFORM whathood.associate_test_points_w_user_polygons( _create_event::neighborhood_polygons_create_event );

  -- 132 seconds
  --PERFORM whathood.create_strength_of_identity( _create_event::neighborhood_polygons_create_event );

  -- 20 seconds
  --PERFORM whathood.create_neighborhood_polygons( _create_event::neighborhood_polygons_create_event );

  -- 16 seconds
  --PERFORM whathood.associate_np_w_up( _create_event::neighborhood_polygons_create_event );

  -- 2 seconds
  --PERFORM whathood.create_contentious_points(_create_event.id );
END;
$BODY$
LANGUAGE plpgsql;