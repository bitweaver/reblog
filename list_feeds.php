<?php
/**
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 * @package reblog
 * @subpackage functions
 */ 

/**
 * Initialization
 */ 
require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPackage( 'reblog' );

require_once( REBLOG_PKG_PATH.'lookup_feed_inc.php');
require_once( REBLOG_PKG_PATH.'BitReBlog.php');

$gBitSystem->setBrowserTitle(tra('View All Reblog Feeds'));

if( $gFeed->isValid() && isset($_REQUEST["remove"])) {
	$gBitSystem->setBrowserTitle(tra('Delete Feed'));

	// Check if has admin perm
	$gBitSystem->verifyPermission( 'p_reblog_admin' );
	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['remove'] = $_REQUEST["remove"];
		$formHash['feed_id'] = $gFeed->mFeedId;
		$gBitSystem->confirmDialog( 
			$formHash, array( 
				'warning' => tra('Are you sure you want to delete this feed?').' '.$gFeed->getTitle().' '.$gFeed->getField('url'),
				'error' => tra('This cannot be undone!')
			)
		);
	} else {
		$gFeed->expunge();
	}
}

$feedsList = $gFeed->getList( $_REQUEST );
//$gBitSmarty->assign( 'listInfo', $_REQUEST['listInfo'] );
$gBitSmarty->assign_by_ref( 'feedsList', $feedsList );

$gBitSystem->setBrowserTitle(tra('View All Reblog Feeds'));
// Display the template
$gBitSystem->display( 'bitpackage:reblog/list_feeds.tpl', NULL, array( 'display_mode' => 'list' ));
?>
