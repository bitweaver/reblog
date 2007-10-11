<?php
/**
 * Update Feeds
 *
 * usage is simple:
 *		php -q update_feeds.php
 * example:
 *		php -q update_feeds.php
 * suggested crontab entry runs the thumbnailer every minute:
 *		* * * * * apache php -q /path/to/bitweaver/reblog/update_feeds.php >> /var/log/httpd/update_feeds_log
 *
 * @version $Header: /cvsroot/bitweaver/_bit_reblog/update_feeds.php,v 1.2 2007/10/11 17:40:13 spiderr Exp $
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
	$feedsList = $reblog->getList();

	$log = array();
	$total = date( 'U' );
	$currTime = $gBitSystem->getUTCTime();
	foreach( array_keys( $feedsList ) as $feedHash ) {
		if ( !(( $feedHash['last_updated'] + $feedHash['refresh'] ) < $currTime) ){
			$feed = new BitReBlog( $feedHash['feed_id'] );
			$begin = date( 'U' );
			if ( !$feed->updateFeed() ){
				$log[$feedHash]['message'] = ' ERROR: '.$feed->mErrors['items'];
			}
			$log[$feedHash]['time'] = date( 'd/M/Y:H:i:s O' );
			$log[$feedHash]['duration'] = date( 'U' ) - $begin;
			$log[$feedHash]['delay'] = date( 'U' ) - $total;
		}
	}

	foreach( array_keys( $log ) as $feedHash ) {
		// generate something that kinda looks like apache common log format
		print $feedHash.' - - ['.$log[$feedHash]['time'].'] "'.$log[$feedHash]['message'].'" '.$log[$feedHash]['duration']."seconds <br/>\n";
	}

	if( count($feedsList) ) {
		print '# '.count($feedsList)." rss feeds processed in ".(date( 'U' ) - $total)." seconds<br/>\n";
	}

?>
