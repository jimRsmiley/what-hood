DROP INDEX IF EXISTS neighborhood_polygon_polygon_idx;
CREATE INDEX neighborhood_polygon_polygon_idx ON neighborhood_polygon USING GIST(polygon);

DROP INDEX IF EXISTS heat_map_test_point_idx;
CREATE INDEX heat_map_test_point_idx ON heat_map_test_point USING GIST(point);

DROP INDEX IF EXISTS region_polygon_idx;
CREATE INDEX region_polygon_idx ON region USING GIST(polygon);

DROP INDEX IF EXISTS user_polygon_neighborhood_id_idx;
CREATE INDEX user_polygon_neighborhood_id_idx ON user_polygon(neighborhood_id);

-- user polygon
CREATE INDEX user_polygon_polygon_idx ON user_polygon USING GIST(polygon);

CREATE INDEX test_point_id_idx ON my_temp_table(test_point_id);
CREATE INDEX neighborhood_name_idx ON my_temp_table(neighborhood_id);
CREATE UNIQUE INDEX neighborhood_name_test_point_id_idx ON my_temp_table(test_point_id,neighborhood_id);
CREATE UNIQUE INDEX test_point_set_num_test_point_idx ON test_point(set_num,point);
VACUUM ANALYZE;

