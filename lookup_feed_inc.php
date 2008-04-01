<?php
/**
 * @package reblog
 * @subpackage functions
 */

/**
 * required setup
 */
global $gFeed;
require_once( REBLOG_PKG_PATH.'BitReBlog.php' );

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
if( empty( $gFeed ) || !is_object( $gFeed ) || !$gFeed->isValid() ) {
	if( @BitBase::verifyId( $_REQUEST['feed_id'] ) ) {
		$gFeed = new BitReBlog( $_REQUEST['feed_id'] );
		$gFeed->load();
	} else {
		$gFeed = new BitReBlog();
	}

	$gBitSmarty->assign_by_ref( 'gFeed', $gFeed );
} 
?>
