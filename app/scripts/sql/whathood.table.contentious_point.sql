--DROP TABLE IF EXISTS contentious_point CASCADE;

CREATE TABLE IF NOT EXISTS contentious_point (
  id serial primary key,
  create_event_id integer REFERENCES neighborhood_polygons_create_event(id),
  test_point_id integer,
  point geometry,
  strength_of_identity double precision
 );
CREATE UNIQUE INDEX cp_ce_tp ON contentious_point(create_event_id,test_point_id);
