{if $nsfwpost neq '' && $board.enableads eq 1 && $board.boardclass eq 1}
<table align="center" width="auto">
<tbody>
<tr>
	<td width="175" valign="middle" align="center">
			{$nsfwpost}
</td>
	&nbsp;&nbsp;&nbsp;
<td align="center" width="175">
{/if}

{if $sfwpost neq '' && $board.enableads eq 1 && $board.boardclass eq 0}
<table align="center" width="175">
<tbody>
<tr>
	<td width="175" valign="middle" align="center">
			{$sfwpost}
</td>
	&nbsp;&nbsp;&nbsp;
<td align="center" width="175">
{/if}
<div class="postarea">
<a id="postbox"></a>
<form name="postform" id="postform" action="{%KU_CGIPATH}/board.php" method="post" enctype="multipart/form-data"
{if $board.enablecaptcha eq 1}
	onsubmit="return checkcaptcha('postform');"
{/if}
>
<input type="hidden" name="board" value="{$board.name}" />
<input type="hidden" name="replythread" value="<!sm_threadid>" />
<input type="hidden" name="token" value="{$tokengen}" />
{if $board.maximagesize > 0}
	<input type="hidden" name="MAX_FILE_SIZE" value="{$board.maximagesize}" />
{/if}
<input type="text" name="email" size="28" maxlength="75" value="" style="display: none;" />
<table class="postform">
	<tbody>
	{if $board.forcedanon neq 1}
		<tr>
			<td class="postblock">
				{t}Name{/t}</td>
			<td>
				<input type="text" name="name" size="28" maxlength="75" accesskey="n" />
			</td>
		</tr>
	{/if}
        {if $board.enableemail eq 1}
        <tr>
                <td class="postblock">
                        {t}Email{/t}</td>
                <td>
                        <input type="text" name="em" size="28" maxlength="75" accesskey="e" />
                </td>
        </tr>
        {/if}
	<tr>
		<td class="postblock">
			{t}Subject{/t}</td>
		<td>
			{strip}<input type="text" name="subject" size="35" maxlength="75" accesskey="s" />&nbsp;<input type="submit" value="
			{if %KU_QUICKREPLY && $replythread eq 0}
				{t}Submit{/t}" accesskey="z" />&nbsp;(<span id="posttypeindicator">{t}New thread{/t}</span>)
			{elseif %KU_QUICKREPLY && $replythread neq 0}
				{t}Reply{/t}" accesskey="z" />&nbsp;(<span id="posttypeindicator">{t}Reply to thread{/t} <!sm_threadid></span>)
			{else}
				{t}Submit{/t}" accesskey="z" />
			{/if}{/strip}
		</td>
	</tr>
	<tr>
		<td class="postblock">
			{t}Message{/t}
		</td>
		<td>
			<textarea name="message" cols="48" rows="4" accesskey="m"></textarea>
		</td>
	</tr>
	{if $board.enablecaptcha eq 1}

		<tr>
                        <td class="postblock">
                        {t}Captcha{/t}</td>
                        {literal}<script type="text/javascript"> var RecaptchaOptions = { theme : 'clean' };</script>{/literal}
			<td colspan="2">
                        {$recaptcha}
			</td>

		</tr>

	{/if}
	{if $board.uploadtype eq 0 || $board.uploadtype eq 1}
		{if $board.max_files gt 1 && $replythread neq 0}
			{section name=files loop=$board.max_files}
				<tr id="file{$.section.files.iteration}"{if !$.section.files.first} style="display:none"{/if}>
					<td class="postblock">
						{t}File{/t} {$.section.files.iteration}
					</td>
					<td>				
					<input{if !$.section.files.last} onchange="document.getElementById('file{$.section.files.iteration + 1}').style.display = '';"{/if} type="file" name="imagefile[]" size="35" accesskey="f" /> 
                   	                {if $board.enableemail eq 0 && $.section.files.first}
                                	<input type="checkbox" name="em" id="sage" value="sage" accesskey="e" /><label for="sage"> {t}No bump{/t}</label>
                        		{/if}
					{if $.section.files.first && $replythread eq 0 && $board.enablenofile eq 1 }
						<input type="checkbox" name="nofile" id="nofile" accesskey="q" /><label for="nofile"> {t}No File{/t}</label>
					{/if}
					</td>
				</tr>
			{/section}
		<tr>
			<td class="postblock">
				{t}File URL{/t}
			</td>
			<td>
				<input type="text" name="fileurl" size="48" accesskey="h" />
			</td>
		</tr>
		{else}
        	<tr>
 			<td class="postblock">
				{t}File{/t}
			</td>
			<td>
			<input type="file" name="imagefile[]" size="35" accesskey="f" />
			{if $replythread eq 0 && $board.enablenofile eq 1 }
				<input type="checkbox" name="nofile" id="nofile" accesskey="q" /><label for="nofile"> {t}No File{/t}</label>
			{/if}
                        {if $board.enableemail eq 0}
                                <input type="checkbox" name="em" id="sage" value="sage" accesskey="e" /><label for="sage"> {t}No bump{/t}</label>
                        {/if}
			</td>
		</tr>
            {if $board.fileurl eq 1}
		<tr>
			<td class="postblock">
				{t}File URL{/t}
			</td>
			<td>
				<input type="text" name="fileurl" size="48" accesskey="h" />
			</td>
		</tr>
            {/if}
  	{/if}
{/if}
	{if ($board.uploadtype eq 1 || $board.uploadtype eq 2) && $board.embeds_allowed neq ''}
		<tr>
			<td class="postblock">
				{t}Embed{/t}
			</td>
			<td>
				<input type="text" name="embed" size="28" maxlength="75" accesskey="e" />&nbsp;<select name="embedtype">
				{foreach name=embed from=$embeds item=embed}
					{if in_array($embed.filetype,explode(',' $board.embeds_allowed))}
						<option value="{$embed.name|lower}">{$embed.name}</option>
					{/if}
				{/foreach}
				</select>
				<a class="rules" href="#postbox" onclick="window.open('{%KU_WEBPATH}/embedhelp.php','embedhelp','toolbar=0,location=0,status=0,menubar=0,scrollbars=0,resizable=0,width=300,height=210');return false;">Help</a>
			</td>
		</tr>
	{/if}
		<tr>
			<td class="postblock">
				{t}Password{/t}
			</td>
			<td>
				<input type="password" name="postpassword" size="8" accesskey="p" />&nbsp;{t}(For post and file deletion){/t}
			</td>
		</tr>
		<tr id="passwordbox"><td></td><td></td></tr>
		<tr>
			<td colspan="2" class="rules">
				<ul style="margin-left: 0; margin-top: 0; margin-bottom: 0; padding-left: 0;">
					<li>{t}Supported file types are{/t}:
					{if $board.filetypes_allowed neq ''}
						{foreach name=files item=filetype from=$board.filetypes_allowed}
							{$filetype.filetype|upper}{if $.foreach.files.last}{else}, {/if}
						{/foreach}
					{else}
						{t}None{/t}
					{/if}
					</li>
					<li>{t}Maximum file size allowed is{/t} {math "round(x/1024)" x=$board.maximagesize} KB.</li>
                                        <li>{t 1=$board.max_files}Maximum number of files per upload is %1{/t}.</li>
					<li>{t 1=%KU_THUMBWIDTH 2=%KU_THUMBHEIGHT}Images greater than %1x%2 pixels will be thumbnailed.{/t}</li>
					<li>{t 1=$board.uniqueposts}Currently %1 unique user posts.{/t}
					{if $board.enablecatalog eq 1} 
						<a href="{%KU_BOARDSFOLDER}{$board.name}/catalog.html">{t}View catalog{/t}</a>
					{/if}
					</li>
				</ul>
			{if %KU_BLOTTER && $blotter}
				<br />
				<ul style="margin-left: 0; margin-top: 0; margin-bottom: 0; padding-left: 0;">
				<li style="position: relative;">
					<span style="color: red;">
				{t}Blotter updated{/t}: {$blotter_updated|date_format:"%Y-%m-%d"}
				</span>
					<span style="color: red;text-align: right;position: absolute;right: 0px;">
						<a href="#" onclick="javascript:toggleblotter(true);return false;">{t}Show/Hide{/t}</a> <a href="{%KU_WEBPATH}/blotter.php">{t}Show All{/t}</a>
					</span>
				</li>
				{$blotter}
				</ul>
				<script type="text/javascript"><!--
				if (getCookie('ku_showblotter') == '1') {
					toggleblotter(false);
				}
				--></script>
			{/if}
			</td>
		</tr>
	</tbody>

</table>
</form>
<hr />
{if $topads neq '' && $board.enableads eq 1 && $board.boardclass eq 0}
	<div class="content ads">
		<center> 
			{$topads}
		</center>
	</div>
	<hr />
{/if}
{if $nsfwtop neq '' && $board.enableads eq 1 && $board.boardclass eq 1}
	<div class="content ads">
		<center> 
			{$nsfwtop}
		</center>
	</div>
	<hr />
{/if}
</div>
	{if $nsfwpost neq '' && $board.enableads eq 1 && $board.boardclass eq 1}
		</td>	&nbsp;&nbsp;&nbsp;
<td width="175" valign="middle" align="center">
{$nsfwpost}
</td></tr>
</tbody></table>
{/if}
{if $sfwpost neq '' && $board.enableads eq 1 && $board.boardclass eq 0}
		</td>	&nbsp;&nbsp;&nbsp;
<td width="175" valign="middle" align="center">
{$sfwpost}
</td></tr>
</tbody></table>
{/if}
<script type="text/javascript"><!--
				set_inputs("postform");
				//--></script>
