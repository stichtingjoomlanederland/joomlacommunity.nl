CREATE TABLE [#__rsfirewall_offenders] (
  [id] int NOT NULL IDENTITY,
  [ip] varchar(255) NOT NULL,
  [date] datetime NOT NULL,
  PRIMARY KEY ([id])
);