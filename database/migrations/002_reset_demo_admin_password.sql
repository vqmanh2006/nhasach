USE `nhasach`;

UPDATE users
SET password = '$2y$12$1/H.TCiqeLp4Nflx0AcrCOi6lJ8mKaGK1RQY0cqus20PPPtQ8O4OC'
WHERE email = 'admin@nhasach.local';
