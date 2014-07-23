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

  _create_event_id := 2;
  
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
  PERFORM whathood.associate_test_points_w_user_polygons( _create_event::neighborhood_polygons_create_event );

  -- 132 seconds
  PERFORM whathood.create_strength_of_identity( _create_event::neighborhood_polygons_create_event );

  -- 20 seconds
  PERFORM whathood.create_neighborhood_polygons( _create_event::neighborhood_polygons_create_event );

  -- 16 seconds
  PERFORM whathood.associate_np_w_up( _create_event::neighborhood_polygons_create_event );

  -- 2 seconds
  PERFORM whathood.create_contentious_points(_create_event.id );
END;
$BODY$
LANGUAGE plpgsql;

SELECT whathood.create_event(50,.99);