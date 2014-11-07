DROP FUNCTION whathood(heat_map_test_point);

CREATE OR REPLACE FUNCTION whathood ( heat_map_test_point ) RETURNS record AS
$BODY$
DECLARE
  result record;
BEGIN
  SELECT * INTO result FROM user_polygon INNER JOIN heat_map_test_point ON ST_Contains(polygon,$1.point);
  RETURN result;
END;
$BODY$
LANGUAGE plpgsql;

SELECT whathood( heat_map_test_point.* ) AS whathood FROM heat_map_test_point;