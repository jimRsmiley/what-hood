DROP TABLE IF EXISTS neighborhood_polygons_create_event CASCADE;

CREATE TABLE neighborhood_polygons_create_event (
  id serial primary key,
  description text NOT NULL,
  test_point_meter_width integer NOT NULL,
  concave_hull_target_precision double precision NOT NULL,
  date_time_created timestamp(0) with time zone NOT NULL
);

DROP TABLE IF EXISTS neighborhood_point_strength_of_identity CASCADE;

CREATE TABLE neighborhood_point_strength_of_identity (
  id serial primary key,
  test_point_id integer REFERENCES test_point(id) NOT NULL,
  neighborhood_id integer REFERENCES neighborhood(id) NOT NULL,
  create_event_id integer REFERENCES neighborhood_polygons_create_event(id) NOT NULL,
  num_polygons_for_name integer,
  num_polygons_for_point integer,
  strength_of_identity double precision
);
-- only want a test point per neighborhood once per creation event
CREATE UNIQUE INDEX npsoi_tp_ce_idx ON neighborhood_point_strength_of_identity(test_point_id,neighborhood_id,create_event_id);
CREATE INDEX npsoi_ce_id_idx ON neighborhood_point_strength_of_identity(create_event_id);
CREATE INDEX npsoi_tp_id_idx ON neighborhood_point_strength_of_identity(test_point_id);
CREATE INDEX npsoi_n_id_idx ON neighborhood_point_strength_of_identity(neighborhood_id);
CREATE INDEX npsoi_soi_idx ON neighborhood_point_strength_of_identity(strength_of_identity);

DROP TABLE IF EXISTS neighborhood_polygon CASCADE;

CREATE TABLE neighborhood_polygon (
  id serial primary key,
  neighborhood_id integer REFERENCES neighborhood(id) NOT NULL,
  create_event_id integer REFERENCES neighborhood_polygons_create_event(id) NOT NULL,
  polygon geometry NOT NULL,
  num_user_polygons bigint,
  date_time_created timestamp(0) with time zone NOT NULL
);
-- only want one neighborhood_polygon per create event
CREATE UNIQUE INDEX neighborhood_polygon_ce_n_idx ON neighborhood_polygon(neighborhood_id,create_event_id);

CREATE INDEX np_neighborhood_id_idx ON neighborhood_polygon(neighborhood_id);

DROP TABLE IF EXISTS trans_ce_tp_up;

CREATE TABLE trans_ce_tp_up (
  id serial primary key,
  create_event_id integer REFERENCES neighborhood_polygons_create_event(id) NOT NULL,
  test_point_id integer REFERENCES test_point(id) NOT NULL,
  user_polygon_id integer REFERENCES user_polygon(id) NOT NULL
);
CREATE INDEX trans_tp_up_tpid_idx ON trans_ce_tp_up(test_point_id);
CREATE INDEX trans_tp_up_upid_idx ON trans_ce_tp_up(user_polygon_id);
CREATE INDEX trans_tp_up_ceid_idx ON trans_ce_tp_up(create_event_id);
CREATE UNIQUE INDEX trans_tp_up_ceid_tpid_upid ON trans_ce_tp_up(create_event_id,test_point_id,user_polygon_id);

DROP TABLE IF EXISTS trans_np_up;
CREATE TABLE trans_np_up (
  id serial primary key,
  create_event_id integer REFERENCES neighborhood_polygons_create_event(id) NOT NULL,
  np_id integer REFERENCES neighborhood_polygon(id) NOT NULL,
  up_id integer REFERENCES user_polygon(id) NOT NULL
);
-- but if we run the create function over and over again, it'll screw us up
CREATE UNIQUE INDEX trans_np_up_user_polygon_id_idx ON trans_np_up(create_event_id,np_id,up_id);
CREATE INDEX trans_np_up_neighborhood_polygon_id_idx ON trans_np_up(np_id);
CREATE INDEX trans_np_up_user_polygon_idx ON trans_np_up(up_id);

--
-- contentious_point
--
DROP TABLE IF EXISTS contentious_point;
CREATE TABLE IF NOT EXISTS contentious_point (
  id serial primary key,
  create_event_id integer REFERENCES neighborhood_polygons_create_event(id),
  test_point_id integer,
  point geometry,
  strength_of_identity double precision
 );
 CREATE UNIQUE INDEX cp_ce_tp ON contentious_point(create_event_id,test_point_id);