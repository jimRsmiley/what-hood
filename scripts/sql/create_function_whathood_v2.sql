DROP FUNCTION whathood(heat_map_test_point);

CREATE OR REPLACE FUNCTION whathood ( heat_map_test_point ) RETURNS table( id integer, point geometry, strength_of_identity integer, set_num integer ) AS
$BODY$
DECLARE
  result record;
BEGIN
  SELECT id=1,* INTO result FROM user_polygon INNER JOIN heat_map_test_point ON ST_Contains(polygon,$1.point) = 't';
  whathood.id := 5;
  point := ST_GeomFromText( 'POINT(39.950585 -75.148936)' );
  RETURN NEXT;
END;
$BODY$
LANGUAGE plpgsql;

SELECT whathood( heat_map_test_point.* ) AS whathood FROM heat_map_test_point;