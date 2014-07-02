DROP TABLE IF EXISTS neighborhood_polygon;

CREATE TABLE neighborhood_polygon(
  id serial primary key,
  neighborhood_id integer,
  polygon geometry,
  set_number integer,
  timestamp timestamp with time zone
);