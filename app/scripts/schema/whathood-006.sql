ALTER TABLE whathood_user ADD COLUMN ip_address text;
alter table whathood.whathood_user drop column user_name;
ALTER TABLE neighborhood_polygon DROP COLUMN create_event_id CASCADE;
