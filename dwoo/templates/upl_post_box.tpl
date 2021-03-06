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
				{t}Submit{/t}" accesskey="z" />&nbsp;(<span id="posttypeindicator">{t}new thread{/t}</span>)
			{elseif %KU_QUICKREPLY && $replythread neq 0}
				{t}Reply{/t}" accesskey="z" />&nbsp;(<span id="posttypeindicator">{t}reply to{/t} <!sm_threadid></span>)
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
			<td colspan="2">
				{literal}
				<script type="text/javascript"> var RecaptchaOptions = { theme : 'clean' }; </script>
			<?php 
			require_once('./recaptchalib.php');
			$publickey = "6LcmgsYSAAAAAKlgf5y5a_PP-KarRsFkMWei-9lc";
			echo recaptcha_get_html($publickey);
			?> 
				{/literal}

			</td>

		</tr>

	{/if}
	{if $board.uploadtype eq 0 || $board.uploadtype eq 1}
		{if $board.maxfiles gt 1 && $replythread neq 0}
			{section name=files loop=$board.maxfiles}
				<tr id="file{$.section.files.iteration}"{if !$.section.files.first} style="display:none"{/if}>
					<td class="postblock">
						{t}File{/t} {$.section.files.iteration}
					</td>
					<td>				
					<input{if !$.section.files.last} onchange="$('#file{$.section.files.iteration + 1}').show()"{/if} type="file" name="imagefile[]" size="35" accesskey="f" />
					{if $.section.files.first && $replythread eq 0 && $board.enablenofile eq 1 }
						<input type="checkbox" name="nofile" id="nofile" accesskey="q" /><label for="nofile"> {t}No File{/t}</label>
					{/if}
					</td>
				</tr>
			{/section}
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
				</td>
			</tr>
		{/if}
	{/if}
	{if $replythread eq 0 && %KU_TAGS neq ''}
		<tr>
			<td class="postblock">
				{t}Tag{/t}
			</td>
			<td>
				<select name="tag">
					<option value="" selected="selected">
						{t}Choose one{/t}:
					</option>

					{if unserialize(%KU_TAGS) neq ''}
						{foreach key=tag item=tag_abbr from=unserialize(%KU_TAGS)}
									<option value="{$tag_abbr}">
										{$tag} [{$tag_abbr}]
									</option>
						{/foreach}
					{/if}
				</select>
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
					<li>{t 1=$board.uniqueposts}Currently %1 unique user posts.{/t}</li>
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
<script type="text/javascript"><!--                                                                                      
				set_inputs("postform");
				//--></script>
