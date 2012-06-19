<!DOCTYPE html>
<html lang="en">
<head>
	<title>{%KU_NAME}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="shortcut icon" href="{%KU_WEBFOLDER}favicon.ico" />
<link rel="stylesheet" type="text/css" href="{%KU_WEBFOLDER}css/img_globals.css">
<link rel="stylesheet" type="text/css" href="{%KU_WEBFOLDER}css/front.css" />
<link rel="stylesheet" type="text/css" href="{%KU_WEBFOLDER}css/site_front.css" />
<link rel="stylesheet" type="text/css" href="{%KU_WEBFOLDER}css/site_global.css">
{if $locale eq 'ja'}
	{literal}
	<style type="text/css">
		*{
			font-family: IPAMonaPGothic, Mona, 'MS PGothic', YOzFontAA97 !important;
			font-size: 1em;
		}
	</style>
	{/literal}

{/if}
<script type="text/javascript" src="{%KU_WEBFOLDER}lib/javascript/gettext.js"></script>
<script type="text/javascript" src="{%KU_WEBFOLDER}lib/javascript/kusaba.js"></script>
<script type="text/javascript" src="{%KU_WEBFOLDER}lib/javascript/menu.js"></script>
<script type="text/javascript" src="{%KU_WEBFOLDER}lib/javascript/protoaculous-compressed.js"></script>
<script type="text/javascript"><!--
	        var ku_boardspath = '{%KU_BOARDSPATH}';
	        var ku_cgipath = '{%KU_CGIPATH}';
	        var val = 0;
	        var loopid;
 
	//--></script>
<script type="text/javascript">
<!--
    function toggle_boards(button, area) {
	var tog=document.getElementById(area);
	if(tog.style.display)    {
		tog.style.display='';
	}    else {
		tog.style.display='none';
	}
	button.innerHTML=(tog.style.display)?'&plus;':'&minus;';
	set_cookie('nav_show_'+area, tog.style.display?'0':'1', 30);
//-->
</script>


</head>
<body>
  <header role="banner">
     <center><h1>{%KU_NAME}</h1><br /><h3>{%KU_SLOGAN}</h3></center>
  </header>
    <br class="clear" />
  <section id="recent">
    <section id="posts">
      <h3>Recent Posts</h3>
			<ul>
      {foreach item=post from=$recentposts}
<li><a  href="{%KU_WEBFOLDER}{$post.boardname}/res/{if $post.parentid eq 0}{$post.id}.html{else}{$post.parentid}.html#{$post.id}{/if}" onclick="return highlight('{$post.id}', true);" class="ref|{$post.boardname}|{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}|{$post.id}">&gt;&gt;&gt;/{$post.boardname}/{$post.id}</a> - {$post.message|strip_tags|stripslashes|substr:0:60}{if strlen($post.message) > 60}...{/if}<br /></li>
                      {/foreach}
		</ul>
           </section>
      </section>
    <section id="images">
      <h3>Stats</h3>
			<ul>
                        {t}Total Posts: {$totalposts}<br />
                        Current Number of Users: {$currentusers}<br />
                        Active Content: {$activecontent}{/t}
			</ul>
    </section>
    <br class="clear" />
  </section>
  <div class="wrap">
  <section id="news">
    <header>
      <ul>
        <li{if $dwoo.get.view == ''} class="selected"{/if}>{if $dwoo.get.view != ''}<a href="{%KU_WEBFOLDER}{if isset($dwoo.get.frame)}?frame{/if}">{/if}News{if $dwoo.get.view != ''}</a>{/if}</li>
        <li{if $dwoo.get.view == 'faq'} class="selected"{/if}>{if $dwoo.get.view != 'faq'}<a href="{%KU_WEBFOLDER}index.php?view=faq{if isset($dwoo.get.frame)}&frame{/if}">{/if}FAQ{if $dwoo.get.view != 'faq'}</a>{/if}</li>
        <li{if $dwoo.get.view == 'rules'} class="selected"{/if}>{if $dwoo.get.view != 'rules'}<a href="{%KU_WEBFOLDER}index.php?view=rules{if isset($dwoo.get.frame)}&frame{/if}">{/if}Rules{if $dwoo.get.view != 'rules'}</a>{/if}</>
        <li{if $dwoo.get.view == 'frames'} class="selected"{/if}>{if $dwoo.get.view != 'frames'}<a href="{%KU_WEBFOLDER}kusaba.php">{/if}Frames{if $dwoo.get.view != 'frames'}</a>{/if}</li>      
</ul>
      <br class="clear" />
    </header>
    
{foreach item=item from=$entries}
    <article>
      <h4 id="id{$item.id}">
       <a class="permalink" href="#id{$item.id}">#</a> <span class="newssub">{$item.subject} {if $dwoo.get.view == 'news' || $dwoo.get.view == ''} by {if $item.email != ''} <a href="mailto:{$item.email}">{/if} {$item.poster} {if $item.email != ''} </a>{/if}  - {$item.timestamp|date_format:"%D @ %I:%M %p %Z"} {/if} </span>
      </h4>
      
      <p>
        {preg_replace("/^<br \/>/", "",str_replace("</p>", "", str_replace("<p>", "<br />", $item.message)))}
      </p>
    </article>
{/foreach} 
    
{if $dwoo.get.view == ''}
    <footer>
  {for i 0 $pages}
      [ {if $dwoo.get.page != $i}<a href="{%KU_WEBFOLDER}index.php?page={$i}{if isset($dwoo.get.frame)}&frame{/if}">{/if}{$i}{if $dwoo.get.page != $i}</a>{/if} ]
  {/for}
    </footer>
{/if}
  </section>
  <section id="boardlist">
    <h3>Boards</h3>
{foreach item=section from=$boards}

      <h4><span class="section_toggle" onclick="toggle_boards('{$section.abbreviation}');" title="{t}Click to show/hide{/t}">{if $section.hidden eq 1}+{else}&minus;{/if}</span>&nbsp;{$section.name}</h4>
      <div style="{if $section.hidden eq 1} display: none;{/if}" id="{$section.abbreviation}" name="{$section.abbreviation}">
      <ul>
{foreach item=board from=$section.boards}
        <li><a href="{%KU_WEBFOLDER}{$board.name}/" title="{%KU_NAME} - {$board.desc}">&bull; {$board.desc}
        {if $board.locked eq 1}
               &nbsp;<img src="{%KU_BOARDSPATH}/css/locked.gif" border="0" alt="{t}Locked{/t}">
	{/if}
        </a></li>
{else}
        <li>No boards</li>
{/foreach}
      <br style="{if $section.hidden eq 1} display: none;{/if}" class="clear" />
      </ul>
      </div>

{/foreach}
  </section>
  </div>
  
  <div class="wrap hfix">
  <div class="lcol"></div>
  <div class="rcol"></div>
  </div>
  <footer>
{%KU_NAME}{t} is powered by <a href="http://anonsaba.net/" target="_top">Anonsaba {$version}</a>{/t}
  </footer>
</body>
</html>

