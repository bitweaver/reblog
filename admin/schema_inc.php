<?php
$tables = array(
'reblog_feeds' => "
	feed_id I4 PRIMARY,
	user_content_id I4 NOTNULL,
	name C(30) NOTNULL,
	description X,
	url C(255) NOTNULL,
	reblog  C(1),
	refresh I4,
	last_updated I8
",

"reblog_items_map" => "
	item_id C(255) PRIMARY,
	content_id I4 NOTNULL,
	feed_id I4 I4 NOTNULL
	CONSTRAINT '
		, CONSTRAINT `reblog_item_map` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)'
",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( REBLOG_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( REBLOG_PKG_NAME, array(
	'description' => "Aggregates RSS feeds from other sites and resaves those items as Blog Posts while maintaining links back to the source of the feed items.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

?>