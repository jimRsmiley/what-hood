CREATE OR REPLACE VIEW whathood.user_polygon_test_point AS
  SELECT
    up.id as user_polygon_id,
    up.polygon as polygon,
    n.name as neighborhood_name,
    n.id as neighborhood_id
  FROM user_polygon up
  INNER JOIN neighborhood n ON n.id = up.neighborhood_id
