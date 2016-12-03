SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ak_profiles]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__ak_profiles] (
	[id] [INT] IDENTITY(1,1) NOT NULL,
	[description] [NVARCHAR](255) NOT NULL,
	[configuration] TEXT NULL,
	[filters] TEXT NULL,
	CONSTRAINT [PK_#__ak_profiles] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

SET IDENTITY_INSERT #__ak_profiles ON;
IF NOT EXISTS (SELECT * FROM #__ak_profiles WHERE id = 1)
BEGIN
INSERT INTO #__ak_profiles (id, description, configuration, filters)
SELECT 1, 'Default Backup profile', '', ''
END;
SET IDENTITY_INSERT #__ak_profiles  OFF;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ak_stats]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__ak_stats] (
	[id] [BIGINT] IDENTITY(1,1) NOT NULL,
	[description] [NVARCHAR](255) NOT NULL,
	[comment] [NVARCHAR](4000) NULL,
	[backupstart] [DATETIME] NOT NULL DEFAULT ('1900-01-01 00:00:00'),
	[backupend] [DATETIME] NOT NULL DEFAULT ('1900-01-01 00:00:00'),
	[status] [NVARCHAR](8) NOT NULL DEFAULT ('run'),
	[origin] [NVARCHAR](30) NOT NULL DEFAULT ('backend'),
	[type] [NVARCHAR](30) NOT NULL DEFAULT ('full'),
	[profile_id] [BIGINT] NOT NULL DEFAULT ('1'),
	[archivename] [NVARCHAR](4000),
	[absolute_path] [NVARCHAR](4000),
	[multipart] [INT] NOT NULL DEFAULT ('0'),
	[tag] [NVARCHAR](255) NULL,
	[filesexist] [TINYINT] NOT NULL DEFAULT ('1'),
	[remote_filename] [NVARCHAR](1000) NULL,
	[total_size] [BIGINT] NOT NULL DEFAULT ('0'),
	CONSTRAINT [PK_#__ak_stats] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__ak_stats]') AND name = N'idx_fullstatus')
BEGIN
CREATE NONCLUSTERED INDEX [idx_fullstatus] ON [#__ak_stats]
(
	[filesexist] ASC,
	[status] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__ak_stats]') AND name = N'idx_stale')
BEGIN
CREATE NONCLUSTERED INDEX [idx_stale] ON [#__ak_stats]
(
	[status] ASC,
	[origin] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ak_storage]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__ak_storage] (
	[tag] [NVARCHAR](255) NOT NULL,
	[lastupdate] [DATETIME] NOT NULL DEFAULT ('1900-01-01 00:00:00'),
	[data] [TEXT]
)
END;