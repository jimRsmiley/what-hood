CREATE OR REPLACE VIEW whathood.user_polygons_not_in_neighborhoods AS 
  SELECT up.*
  FROM user_polygon up
  WHERE up.id NOT IN ( SELECT up_id FROM trans_np_up )
