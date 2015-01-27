-- when creating ZfcUser, there was a problem referring to public.user table, so have to specify it:
CREATE SEQUENCE public.user_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE public."user" (user_id INT NOT NULL, username VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, display_name VARCHAR(50) DEFAULT NULL, password VARCHAR(128) NOT NULL, state SMALLINT DEFAULT NULL, PRIMARY KEY(user_id
));
CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON public."user" (username);
CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON public."user" (email);
