alter table jos_discuss_acl add column ( `public` tinyint(1) default '0');

update jos_discuss_acl set public = '1' where `id` in ( '1', '2', '3', '4', '12' );