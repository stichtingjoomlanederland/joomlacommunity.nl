CREATE TABLE [#__rsfirewall_lists] (
  [id] int NOT NULL IDENTITY,
  [ip] varchar(255) NOT NULL,
  [type] tinyint NOT NULL,
  [reason] varchar(255) NOT NULL,
  [date] datetime NOT NULL,
  [published] tinyint NOT NULL,
  PRIMARY KEY ([id]),
  KEY [ip] ([ip])
);