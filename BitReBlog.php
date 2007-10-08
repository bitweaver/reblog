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
		
		if( !empty( $pParamHash['reblog'] ) ) {
			$pParamHash['feed_store']['reblog'] = $pParamHash['reblog'];
		}else{
			$pParamHash['feed_store']['reblog'] = 'n';
		}
		
		if( !empty( $pParamHash['refresh'] ) ) {
			$pParamHash['feed_store']['refresh'] = (int)$pParamHash['refresh'] * 60;  //convert minutes to seconds
		}else{
			$pParamHash['feed_store']['refresh'] = 600;
		}
		
		$pParamHash['feed_store']['last_updated'] = $gBitSystem->getUTCTime();
		vd($this->mErrors);
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

		$bindVars = array();
		
		$sort_mode_prefix = 'fd';

		$sortHash = array(
			'feed_id_desc',
			'feed_id_asc',
			'name_asc',
			'name_desc',
		);

		if( empty( $pParamHash['sort_mode'] ) || in_array( $pParamHash['sort_mode'], $sortHash ) ) {
			$pParamHash['sort_mode'] = 'name_asc';
		}
		
		$sort_mode = $sort_mode_prefix . '.' . $this->mDb->convertSortmode( $pParamHash['sort_mode'] );
		
		$query = "SELECT * FROM `".BIT_DB_PREFIX."reblog_feeds` fd ORDER BY $sort_mode";
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
			$whereSql .= 'rim.`feed_id` = ?';

			$sort_mode = $sort_mode_prefix . '.' . $this->mDb->convertSortmode( 'feed_id_desc' );

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
	* This function gets a feed and checks if any of its items is new
	**/
	function updateFeed(){
		// parse feed
		$feeditems = $this->parseFeeds( $this->mFeedId );
		
		$listHash = array();
		$listHash['feed_id'] = $this->mFeedId;
		$listHash['max_records'] = $items->get_item_quantity();
		
		$storeditems = $this->getItems( $listHash );
		
		foreach( $feeditems as $item ){
			// check ids in feed against items
			foreach( $storeditems as $stored ){
				$new == TRUE;
				$itemId = pack('H*', $pItem->get_id(TRUE));
				if ( $itemid == $stored['item_id'] ){
					$new = FALSE;
					break;
				}
				if ( $new == TRUE ){
					$this->reblogItem( $item );
				}
			}
		}
	}

	/**
	* This function stores a RSS feed item as a blog post
	**/
	function reblogItem( &$pItem ){
		require_once( BLOGS_PKG_URL.'BitBlogPost.php' );
		$blogPost = new BitBlogPost();
		$postHash = array();
		$postHash['user_id'] = $this->mInfo['user_id'];
		$postHash['data'] = $pItem->get_content();
		//$postHash['blog_content_id'] = ( !empty($this->mInfo['blog_content_id'] )?$this->mInfo['blog_content_id']:NULL;
		if ( $post = $blogPost->store( $itemHash ) ){
			$itemHash['blog_post_content_id'] = $post->mInfo['content_id'];
			$itemHash['item_id'] = pack('H*', $pItem->get_id(TRUE));
			$itemHash['feed_id'] = $this->mFeedId;
			//store a reference to the blog post item in the reblog item map
			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."reblog_items_map";
			$result = $this->mDb->associateInsert( $table, $itemHash );
			$this->mDb->CompleteTrans();
		}
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
	if ( $gBitSystem->isPackageActive( 'reblog' ) ) {
		$ret['select_sql'] = ", rf.`feed_id`, rf.`name`, rf.`description`, rf.`url`";
		$ret['join_sql'] = "INNER JOIN `".BIT_DB_PREFIX."reblog_items_map` rim ON ( lc.`content_id`=rim.`content_id` )
							INNER JOIN `".BIT_DB_PREFIX."reblog_feeds` rf ON ( rf.`feed_id`=rim.`feed_id` )";
	}
	return $ret;
}

function reblog_content_list_sql( &$pObject, $pParamHash=NULL ) {
	global $gBitSystem;
	$ret = array();
	if ( $gBitSystem->isPackageActive( 'reblog' ) && $gBitSystem->mActivePackage == 'reblog' ) {
		$ret['select_sql'] = ", rf.`feed_id`, rf.`name`, rf.`description`, rf.`url`";
		$ret['join_sql'] = "INNER JOIN `".BIT_DB_PREFIX."reblog_items_map` rim ON ( lc.`content_id`=rim.`content_id` )
							INNER JOIN `".BIT_DB_PREFIX."reblog_feeds` rf ON ( rf.`feed_id`=rim.`feed_id` )";
		$ret['where_sql'] = ' AND rim.`content_id` IS NOT NULL ';
	}
	return $ret;
}
