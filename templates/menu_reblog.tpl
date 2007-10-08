{strip}
<ul>
	{if $gBitUser->hasPermission('p_reblog_view')}
		<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}index.php">{biticon iname="view-refresh" iexplain="Recent Reblogged Posts" ilocation=menu}</a></li>
	{/if}
	{if $gBitUser->hasPermission('p_reblog_view')}
		<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}list_feeds.php">{biticon iname="format-justify-fill" iexplain="List Feeds" ilocation=menu}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_reblog_admin' )}
		<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}edit_feed.php">{biticon iname="document-new" iexplain="Add Feed" ilocation=menu}</a></li>
	{/if}
</ul>
{/strip}
