CREATE TABLE whathood_user (id INT NOT NULL, facebook_user_id BIGINT DEFAULT NULL, user_name VARCHAR(255) NOT NULL, PRIMARY KEY(id));
CREATE UNIQUE INDEX whathood_user_idx ON whathood_user (user_name);

