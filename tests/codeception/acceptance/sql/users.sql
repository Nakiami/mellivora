INSERT INTO
  users (
    email,
    team_name,
    added,
    passhash,
    download_key,
    class,
    enabled,
    competing,
    country_id
  ) VALUES (
    'ci-admin@mellivora.co',
    'ci-admin',
    UNIX_TIMESTAMP(),
    '$2y$10$dvjRH6GA4B4owKhXMWobKOfbqT48HH0SKSwsL0c9ckUXTSSfaq1l2',
    'a4c62dcecca552be3890df0c56603a810a9c8a0081f0e9993ae9c53e344e81d4',
    100,
    1,
    0,
    1
);