\connect whathood;

-- why revoke? https://dba.stackexchange.com/questions/33943/granting-access-to-all-tables-for-a-user

REVOKE CONNECT ON DATABASE whathood FROM PUBLIC;
GRANT CONNECT
ON DATABASE whathood
TO whathood;

REVOKE ALL
ON ALL TABLES IN SCHEMA public 
FROM PUBLIC;

GRANT SELECT, INSERT, UPDATE, DELETE
ON ALL TABLES IN SCHEMA public
TO whathood;

-- REVOKE ALL
-- ON ALL TABLES IN SCHEMA whathood
-- FROM PUBLIC;

-- GRANT SELECT, INSERT, UPDATE, DELETE
-- ON ALL TABLES IN SCHEMA whathood
-- TO whathood;