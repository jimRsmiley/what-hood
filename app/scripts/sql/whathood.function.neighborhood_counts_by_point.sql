--
-- given a test point, return the neighborhood names and count of user polygons that it hits
--

DROP TYPE IF EXISTS whathood.neighborhood_counts_by_point_result CASCADE;
CREATE TYPE whathood.neighborhood_counts_by_point_result AS (
  neighborhood_name varchar(255),
  neighborhood_id integer,
  total_user_polygons bigint
);

DROP FUNCTION IF EXISTS whathood.neighborhood_counts_by_point( _test_point geometry );

CREATE OR REPLACE FUNCTION whathood.neighborhood_counts_by_point( _test_point geometry )
RETURNS SETOF whathood.neighborhood_counts_by_point_result
AS
$BODY$
BEGIN
  RETURN QUERY SELECT
    neighborhood_name,
    neighborhood_id,
    COUNT(*) total_user_polygons
  FROM whathood.user_polygon_test_point c1
  WHERE
    ST_Contains(c1.polygon,_test_point)
  GROUP BY
    neighborhood_name,
    neighborhood_id
  ORDER BY total_user_polygons DESC;
END;
$BODY$
LANGUAGE plpgsql;
