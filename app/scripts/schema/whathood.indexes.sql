CREATE UNIQUE INDEX IDX_up_np_pair ON up_np(np_id,up_id);
CREATE INDEX IDX_neighborhood_polygon_polygon ON neighborhood_polygon USING GIST(polygon);
CREATE INDEX IDX_user_polygon_polygon ON user_polygon USING GIST(polygon);
