{* $Header: /cvsroot/bitweaver/_bit_reblog/templates/edit_feed.tpl,v 1.4 2007/11/21 20:07:33 wjames5 Exp $ *}
{strip}
<div class="edit reblog">
	<div class="header">
		<h1>{if $gFeed->isValid()}
				{tr}Edit ReBlog Feed{/tr}: {$feedInfo.name}
			{else}
				{tr}Create ReBlog Feed{/tr}
			{/if}
		</h1>
	</div>

	<div class="body">
		{form enctype="multipart/form-data" id="editfeedform"}
			<input type="hidden" name="feed_id" value="{$feedInfo.feed_id|escape}" />

			{legend legend="Reblog Feed"}
				<div class="row">
					{formlabel label="Name" for="name"}
					{forminput}
						<input type="text" size="50" name="name" id="name" value="{$feedInfo.name|escape}" />
						{formhelp note="A name for the feed you would like displayed."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Description" for="description"}
					{forminput}
						<input type="text" size="50" name="description" id="description" value="{$feedInfo.description|escape}" />
						{formhelp note="A description of the feed."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="User ID" for="user_id"}
					{forminput}
						<input type="text" size="5" name="user_id" id="user_id" value="{$feedInfo.user_content_id|default:$gBitUser->mUserId}" />
						{formhelp note="Each feed must be associated with a user id so that reblogged posts are attributable to someone. It is recommended that you set up a unique user to represent each site you are reblogging from. The user account will let you give the feeding site a complete profile."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="URL" for="url"}
					{forminput}
						<input type="text" size="50" name="url" id="url" value="{$feedInfo.url|escape}" />
						{formhelp note="The URL of a valid RSS feed."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Auto Reblog" for="reblog"}
					{forminput}
						<input type="checkbox" name="reblog" value="y" {if $feedInfo.reblog eq 'y'}checked="checked"{/if} />
						{formhelp note="All items for this feed will automatically be reposted into the blogs section of the site. If you want to select feed items for reblogging manually then uncheck this box."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Full Posts" for="fullpost"}
					{forminput}
						<input type="checkbox" name="fullpost" value="y" {if $feedInfo.fullpost eq 'y'}checked="checked"{/if} />
						{formhelp note="Check if the feed supplies the complete content of the blog post. This allows you to toggle display of a 'there is more' message on the reblogged post."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Refresh Rate" for="refresh"}
					{forminput}
						<select name="refresh" id="refresh">
							<option value="10"   {if $feedInfo.refresh eq 600}selected="selected"{/if}  >10  </option>
							<option value="15"   {if $feedInfo.refresh eq 900}selected="selected"{/if}  >15  </option>
							<option value="20"   {if $feedInfo.refresh eq 1200}selected="selected"{/if} >20  </option>
							<option value="30"   {if $feedInfo.refresh eq 1800}selected="selected"{/if} >30  </option>
							<option value="45"   {if $feedInfo.refresh eq 2700}selected="selected"{/if} >45  </option>
							<option value="60"   {if $feedInfo.refresh eq 3600}selected="selected"{/if} >60  </option>
							<option value="90"   {if $feedInfo.refresh eq 5400}selected="selected"{/if} >90  </option>
							<option value="120"  {if $feedInfo.refresh eq 7200}selected="selected"{/if} >120 </option>
							<option value="360"  {if $feedInfo.refresh eq 21600}selected="selected"{/if}>360 </option>
							<option value="720"  {if $feedInfo.refresh eq 43200}selected="selected"{/if}>720 </option>
							<option value="1440" {if $feedInfo.refresh eq 86400}selected="selected"{/if}>1440</option>
						</select> {tr}minutes{/tr}
						{formhelp note="This sets the frequency, in minutes, that a feed will be checked for updates. The shortest frequency is ten minutes, as the auto-reblogging server script only runs every ten minutes. If you are reblogging many feeds you may want to make the refresh rates longer to reduce processor load on your server."}
					{/forminput}
				</div>

				<div class="row">
					{formfeedback error=$errors.format}
					{formlabel label="Content Format"}
					{forminput}
						<select name="format_guid">
							{foreach name=formatPlugins from=$gLibertySystem->mPlugins item=plugin key=guid}
								{if $plugin.plugin_type eq 'format'}
									<option value="{$plugin.plugin_guid}" {if $feedInfo.format_guid eq $plugin.plugin_guid}selected="selected"{/if}>{$plugin.edit_label}</option>
								{/if}
							{/foreach}
						</select>
						{formhelp note="Select the format the feed content is delevered in. If the feed is plain text use the format you typically use for your site. Generally this is a wiki syntax or html."}
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="save_feed" value="{tr}Save{/tr}" />
				</div>
			{/legend}
		{/form}
	</div><!-- end .body -->
</div><!-- end .blogs -->
{/strip}
