{strip}
{if ($smarty.const.ACTIVE_PACKAGE != 'blogs' || $smarty.const.ACTIVE_PACKAGE != 'reblog') && ( $aPost.feed_id || $post_info.feed_id )}
	{if $post_info }
		{assign var=aPost value=$post_info}
	{/if}
	<div id="reblog-source" class="date">
		{tr}This is{/tr} <a href="{$smarty.const.REBLOG_PKG_URL}">{tr}Reblogged{/tr}</a> {tr}from{/tr} <a href="{$aPost.item_link}">{$aPost.feed_name}</a><br/>
		{tr}Originally authored by{/tr} <strong>{$aPost.item_author}</strong>
	</div>
{/if}
{/strip}