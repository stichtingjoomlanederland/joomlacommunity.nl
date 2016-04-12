CREATE TABLE [#__rsfirewall_hashes] (
  [id] int NOT NULL IDENTITY,
  [file] text NOT NULL,
  [hash] varchar(32) NOT NULL,
  [type] varchar(64) NOT NULL,
  [flag] varchar(1) NOT NULL,
  [date] varchar(255) NOT NULL,
  PRIMARY KEY ([id])
);