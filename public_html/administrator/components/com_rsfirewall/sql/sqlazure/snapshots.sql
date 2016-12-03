CREATE TABLE [#__rsfirewall_snapshots] (
  [id] int NOT NULL IDENTITY,
  [user_id] int NOT NULL,
  [snapshot] text NOT NULL,
  [type] varchar(16) NOT NULL,
  PRIMARY KEY ([id])
);