CREATE TABLE [#__rsfirewall_signatures] (
  [signature] varchar(255) NOT NULL,
  [type] varchar(16) NOT NULL,
  [reason] varchar(255) NOT NULL,
  PRIMARY KEY ([signature],[type])
);