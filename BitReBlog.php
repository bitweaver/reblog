<?php
/**
 * @package reblog
 *
 * Copyright (c) 2007 bitweaver.org
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 */

/**
 * @package reblog
 */
class BitReBlog extends BitBase {
	var $mFeedId;

	function BitReBlog( $pFeedId = NULL ) {
		BitBase::BitBase();
		$this->mFeedId = (int)$pFeedId;
	}
	
	/**
	* Loads a RSS feed entry
	**/
	function load() {
		if( $this->verifyId( $this->mFeedId ) ) {
			global $gBitSystem, $gBitUser, $gLibertySystem;
			
			$selectSql = ''; $joinSql = ''; $whereSql = '';
			$bindVars = array();

			$whereSql .= "WHERE fd.`feed_id` = ?";
			$bindVars[] = $this->mFeedId;

			$query = "
					SELECT fd.*
					FROM `".BIT_DB_PREFIX."reblog_feeds` fd
					$whereSql";

			if ( $result = $this->mDb->getRow( $query, $bindVars ) ){
				$this->mInfo = $result;
			};
		}
		return( count( $this->mInfo ) );
	}


	function verify( &$pParamHash ) {
		global $gBitSystem;		
		$pParamHash['feed_store'] = array();
		
		if( !empty( $pParamHash['user_id'] ) ) {
			$pParamHash['feed_store']['user_content_id'] = (int)$pParamHash['user_id'];
		} else {
			$this->mErrors['user_id'] = "No user id specified.";
		}
		
		if( !empty( $pParamHash['name'] ) ) {
			$pParamHash['feed_store']['name'] = $pParamHash['name'];
		} else {
			$this->mErrors['name'] = "No name given.";
		}
		
		if( !empty( $pParamHash['description'] ) ) {
			$pParamHash['feed_store']['description'] = $pParamHash['description'];
		}
		
		if( !empty( $pParamHash['url'] ) ) {
			$pParamHash['feed_store']['url'] = $pParamHash['url'];
		} else {
			$this->mErrors['url'] = "No url specified.";
		}
		
		if( empty( $pParamHash['format_guid'] ) ) {
			$pParamHash['feed_store']['format_guid'] = $gBitSystem->getConfig( 'default_format', 'tikiwiki' );
		}else{
			$pParamHash['feed_store']['format_guid'] = $pParamHash['format_guid'];
		}
		
		if( !empty( $pParamHash['reblog'] ) ) {
			$pParamHash['feed_store']['reblog'] = $pParamHash['reblog'];
		}else{
			$pParamHash['feed_store']['reblog'] = 'n';
		}
		
		if( !empty( $pParamHash['fullpost'] ) ) {
			$pParamHash['feed_store']['fullpost'] = $pParamHash['fullpost'];
		}else{
			$pParamHash['feed_store']['fullpost'] = 'n';
		}
		
		if( !empty( $pParamHash['refresh'] ) ) {
			$pParamHash['feed_store']['refresh'] = (int)$pParamHash['refresh'] * 60;  //convert minutes to seconds
		}else{
			$pParamHash['feed_store']['refresh'] = 600;
		}
		
		$pParamHash['feed_store']['last_updated'] = $gBitSystem->getUTCTime();

		return( count( $this->mErrors ) == 0 );
	}	
	
	/**
	* @param array pParams hash of values that will be used to store the feed
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	* @access public
	**/
	function store( &$pParamHash ) {
		if( $this->verify( $pParamHash ) ) {
			$this->mDb->StartTrans();
			if (!empty($pParamHash['feed_store'])) {
				$table = BIT_DB_PREFIX."reblog_feeds";

				if( $this->isValid() ) {
					//update an existing feed
					$locId = array( "feed_id" => $this->mFeedId );
					$result = $this->mDb->associateUpdate( $table, $pParamHash['feed_store'], $locId );
				} else {
					if( @$this->verifyId( $pParamHash['feed_id'] ) ) {
						$pParamHash['feed_store']['feed_id'] = $pParamHash['feed_id'];
					} else {
						$pParamHash['feed_store']['feed_id'] = $this->mDb->GenID( 'reblog_feed_id_seq' );
					}
					$this->mFeedId = $pParamHash['feed_store']['feed_id'];
					//store an new feed
					$result = $this->mDb->associateInsert( $table, $pParamHash['feed_store'] );
				}
			}
			$this->mDb->CompleteTrans();
			$this->load();
		}
		return( count( $this->mErrors )== 0 );
	}

	function isValid() {
		return( $this->verifyId( $this->mFeedId ) && is_numeric( $this->mFeedId ) && $this->mFeedId > 0 );
	}
	
	/**
	* This function removes a feed entry
	**/
	function expunge( $feed_id ) {
		$ret = FALSE;
		$this->mDb->StartTrans();
		$query = "DELETE FROM `".BIT_DB_PREFIX."reblog_feeds` WHERE `feed_id` = ?";
		$result = $this->mDb->query( $query, array( $feed_id ) );
		$this->mDb->CompleteTrans();
		return $ret;
	}
	
	/**
	* This function gets a list of feeds
	**/
	function getList( &$pParamHash ) {
		global $gBitSystem;
		$ret = NULL;

		$selectSql = ''; $joinSql = ''; $whereSql = '';
		$bindVars = array();
		
		$sort_mode_prefix = 'fd';
		$sortHash = array(
			'feed_id_desc',
			'feed_id_asc',
			'name_asc',
			'name_desc',
		);
		
		if (!empty( $pParamHash['auto_only'] ) ){
			$whereSql .= "WHERE reblog = ?";
			$bindVars[]= 'y';
		}

		if( empty( $pParamHash['sort_mode'] ) || in_array( $pParamHash['sort_mode'], $sortHash ) ) {
			$pParamHash['sort_mode'] = 'name_asc';
		}
		
		$sort_mode = $sort_mode_prefix . '.' . $this->mDb->convertSortmode( $pParamHash['sort_mode'] );
		
		$query = "SELECT * FROM `".BIT_DB_PREFIX."reblog_feeds` fd $whereSql ORDER BY $sort_mode";
		$result = $this->mDb->query( $query, $bindVars );
		$ret = array();
		while ($res = $result->fetchrow()) {
			$ret[] = $res;
		};
		return $ret;
	}
	
	/**
	* Get list of items stored by feed id
	**/
	function getItems( &$pParamHash ){
		$ret = NULL;
		if( @$this->verifyId( $pParamHash['feed_id'] ) ) {
			$bindVars = array();
			array_push( $bindVars, (int)$pParamHash['feed_id'] );
			$whereSql = 'rim.`feed_id` = ?';

			$sortModePrefix = 'rim';
			$sort_mode = $sortModePrefix . '.' . $this->mDb->convertSortmode( 'content_id_desc' );

			$query = "SELECT * FROM `".BIT_DB_PREFIX."reblog_items_map` rim 
						WHERE $whereSql
						ORDER BY $sort_mode";
						
			$result = $this->mDb->query( $query, $bindVars, $pParamHash['max_records'] );
			
			$ret = array();
			while ($res = $result->fetchrow()) {
				$ret[] = $res;
			};
			return $ret;
		}
		return $ret;
	}
	
	/**
	* This function checks if any of a feed's items is new
	**/
	function updateFeed(){
		global $gBitSystem;

		// parse feed
		if ( $feedItems = $this->parseFeeds( $this->mInfo ) ){
			/* feeds are parsed newest first - 
			 * but for storing we want to store the 
			 * oldest first so that when we check them 
			 * later, the newer items have 
			 * larger content ids
			 */
			$feedItems = array_reverse($feedItems);
		
			$listHash = array();
			$listHash['feed_id'] = $this->mFeedId;
			$listHash['max_records'] = count( $feedItems );

			$storedItems = $this->getItems( $listHash );
			$storedItems = array_reverse($storedItems);

			/* check if ids are unique
			 * if they are not we need to get a full SHA-1 hash id 
			 * based on the entire xml - which itself is not very reliable
			 * 
			 * this is not a super clean way to go - should a feed  
			 * suddenly have duplicate ids because if one of the items 
			 * was previously stored in the db with a different id key
			 * then we'll get a duplicate record - but what can you do. 
			 * Bad feeds are bad.
			 */
			$ids = array();
			foreach( $feedItems as $item ){
				$ids[] = $item->get_id();
				$uids = array_unique($ids);
			}
			$use_hash = ( count( $ids ) == count( $uids) )?FALSE:TRUE;
			
			$n=0;
			foreach( $feedItems as $item ){
				$new = TRUE;
				$store = TRUE;
				// check ids in feed against items
				$itemId = $item->get_id( $use_hash );
				if ( $storedItems != null ){
					foreach( $storedItems as $stored ){
						if ( $itemId == $stored['item_id'] ){
							$new = FALSE;
							$store = FALSE;
							break;
						}
					}
				}
				/* if the reblog_items_map table gets mixed up at all
				 * then we can accidentally think its new. so we do one final check 
				 * before storing the item. its possible that maybe we should 
				 * just do this against all item feeds, but running the batch check first
				 * keeps the query count down.
				 */
				if ( $new ){
					$rslt = $this->mDb->getOne( "SELECT `item_id` FROM `".BIT_DB_PREFIX."reblog_items_map` WHERE `item_id`=?", array( $itemId ));
					if ( $rslt ){
						$store = FALSE;
					}
				}
				if ( $store ){
					$storeHash['item'] = $item;
					$storeHash['user_id'] = $this->mInfo['user_content_id'];
					$storeHash['format_guid'] = $this->mInfo['format_guid'];
					$storeHash['use_hash'] = $use_hash;
					$storeHash['publish_date'] = $gBitSystem->getUTCTime() + $n;
					if( $errors = $this->reblogItem( $storeHash ) ) {
						$this->mErrors['reblog'][] = $errors;
					}else{
						$n++;
					}
				}
			}
			// when where done posting all the items update the feed record with the time
			$this->mDb->StartTrans();
			$feedHash;
			$feedHash['last_updated'] = $gBitSystem->getUTCTime();
			$table = BIT_DB_PREFIX."reblog_feeds";
			$result = $this->mDb->associateUpdate( $table, $feedHash, array( "feed_id" => $this->mFeedId ) );
			$this->mDb->CompleteTrans();
		}
		return ( count( $this->mErrors ) == 0 );
	}

	/**
	* This function stores a RSS feed item as a blog post
	**/
	function reblogItem( &$pParamHash ){
		require_once( BLOGS_PKG_PATH.'BitBlogPost.php');
		$blogPost = new BitBlogPost();
		$postHash = array();
		$postHash['data'] = $pParamHash['item']->get_content();
		$postHash['title'] = $pParamHash['item']->get_title();
		$postHash['user_id'] = $pParamHash['user_id'];
		$postHash['format_guid'] = $pParamHash['format_guid'];
		$postHash['publish_date'] = $postHash['expire_date'] = $pParamHash['publish_date'];
		if ( $blogPost->store( $postHash ) ){
			$itemHash; 
			$itemHash['item_id'] = $pParamHash['item']->get_id( $pParamHash['use_hash'] );
			$itemHash['item_link'] = $pParamHash['item']->get_link();
			$author = $pParamHash['item']->get_author();
			$itemHash['item_author'] = $author->get_name();
			$itemHash['content_id'] = $blogPost->mInfo['content_id'];
			$itemHash['feed_id'] = $this->mFeedId;
						
			//store a reference to the blog post item in the reblog item map
			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."reblog_items_map";
			$result = $this->mDb->associateInsert( $table, $itemHash );
			$this->mDb->CompleteTrans();
		}
		return( $blogPost->mErrors );
	}

	
	/**
	* This function parses the data of feeds passed to it. 
	* Groups of feeds will be automatically mixed and sorted by date!
	* @pParamHash an array of feed_ids
	* Returns an array of feed items sorted by date
	**/
	function parseFeeds( $pParamHash ){
		//set path to rss feed cache
		$cache_path = TEMP_PKG_PATH.'rss/simplepie';
		
		//we do this earlier instead of later because if we can't cache the source we shouldn't be pulling the rss feed.
		if( !is_dir( $cache_path ) && !mkdir_p( $cache_path ) ) {
			bit_log_error( 'Can not create the cache directory: '.$cache_path );
			
			return FALSE;
		}else{
			//load up parser SimplePie
			require_once( UTIL_PKG_PATH.'simplepie/simplepie.inc' );

			/* 
			if (!is_array($pParamHash['feed_id'])){
				$ids = explode( ",", $pParamHash['feed_id'] );
			}else{
				$ids = $pParamHash['feed_id'];
			}
			
			$urls = Array();
			
			foreach ($ids as $id){
				if( @BitBase::verifyId( $id ) ) {
					$feedHash = $this->get_rss_module( $id );
					$urls[] = $feedHash['url'];
				}else{
					//todo assign this as an error
					//$repl = '<b>rss can not be found, id must be a number</b>';
				}
			}
			*/

			$urls = Array();
			if (!is_array($pParamHash['url'])){
				$urls = explode( ",", $pParamHash['url'] );
			}else{
				$urls[] = $pParamHash['url'];
			}
			
			$feed = new SimplePie();
			
			//Instead of only passing in one feed url, we'll pass in an array of multiple feeds
			$feed->set_feed_url( $urls );
			
			$feed->set_cache_location( $cache_path );
			
			//set cache time
			$cache_time = !empty($pParamHash['cache_time'])?$pParamHash['cache_time']:1;
			$feed->set_cache_duration( $cache_time );
			
			//not sure - we may want to eventually use this
			//$feed->set_stupidly_fast(TRUE);
			 
			// Initialize the feed object
			$feed->init();
			 
			// This will work if all of the feeds accept the same settings.
			$feed->handle_content_type();
			
			$items = $feed->get_items();
			
			return $items;
		}
	}
}

function reblog_content_load_sql( &$pObject, $pParamHash = NULL ) {
	global $gBitSystem;
	$ret = array();
	if ( $gBitSystem->isPackageActive( 'reblog' ) && $pObject->mContentTypeGuid == 'bitblogpost' ) {
		$ret['select_sql'] = ", rim.`item_link`, rim.`item_author`, rf.`feed_id`, rf.`name` as feed_name, rf.`description` as feed_description, rf.`url` as feed_url, rf.`fullpost`";
		$ret['join_sql'] = "LEFT JOIN `".BIT_DB_PREFIX."reblog_items_map` rim ON ( lc.`content_id`=rim.`content_id` )
							LEFT JOIN `".BIT_DB_PREFIX."reblog_feeds` rf ON ( rf.`feed_id`=rim.`feed_id` )";
	}
	return $ret;
}

function reblog_content_list_sql( &$pObject, $pParamHash=NULL ) {
	global $gBitSystem, $gBitThemes;
	$ret = array();
	if ( $gBitSystem->isPackageActive( 'reblog' ) && $pObject->mContentTypeGuid == 'bitblogpost') {
		$ret['select_sql'] = ", rim.`item_link`, rim.`item_author`, rf.`feed_id`, rf.`name` as feed_name, rf.`description` as feed_description, rf.`url` as feed_url, rf.`fullpost`";
		$ret['join_sql'] = "LEFT JOIN `".BIT_DB_PREFIX."reblog_items_map` rim ON ( lc.`content_id`=rim.`content_id` )
							LEFT JOIN `".BIT_DB_PREFIX."reblog_feeds` rf ON ( rf.`feed_id`=rim.`feed_id` )";
 		if ($gBitSystem->mActivePackage == 'reblog' || $gBitThemes->isModuleLoaded( 'bitpackage:reblog/center_list_reblog_posts.tpl', 'c' ) ){
			$ret['where_sql'] = ' AND rim.`content_id` IS NOT NULL ';
		}
	}
	return $ret;
}
