DROP TABLE trans_np_up cascade;
DROP TABLE test_point CASCADE;
DROP TABLE phila_neighborhoods CASCADE;
DROP TABLE contentious_point CASCADE;
DROP TABLE my_temp_table CASCADE;
DROP TABLE phila_city_limits CASCADE;
DROP TABLE neighborhood_polygons_create_event CASCADE;
-- this is a long table drop
--DROP TABLE neighborhood_point_strength_of_identity;

-- neighborhood
ALTER TABLE neighborhood ADD CONSTRAINT FK_FEF1E9EE98260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE neighborhood ADD COLUMN no_build_border boolean DEFAULT false;
CREATE INDEX IDX_FEF1E9EE98260155 ON neighborhood (region_id);
CREATE UNIQUE INDEX name_region_idx ON neighborhood (name, region_id);

-- region
ALTER TABLE region DROP COLUMN center_point;
ALTER TABLE region DROP COLUMN border;
CREATE UNIQUE INDEX region_name_idx ON region (name);

-- whathood_user
ALTER table whathood_user DROP column user_name;
ALTER TABLE whathood_user DROP COLUMN facebook_user_id;
ALTER TABLE whathood_user ADD COLUMN ip_address CHARACTER VARYING(255);
ALTER TABLE whathood_user ALTER COLUMN ip_address DROP DEFAULT;
ALTER TABLE whathood_user ALTER COLUMN ip_address SET NOT NULL;
CREATE UNIQUE INDEX UNIQ_9E2AFB1622FFD58C ON whathood_user (ip_address);
CREATE UNIQUE INDEX whathood_user_idx ON whathood_user (user_name);

-- neighborhood polygon
TRUNCATE TABLE neighborhood_polygon;
ALTER TABLE neighborhood_polygon DROP COLUMN num_user_polygons;
ALTER TABLE neighborhood_polygon DROP COLUMN create_event_id;
ALTER TABLE neighborhood_polygon RENAME COLUMN polygon TO geom;
ALTER TABLE neighborhood_polygon RENAME COLUMN date_time_created TO created_at;
ALTER TABLE neighborhood_polygon ALTER COLUMN created_at SET NOT NULL;
ALTER TABLE neighborhood_polygon ALTER COLUMN created_at SET DEFAULT now();
CREATE INDEX IDX_9A443078803BB24B ON neighborhood_polygon (neighborhood_id);
ALTER TABLE neighborhood_polygon ADD CONSTRAINT FK_9A443078803BB24B FOREIGN KEY (neighborhood_id) REFERENCES neighborhood (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
COMMENT ON COLUMN neighborhood_polygon.geom IS '(DC2Type:geometry)';

-- user_polygon
CREATE INDEX IDX_9FA93F18803BB24B ON user_polygon (neighborhood_id);
CREATE INDEX IDX_9FA93F1898260155 ON user_polygon (region_id);
CREATE INDEX IDX_9FA93F185219EBCC ON user_polygon (whathood_user_id);
COMMENT ON COLUMN user_polygon.polygon IS '(DC2Type:polygon)';
ALTER TABLE user_polygon ADD COLUMN is_deleted BOOLEAN DEFAULT NULL;
ALTER TABLE user_polygon ADD CONSTRAINT FK_9FA93F18803BB24B FOREIGN KEY (neighborhood_id) REFERENCES neighborhood (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user_polygon ADD CONSTRAINT FK_9FA93F1898260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user_polygon ADD CONSTRAINT FK_9FA93F185219EBCC FOREIGN KEY (whathood_user_id) REFERENCES whathood_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

-- ZfcUser stuff
CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE users (id INT NOT NULL, username VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, displayName VARCHAR(50) DEFAULT NULL, password VARCHAR(128) NOT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username);
CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email);
CREATE TABLE users_roles (user_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(user_id, role_id));
CREATE INDEX IDX_51498A8EA76ED395 ON users_roles (user_id);
CREATE INDEX IDX_51498A8ED60322AC ON users_roles (role_id);
CREATE TABLE role (id INT NOT NULL, parent_id INT DEFAULT NULL, roleId VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_57698A6AB8C2FD88 ON role (roleId);
CREATE INDEX IDX_57698A6A727ACA70 ON role (parent_id);
ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8ED60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE role ADD CONSTRAINT FK_57698A6A727ACA70 FOREIGN KEY (parent_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

-- up_np
CREATE TABLE up_np (np_id INT NOT NULL, up_id INT NOT NULL, PRIMARY KEY(np_id, up_id));
CREATE INDEX IDX_50B466A6121F828F ON up_np (np_id);
CREATE INDEX IDX_50B466A652F241C ON up_np (up_id);
CREATE UNIQUE INDEX IDX_up_np_pair ON up_np(np_id,up_id);

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

-- insert ZfcUser into
INSERT INTO role
    (id, parent_id, roleId)
VALUES
    (1, NULL, 'guest'),
    (2, 1, 'user'),
    (3, 2, 'moderator'),
    (4, 3, 'administrator');
