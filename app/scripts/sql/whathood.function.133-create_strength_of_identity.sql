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
