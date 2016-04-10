INSERT INTO challenges (
  added,
  added_by,
  title,
  category,
  description,
  exposed,
  available_from,
  available_until,
  flag,
  case_insensitive,
  automark,
  points,
  num_attempts_allowed,
  min_seconds_between_submissions
) VALUES (
  UNIX_TIMESTAMP(),
  1, -- added_by
  'Default CI Challenge',
  1, -- category
  'This is the default CI Challenge',
  1, -- exposed
  1451635200,
  4070937600,
  'abc123',
  0, -- case_insensitive
  1, -- automark
  666, -- points
  10, -- num_attempts_allowed
  0 -- min_seconds_between_submissions
);

INSERT INTO challenges (
  added,
  added_by,
  title,
  category,
  description,
  exposed,
  available_from,
  available_until,
  flag,
  case_insensitive,
  automark,
  points,
  num_attempts_allowed,
  min_seconds_between_submissions
) VALUES (
  UNIX_TIMESTAMP(),
  1, -- added_by
  'Editable CI Challenge',
  2, -- category
  'This is the editable CI Challenge',
  1, -- exposed
  1451635200,
  4070937600,
  'abc123',
  0, -- case_insensitive
  1, -- automark
  666, -- points
  10, -- num_attempts_allowed
  0 -- min_seconds_between_submissions
);