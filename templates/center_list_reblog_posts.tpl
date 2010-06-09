{* $Header$ *}
{if $blogPosts || $showEmpty}
<div class="floaticon">{bithelp}</div>

<div class="display blogs">
	<div class="header">
		<h1>{tr}Recent ReBlogged Posts{/tr}</h1>
	</div>

	<div class="body">
		{if ($gBitUser->hasPermission( 'p_blog_posts_read_future' ) || $gBitUser->isAdmin() ) && $futures}
			<h3>{tr}Upcoming Blog Posts{/tr}</h3>
			<ul>
				{foreach from=$futures item=future}
					<li>{$future.display_link} <small>({tr}By {displayname hash=$future}, to be published {$future.publish_date|bit_long_datetime}{/tr})</small></li>
				{/foreach}
			</ul>
		{/if}
	
		{foreach from=$blogPosts item=aPost}
			{include file="bitpackage:blogs/blog_list_post.tpl"}
		{foreachelse}
			<div class="norecords">{tr}No records found{/tr}</div>
		{/foreach}
	</div><!-- end .body -->

	{pagination url="`$smarty.const.REBLOG_PKG_URL`index.php" user_id="`$gQueryUserId`"}

	{*minifind sort_mode=$sort_mode*}
</div>
{/if}
