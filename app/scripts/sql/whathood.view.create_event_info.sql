CREATE OR REPLACE VIEW create_event_info AS
  SELECT a.*, count 
  FROM neighborhood_polygons_create_event a
  INNER JOIN (
    SELECT set_num, COUNT(*) FROM test_point GROUP BY set_num
    ) as b
  ON a.test_point_meter_width = b.set_num;
