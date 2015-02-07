CREATE OR REPLACE FUNCTION select_latest_neighborhood_polygons( test_region_id integer )
RETURNS SETOF neighborhood_polygon
AS 
$BODY$
BEGIN
  RETURN QUERY 
    SELECT 
      np_a.id as neighborhood_polygon_id,
      neighborhood.id as neighborhood_id,
      np_a.polygon as polygon,
      CAST( COUNT(trans_np_up.user_polygon_id) AS int ) as num_user_polygons,
      CAST( set_number AS int),
      current_timestamp
    FROM neighborhood_polygon np_a
    INNER JOIN trans_np_up 
      ON np_a.id = trans_np_up.neighborhood_polygon_id
    INNER JOIN neighborhood 
      ON np_a.neighborhood_id = neighborhood.id 
    WHERE NOT EXISTS (
	SELECT * FROM neighborhood_polygon b 
	INNER JOIN neighborhood ON b.neighborhood_id = neighborhood.id 
	WHERE 
	  np_a.neighborhood_id = b.neighborhood_id 
	  AND np_a.timestamp < b.timestamp
	  AND neighborhood.region_id = test_region_id
    )
  GROUP BY 
    np_a.id,
    neighborhood.name,
    neighborhood.id,
    np_a.polygon,
    np_a.set_number
  ;
END;
$BODY$
LANGUAGE plpgsql;

