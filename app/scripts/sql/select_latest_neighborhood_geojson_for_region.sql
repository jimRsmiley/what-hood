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

DROP FUNCTION IF EXISTS neighborhoods_geojson( test_region_id integer );

CREATE OR REPLACE FUNCTION neighborhoods_geojson( test_region_id integer )
RETURNS varchar
AS 
$BODY$
DECLARE
  geojson varchar;
BEGIN
  SELECT row_to_json( fc ) INTO geojson
    FROM ( SELECT 'FeatureCollection' as type, array_to_json(array_agg(f)) as features
    FROM( SELECT 'Feature' as type
      , ST_AsGeoJSON( slnp.polygon)::json AS geometry
      , row_to_json( 
        (SELECT l FROM ( SELECT name,slnp.id,slnp.num_user_polygons) AS l)
      ) AS properties
  FROM select_latest_neighborhood_polygons(test_region_id) slnp
    INNER JOIN neighborhood 
      ON slnp.neighborhood_id = neighborhood.id ) as f ) as fc;
  RETURN geojson;
END;
$BODY$
LANGUAGE plpgsql;

--SELECT * FROM select_latest_neighborhood_polygons(1);
--SELECT * FROM neighborhoods_geojson(1);
SELECT neighborhoods_geojson(1) as geojson;
--SELECT row_to_json( fc ) as geojson
--FROM ( SELECT 'FeatureCollection' as type, array_to_json(array_agg(f)) as features
--FROM( SELECT 'Feature' as type
--    , ST_AsGeoJSON( neighborhood_location_count.neighborhood_polygon)::json AS geometry
--    , row_to_json( (SELECT l FROM ( SELECT neighborhood_name,y2007,y2008,y2009,y2010,y2011,y2012,y2013, ((((cast(y2007 as float)+y2008+y2009+y2010+y2011)/5)-((y2012+y2013)/2))/(y2007+y2008+y2009+y2010+y2011)/5)*100 as gentrifyer ) AS l
  --  )) AS properties
---FROM neighborhood_location_count ) as f ) as fc
