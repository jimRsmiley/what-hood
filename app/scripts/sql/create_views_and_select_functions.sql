--ALTER TABLE neighborhood_polygon DROP COLUMN num_user_polygons CASCADE;

--
-- name: np_w_up_count
-- desc: select neighborhood_polygons with a count of user polygons that created them
--
DROP VIEW IF EXISTS whathood.np_w_up_count;
CREATE OR REPLACE VIEW whathood.np_w_up_count AS
  SELECT 
    np.*, 
    COUNT( trans_np_up.up_id ) as num_up 
  from neighborhood_polygon np 
  INNER JOIN trans_np_up 
    ON trans_np_up.np_id = np.id 
  GROUP BY np.id;

--
-- name: neighborhood_user_polygon
-- desc: select a user polygon with it's neighborhood
--
DROP VIEW IF EXISTS whathood.neighborhood_user_polygon;
CREATE OR REPLACE VIEW whathood.neighborhood_user_polygon AS
 SELECT np_id, up_id,name
FROM trans_np_up
INNER JOIN neighborhood_polygon np ON np.id = trans_np_up.np_id
INNER JOIN neighborhood n ON n.id = np.neighborhood_id
WHERE np_id  = 790
ORDER BY up_id
;

--
-- name: neighborhood_polygons_geojson_by_id
-- desc: select neighborhood_polygons by np_id
--
DROP FUNCTION IF EXISTS whathood.neighborhood_polygons_geojson_by_id( test_np_id integer );

CREATE OR REPLACE FUNCTION whathood.neighborhood_polygons_geojson_by_id( test_np_id integer )
RETURNS varchar
AS 
$BODY$
DECLARE
  geojson varchar;
BEGIN
  SELECT row_to_json( fc ) INTO geojson
    FROM ( SELECT 'FeatureCollection' as type, array_to_json(array_agg(f)) as features
    FROM( SELECT 'Feature' as type
      , ST_AsGeoJSON( np.polygon)::json AS geometry
      , row_to_json( 
        (SELECT l FROM ( SELECT name,up.id) AS l)
      ) AS properties
  FROM neighborhood_polygon np
    INNER JOIN neighborhood 
      ON np.neighborhood_id = neighborhood.id 
      WHERE np.id = test_np_id ) as f ) as fc;
  RETURN geojson;
END;
$BODY$
LANGUAGE plpgsql;

--
-- name: user_polygons_geojson_by_user_id
-- desc: select all user_polygons by a user_id, return geojson
-- 
DROP FUNCTION IF EXISTS user_polygons_geojson_by_user_id( test_whathood_user_id integer );

CREATE OR REPLACE FUNCTION user_polygons_geojson_by_user_id( test_whathood_user_id integer )
RETURNS varchar
AS 
$BODY$
DECLARE
  geojson varchar;
BEGIN
  SELECT row_to_json( fc ) INTO geojson
    FROM ( SELECT 'FeatureCollection' as type, array_to_json(array_agg(f)) as features
    FROM( SELECT 'Feature' as type
      , ST_AsGeoJSON( up.polygon)::json AS geometry
      , row_to_json( 
        (SELECT l FROM ( SELECT name,up.id) AS l)
      ) AS properties
  FROM user_polygon up
    INNER JOIN neighborhood 
      ON up.neighborhood_id = neighborhood.id 
      WHERE up.whathood_user_id = test_whathood_user_id ) as f ) as fc;
  RETURN geojson;
END;
$BODY$
LANGUAGE plpgsql;

--
-- name: neighborhoods_geojson
-- desc: select neighborhoods_geojson y create_event_id and region_id
--
DROP FUNCTION whathood.neighborhoods_geojson(integer,integer);
CREATE OR REPLACE FUNCTION whathood.neighborhoods_geojson( _create_event_id integer, _region_id integer )
RETURNS varchar
AS 
$BODY$
DECLARE
  geojson varchar;
BEGIN

  SELECT row_to_json( fc ) INTO geojson
    FROM ( SELECT 'FeatureCollection' as type, array_to_json(array_agg(f)) as features
    FROM( 
      SELECT 
        'Feature' as type
        , ST_AsGeoJSON( polygon )::json AS geometry
        , row_to_json( 
          (
            SELECT l FROM ( 
              SELECT 
                neighborhood.name,
                np_info.id,
                num_up
            ) AS l
          )
        ) AS properties
      FROM whathood.np_w_up_count np_info
      INNER JOIN neighborhood 
        ON neighborhood_id = neighborhood.id
      WHERE
        create_event_id = _create_event_id
        AND region_id = _region_id
    ) as f ) as fc;
    
  RETURN geojson;
END;
$BODY$
LANGUAGE plpgsql;

DROP FUNCTION IF EXISTS whathood.last_create_event();
CREATE OR REPLACE FUNCTION whathood.last_create_event()
RETURNS neighborhood_polygons_create_event
AS
$BODY$
BEGIN
  SELECT * FROM whathood.create_neighborhood_polygons_event ORDER BY id DESC LIMIT 1;
END;
$BODY$
LANGUAGE plpgsql;


--
-- name: neighborhood_polygons_by_create_event_id
--
CREATE OR REPLACE FUNCTION whathood.neighborhood_polygons_by_create_event_id( _create_event_id integer, _region_id integer )
RETURNS SETOF neighborhood_polygon
AS 
$BODY$
BEGIN
  RETURN QUERY 
    SELECT 
      np.id as id,
      np.neighborhood_id as neighborhood_id,
      np.create_event_id,
      np.polygon as polygon,
      np.date_time_created
    FROM neighborhood_polygon np
    INNER JOIN trans_np_up 
      ON np.id = trans_np_up.np_id
    INNER JOIN neighborhood 
      ON np.neighborhood_id = neighborhood.id 
    WHERE np.create_event_id = _create_event_id
      AND neighborhood.region_id = _region_id
  GROUP BY 
    np.id,
    neighborhood.name,
    neighborhood.id,
    np.polygon
  ;
END;
$BODY$
LANGUAGE plpgsql;

--
-- neighborhood_heat_map
--
DROP TYPE IF EXISTS neighborhood_heat_map_type CASCADE;
CREATE TYPE neighborhood_heat_map_type AS (
  test_point_id integer, x double precision, y double precision, strength_of_identity double precision);
  
DROP FUNCTION IF EXISTS neighborhood_heat_map( test_neighborhood_id integer, test_set_num integer );
CREATE OR REPLACE FUNCTION neighborhood_heat_map( test_neighborhood_id integer, test_set_num integer )
RETURNS SETOF neighborhood_heat_map_type
AS 
$BODY$
BEGIN
  RETURN QUERY
    SELECT test_point.id, ST_X(point), ST_Y(point), strength_of_identity FROM neighborhood_point_strength_of_identity
      INNER JOIN test_point ON test_point.id = neighborhood_point_strength_of_identity.test_point_id
      WHERE neighborhood_id = test_neighborhood_id
        AND test_point.set_num = test_set_num;
END;
$BODY$
LANGUAGE plpgsql;

--
-- create a pleasant view for neighborhood_heat_map_point_info
--
DROP VIEW IF EXISTS whathood.neighborhood_point_info;
CREATE OR REPLACE VIEW whathood.neighborhood_point_info AS
    SELECT 
	test_point.id,
	ST_AsText(test_point.point), 
	neighborhood.name AS neighborhood_name, 
	num_polygons_for_name,
	num_polygons_for_point,
	strength_of_identity
    FROM test_point
    INNER JOIN neighborhood_point_strength_of_identity on neighborhood_point_strength_of_identity.test_point_id = test_point.id
    INNER JOIN neighborhood ON neighborhood_point_strength_of_identity.neighborhood_id = neighborhood.id
    ;

SELECT * FROM whathood.neighborhoods_geojson( 1, 1 );