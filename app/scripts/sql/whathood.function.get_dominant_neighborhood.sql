DROP FUNCTION IF EXISTS whathood.get_dominant_neighborhood(_test_point geometry) CASCADE;

CREATE OR REPLACE FUNCTION whathood.get_dominant_neighborhood(_test_point geometry)
RETURNS integer
AS
$BODY$
DECLARE
  _total integer;
  _max_user_polygons integer;
  _neighborhood_id integer;
BEGIN
  SELECT MAX(total_user_polygons) INTO _max_user_polygons
  FROM whathood.neighborhood_counts_by_point(_test_point);

  SELECT COUNT(*) INTO _total
  FROM whathood.neighborhood_counts_by_point(_test_point)
  WHERE total_user_polygons = _max_user_polygons;

  IF _total = 0 THEN
    RETURN 0;
  ELSIF _total = 1 THEN
    SELECT a.neighborhood_id INTO _neighborhood_id FROM whathood.neighborhood_counts_by_point(_test_point) a;
    RETURN _neighborhood_id;
  ELSE
    RETURN -1;
  END IF;
END;
$BODY$
LANGUAGE plpgsql;
