CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE INDEX IDX_9FA93F18803BB24B ON user_polygon (neighborhood_id);
CREATE INDEX IDX_9FA93F1898260155 ON user_polygon (region_id);
CREATE INDEX IDX_9FA93F185219EBCC ON user_polygon (whathood_user_id);
COMMENT ON COLUMN user_polygon.polygon IS '(DC2Type:polygon)';
CREATE INDEX IDX_FEF1E9EE98260155 ON neighborhood (region_id);
CREATE UNIQUE INDEX name_region_idx ON neighborhood (name, region_id);
CREATE UNIQUE INDEX region_name_idx ON region (name);
COMMENT ON COLUMN region.center_point IS '(DC2Type:point)';
CREATE UNIQUE INDEX whathood_user_idx ON whathood_user (user_name);
CREATE TABLE neighborhood_heat_map_point (id INT NOT NULL, neighborhood_id INT DEFAULT NULL, point geometry(Point) NOT NULL, strength_of_identity DOUBLE PRECISION NOT NULL, set_num INT NOT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_EF552795803BB24B ON neighborhood_heat_map_point (neighborhood_id);
COMMENT ON COLUMN neighborhood_heat_map_point.point IS '(DC2Type:point)';
CREATE INDEX IDX_9A443078803BB24B ON neighborhood_polygon (neighborhood_id);
CREATE UNIQUE INDEX neighborhood_polygon_set_number_idx ON neighborhood_polygon (neighborhood_id, set_number);
COMMENT ON COLUMN neighborhood_polygon.polygon IS '(DC2Type:polygon)';
CREATE TABLE users (id INT NOT NULL, username VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, displayName VARCHAR(50) DEFAULT NULL, password VARCHAR(128) NOT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username);
CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email);
CREATE TABLE users_roles (user_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(user_id, role_id));
CREATE INDEX IDX_51498A8EA76ED395 ON users_roles (user_id);
CREATE INDEX IDX_51498A8ED60322AC ON users_roles (role_id);
CREATE TABLE role (id INT NOT NULL, parent_id INT DEFAULT NULL, roleId VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX UNIQ_57698A6AB8C2FD88 ON role (roleId);
CREATE INDEX IDX_57698A6A727ACA70 ON role (parent_id);
ALTER TABLE user_polygon ADD CONSTRAINT FK_9FA93F18803BB24B FOREIGN KEY (neighborhood_id) REFERENCES neighborhood (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user_polygon ADD CONSTRAINT FK_9FA93F1898260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE user_polygon ADD CONSTRAINT FK_9FA93F185219EBCC FOREIGN KEY (whathood_user_id) REFERENCES whathood_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE neighborhood ADD CONSTRAINT FK_FEF1E9EE98260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE neighborhood_heat_map_point ADD CONSTRAINT FK_EF552795803BB24B FOREIGN KEY (neighborhood_id) REFERENCES neighborhood (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE neighborhood_polygon ADD CONSTRAINT FK_9A443078803BB24B FOREIGN KEY (neighborhood_id) REFERENCES neighborhood (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8ED60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE role ADD CONSTRAINT FK_57698A6A727ACA70 FOREIGN KEY (parent_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
