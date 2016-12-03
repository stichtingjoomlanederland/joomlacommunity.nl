CREATE TABLE [#__rsfirewall_exceptions] (
  [id] int NOT NULL IDENTITY,
  [type] varchar(4) NOT NULL,
  [regex] tinyint NOT NULL,
  [match] text NOT NULL,
  [php] tinyint NOT NULL,
  [sql] tinyint NOT NULL,
  [js] tinyint NOT NULL,
  [uploads] tinyint NOT NULL,
  [reason] text NOT NULL,
  [date] datetime NOT NULL,
  [published] tinyint NOT NULL,
  PRIMARY KEY ([id])
);