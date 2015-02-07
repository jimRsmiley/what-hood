DROP FUNCTION IF EXISTS latest_neighborhoods_geojson( test_region_id integer );

CREATE OR REPLACE FUNCTION latest_neighborhoods_geojson( test_region_id integer )
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
  FROM latest_neighborhoods slnp
    INNER JOIN neighborhood
      ON slnp.neighborhood_id = neighborhood.id ) as f ) as fc;
  RETURN geojson;
END;
$BODY$
LANGUAGE plpgsql;

