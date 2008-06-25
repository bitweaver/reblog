<?php
/** $Header: /cvsroot/bitweaver/_bit_reblog/edit_feed.php,v 1.5 2008/06/25 22:21:21 spiderr Exp $
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * @package reblog
 * @subpackage functions
 **/

/**
 * Initialization
 **/ 
require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPackage( 'reblog' );

require_once( REBLOG_PKG_PATH.'lookup_feed_inc.php' );
require_once( REBLOG_PKG_PATH.'BitReBlog.php');

$gBitSystem->verifyPermission( 'p_reblog_admin' );

if( !empty( $_REQUEST['action'] ) ) {
	if( $_REQUEST['action'] == 'remove' && $gFeed->isValid() ) {
		if( isset( $_REQUEST["confirm"] ) ) {
			$redirect = REBLOG_PKG_URL;
			if( $gFeed->expunge() ) {
				bit_redirect( $redirect );
			} else {
				$feedback['error'] = $gFeed->mErrors;
			}
		}
		$gBitSystem->setBrowserTitle( 'Confirm removal of '.$gFeed->getTitle().' ReBlog Feed' );		
		$formHash['remove'] = TRUE;
		$formHash['action'] = 'remove';
		$formHash['feed_id'] = $_REQUEST['feed_id'];
		$msgHash = array(
			'label' => 'Remove RSS Feed',
			'confirm_item' => $gFeed->mInfo['name'],
			'warning' => 'This will remove the above reblog rss feed. This cannot be undone.',
		);
		$gBitSystem->confirmDialog( $formHash, $msgHash );
	}
} elseif (isset($_REQUEST['save_feed']) ) {
	if ( $gFeed->store( $_REQUEST ) ){
		bit_redirect( REBLOG_PKG_URL.'list_feeds.php' );
	} else {
		$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
	}
}

$gBitSmarty->assign_by_ref('feedInfo', $gFeed->mInfo);

$gBitSmarty->assign_by_ref( 'errors', $gFeed->mErrors );

$gBitSystem->display( 'bitpackage:reblog/edit_feed.tpl', $gFeed->isValid() ? tra( "Edit ReBlog Feed" ).": ".$gFeed->mInfo['name'] : tra( "Create ReBlog Feed" ) , array( 'display_mode' => 'edit' ));
?>

