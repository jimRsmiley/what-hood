
--
-- find the points that are an area of contention
--
CREATE OR REPLACE FUNCTION whathood.create_contentious_points( _create_event_id integer )
RETURNS VOID
AS
$BODY$
BEGIN
  INSERT INTO contentious_point( create_event_id, test_point_id, point, strength_of_identity ) (
    SELECT DISTINCT
      _create_event_id,
      test_point.id,
      test_point.point,
      a.strength_of_identity
    FROM
      neighborhood_point_strength_of_identity a,
      neighborhood_point_strength_of_identity b,
      test_point

    WHERE
      test_point.id = a.test_point_id
      AND  a.id <> b.id
      AND a.test_point_id = b.test_point_id
      AND a.strength_of_identity = b.strength_of_identity
      AND a.create_event_id = _create_event_id
      AND a.create_event_id = b.create_event_id
    GROUP BY test_point.id,a.strength_of_identity,test_point.point
  );
END;
$BODY$
LANGUAGE plpgsql;

SELECT whathood.create_contentious_points(1);

SELECT * FROM contentious_point;
