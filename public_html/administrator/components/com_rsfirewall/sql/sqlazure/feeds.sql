CREATE TABLE [#__rsfirewall_feeds] (
  [id] int NOT NULL IDENTITY,
  [url] text NOT NULL,
  [limit] tinyint NOT NULL,
  [ordering] int NOT NULL,
  [published] tinyint NOT NULL,
  PRIMARY KEY ([id])
);