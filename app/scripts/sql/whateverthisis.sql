SELECT 
      npa.id as neighborhood_polygon_id,
      neighborhood.id as neighborhood_id,
      npa.polygon as polygon,
      CAST( COUNT(trans_np_up.user_polygon_id) AS int ) as num_user_polygons,
      CAST( set_number AS int),
      current_timestamp
    FROM neighborhood_polygon npa
    INNER JOIN trans_np_up ON npa.id = trans_np_up.neighborhood_polygon_id
    INNER JOIN neighborhood 
      ON npa.neighborhood_id = neighborhood.id 
    WHERE NOT EXISTS (
	SELECT * FROM neighborhood_polygon b 
	INNER JOIN neighborhood ON b.neighborhood_id = neighborhood.id 
	WHERE 
	  npa.neighborhood_id = b.neighborhood_id 
	  AND npa.timestamp < b.timestamp
	  AND neighborhood.region_id = 1
    )
  GROUP BY 
    npa.id,
    --trans_np_up.id,
    --trans_np_up.neighborhood_polygon_id,
    neighborhood.name,
    neighborhood.id,
    npa.polygon,
    npa.set_number

  ORDER BY num_user_polygons DESC