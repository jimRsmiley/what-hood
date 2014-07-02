DROP TABLE IF EXISTS public.my_temp_table;

CREATE TABLE public.my_temp_table (
  id serial PRIMARY KEY,
  test_point_id integer,
  point geometry,
  neighborhood_id integer,
  num_polygons_for_neighborhood integer, 
  num_polygons_for_point integer
);

CREATE INDEX test_point_id_idx ON my_temp_table(test_point_id);
CREATE INDEX neighborhood_id_idx ON my_temp_table(neighborhood_id);
CREATE UNIQUE INDEX neighborhood_name_test_point_id_idx ON my_temp_table(test_point_id,neighborhood_id);