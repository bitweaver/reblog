{strip}
{if $packageMenuTitle}<a href="#"> {tr}{$packageMenuTitle|capitalize}{/tr}</a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}list_feeds.php">{tr}List feeds{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}update_feeds.php" title="{tr}Update feeds now{/tr}">{tr}Update feeds{/tr}</a></li>
</ul>
{/strip}
