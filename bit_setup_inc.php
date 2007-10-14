<?php
global $gBitSystem, $gBitUser;

$registerHash = array(
        'package_name' => 'reblog',
        'package_path' => dirname( __FILE__ ).'/',
        'service' => LIBERTY_SERVICE_REBLOG,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'reblog' ) && $gBitSystem->isPackageActive( 'blogs' ) ) {
	require_once( REBLOG_PKG_PATH.'BitReBlog.php' );

	$menuHash = array(
		'package_name'  => REBLOG_PKG_NAME,
		'index_url'     => REBLOG_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:reblog/menu_reblog.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );

	$gLibertySystem->registerService( LIBERTY_SERVICE_REBLOG, REBLOG_PKG_NAME, array(
			'content_load_sql_function' => 'reblog_content_load_sql',
			'content_list_sql_function' => 'reblog_content_list_sql',
			'content_body_tpl'       => 'bitpackage:reblog/reblog_inline_service.tpl',		
	) );
}
?>
