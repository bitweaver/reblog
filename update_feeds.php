<?php
/**
 * Update Feeds
 *
 * usage is simple:
 *		php -q update_feeds.php
 * example:
 *		php -q update_feeds.php
 * suggested crontab entry runs the feed updater every minute:
 *		* * * * * apache php -q /path/to/bitweaver/reblog/update_feeds.php >> /var/log/httpd/update_feeds_log
 *
 * @version $Header: /cvsroot/bitweaver/_bit_reblog/update_feeds.php,v 1.12 2007/11/21 20:10:19 wjames5 Exp $
 * @package reblog
 * @subpackage functions
 */

	global $gBitSystem, $_SERVER;

	if( !empty( $argc ) ) {
		$_SERVER['SCRIPT_URL'] = '';
		$_SERVER['HTTP_HOST'] = '';
		$_SERVER['HTTP_HOST'] = '';
		$_SERVER['HTTP_HOST'] = '';
		$_SERVER['SERVER_NAME'] = '';
		define( 'BIT_ROOT_URI', '' );
	}

/**
 * required setup
 */
	if( !empty( $argc ) ) {
		// reduce feedback for command line to keep log noise way down
		define( 'BIT_PHP_ERROR_REPORTING', E_ERROR | E_PARSE );
	}

	// running from cron can cause us not to be in the right dir.
	chdir( dirname( __FILE__ ) );
	require_once( '../bit_setup_inc.php' );
	require_once( REBLOG_PKG_PATH.'BitReBlog.php' );

	// add some protection for arbitrary thumbail execution.
	// if argc is present, we will trust it was exec'ed command line.
	if( empty( $argc ) && !$gBitUser->isAdmin() ) {
		$gBitSystem->fatalError( tra( 'You do not have permission to access this page.' ));
	}
	
	$reblog = new BitReBlog();
	
	$_REQUEST['auto_only'] = TRUE;
	$feedsList = $reblog->getList( $_REQUEST );
	
	$log = array();
	$total = date( 'U' );
	$currTime = $gBitSystem->getUTCTime();
	foreach( array_keys( $feedsList ) as $key ) {
		$feedHash = $feedsList[$key];
		if ( (( $feedHash['last_updated'] + $feedHash['refresh'] ) <= $currTime) ){
			$feed = new BitReBlog( $feedHash['feed_id'] );
			$feed->load();
			$begin = date( 'U' );
			if ( !$feed->updateFeed() ){
				$error = TRUE;
				$log[$key]['message'] = ' ERROR: '.implode( ',', $feed->mErrors['reblog'] );
			}
			$log[$key]['time'] = date( 'd/M/Y:H:i:s O' );
			$log[$key]['duration'] = date( 'U' ) - $begin;
			$log[$key]['delay'] = date( 'U' ) - $total;
		}
	}

	foreach( array_keys( $log ) as $feedHash ) {
		// generate something that kinda looks like apache common log format
		$logLine = $feedHash.' - - ['.$log[$feedHash]['time'].'] "'.$log[$feedHash]['message'].'" '.$log[$feedHash]['duration']."seconds <br/>\n";
		if( strpos( $log[$feedHash['message']], 'ERROR' ) !== FALSE ) {
			bit_log_error( $logLine );
		}
		print $logLine;
	}

	if( count($feedsList) ) {
		print '# '.count($feedsList)." rss feeds processed in ".(date( 'U' ) - $total)." seconds<br/>\n";
	}

?>
