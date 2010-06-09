<?php
/**
 * @version $Header$

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

/* we display a list of recent blog posts since that is what reblog feed all rss content too
 * the list is however limited to items from the feeds and does not include all blog posts
 * the list is limited through the reblog_content_list_sql service function
 */
// Is package installed and enabled
$gBitSystem->verifyPackage( 'reblog' );
$gBitSystem->verifyPackage( 'blogs' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_blogs_view' );

require_once( BLOGS_PKG_PATH.'lookup_blog_inc.php');

$gBitSmarty->assign( 'showEmpty', TRUE );

$gContent->invokeServices( 'content_list_function', $_REQUEST );
$gDefaultCenter = 'bitpackage:reblog/center_list_reblog_posts.tpl';
$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', 'List ReBlogged Posts' , array( 'display_mode' => 'display' ));
?>
