-- ***************** views ************************
--
-- create_event

--
-- NAME: create_test_points(_point_width)
--
-- returns a count of inserted test points
--
DROP FUNCTION IF EXISTS whathood.create_test_points(_point_width integer);
CREATE OR REPLACE FUNCTION whathood.create_test_points(_point_width integer)
RETURNS integer
AS
$BODY$
DECLARE _count integer;
BEGIN

  INSERT INTO test_point (set_num,point) (
    SELECT 
      _point_width,
      (ST_Dump(
        makegrid_2d(region.border,_point_width)
      )).geom AS point 
    FROM region 
    WHERE region.name = 'Philadelphia'
  );

  SELECT COUNT(*) INTO _count FROM test_point WHERE set_num = _point_width;

  RETURN _count;
END;
$BODY$
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
