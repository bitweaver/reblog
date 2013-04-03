{strip}
<li class="dropdown-submenu">
    <a href="#" onclick="return(false);" tabindex="-1" class="sub-menu-root">{tr}{$smarty.const.REBLOG_PKG_NAME|capitalize}{/tr}</a>
	<ul class="dropdown-menu sub-menu">
		<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}list_feeds.php">{tr}List feeds{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.REBLOG_PKG_URL}update_feeds.php" title="{tr}Update feeds now{/tr}">{tr}Update feeds{/tr}</a></li>
	</ul>
</li>
{/strip}
