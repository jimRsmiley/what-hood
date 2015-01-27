CREATE OR REPLACE VIEW test_point_info AS
  SELECT set_num,count(*) as count
  FROM test_point
  GROUP BY set_num ORDER BY count DESC;
