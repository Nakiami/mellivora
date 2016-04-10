INSERT INTO
  users (
    id,
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
    1,
    'ci-admin@mellivora.co',
    'ci-admin',
    UNIX_TIMESTAMP(),
    '$2y$10$dvjRH6GA4B4owKhXMWobKOfbqT48HH0SKSwsL0c9ckUXTSSfaq1l2',
    'a4c62dcecca552be3890df0c56603a810a9c8a0081f0e9993ae9c53e344e81d4',
    100, -- class
    1, -- enabled
    0, -- competing
    1 -- country_id
);

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
    'competitor@mellivora.co',
    'competitor',
    UNIX_TIMESTAMP(),
    '$2y$10$dvjRH6GA4B4owKhXMWobKOfbqT48HH0SKSwsL0c9ckUXTSSfaq1l2',
    'b5d62dcecca552be3890df0c56603a810a9c8a0081f0e9993ae9c53e344e82e5',
    0, -- class
    1, -- enabled
    1, -- competing
    1 -- country_id
);