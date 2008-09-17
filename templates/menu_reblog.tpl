{strip}
<ul>
	{if $gBitUser->hasPermission('p_reblog_view')}
		<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}index.php">{biticon iname="view-refresh" iexplain="Reblogged Posts" ilocation=menu}</a></li>
	{/if}
	{if $gBitUser->hasPermission('p_reblog_view')}
		<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}list_feeds.php">{biticon iname="format-justify-fill" iexplain="List Feeds" ilocation=menu}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_reblog_admin' )}
		<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}edit_feed.php">{biticon iname="document-new" iexplain="Add Feed" ilocation=menu}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_reblog_admin' )}
		<li><a href="#">{biticon iname="view-refresh" iexplain="Update feeds now" iforce="icon"}</a>{jspopup class="popup_link" notra=1 href=update_feeds.php title="Update feeds" width="null" height="null"}</li>
	{/if}
</ul>
{/strip}
