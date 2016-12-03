CREATE TABLE [#__rsfirewall_logs] (
  [id] int NOT NULL IDENTITY,
  [level] varchar(16) NOT NULL,
  [date] datetime NOT NULL,
  [ip] varchar(255) NOT NULL,
  [user_id] int NOT NULL,
  [username] varchar(255) NOT NULL,
  [page] text NOT NULL,
  [referer] text NOT NULL,
  [code] varchar(255) NOT NULL,
  [debug_variables] text NOT NULL,
  PRIMARY KEY ([id]),
  KEY [ip] ([ip])
);