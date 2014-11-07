CREATE OR REPLACE FUNCTION public.whathood ( test_point public.geometry ) RETURNS SETOF user_polygon STABLE AS
$BODY$
DECLARE
BEGIN
  RETURN QUERY SELECT * FROM user_polygon WHERE ST_Contains(polygon,test_point);
END;
$BODY$
LANGUAGE 'plpgsql';

SELECT whathood( ST_PointFromText( "POINT(-75.3615201250251 39.8465952069551)" ) );