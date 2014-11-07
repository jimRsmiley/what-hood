EXPLAIN SELECT DISTINCT 
    a.id, 
    a.test_point_id,
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
      AND a.create_event_id = 1
      AND a.create_event_id = b.create_event_id;