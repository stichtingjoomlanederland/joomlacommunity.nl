SET QUOTED_IDENTIFIER ON;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ak_profile]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__ak_profiles]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ak_stats]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__ak_stats]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__ak_storage]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__ak_storage]
END;
