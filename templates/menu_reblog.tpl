{strip}
{if $gBitUser->hasPermission('p_reblog_view')}
{if $packageMenuTitle}<a class="dropdown-toggle" data-toggle="dropdown" href="#"> {tr}{$packageMenuTitle}{/tr} <b class="caret"></b></a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}index.php">{booticon iname="icon-recycle"   iexplain="Reblogged Posts" ilocation="menu"}</a></li>
	{if $gBitUser->hasPermission('p_reblog_view')}
		<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}list_feeds.php">{booticon iname="icon-list" iexplain="List Feeds" ilocation="menu"}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_reblog_admin' )}
		<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}edit_feed.php">{booticon iname="icon-file" iexplain="Add Feed" ilocation="menu"}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_reblog_admin' )}
		<li><a href="#">{booticon iname="icon-recycle"   iexplain="Update feeds now" iforce="icon"}</a>{jspopup class="popup_link" notra=1 href="update_feeds.php" title="Update feeds" width="null" height="null"}</li>
	{/if}
</ul>
{/if}
{/strip}
