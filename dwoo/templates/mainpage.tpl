<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head>
	<title>{$dwoo.const.KU_NAME}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />




	
	{for style $styles}
				<link rel="{if $styles[$style] neq $dwoo.const.KU_DEFAULTMENUSTYLE}alternate {/if}stylesheet" type="text/css" href="{$dwoo.const.KU_WEBFOLDER}css/site_{$styles[$style]}.css" title="{$styles[$style]|capitalize}" />
	{/for}
<script type="text/javascript"><!--
	var style_cookie_site = "kustyle_site";
//--></script>
<link rel="stylesheet" type="text/css" href="{%KU_WEBFOLDER}css/mainpage.css">
<link rel="shortcut icon" href="{$dwoo.const.KU_WEBFOLDER}favicon.ico" />
<script type="text/javascript" src="{%KU_WEBFOLDER}lib/javascript/gettext.js"></script>
<script type="text/javascript" src="{$dwoo.const.KU_WEBFOLDER}lib/javascript/kusaba.js"></script>
<script type="text/javascript" src="{%KU_WEBFOLDER}lib/javascript/menu.js"></script>
<script type="text/javascript" src="{%KU_WEBFOLDER}lib/javascript/protoaculous-compressed.js"></script>
<script type="text/javascript"><!--
	        var ku_boardspath = '{%KU_BOARDSPATH}';
	        var ku_cgipath = '{%KU_CGIPATH}';
	        var val = 0;
	        var loopid;
 
	//--></script>

</head>
<body>




	<h1>{$dwoo.const.KU_NAME}</h1>
	{if $dwoo.const.KU_SLOGAN neq ''}<h3>{$dwoo.const.KU_SLOGAN}</h3>{/if}
	
	
	<div style='float: left; width: 60%;'>
	<div id='here'></div>
	<div class="content">
		<h2><span class="newssub">title</span><span class='permalink'>&nbsp;</span></h2>
		<strong>SOME TEXTSOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT SOME TEXT!</strong>
	</div><br />

	<div id='polhide'>
	<div class="content">
		<h2><span class="newssub">{$dwoo.const.KU_NAME}</span><span class='permalink'>&nbsp;</span></h2>
{$dwoo.const.KU_NAME} derp derp derp <br><br>
	</div><br /></div>

        <div class="content">
                <h2><span class="newssub">Recent images</span><span class='permalink'>&nbsp;</span></h2>
{foreach item=entrs from=$lastimages}
<div style='float: left; width: 24%; padding-right: 1%'>
{foreach item=entry from=$entrs}
	<a href='/{$entry.name}/res/{if $entry.parentid == 
0}{$entry.id}{else}{$entry.parentid}{/if}.html#{$entry.id}'>
		<img src='/{$entry.name}/thumb/{$entry.file}s.{$entry.file_type}' 
		style='border: 1px solid black; margin: 1px; width: 100%; max-height: 300px;' alt='' /></a><br />
{/foreach}
</div>
{/foreach}
        </div>
	</div>




	<div style='float: left; width: 40%;'>

<div class="content">
 <h2><span class="newssub">Recent posts</span><span class='permalink'>&nbsp;</span></h2>
			
      {foreach item=post from=$recentposts}
<li><a  href="{%KU_WEBFOLDER}{$post.boardname}/res/{if $post.parentid eq 0}{$post.id}.html{else}{$post.parentid}.html#{$post.id}{/if}" onclick="return highlight('{$post.id}', true);" class="ref|{$post.boardname}|{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}|{$post.id}">&gt;&gt;&gt;/{$post.boardname}/{$post.id}</a> - {$post.message|strip_tags|stripslashes|substr:0:130}{if strlen($post.message) > 130}...{/if}<hr /></li>
                      {/foreach}
		
           </section>
      </section>
</div>

<br>

<div class="content" id="news">
   <div class="menu" id="topmenu">
		
		{strip}<ul>
        <li class="{if $dwoo.get.view == ''}current {else}tab {/if}first">{if $dwoo.get.view != ''}<a target="top" href="{%KU_WEBFOLDER}{if isset($dwoo.get.frame)}?frame{/if}">{/if}News{if $dwoo.get.view != ''}</a>{/if}</li>
        <li class="{if $dwoo.get.view == 'faq'}current {else}tab{/if}">{if $dwoo.get.view != 'faq'}<a href="{%KU_WEBFOLDER}mainpage.php?view=faq{if isset($dwoo.get.frame)}&frame{/if}">{/if}FAQ{if $dwoo.get.view != 'faq'}</a>{/if}</li>
        <li class="{if $dwoo.get.view == 'rules'}current {else}tab {/if}">{if $dwoo.get.view != 'rules'}<a href="{%KU_WEBFOLDER}mainpage.php?view=rules{if isset($dwoo.get.frame)}&frame{/if}">{/if}Rules{if $dwoo.get.view != 'rules'}</a>{/if}</>
</ul>{/strip}
      <br/></div>
    
    
{foreach item=item from=$entries}
    <article>
      <h2 id="id{$item.id}"><span class="newssub">
        {$item.subject} {if $dwoo.get.view == 'news' || $dwoo.get.view == ''} by {if $item.email != ''} <a href="mailto:{$item.email}">{/if} {$item.poster} {if $item.email != ''} </a>{/if}  - {$item.timestamp|date_format:"%D @ %I:%M %p %Z"} {/if} </span><a class="permalink" href="#id{$item.id}">#</a>
      </h2>
      
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
  </div>



</div>
	
</body>
</html>
