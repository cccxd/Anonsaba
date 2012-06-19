<form id="delform" action="{%KU_CGIPATH}/board.php" method="post">
<input type="hidden" name="board" value="{$board.name}" />
{foreach name=thread item=postsa from=$posts}
	{foreach key=postkey item=post from=$postsa}
		{if $post.parentid eq 0}
			<span id="unhidethread{$post.id}{$board.name}" style="display: none;">
			{t}Thread{/t} <a href="{%KU_BOARDSFOLDER}{$board.name}/res/{$post.id}.html">{$post.id}</a> {t}hidden.{/t}
			<a href="#" onclick="javascript:togglethread('{$post.id}{$board.name}');return false;" title="{t}Un-Hide Thread{/t}">
				<img src="{$cwebpath}css/icons/blank.gif" border="0" class="unhidethread" alt="{t}Un-Hide Thread{/t}" />
			</a>
	</span>
	<div id="thread{$post.id}{$board.name}">
	<script type="text/javascript"><!--
		if (hiddenthreads.toString().indexOf('{$post.id}{$board.name}')!==-1) {
			document.getElementById('unhidethread{$post.id}{$board.name}').style.display = 'block';
			document.getElementById('thread{$post.id}{$board.name}').style.display = 'none';
		}
		//--></script>
			<a name="s{$.foreach.thread.iteration}"></a>
			
			{if ($post.file.0 neq '' || $post.file_type.0 neq '' ) && (($post.videobox eq '' && $post.file.0 neq '') && $post.file.0 neq 'removed')}
				<span class="filesize">
				{if $post.file_type.0 eq 'mp3'}
					{t}Audio{/t}
				{else}
					{t}File{/t}
				{/if}
				{if $post.file_type.0 neq 'jpg' && $post.file_type.0 neq 'gif' && $post.file_type.0 neq 'png' && $post.videobox eq ''}
					<a 
					{if %KU_NEWWINDOW}
						target="_blank" 
					{/if}
					href="{$file_path}/src/{$post.file.0}.{$post.file_type.0}">
				{else}
					<a href="{$file_path}/src/{$post.file.0}.{$post.file_type.0}" onclick="javascript:expandimg('{$post.id}', '{$file_path}/src/{$post.file.0}.{$post.file_type.0}', '{$file_path}/thumb/{$post.file.0}s.{$post.file_type.0}', '{$post.image_w.0}', '{$post.image_h.0}', '{$post.thumb_w.0}', '{$post.thumb_h.0}');return false;">
				{/if}
				{if isset($post.id3.0.comments_html)}
					{if $post.id3.0.comments_html.artist.0 neq ''}
					{$post.id3.0.comments_html.artist.0}
						{if $post.id3.0.comments_html.title.0 neq ''}
							- 
						{/if}
					{/if}
					{if $post.id3.0.comments_html.title.0 neq ''}
						{$post.id3.0.comments_html.title.0}
					{/if}
					</a>
				{else}
					{$post.file.0}.{$post.file_type.0}</a>
				{/if}
				- ({$post.file_size_formatted.0}
				{if $post.id3.0.comments_html.bitrate neq 0 || $post.id3.0.audio.sample_rate neq 0}
					{if $post.id3.0.audio.bitrate neq 0}
						- {round($post.id3.0.audio.bitrate / 1000)} kbps
						{if $post.id3.0.audio.sample_rate neq 0}
							- 
						{/if}
					{/if}
					{if $post.id3.0.audio.sample_rate neq 0}
						{$post.id3.0.audio.sample_rate / 1000} kHz
					{/if}
				{/if}
				{if $post.image_w.0 > 0 && $post.image_h.0 > 0}
					, {$post.image_w.0}x{$post.image_h.0}
				{/if}
				{if $post.file_original.0 neq '' && $post.file_original.0 neq $post.file.0}
					, {$post.file_original.0}.{$post.file_type.0}
				{/if}
				)
				{if $post.id3.0.playtime_string neq ''}
					{t}Length{/t}: {$post.id3.0.playtime_string}
				{/if}
				{if $board.imgedit eq 1}
                                {if $post.file_type.0 eq 'jpg' || $post.file_type.0 eq 'gif' || $post.file_type.0 eq 'png'}
                                <a href="{%KU_WEBPATH}/imgedit.php?B={$board.name}&I={$post.file.0}&T={$posts.0.id}">
                                <img src="{%KU_WEBPATH}/css/edit.gif" alt="edit" />
                                </a>
                                {/if}
				{/if}
				</span>
				{if %KU_THUMBMSG}
					<span class="thumbnailmsg"> 
					{if $post.file_type.0 neq 'jpg' && $post.file_type.0 neq 'gif' && $post.file_type.0 neq 'png' && $post.videobox eq ''}
						{t}Extension icon displayed, click image to open file.{/t}
					{else}
						{t}Thumbnail displayed, click image for full size.{/t}
					{/if}
					</span>
				{/if}
				<br />
			{/if}
			{if $post.videobox eq '' && $post.file.0 neq '' && ( $post.file_type.0 eq 'jpg' || $post.file_type.0 eq 'gif' || $post.file_type.0 eq 'png')}
				{if $post.file.0 eq 'removed'}
					<div class="nothumb">
						{t}File<br />Removed{/t}
					</div>
				{else}
					<a 
					{if %KU_NEWWINDOW}
						target="_blank" 
					{/if}
					href="{$file_path}/src/{$post.file.0}.{$post.file_type.0}">
					<span id="thumb{$post.id}"><img src="{$file_path}/thumb/{$post.file.0}s.{$post.file_type.0}" alt="{$post.id}" class="thumb" height="{$post.thumb_h.0}" width="{$post.thumb_w.0}" /></span>
					</a>
				{/if}
			{elseif $post.nonstandard_file.0 neq ''}
				{if $post.file.0 eq 'removed'}
					<div class="nothumb">
						{t}File<br />Removed{/t}
					</div>
				{else}
					<a 
					{if %KU_NEWWINDOW}
						target="_blank" 
					{/if}
					href="{$file_path}/src/{$post.file.0}.{$post.file_type.0}">
					<span id="thumb{$post.id}"><img src="{$post.nonstandard_file.0}" alt="{$post.id}" class="thumb" height="{$post.thumb_h.0}" width="{$post.thumb_w.0}" /></span>
					</a>
				{/if}
			{/if}
			<a name="{$post.id}"></a>
			<label>
			<input type="checkbox" name="post[]" value="{$post.id}" />
			{if $post.subject neq ''}
				<span class="filetitle">
					{$post.subject}
				</span>
			{/if}
			{strip}
				<span class="postername">
				
				{if $post.email && $board.anonymous && $board.enableemail eq 1}
					<a href="mailto:{$post.email}">
				{/if}
				{if $post.name eq '' && $post.tripcode eq ''}
					{$board.anonymous}
				{elseif $post.name eq '' && $post.tripcode neq ''}
				{else}
					{$post.name}
				{/if}
				{if $post.email neq '' && $board.anonymous neq ''}
					</a>
				{/if}

				</span>

				{if $post.tripcode neq ''}
					<span class="postertrip">!{$post.tripcode}</span>
				{/if}
			{/strip}
			{if $post.posterauthority eq 1}
				<span class="admin">
					&#35;&#35;&nbsp;{t}Admin{/t}&nbsp;&#35;&#35;
				</span>
			{elseif $post.posterauthority eq 4}
				<span class="mod">
					&#35;&#35;&nbsp;{t}Super Mod{/t}&nbsp;&#35;&#35;
				</span>
			{elseif $post.posterauthority eq 2}
				<span class="mod">
					&#35;&#35;&nbsp;{t}Mod{/t}&nbsp;&#35;&#35;
				</span>
			{/if}
			{$post.timestamp_formatted}
			</label>
			<span class="reflink">
				{$post.reflink}
			</span>
			{if $board.showid}
				ID: {$post.ipmd5|substr:0:6}
			{/if}
			<span class="extrabtns">
			{if $post.locked eq 1}
				<img style="border: 0;" src="{$boardpath}css/locked.gif" alt="{t}Locked{/t}" />
			{/if}
			{if $post.stickied eq 1}
				<img style="border: 0;" src="{$boardpath}css/sticky.gif" alt="{t}Stickied{/t}" />
			{/if}
			<span id="hide{$post.id}"><a href="#" onclick="javascript:togglethread('{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}{$board.name}');return false;" title="Hide Thread"><img src="{$boardpath}css/icons/blank.gif" border="0" class="hidethread" alt="hide" /></a></span>
			{if %KU_WATCHTHREADS}
				<a href="#" onclick="javascript:addtowatchedthreads('{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}','{$board.name}');return false;" title="Watch Thread"><img src="{$boardpath}css/icons/blank.gif" border="0" class="watchthread" alt="watch" /></a>
			{/if}
			{if %KU_EXPAND && $post.replies && ($post.replies + %KU_REPLIES) < 300}
				<a href="#" onclick="javascript:expandthread('{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}','{$board.name}');return false;" title="Expand Thread"><img src="{$boardpath}css/icons/blank.gif" border="0" class="expandthread" alt="expand" /></a>
			{/if}
			{if %KU_QUICKREPLY}
				<a href="#postbox" onclick="javascript:quickreply('{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}');" title="{t}Quick Reply{/t}"><img src="{$boardpath}css/icons/blank.gif" border="0" class="quickreply" alt="quickreply" /></a>
			{/if}
			</span>
			<span id="dnb-{$board.name}-{$post.id}-y"></span>
			[<a href="{%KU_BOARDSPATH}/{$board.name}/res/{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}.html">{t}Reply{/t}</a>]
			{if %KU_FIRSTLAST && (($post.stickied eq 1 && $post.replies + %KU_REPLIESSTICKY > 50) || ($post.stickied eq 0 && $post.replies + %KU_REPLIES > 50))}
				{if (($post.stickied eq 1 && $post.replies + %KU_REPLIESSTICKY > 100) || ($post.stickied eq 0 && $post.replies + %KU_REPLIES > 100))}
					[<a href="{%KU_BOARDSPATH}/{$board.name}/res/{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}-100.html">{t}First 100 posts{/t}</a>]
				{/if}
				[<a href="{%KU_BOARDSPATH}/{$board.name}/res/{$post.id}+50.html">{t}Last 50 posts{/t}</a>]
			{/if}
			<br />
		{else}
			<table>
				<tbody>
				<tr>
					<td class="doubledash">
						&gt;&gt;
					</td>
					<td class="reply" id="reply{$post.id}">
						<a name="{$post.id}"></a>
						<label>
						<input type="checkbox" name="post[]" value="{$post.id}" />
						
						
						{if $post.subject neq ''}
							<span class="filetitle">
								{$post.subject}
							</span>
						{/if}
						{strip}
								<span class="postername">
								
								{if $post.email && $board.anonymous && $board.enableemail eq 1}
                                                                <a href="mailto:{$post.email}">
                                                                {/if}
								{if $post.name eq '' && $post.tripcode eq ''}
									{$board.anonymous}
								{elseif $post.name eq '' && $post.tripcode neq ''}
								{else}
									{$post.name}
								{/if}
								{if $post.email neq '' && $board.anonymous neq ''}
									</a>
								{/if}

								</span>

							{if $post.tripcode neq ''}
								<span class="postertrip">!{$post.tripcode}</span>
							{/if}
						{/strip}
						{if $post.posterauthority eq 1}
							<span class="admin">
								&#35;&#35;&nbsp;{t}Admin{/t}&nbsp;&#35;&#35;
							</span>
						{elseif $post.posterauthority eq 4}
							<span class="mod">
								&#35;&#35;&nbsp;{t}Super Mod{/t}&nbsp;&#35;&#35;
							</span>
						{elseif $post.posterauthority eq 2}
							<span class="mod">
								&#35;&#35;&nbsp;{t}Mod{/t}&nbsp;&#35;&#35;
							</span>
						{/if}

						{$post.timestamp_formatted}
						</label>

						<span class="reflink">
							{$post.reflink}
						</span>
						{if $board.showid}
							ID: {$post.ipmd5|substr:0:6}
						{/if}
						<span class="extrabtns">
						{if $post.locked eq 1}
							<img style="border: 0;" src="{%KU_STATICPATH}/images/locked.gif" alt="{t}Locked{/t}" />
						{/if}
						{if $post.stickied eq 1}
							<img style="border: 0;" src="{%KU_STATICPATH}/images/sticky.gif" alt="{t}Stickied{/t}" />
						{/if}
						</span>
						<span id="dnb-{$board.name}-{$post.id}-n"></span>
						{if count($post.file) eq 1 && ($post.file.0 neq '' || $post.file_type.0 neq '' ) && (($post.videobox eq '' && $post.file.0 neq '') && $post.file.0 neq 'removed')}
							<br /><span class="filesize">
							{if $post.file_type.0 eq 'mp3'}
								{t}Audio{/t}
							{else}
								{t}File{/t}
							{/if}
							{if $post.file_type.0 neq 'jpg' && $post.file_type.0 neq 'gif' && $post.file_type.0 neq 'png' && $post.videobox eq ''}
								<a {if %KU_NEWWINDOW}target="_blank" {/if}href="{$file_path}/src/{$post.file.0}.{$post.file_type.0}">
							{else}
								<a href="{$file_path}/src/{$post.file.0}.{$post.file_type.0}" onclick="javascript:expandimg('{$post.id}', '{$file_path}/src/{$post.file.0}.{$post.file_type.0}', '{$file_path}/thumb/{$post.file.0}s.{$post.file_type.0}', '{$post.image_w.0}', '{$post.image_h.0}', '{$post.thumb_w.0}', '{$post.thumb_h.0}');return false;">
							{/if}
							{if isset($post.id3.0.comments_html)}
								{if $post.id3.0.comments_html.artist.0 neq ''}
								{$post.id3.0.comments_html.artist.0}
									{if $post.id3.0.comments_html.title.0 neq ''}
										- 
									{/if}
								{/if}
								{if $post.id3.0.comments_html.title.0 neq ''}
									{$post.id3.0.comments_html.title.0}
								{/if}
								</a>
							{else}
								{$post.file.0}.{$post.file_type.0}</a>
							{/if}
							- ({$post.file_size_formatted.0}
							{if $post.id3.0.comments_html.bitrate neq 0 || $post.id3.0.audio.sample_rate neq 0}
								{if $post.id3.0.audio.bitrate neq 0}
									- {round($post.id3.0.audio.bitrate / 1000)} kbps
									{if $post.id3.0.audio.sample_rate neq 0}
										- 
									{/if}
								{/if}
								{if $post.id3.0.audio.sample_rate neq 0}
									{$post.id3.0.audio.sample_rate / 1000} kHz
								{/if}
							{/if}
							{if $post.image_w.0 > 0 && $post.image_h.0 > 0}
								, {$post.image_w.0}x{$post.image_h.0}
							{/if}
							{if $post.file_original.0 neq '' && $post.file_original.0 neq $post.file.0}
								, {$post.file_original.0}.{$post.file_type.0}
							{/if}
							)
							{if $post.id3.0.playtime_string neq ''}
								{t}Length{/t}: {$post.id3.0.playtime_string}
							{/if}
							{if $board.imgedit eq 1}
                                			{if $post.file_type.0 eq 'jpg' || $post.file_type.0 eq 'gif' || $post.file_type.0 eq 'png'}
                                        			  <a href="{%KU_WEBPATH}/imgedit.php?B={$board.name}&I={$post.file.0}&T={$posts.0.id}">
                                        			  <img src="{%KU_WEBPATH}/css/edit.gif" alt="edit" />
                                       				  </a>
                                			{/if}
							{/if}
							</span>
							{if %KU_THUMBMSG}
								<span class="thumbnailmsg"> 
								{if $post.file_type.0 neq 'jpg' && $post.file_type.0 neq 'gif' && $post.file_type.0 neq 'png' && $post.videobox eq ''}
									{t}Extension icon displayed, click image to open file.{/t}
								{else}
									{t}Thumbnail displayed, click image for full size.{/t}
								{/if}
								</span>
							{/if}
							<br />
						{/if}
						{if count($post.file) gt 1}
							{foreach key=fileskey name=filesloop item=file from=$post.file}
								{if $post.file.$fileskey neq '' && ( $post.file_type.$fileskey eq 'jpg' || $post.file_type.$fileskey eq 'gif' || $post.file_type.$fileskey eq 'png')}
								{if $fileskey % 3 eq 0}<br style="clear:both" />{/if}
                                                                {if $post.file.$fileskey eq 'removed'}
                                                                       <div class="nothumb">
								             {t}File<br />Removed{/t}
								       </div>
                                                                {else}
									<div style="float:left">
									<span class="{if $.foreach.filesloop.first || $fileskey % 3 eq 0}multithumbfirst{else}multithumb{/if}"><a href="{$file_path}/src/{$post.file.$fileskey}.{$post.file_type.$fileskey}" onclick="javascript:expandimg('{$post.id}-{$fileskey}', '{$file_path}/src/{$post.file.$fileskey}.{$post.file_type.$fileskey}', '{$file_path}/thumb/{$post.file.$fileskey}s.{$post.file_type.$fileskey}', '{$post.image_w.$fileskey}', '{$post.image_h.$fileskey}', '{$post.thumb_w.$fileskey}', '{$post.thumb_h.$fileskey}');return false;">({if $post.image_w.$fileskey > 0 && $post.image_h.$fileskey > 0}{$post.image_w.$fileskey}x{$post.image_h.$fileskey}{else}{$post.file_size_formatted.$fileskey}{/if})</a></span><br /><a {if %KU_NEWWINDOW}target="_blank"{/if} href="{$file_path}/src/{$post.file.$fileskey}.{$post.file_type.$fileskey}">
									<span id="thumb{$post.id}-{$fileskey}"><img class="{if $.foreach.filesloop.first || $fileskey % 3 eq 0}multithumbfirst{else}multithumb{/if}" src="{$file_path}/thumb/{$post.file.$fileskey}s.{$post.file_type.$fileskey}" alt="{$post.id}" height="{$post.thumb_h.$fileskey}" width="{$post.thumb_w.$fileskey}" title="{$post.file.$fileskey}.{$post.file_type.$fileskey} - ({$post.file_size_formatted.$fileskey}{if $post.image_w.$fileskey > 0 && $post.image_h.$fileskey > 0}, {$post.image_w.$fileskey}x{$post.image_h.$fileskey}{/if}{if $post.file_original.$fileskey neq '' && $post.file_original.$fileskey neq $post.file.$fileskey}, {$post.file_original.$fileskey}.{$post.file_type.$fileskey}){/if}" /></span>
									</a></div>
                                                                {/if}
								{elseif $post.nonstandard_file neq ''}
								{if $fileskey eq 3}<br style="clear:left" />{/if}
									<div style="float:left">
									<span class="{if $.foreach.filesloop.first || $fileskey % 3 eq 0}multithumbfirst{else}multithumb{/if}"><a {if %KU_NEWWINDOW}target="_blank" {/if}href="{$file_path}/src/{$post.file.$fileskey}.{$post.file_type.$fileskey}">{$post.file_type.$fileskey|upper} ({$post.file_size_formatted.$fileskey})</a></span><br />
									<a {if %KU_NEWWINDOW}target="_blank" {/if}href="{$file_path}/src/{$post.file.$fileskey}.{$post.file_type.$fileskey}">
									<span id="thumb{$post.id}-{$fileskey}"><img class="{if $.foreach.filesloop.first || $fileskey % 3 eq 0}multithumbfirst{else}multithumb{/if}" src="{$post.nonstandard_file.$fileskey}" alt="{$post.id}" height="{$post.thumb_h.$fileskey}" width="{$post.thumb_w.$fileskey}" title="{if isset($post.id3.$fileskey.comments_html)}{if $post.id3.$fileskey.comments_html.artist.0 neq ''}{$post.id3.$fileskey.comments_html.artist.0}{if $post.id3.$fileskey.comments_html.title.0 neq ''} - {/if}{/if}{if $post.id3.$fileskey.comments_html.title.0 neq ''}{$post.id3.$fileskey.comments_html.title.0}{/if}{else}{$post.file.$fileskey}.{$post.file_type.$fileskey}{/if} - ({$post.file_size_formatted.$fileskey}{if $post.id3.$fileskey.comments_html.bitrate neq 0 || $post.id3.$fileskey.audio.sample_rate neq 0}{if $post.id3.$fileskey.audio.bitrate neq 0} - {round($post.id3.$fileskey.audio.bitrate / 1000)} kbps{if $post.id3.$fileskey.audio.sample_rate neq 0} - {/if}{/if}{if $post.id3.$fileskey.audio.sample_rate neq 0}{$post.id3.$fileskey.audio.sample_rate / 1000} kHz{/if}{/if}{if $post.file_original.$fileskey neq '' && $post.file_original.$fileskey neq $post.file.$fileskey}, {$post.file_original.$fileskey}.{$post.file_type.$fileskey}{/if}){if $post.id3.$fileskey.playtime_string neq ''} {t}Length{/t}: {$post.id3.$fileskey.playtime_string}{/if}" /></span>
									</a></div>
								{/if}
								{if $.foreach.filesloop.last}
								<br style="clear:both" />
								{/if}
							{/foreach}
						{elseif count($post.file) eq 1 && $post.videobox eq '' && $post.file.0 neq '' && ( $post.file_type.0 eq 'jpg' || $post.file_type.0 eq 'gif' || $post.file_type.0 eq 'png')}
							{if $post.file.0 eq 'removed'}
								<div class="nothumb">
									{t}File<br />Removed{/t}
								</div>
							{else}
								<a {if %KU_NEWWINDOW}target="_blank"{/if} href="{$file_path}/src/{$post.file.0}.{$post.file_type.0}">
								<span id="thumb{$post.id}"><img src="{$file_path}/thumb/{$post.file.0}s.{$post.file_type.0}" alt="{$post.id}" class="thumb" height="{$post.thumb_h.0}" width="{$post.thumb_w.0}" /></span>
								</a>
							{/if}
						{elseif $post.nonstandard_file.0 neq ''}
							{if $post.file.0 eq 'removed'}
								<div class="nothumb">
									{t}File<br />Removed{/t}
								</div>
							{else}
								<a {if %KU_NEWWINDOW}target="_blank"{/if} href="{$file_path}/src/{$post.file.0}.{$post.file_type.0}">
								<span id="thumb{$post.id}"><img src="{$post.nonstandard_file.0}" alt="{$post.id}" class="thumb" height="{$post.thumb_h.0}" width="{$post.thumb_w.0}" /></span>
								</a>
							{/if}
						{/if}
		{/if}
		{if is_array($post.file_type) && in_array('mp3', $post.file_type)}
			{foreach key=fkey name=filesloop item=file from=$post.file}
				{if $fkey eq 0 && count($post.file) gt 1}
					<br /><span class="multithumbfirst">
				{/if}
				{if $post.file_type.$fkey eq 'mp3'}
					<!--[if !IE]> -->
					<object type="application/x-shockwave-flash" data="{%KU_CGIPATH}/inc/player/player.swf?playerID={$post.id}&amp;soundFile={$file_path}/src/{$post.file.$fkey|utf8_encode|urlencode}.mp3{if $post.id3.$fkey.comments_html.artist.0 neq ''}&amp;artists={$post.id3.$fkey.comments_html.artist.0}{/if}{if $post.id3.$fkey.comments_html.title.0 neq ''}&amp;titles={urlencode(mb_convert_encoding(html_entity_decode($post.id3.$fkey.comments_html.title.0), "UTF-8"))}{/if}&amp;wmode=transparent" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,22,87" width="290" height="24">
					<param name="wmode" value="transparent" />
					<!-- <![endif]-->
					<!--[if IE]>
					<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,22,87" width="290" height="24">
						<param name="movie" value="{%KU_CGIPATH}/inc/player/player.swf?playerID={$post.id}&amp;soundFile={$file_path}/src/{$post.file.$fkey|utf8_encode|urlencode}.mp3{if $post.id3.$fkey.comments_html.artist.0 neq ''}&amp;artists={$post.id3.$fkey.comments_html.artist.0}{/if}{if $post.id3.$fkey.comments_html.title.0 neq ''}&amp;titles={urlencode(mb_convert_encoding(html_entity_decode($post.id3.$fkey.comments_html.title.0), "UTF-8"))}{/if}&amp;wmode=transparent" />
						<param name="wmode" value="transparent" />
					<!-->
					</object>
					<!-- <![endif]-->
					{if count($post.file) gt 1}
						<br />
					{/if}
				{/if}
				{if $fkey gt 0 && $.foreach.filesloop.last}
					</span><br style="clear:both" />
				{/if}
			{/foreach}
		{/if}
		<blockquote>
		{if $post.videobox}
			{$post.videobox}
		{/if}
		{$post.message}
		</blockquote>
		{if not $post.stickied && $post.parentid eq 0 && (($board.maxage > 0 && ($post.timestamp + ($board.maxage * 3600)) < (time() + 7200 ) ) || ($post.deleted_timestamp > 0 && $post.deleted_timestamp <= (time() + 7200)))}
			<span class="oldpost">
				{t}Marked for deletion (old){/t}
			</span>
			<br />
		{/if}
		{if $post.parentid eq 0}
			<div id="replies{$post.id}{$board.name}">
			{if $post.replies}
				<span class="omittedposts">
					{if $post.stickied eq 0}
						{$post.replies} 
						{if $post.replies eq 1}
							{t lower="yes"}Post{/t} 
						{else}
							{t lower="yes"}Posts{/t} 
						{/if}
					{else}
						{$post.replies}
						{if $post.replies eq 1}
							{t lower="yes"}Post{/t} 
						{else}
							{t lower="yes"}Posts{/t} 
						{/if}
					{/if}
					{if $post.images > 0}
						{t}and{/t} {$post.images}
						{if $post.images eq 1}
							{t lower="yes"}Image{/t} 
						{else}
							{t lower="yes"}Images{/t} 
						{/if}
					{/if}
					{t}omitted{/t}. {t}Click Reply to view.{/t}
					</span>
				{/if}
			{else}
				</td>
			</tr>
		</tbody>
		</table>
		
		{/if}
	{/foreach}
			</div>
			</div>
		<br clear="left" />
		<hr />
{/foreach}