DROP VIEW IF EXISTS create_event_info;
CREATE OR REPLACE VIEW create_event_info AS
  SELECT 
    id as create_event_id, 
    date_time_created, 
    test_point_meter_width, 
    concave_hull_target_precision, 
    num_test_points,
    num_strength_of_identity,
    num_neighborhood_polygons
  FROM create_neighborhood_polygons_event a

  -- how many test points
  LEFT JOIN (
    SELECT 
      cnpe.id AS b_create_event_id, COUNT(tp) as num_test_points 
    FROM create_neighborhood_polygons_event as cnpe
    INNER JOIN test_point tp ON tp.set_num = cnpe.test_point_meter_width
    GROUP BY cnpe.id
    ) b
  ON a.id = b.b_create_event_id  

  -- how many strengths_of_identity
  LEFT JOIN (
    SELECT 
      cnpe.id AS create_event_id, COUNT(npsoi) AS num_strength_of_identity 
    FROM create_neighborhood_polygons_event as cnpe
    INNER JOIN neighborhood_point_strength_of_identity npsoi ON npsoi.create_event_id = cnpe.id
    
    GROUP BY cnpe.id
    ) c
  ON a.id = c.create_event_id

    -- how many strengths_of_identity
    LEFT JOIN (
    SELECT 
      cnpe.id AS create_event_id, COUNT( np ) AS num_neighborhood_polygons
    FROM create_neighborhood_polygons_event as cnpe
    INNER JOIN neighborhood_polygon np ON np.create_event_id = cnpe.id
    
    GROUP BY cnpe.id
    ) d
  ON a.id = d.create_event_id

  

  ORDER BY date_time_created DESC
  ;
