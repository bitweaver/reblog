{strip}

<div class="floaticon">{bithelp}</div>

<div class="listing reblog">
	<div class="header">
		<h1>{tr}ReBlog Feeds{/tr}</h1>
	</div>

	<div class="body">
		<ul class="clear data">
			{foreach from=$feedsList item=feed}
				<li class="item {cycle values='odd,even'}">
					<div class="floaticon">
						{if $gBitUser->hasPermission( 'p_reblog_admin' )}
							<a title="{tr}edit{/tr}" href="{$smarty.const.REBLOG_PKG_URL}edit_feed.php?feed_id={$feed.feed_id}">{biticon ipackage="icons" iname="document-properties" iexplain="configure"}</a>
							<a title="{tr}remove{/tr}" href="{$smarty.const.REBLOG_PKG_URL}list_feeds.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove=1&amp;feed_id={$feed.feed_id}">{booticon iname="icon-trash" ipackage="icons" iexplain="delete"}</a>
						{/if}
					</div>

					<h2><a title="{$feed.name|escape}" href="{$feed.feed_url}">{$feed.name|escape}</a></h2>

					<p>{$feed.description}</p>

					<div class="date">
						{if $gBitSystem->getConfig('blog_list_user_as') eq 'link'}
							{tr}Published by {$feed.user|userlink}{/tr}
						{elseif $gBitSystem->getConfig('blog_list_user_as') eq 'avatar'}
							{$feed.user|avatarize}
						{else}
							{tr}Published by {$feed.user}{/tr}
						{/if}
					</div>
					<div class="clear"></div>
				</li>
			{foreachelse}
				<li class="item norecords">
					{tr}No records found{/tr}
				</li>
			{/foreach}
		</ul>

		{pagination}
	</div><!-- end .body -->
</div><!-- end .reblog -->

{/strip}
