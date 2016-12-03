CREATE TABLE IF NOT EXISTS "#__ak_profiles" (
	"id" serial NOT NULL,
	"description" character varying(255) NOT NULL,
	"configuration" text,
	"filters" text,
	PRIMARY KEY ("id")
);

CREATE RULE "#__ak_profiles_on_duplicate_ignore" AS ON INSERT TO "#__ak_profiles"
	WHERE EXISTS(SELECT 1 FROM "#__ak_profiles"
		WHERE ("id")=(NEW."id"))
	DO INSTEAD NOTHING;

INSERT INTO "#__ak_profiles"
("id","description", "configuration", "filters") VALUES
(1,'Default Backup Profile','','');

DROP RULE "#__ak_profiles_on_duplicate_ignore" ON "#__ak_profiles";

CREATE TABLE IF NOT EXISTS "#__ak_stats" (
	"id" serial NOT NULL,
	"description" character varying(255) NOT NULL,
	"comment" text,
	"backupstart" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
	"backupend" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
	"status" character varying(10) NOT NULL DEFAULT 'run',
	"origin" character varying(30) NOT NULL DEFAULT 'backend',
	"type" character varying(30) NOT NULL DEFAULT 'full',
	"profile_id" bigint NOT NULL DEFAULT '1',
	"archivename" text,
	"absolute_path" text,
	"multipart" int NOT NULL DEFAULT '0',
	"tag" character varying(255) DEFAULT NULL,
	"filesexist" smallint NOT NULL DEFAULT '1',
	"remote_filename" character varying(1000) DEFAULT NULL,
	"total_size" bigint NOT NULL DEFAULT '0',
	PRIMARY KEY ("id")
);

CREATE INDEX "#__ak_stats_idx_fullstatus" ON "#__ak_stats" ("filesexist", "status");
CREATE INDEX "#__ak_stats_idx_stale" ON "#__ak_stats" ("status", "origin");

CREATE TABLE IF NOT EXISTS "#__ak_storage" (
	"tag" character varying(255) NOT NULL DEFAULT 'backend',
	"lastupdate" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
	"data" text,
	PRIMARY KEY ("tag")
);