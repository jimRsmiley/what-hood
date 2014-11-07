SELECT 
    np.id as neighborhood_polygon_id, 
    user_polygon.id as up_id 
  FROM neighborhood_point_strength_of_identity
  INNER JOIN test_point 
    ON test_point.id = neighborhood_point_strength_of_identity.test_point_id
  INNER JOIN trans_ce_tp_up 
    ON trans_ce_tp_up.test_point_id = test_point.id AND trans_ce_tp_up.create_event_id = 1
  INNER JOIN user_polygon 
    ON user_polygon.id = trans_ce_tp_up.user_polygon_id
  INNER JOIN neighborhood 
    ON neighborhood.id = neighborhood_point_strength_of_identity.neighborhood_id
  INNER JOIN neighborhood_polygon np
    ON np.neighborhood_id = neighborhood.id
  WHERE 
    test_point.set_num = 100
    AND np.neighborhood_id = user_polygon.neighborhood_id
  GROUP BY up_id, neighborhood_polygon_id;