INSERT INTO categories (
  id,
  added,
  added_by,
  title,
  description,
  exposed,
  available_from,
  available_until
) VALUES (
  1,
  UNIX_TIMESTAMP(),
  1,
  'Default CI Category',
  'This is the default CI category',
  1,
  1451635200,
  4070937600
);

INSERT INTO categories (
  id,
  added,
  added_by,
  title,
  description,
  exposed,
  available_from,
  available_until
) VALUES (
  2,
  UNIX_TIMESTAMP(),
  1,
  'Editable CI Category',
  'This is the editable CI category',
  1,
  1451635200,
  4070937600
);