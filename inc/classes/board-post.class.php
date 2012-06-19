<?php
/*
 * This file is part of kusaba.
 *
 * kusaba is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * kusaba is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * kusaba; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
/**
 * Board and Post classes
 *
 * @package kusaba
 */
/**
 * Board class
 *
 * Contains all board configurations.  This class handles all board page
 * rendering, using the templates
 *
 * @package kusaba
 *
 * TODO: replace repetitive code blocks with functions.
 */
class Board {
	/* Declare the public variables */
	/**
	 * Array to hold the boards settings
	 */
	var $board = array();
	/**
	 * Archive directory, set when archiving is enabled
	 *
	 * @var string Archive directory
	 */
	var $archive_dir;
	/**
	 * Dwoo class
	 *
	 * @var class Dwoo
	 */
	var $dwoo;
	/**
	 * Dwoo data class
	 *
	 * @var class Dwoo
	 */
	var $dwoo_data;
	/**
	 * Load balancer class
	 *
	 * @var class Load balancer
	 */
	var $loadbalancer;

	/**
	 * Initialization function for the Board class, which is called when a new
	 * instance of this class is created. Takes a board directory as an
	 * argument
	 *
	 * @param string $board Board name/directory
	 * @param boolean $extra grab additional data for page generation purposes. Only false if all that's needed is the board info.
	 * @return class
	 */
	function Board($board, $extra = true) {
		global $tc_db, $CURRENTLOCALE;

		// If the instance was created with the board argument present, get all of the board info and configuration values and save it inside of the class
		if ($board!='') {
			$query = "SELECT * FROM `".KU_DBPREFIX."boards` WHERE `name` = ".$tc_db->qstr($board)." LIMIT 1";
			$results = $tc_db->GetAll($query);
			foreach ($results[0] as $key=>$line) {
				if (!is_numeric($key)) {
					$this->board[$key] = $line;
				}
			}
			// Type
			$types = array('img', 'txt', 'oek', 'upl');
			$this->board['text_readable'] = $types[$this->board['type']];
			if ($extra) {
				// Boardlist
				$this->board['boardlist'] = $this->DisplayBoardList();

				// Get the unique posts for this board
				$this->board['uniqueposts']   = $tc_db->GetOne("SELECT COUNT(DISTINCT `ipmd5`) FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $this->board['id']. " AND  `IS_DELETED` = 0");
			
				if($this->board['type'] != 1) {
					$this->board['filetypes_allowed'] = $tc_db->GetAll("SELECT ".KU_DBPREFIX."filetypes.filetype FROM ".KU_DBPREFIX."boards, ".KU_DBPREFIX."filetypes, ".KU_DBPREFIX."board_filetypes WHERE ".KU_DBPREFIX."boards.id = " . $this->board['id'] . " AND ".KU_DBPREFIX."board_filetypes.boardid = " . $this->board['id'] . " AND ".KU_DBPREFIX."board_filetypes.typeid = ".KU_DBPREFIX."filetypes.id ORDER BY ".KU_DBPREFIX."filetypes.filetype ASC;");
				}
				
				if ($this->board['locale'] && $this->board['locale'] != KU_LOCALE) {
					changeLocale($this->board['locale']);
				}
			}
			$this->board['loadbalanceurl_formatted'] = ($this->board['loadbalanceurl'] != '') ? substr($this->board['loadbalanceurl'], 0, strrpos($this->board['loadbalanceurl'], '/')) : '';

			if ($this->board['loadbalanceurl'] != '' && $this->board['loadbalancepassword'] != '') {
				require_once KU_ROOTDIR . 'inc/classes/loadbalancer.class.php';
				$this->loadbalancer = new Load_Balancer;

				$this->loadbalancer->url = $this->board['loadbalanceurl'];
				$this->loadbalancer->password = $this->board['loadbalancepassword'];
			}
		}
	}

	function __destruct() {
		changeLocale(KU_LOCALE);
	}
	
	/**
	 * Regenerate all board and thread pages
	 */
	function RegenerateAll() {
		$this->RegeneratePages();
		$this->RegenerateThreads();
	}

	/**
	 * Regenerate all pages
	 */
	function RegeneratePages() {
		global $tc_db, $CURRENTLOCALE;

		$this->InitializeDwoo();

		$this->board['filetypes'] = array();
		foreach ($tc_db->GetAll('SELECT `filetype` FROM `' . KU_DBPREFIX . 'embeds`') as $line) {
			$this->board['filetypes'][] = $line['filetype'];
		}

		$this->dwoo_data->assign('filetypes', $this->board['filetypes']);
		//$maxpages = $this->board['maxpages']; //Does nothing
		
		if ($this->board['type'] == 1) {
			$postsperpage = KU_THREADSTXT;
		} elseif ($this->board['type'] == 3) {
			$postsperpage = 30;
		} else {
			$postsperpage = KU_THREADS;
		}
		$i = 0;
		$liststooutput = 0;
		$totalpages = calculatenumpages($this->board['type'], (array_pop(array_pop($tc_db->GetAll('SELECT COUNT(*) FROM `' . KU_DBPREFIX . 'posts` WHERE `boardid` = ' . $this->board['id'] . ' AND `parentid` = 0 AND `IS_DELETED` = 0')))));
		if ($totalpages == '-1') {
			$totalpages = 0;
		}
		$this->dwoo_data->assign('numpages', $totalpages);
		while ($i <= $totalpages) {
			$newposts = Array();
			$this->dwoo_data->assign('thispage', $i);

			$executiontime_start_page = microtime_float();
			foreach ($tc_db->GetAll('SELECT * FROM `' . KU_DBPREFIX . 'posts` WHERE `boardid` = ' . $this->board['id'] . ' AND `parentid` = 0 AND `IS_DELETED` = 0 ORDER BY `stickied` DESC, `bumped` DESC LIMIT '. ($postsperpage).' OFFSET '. $postsperpage * $i) as $k=>$thread) {
				// If the thread is on the page set to mark, && hasn't been marked yet, mark it
				if ($thread['deleted_timestamp'] == 0 && $this->board['markpage'] > 0 && $i >= $this->board['markpage']) {
					$tc_db->Execute('UPDATE `'.KU_DBPREFIX.'posts` SET `deleted_timestamp` = \'' . (time() + 7200) . '\' WHERE `boardid` = ' . $tc_db->quote($this->board['id']).' AND `id` = \'' . $thread['id'] . '\'');
					clearPostCache($thread['id'], $this->board['name']);
					$this->RegenerateThreads($thread['id']);
					$this->dwoo_data->assign('replythread', 0);
				}
				$thread = $this->BuildPost($thread, true);
				
				if ($this->board['type'] != 3) {
					$omitids = '""';
					$posts = $tc_db->GetAll('SELECT * FROM `' . KU_DBPREFIX . 'posts` WHERE `boardid` = ' . $this->board['id'].' AND `parentid` = '.$thread['id'].' ' . (($this->board['type'] != 1) ? ('AND `IS_DELETED` = 0') : ('')) . ' ORDER BY `id` DESC LIMIT '.(($thread['stickied'] == 1) ? (KU_REPLIESSTICKY) : (KU_REPLIES)));
					foreach ($posts as $key=>$post) {
						$omitids .= ', '.$post['id'];
						$posts[$key] = $this->BuildPost($post, true);
					}

					$posts = array_reverse($posts);
					array_unshift($posts, $thread);
					$newposts[] = $posts;
					unset($posts);
				} else {
					if (!$thread['tag']) $thread['tag'] = '*';
					$newposts[] = $thread;
				}
				$replycount = Array();
				if ($this->board['type'] == 1 || $this->board['type'] == 3 ) {
					$replycount['replies'] = $tc_db->GetOne('SELECT COUNT(`id`) FROM `' . KU_DBPREFIX . 'posts` WHERE `boardid` = ' . $tc_db->quote($this->board['id']).' AND `parentid` = ' . $thread['id']);
				} else {
					$posts = $tc_db->GetAll('SELECT `id` FROM `'.KU_DBPREFIX.'posts` WHERE `boardid` = '.$tc_db->quote($this->board['id']).' AND `parentid` = '.$thread['id'].' AND `id` NOT IN (' . $omitids . ') AND `IS_DELETED` = 0');
					$idlist = '""';
					if (is_array($posts)) {
						foreach ($posts as $post) {
							$idlist .= ', '.$post['id'];
						}
						$posts = count($posts);
					} else {
						$posts = '';
					}
					$replycount['replies'] = $posts;
					$replycount['images'] = $tc_db->GetOne('SELECT DISTINCT COUNT(`file_md5`) FROM `'.KU_DBPREFIX.'post_files` WHERE `boardid` = '.$this->board['id'].'  AND `id` IN ('.$idlist.') AND `IS_DELETED` = 0 AND `id` NOT IN (' . $omitids . ') AND `file_md5` != \'\' AND `file_type` NOT IN (\'you\', \'goo\')');
				}
				if ($this->board['type'] == 3 ){
					$newposts[$k]['replies'] = (isset($replycount['replies']) ? $replycount['replies'] : '');
					$newposts[$k]['images'] = (isset($replycount['images']) ? $replycount['images'] : '');
				} else {
					$newposts[$k][0]['replies'] = (isset($replycount['replies']) ? $replycount['replies'] : '');
					$newposts[$k][0]['images'] = (isset($replycount['images']) ? $replycount['images'] : '');
				}
			}
			if ($this->board['type'] == 0 && !isset($embeds)) {
				$this->dwoo_data->assign('embeds', $tc_db->GetAll('SELECT * FROM `' . KU_DBPREFIX . 'embeds`'));
			}
			if (!isset($header)){
				$header = str_replace('<!sm_threadid>', 0, $this->PageHeader());
			}
			if (!isset($postbox)) {
				$postbox = str_replace('<!sm_threadid>', 0, $this->Postbox());
			}
			$this->dwoo_data->assign('posts', $newposts);
			$this->dwoo_data->assign('file_path', getCLBoardPath($this->board['name'], $this->board['loadbalanceurl_formatted'], ''));
			$content = $header.$postbox.$this->dwoo->get(KU_TEMPLATEDIR . '/' . $this->board['text_readable'] . '_board_page.tpl', $this->dwoo_data).$this->Footer(false, (microtime_float() - $executiontime_start_page), (($this->board['type'] == 1) ? (true) : (false)));
			$content = str_replace('\t', '',$content);
			$content = str_replace('&nbsp;\r\n', '&nbsp;',$content);

			if ($i == 0) {
				$this->PrintPage(KU_BOARDSDIR.$this->board['name'].'/'.KU_FIRSTPAGE, $content, $this->board['name']);
			} else {
				$this->PrintPage(KU_BOARDSDIR.$this->board['name'].'/'.$i.'.html', $content, $this->board['name']);
			}
			$i++;
		}
		// If text board, rebuild thread list html files
		if ($this->board['type'] == 1) {
			$numpostsleft = $tc_db->GetOne('SELECT COUNT(*) FROM `' . KU_DBPREFIX . 'posts` WHERE `boardid` = ' . $this->board['id'] . ' AND `IS_DELETED` = 0 AND `parentid` = 0');
			$liststooutput = floor(($numpostsleft-1) / 40);
			$this->dwoo_data->assign('numpages', $liststooutput+1);
			$listpage = 0;
			$currentpostwave = 0;
			while ($numpostsleft>0) {
				$this->dwoo_data->assign('thispage', $listpage+1);
				$executiontime_start_list = microtime_float();
				$page = $this->PageHeader(0, $currentpostwave, $liststooutput);
				$this->Footer(false, (microtime_float()-$executiontime_start_list), true);
				if ($listpage==0) {
					$this->PrintPage(KU_BOARDSDIR.$this->board['name'].'/list.html', $page, $this->board['name']);
				} else {
					$this->PrintPage(KU_BOARDSDIR.$this->board['name'].'/list'.($listpage+1).'.html', $page, $this->board['name']);
				}
				$currentpostwave += 40;
				$numpostsleft -= 40;
				$listpage++;
			}
		}
		// If the board has catalog mode enabled, build it
		if ($this->board['enablecatalog'] == 1 && ($this->board['type'] == 0 || $this->board['type'] == 2)) {
			$executiontime_start_catalog = microtime_float();
			$catalog_head = $this->PageHeader().
			'&#91;<a href="' . KU_BOARDSFOLDER . $this->board['name'] . '">'._gettext('Return').'</a>&#93; <div class=\'catalogmode\'>'._gettext('Catalog Mode').'</div>' . "\n" .
			'<table border=\'1\' align=\'center\'>' . "\n" . '<tr>' . "\n";
			$catalog_page = '';
			$results = $tc_db->GetAll('SELECT `id` , `subject` FROM `' . KU_DBPREFIX . 'posts` WHERE `boardid` = ' . $this->board['id'] . ' AND `IS_DELETED` = 0 AND `parentid` = 0 ORDER BY `stickied` DESC, `bumped` DESC');
			$numresults = count($results);
			if ($numresults > 0) {
				$celnum = 0;
				$trbreak = 0;
				$row = 1;
				// Calculate the number of rows we will actually output
				foreach ($results as $line) {
					$celnum++;
					$trbreak++;
					if ($trbreak == 13 && $celnum != $numresults) {
						$catalog_page .= '</tr>' . "\n" . '<tr>' . "\n";
						$row++;
						$trbreak = 1;
					}
					if ($row <= max(1, (($numresults - ($numresults % 12)) / 12))) {
						$catalog_page .= '<td valign=\'middle\'>' . "\n" .
						'<a href=\'' . KU_BOARDSFOLDER . $this->board['name'] . '/res/' . $line['id'] . '.html\'';
						if (!empty($line['subject'])) {
							$catalog_page .= ' title=\'' . $line['subject'] . '\'';
						}
						$catalog_page .= '>';
						
						$files = $tc_db->GetAll('SELECT * FROM `'.KU_DBPREFIX.'post_files` WHERE `boardid` = ' . $this->board['id'] . ' AND `id` = ' . $line['id']);
						if (isset($files[0])) {
							if ($files[0]['file_type'] == 'jpg' || $files[0]['file_type'] == 'png' || $files[0]['file_type'] == 'gif') {
								$file_path = getCLBoardPath($this->board['name'], $this->board['loadbalanceurl_formatted'], $this->archive_dir);
								$catalog_page .= '<img src=\'' . $file_path . '/thumb/' . $files[0]['file'] . 'c.' . $files[0]['file_type'] . '\' alt=\'' . $files[0]['id'] . '\' border=\'0\' />';
								if (count($files) > 1) {
									array_shift($files);
									foreach ($files as $file) {
										$catalog_page .= '<img src=\'' . $file_path . '/thumb/' . $file['file'] . 'c.' . $file['file_type'] . '\' alt=\'' . $file['id'] . '\' border=\'0\' />';
									}
								}
							} else {
								$catalog_page .= _gettext('File');
							}
						} elseif (isset($files[0]['file']) && $files[0]['file'] == 'removed') {
							$catalog_page .= 'Rem.';
						} else {
							$catalog_page .= _gettext('None');
						}
						
						$catalog_page .= '</a><br />' . "\n" . '<small>' . $tc_db->GetOne('SELECT COUNT(*) FROM `'.KU_DBPREFIX.'posts` WHERE `boardid` = ' . $this->board['id'] . ' AND `IS_DELETED` = 0 AND `parentid` = ' . $line['id']) . '</small>' . "\n" . '</td>' . "\n";
					}
				}
			} else {
				$catalog_page .= '<td>'."\n"._gettext('No threads.')."\n".'</td>'."\n";
			}
			unset($results);
			$catalog_page .= '</tr>'."\n".'</table><br /><hr />'.$this->Footer(false, (microtime_float()-$executiontime_start_catalog));
			$this->PrintPage(KU_BOARDSDIR.$this->board['name'].'/catalog.html', $catalog_head.$catalog_page, $this->board['name']);
		}
		// Delete old pages
		$dir = KU_BOARDSDIR.$this->board['name'];
		$files = glob($dir.'/*.html');
		if (is_array($files)) {
			foreach ($files as $htmlfile) {
				if (preg_match('/[0-9+].html/', $htmlfile)) {
					if (substr(basename($htmlfile), 0, strpos(basename($htmlfile), '.html'))>$totalpages) {
						@unlink($htmlfile);
					}
				}
				if (preg_match('/list[0-9+].html/', $htmlfile)) {
					if (substr(basename($htmlfile), 4, strpos(basename($htmlfile), '.html'))>($liststooutput+1)) {
						@unlink($htmlfile);
					}
				}
				if (preg_match('/catalog.html/', $htmlfile)) {
					if (!($this->board['enablecatalog'] == 1 && ($this->board['type'] == 0 || $this->board['type'] == 2))) {
						@unlink($htmlfile);
					}
				}
			}
		}
	}
	/**
	 * Regenerate each thread's corresponding html file, starting with the most recently bumped
	 */
	function RegenerateThreads($id = 0) {
		global $tc_db, $CURRENTLOCALE;
		
		require_once(KU_ROOTDIR."lib/dwoo.php");
		if (!isset($this->dwoo)) { $this->dwoo = New Dwoo; $this->dwoo_data = new Dwoo_Data(); $this->InitializeDwoo(); }
		$embeds = Array();
		$numimages = 0;
		if ($this->board['type'] != 1 && !$embeds) {
				$embeds = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "embeds`");
				$this->dwoo_data->assign('embeds', $embeds);
				foreach ($embeds as $embed) {
					$this->board['filetypes'][] = $embed['filetype'];
				}
				$this->dwoo_data->assign('filetypes', $this->board['filetypes']);
		}
		if ($id == 0) {
			// Build every thread
			$header = $this->PageHeader(1);
			if ($this->board['type'] != 2){
				$postbox = $this->Postbox(1);
			}
			$threads = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $this->board['id'] . " AND `parentid` = 0 AND `IS_DELETED` = 0 ORDER BY `id` DESC");

			if (count($threads) > 0) {
				foreach($threads as $thread) {
					$numimages = 0;
					$executiontime_start_thread = microtime_float();
					$posts = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $this->board['id'] . " AND (`id` = " . $thread['id'] . " OR `parentid` = " . $thread['id'] . ") " . (($this->board['type'] != 1) ? ("AND `IS_DELETED` = 0") : ("")) . " ORDER BY `id` ASC");
					if ($this->board['type'] != 1 || ((isset($posts[0]['IS_DELETED']) && $posts[0]['IS_DELETED'] == 0) || (isset($posts[0]['is_deleted']) && $posts[0]['is_deleted'] == 0))) { 
						// There might be a chance that the post was deleted during another RegenerateThreads() session, if there are no posts, move on to the next thread.
						if(count($posts) > 0){
							foreach ($posts as $key=>$post) {
								$files = $tc_db->GetAll("SELECT `file_md5` FROM `".KU_DBPREFIX."post_files` WHERE `boardid` = ".$this->board['id']." AND `id` = ".$post['id']." AND `file_type` IN ('gif', 'jpg', 'png') AND `file_md5` != ''");
								$numimages += count($files);
								$posts[$key] = $this->BuildPost($post, false);
							}
							$header_replaced = str_replace("<!sm_threadid>", $thread['id'], $header);
							$this->dwoo_data->assign('numimages', $numimages);
							$this->dwoo_data->assign('replythread', $thread['id']);
							$this->dwoo_data->assign('posts', $posts);
							$this->dwoo_data->assign('file_path', getCLBoardPath($this->board['name'], $this->board['loadbalanceurl_formatted'], ''));
							if ($this->board['type'] != 2){
								$postbox_replaced = str_replace("<!sm_threadid>", $thread['id'], $postbox);
							}
							else {
								$postbox_replaced = $this->Postbox($thread['id']);
							}
							$reply	 = $this->dwoo->get(KU_TEMPLATEDIR . '/' . $this->board['text_readable'] . '_reply_header.tpl', $this->dwoo_data);
							$content = $this->dwoo->get(KU_TEMPLATEDIR . '/' . $this->board['text_readable'] . '_thread.tpl', $this->dwoo_data);
							if (!isset($footer)) $footer = $this->Footer(false, (microtime_float() - $executiontime_start_thread), (($this->board['type'] == 1) ? (true) : (false)));
							$content = $header_replaced.$reply.$postbox_replaced.$content.$footer;

							$content = str_replace("\t", '',$content);
							$content = str_replace("&nbsp;\r\n", '&nbsp;',$content);

							$this->PrintPage(KU_BOARDSDIR . $this->board['name'] . $this->archive_dir . '/res/' . $thread['id'] . '.html', $content, $this->board['name']);
							if (KU_FIRSTLAST) {

								$replycount = (count($posts)-1);
								if ($replycount > 50) {
									$this->dwoo_data->assign('replycount', $replycount);
									$this->dwoo_data->assign('modifier', "last50");

									// Grab the last 50 replies
									$posts50 = array_slice($posts, -50, 50);

									// Add on the OP
									array_unshift($posts50, $posts[0]);
									
									$this->dwoo_data->assign('posts', $posts50);

									$content = $this->dwoo->get(KU_TEMPLATEDIR . '/img_thread.tpl', $this->dwoo_data);
									$content = $header_replaced.$reply.$postbox_replaced.$content.$footer;
									$content = str_replace("\t", '',$content);
									$content = str_replace("&nbsp;\r\n", '&nbsp;',$content);

									unset($posts50);

									$this->PrintPage(KU_BOARDSDIR . $this->board['name'] . $this->archive_dir . '/res/' . $thread['id'] . '+50.html', $content, $this->board['name']);
									if ($replycount > 100) {
										$this->dwoo_data->assign('modifier', "first100");

										// Grab the first 100 posts
										$posts100 = array_slice($posts, 0, 100);

										$this->dwoo_data->assign('posts', $posts100);

										$content = $this->dwoo->get(KU_TEMPLATEDIR . '/img_thread.tpl', $this->dwoo_data);
										$content = $header_replaced.$reply.$postbox_replaced.$content.$footer;
										$content = str_replace("\t", '',$content);
										$content = str_replace("&nbsp;\r\n", '&nbsp;',$content);

										unset($posts100);
										
										$this->PrintPage(KU_BOARDSDIR . $this->board['name'] . $this->archive_dir . '/res/' . $thread['id'] . '-100.html', $content, $this->board['name']);
									}
									$this->dwoo_data->assign('modifier', "");
								}
							}
						}
					}
				}
			}
		} else {
			$executiontime_start_thread = microtime_float();
			// Build only that thread
			$thread = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $this->board['id'] . " AND (`id` = " . $id . " OR `parentid` = " . $id . ") " . (($this->board['type'] != 1) ? ("AND `IS_DELETED` = 0") : ("")) . " ORDER BY `id` ASC");
			if ($this->board['type'] != 1 || ((isset($thread[0]['IS_DELETED']) && $thread[0]['IS_DELETED'] == 0) || (isset($thread[0]['is_deleted']) && $thread[0]['is_deleted'] == 0))) { 
				foreach ($thread as $key=>$post) {
					$files = $tc_db->GetAll("SELECT `file_md5` FROM `".KU_DBPREFIX."post_files` WHERE `boardid` = ".$this->board['id']." AND `id` = ".$post['id']." AND `file_type` IN ('gif', 'jpg', 'png') AND `file_md5` != ''");
					$numimages += count($files);
					$thread[$key] = $this->BuildPost($post, false);
				}
				$header = $this->PageHeader($id);
				$postbox = $this->Postbox($id);
				$this->dwoo_data->assign('numimages', $numimages);
				$header = str_replace("<!sm_threadid>", $id, $header);

				$this->dwoo_data->assign('replythread', $id);
				if ($this->board['type'] != 2){
					$postbox = str_replace("<!sm_threadid>", $id, $postbox);
				}

				$this->dwoo_data->assign('threadid', $thread[0]['id']);
				$this->dwoo_data->assign('posts', $thread);
				$this->dwoo_data->assign('file_path', getCLBoardPath($this->board['name'], $this->board['loadbalanceurl_formatted'], ''));
				
				$postbox = $this->dwoo->get(KU_TEMPLATEDIR . '/' . $this->board['text_readable'] . '_reply_header.tpl', $this->dwoo_data).$postbox;
				$content = $this->dwoo->get(KU_TEMPLATEDIR . '/' . $this->board['text_readable'] . '_thread.tpl', $this->dwoo_data);
				
				if (!isset($footer)) $footer = $this->Footer(false, (microtime_float() - $executiontime_start_thread), (($this->board['type'] == 1) ? (true) : (false)));
				$content = $header.$postbox.$content.$footer;

				$content = str_replace("\t", '',$content);
				$content = str_replace("&nbsp;\r\n", '&nbsp;',$content);

				$this->PrintPage(KU_BOARDSDIR . $this->board['name'] . $this->archive_dir . '/res/' . $id . '.html', $content, $this->board['name']);
				if (KU_FIRSTLAST) {
					$replycount = $tc_db->GetOne("SELECT COUNT(`id`) FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $this->board['id'] . " AND `parentid` = " . $id . " AND `IS_DELETED` = 0");
					if ($replycount > 50) {
						$this->dwoo_data->assign('replycount', $replycount);
						$this->dwoo_data->assign('modifier', "last50");

						// Grab the last 50 replies
						$posts50 = array_slice($thread, -50, 50);

						// Add the thread to the top of this, since it wont be included in the result
						array_unshift($posts50, $thread[0]); 

						$this->dwoo_data->assign('posts', $posts50);

						$content = $this->dwoo->get(KU_TEMPLATEDIR . '/img_thread.tpl', $this->dwoo_data);
						$content = $header.$reply.$postbox.$content.$footer;
						$content = str_replace("\t", '',$content);
						$content = str_replace("&nbsp;\r\n", '&nbsp;',$content);

						unset($posts50);					

						$this->PrintPage(KU_BOARDSDIR . $this->board['name'] . $this->archive_dir . '/res/' . $id . '+50.html', $content, $this->board['name']);
						if ($replycount > 100) {
							$this->dwoo_data->assign('modifier', "first100");

							// Grab the first 100 posts
							$posts100 = array_slice($thread, 0, 100);

							$this->dwoo_data->assign('posts', $posts100);

							$this->dwoo_data->assign('posts', $posts);
							$content = $this->dwoo->get(KU_TEMPLATEDIR . '/img_thread.tpl', $this->dwoo_data);
							$content = $header.$reply.$postbox.$content.$footer;
							$content = str_replace("\t", '',$content);
							$content = str_replace("&nbsp;\r\n", '&nbsp;',$content);

							unset($posts100);

							$this->PrintPage(KU_BOARDSDIR . $this->board['name'] . $this->archive_dir . '/res/' . $id . '-100.html', $content, $this->board['name']);
						}
						$this->dwoo_data->assign('modifier', "");
					}
				}
			}
		}
	}

	function BuildPost($post, $page) {
		global $tc_db, $CURRENTLOCALE;
		/*if ($this->board['type'] == 1 && ((isset($post['IS_DELETED']) && $post['IS_DELETED'] == 1) || (isset($post['is_deleted']) && $post['is_deleted'] == 1))) { 
			$post['name'] = '';
			$post['email'] = '';
			$post['tripcode'] = _gettext('Deleted');
			$post['message'] = '<font color="gray">'._gettext('This post has been deleted.').'</font>';
		}*/
		$dateEmail = (empty($this->board['anonymous'])) ? $post['email'] : 0;
		$post['message'] = stripslashes(formatLongMessage($post['message'], $this->board['name'], (($post['parentid'] == 0) ? ($post['id']) : ($post['parentid'])), $page));
		$post['timestamp_formatted'] = formatDate($post['timestamp'], 'post', $CURRENTLOCALE, $dateEmail);
		$post['reflink'] = formatReflink($this->board['name'], (($post['parentid'] == 0) ? ($post['id']) : ($post['parentid'])), $post['id'], $CURRENTLOCALE);
		
		$post['file'] = $post['file_type'] = $post['file_original'] = $post['file_size'] = $post['file_size_formatted'] = $post['image_w'] = $post['image_h'] = $post['thumb_w'] = $post['thumb_h'] = array();
		if (isset($this->board['filetypes']) && isset($this->board['filetypes_allowed'])) {
			$filetypes = array_merge($this->board['filetypes_allowed'], $this->board['filetypes']);
			$ftypes = '"';
			foreach ($this->board['filetypes'] as $filetype) {
				$ftypes .= $filetype.'", "';
			}
			foreach ($this->board['filetypes_allowed'] as $filetype) {
				$ftypes .= $filetype['filetype'].'", "';
			}
			$ftypes = substr($ftypes, 0, -3);
			$files = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."post_files` WHERE `boardid` = " . $this->board['id'] . " AND `id` = " . $post['id'].' AND `file_type` IN ('.$ftypes.')');
			if (count($files) > 0) {
				foreach ($files as $file) {
					if (!in_array($file['file_type'], array('jpg', 'gif', 'png'))&&!in_array($file['file_type'], $this->board['filetypes'])) {
					
						if ($file['file_type'] == 'mp3' && $this->board['loadbalanceurl'] == '') {
							//Grab the ID3 info. TODO: Make this work for load-balanced boards.
							// include getID3() library
							require_once(KU_ROOTDIR . 'lib/getid3/getid3.php');
							// Initialize getID3 engine
							$getID3 = new getID3;
							$post['id3'] = array();
							$post['id3'] = $getID3->analyze(KU_BOARDSDIR.$this->board['name'].'/src/'.$file['file'].'.mp3');
							getid3_lib::CopyTagsToComments($file['id3']);
						}				
					
						if(!isset($filetype_info[$file['file_type']])) $filetype_info[$file['file_type']] = getfiletypeinfo($file['file_type']);
						$file['nonstandard_file'] = KU_WEBPATH . '/inc/filetypes/' . $filetype_info[$file['file_type']][0];
						if($file['thumb_w']!=0&&$file['thumb_h']!=0) {
							if(file_exists(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'s.jpg'))
								$file['nonstandard_file'] = KU_WEBPATH . '/' .$this->board['name'].'/thumb/'.$file['file'].'s.jpg';
							elseif(file_exists(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'s.png'))
								$file['nonstandard_file'] = KU_WEBPATH . '/' .$this->board['name'].'/thumb/'.$file['file'].'s.png';
							elseif(file_exists(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'s.gif'))
								$file['nonstandard_file'] = KU_WEBPATH . '/' .$this->board['name'].'/thumb/'.$file['file'].'s.gif';
							else {
								$file['thumb_w'] = $filetype_info[$file['file_type']][1];
								$file['thumb_h'] = $filetype_info[$file['file_type']][2];
							}
						}
						else {
							$file['thumb_w'] = $filetype_info[$file['file_type']][1];
							$file['thumb_h'] = $filetype_info[$file['file_type']][2];
						}
					}
					$post['file'][] = $file['file'];
					$post['file_type'][] = $file['file_type'];
					$post['file_original'][] = $file['file_original'];
					$post['file_size'][] = $file['file_size'];
					$post['file_size_formatted'][] = $file['file_size_formatted'];
					$post['image_w'][] = $file['image_w'];
					$post['image_h'][] = $file['image_h'];
					$post['thumb_w'][] = $file['thumb_w'];
					$post['thumb_h'][] = $file['thumb_h'];
					$post['nonstandard_file'][] = (isset($file['nonstandard_file'])) ? $file['nonstandard_file'] : '';
					
					if (in_array($file['file_type'], $this->board['filetypes'])) {
						$post['videobox'] = embeddedVideoBox($file);
					}
				}
			}
		}
		return $post;
	}
	/**
	 * Build the page header
	 *
	 * @param integer $replythread The ID of the thread the header is being build for.  0 if it is for a board page
	 * @param integer $liststart The number which the thread list starts on (text boards only)
	 * @param integer $liststooutput The number of list pages which will be generated (text boards only)
	 * @return string The built header
	 */
	function PageHeader($replythread = '0', $liststart = '0', $liststooutput = '-1') {
		global $tc_db, $CURRENTLOCALE;

		$tpl = Array();

		$tpl['htmloptions'] = ((KU_LOCALE == 'he' && empty($this->board['locale'])) || $this->board['locale'] == 'he') ? ' dir="rtl"' : '' ;

		$tpl['title'] = '';

		if (KU_DIRTITLE) {
			$tpl['title'] .= '/' . $this->board['name'] . '/ - ';
		}
		$tpl['title'] .= $this->board['desc'];

		$ad_top = 185;
		$ad_right = 25;
		if ($this->board['type']==1) {
			$ad_top -= 50;
		} else {
			if ($replythread!=0) {
				$ad_top += 50;
			}
		}
		if ($this->board['type']==2) {
			$ad_top += 40;
		}
		$this->dwoo_data->assign('title', $tpl['title']);
		$this->dwoo_data->assign('htmloptions', $tpl['htmloptions']);
		$this->dwoo_data->assign('locale', $CURRENTLOCALE);
		$this->dwoo_data->assign('ad_top', $ad_top);
		$this->dwoo_data->assign('ad_right', $ad_right);
		$this->dwoo_data->assign('board', $this->board);
		$this->dwoo_data->assign('replythread', $replythread);
		if ($this->board['type'] != 1) {
			$topads = $tc_db->GetOne("SELECT code FROM `" . KU_DBPREFIX . "ads` WHERE `position` = 'sfwtop' AND `disp` = '1'");
			$nsfwtop = $tc_db->GetOne("SELECT code FROM `" . KU_DBPREFIX . "ads` WHERE `position` = 'nsfwtop' AND `disp` = '1'");
			$sfwpost = $tc_db->GetOne("SELECT code FROM `" . KU_DBPREFIX . "ads` WHERE `position` = 'sfwpost' AND `disp` = '1'");
			$nsfwpost = $tc_db->GetOne("SELECT code FROM `" . KU_DBPREFIX . "ads` WHERE `position` = 'nsfwpost' AND `disp` = '1'");
			$this->dwoo_data->assign('topads', $topads);
			$this->dwoo_data->assign('nsfwtop', $nsfwtop);
			$this->dwoo_data->assign('sfwpost', $sfwpost);
			$this->dwoo_data->assign('nsfwpost', $nsfwpost);
			$this->dwoo_data->assign('ku_styles', explode(':', KU_STYLES));
			$this->dwoo_data->assign('ku_defaultstyle', (!empty($this->board['defaultstyle']) ? ($this->board['defaultstyle']) : (KU_DEFAULTSTYLE)));
		} else {
			$this->dwoo_data->assign('ku_styles', explode(':', KU_TXTSTYLES));
			$this->dwoo_data->assign('ku_defaultstyle', (!empty($this->board['defaultstyle']) ? ($this->board['defaultstyle']) : (KU_DEFAULTTXTSTYLE)));
		}
		$this->dwoo_data->assign('boardlist', $this->board['boardlist']);

		$global_header = $this->dwoo->get(KU_TEMPLATEDIR . '/global_board_header.tpl', $this->dwoo_data);

		if ($this->board['type'] != 1) {
			$header = $this->dwoo->get(KU_TEMPLATEDIR . '/' . $this->board['text_readable'] . '_header.tpl', $this->dwoo_data);
		} else {
			if ($liststooutput == -1) {
				$this->dwoo_data->assign('isindex', true);
			} else {
				$this->dwoo_data->assign('isindex', false);
			}
			if ($replythread != 0) $this->dwoo_data->assign('isthread', true);
			$header = $this->dwoo->get(KU_TEMPLATEDIR . '/txt_header.tpl', $this->dwoo_data);

			if ($replythread == 0) {
				$startrecord = ($liststooutput >= 0 || $this->board['compactlist']) ? 40 : KU_THREADSTXT ;
				$threads = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $tc_db->qstr($this->board['id']) . " AND `parentid` = 0 AND `IS_DELETED` = 0 ORDER BY `stickied` DESC, `bumped` DESC LIMIT " . $startrecord . " OFFSET " . $liststart);
				foreach($threads AS $key=>$thread) {
					$replycount = $tc_db->GetOne("SELECT COUNT(`id`) FROM `" . KU_DBPREFIX . "posts` WHERE `boardid` = " . $tc_db->qstr($this->board['id']) . " AND `parentid` = " . $thread['id']);
					$threads[$key]['replies'] = $replycount;
				}
				$this->dwoo_data->assign('threads', $threads);
				$header .= $this->dwoo->get(KU_TEMPLATEDIR . '/txt_threadlist.tpl', $this->dwoo_data);
			}
		}

		return $global_header.$header;
	}

	/**
	 * Build the page header for an oekaki posting
	 *
	 * @param integer $replyto The ID of the thread being replied to.  0 for a new thread
	 */
	function OekakiHeader($replyto, $postoek) {
		$executiontime_start = microtime_float();
		$this->InitializeDwoo();

		$page = $this->PageHeader();
		$this->dwoo_data->assign('replythread', $replyto);
		$page .= $this->Postbox();

		$executiontime_stop = microtime_float();

		$page .= $this->Footer(false, ($executiontime_stop - $executiontime_start));

		$this->PrintPage('', $page, true);
	}

	/**
	 * Generate the postbox area
	 *
	 * @param integer $replythread The ID of the thread being replied to.  0 if not replying
	 * @param string $postboxnotice The postbox notice
	 * @return string The generated postbox
	 */
	function Postbox($replythread = 0) {
		global $tc_db;
		if (KU_BLOTTER && $this->board['type'] != 1) {
			$this->dwoo_data->assign('blotter', getBlotter());
			$this->dwoo_data->assign('blotter_updated', getBlotterLastUpdated());
		}
		$postbox = '';
		if ($this->board['type'] == 2 && $replythread > 0) {
			$oekposts = $tc_db->GetAll("SELECT `id` FROM `" . KU_DBPREFIX."posts` WHERE `boardid` = " . $this->board['id']." AND (`id` = ".$replythread." OR `parentid` = ".$replythread.") AND `file` != '' AND `file` != 'removed' AND `file_type` IN ('jpg', 'gif', 'png') AND `IS_DELETED` = 0 ORDER BY `parentid` ASC, `timestamp` ASC");
			$this->dwoo_data->assign('oekposts', $oekposts);
		}
		if(($this->board['type'] == 1 && $replythread == 0) || $this->board['type'] != 1) {
			$postbox .= $this->dwoo->get(KU_TEMPLATEDIR . '/' . $this->board['text_readable'] . '_post_box.tpl', $this->dwoo_data);
		}
		return $postbox;
	}

	/**
	 * Display the user-defined list of boards found in boards.html
	 *
	 * @param boolean $is_textboard If the board this is being displayed for is a text board
	 * @return string The board list
	 */
	function DisplayBoardList($is_textboard = false) {
		if (KU_GENERATEBOARDLIST) {
			global $tc_db;

			$output = '';
			$results = $tc_db->GetAll("SELECT `id` FROM `" . KU_DBPREFIX . "sections` ORDER BY `order` ASC");
			$boards = array();
			foreach($results AS $line) {
				$results2 = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "boards` WHERE `section` = '" . $line['id'] . "' ORDER BY `order` ASC, `name` ASC");
				foreach($results2 AS $line2) {
					$boards[$line['id']][$line2['id']]['name'] = htmlspecialchars($line2['name']);
					$boards[$line['id']][$line2['id']]['desc'] = htmlspecialchars($line2['desc']);
				}
			}
		} else {
			$boards = KU_ROOTDIR . 'boards.html';
		}

		return $boards;
	}


	/**
	 * Display the page footer
	 *
	 * @param boolean $noboardlist Force the board list to not be displayed
	 * @param string $executiontime The time it took the page to be created
	 * @param boolean $hide_extra Hide extra footer information, and display the manage link
	 * @return string The generated footer
	 */
	function Footer($noboardlist = false, $executiontime = '', $hide_extra = false) {
		global $tc_db, $dwoo, $dwoo_data;

		$footer = '';
		$version = $tc_db->GetOne("SELECT `version` FROM `".KU_DBPREFIX."options`");
		$this->dwoo_data->assign('version', $version);

		if ($hide_extra || $noboardlist) $this->dwoo_data->assign('boardlist', '');

		if ($executiontime != '') $this->dwoo_data->assign('executiontime', round($executiontime, 2));

		$botcheck = $this->board['name'];		
		$botads = $tc_db->GetOne("SELECT code FROM `" . KU_DBPREFIX . "ads` WHERE `position` = 'sfwbot' AND `disp` = '1'");
		$nsfwbot = $tc_db->GetOne("SELECT code FROM `" . KU_DBPREFIX . "ads` WHERE `position` = 'nsfwbot' AND `disp` = '1'");
		$this->dwoo_data->assign('botcheck', $botcheck);
		$this->dwoo_data->assign('botads', $botads);
		$this->dwoo_data->assign('nsfwbot', $nsfwbot);
		$footer = $this->dwoo->get(KU_TEMPLATEDIR . '/' . $this->board['text_readable'] . '_footer.tpl', $this->dwoo_data);
		
		$footer .= $this->dwoo->get(KU_TEMPLATEDIR . '/global_board_footer.tpl', $this->dwoo_data);

		return $footer;
	}

	/**
	 * Finalize the page and print it to the specified filename
	 *
	 * @param string $filename File to print the page to
	 * @param string $contents Page contents
	 * @param string $board Board which the file is being generated for
	 * @return string The page contents, if requested
	 */
	function PrintPage($filename, $contents, $board) {

		if ($board !== true) {
			print_page($filename, $contents, $board);
		} else {
			echo $contents;
		}
	}

	/**
	 * Initialize the instance of smary which will be used for generating pages
	 */
	function InitializeDwoo() {

		require_once KU_ROOTDIR . 'lib/dwoo.php';
		$this->dwoo = new Dwoo();
		$this->dwoo_data = new Dwoo_Data();

		$this->dwoo_data->assign('cwebpath', getCWebpath());
		$this->dwoo_data->assign('boardpath', getCLBoardPath());
	}

	/**
	 * Enable/disable archive mode
	 *
	 * @param boolean $mode True/false for enabling/disabling archive mode
	 */
	function ArchiveMode($mode) {
		$this->archive_dir = ($mode && $this->board['enablearchiving'] == 1) ? '/arch' : '';
	}
}

/**
 * Post class
 *
 * Used for post insertion, deletion, and reporting.
 *
 * @package kusaba
 */
class Post extends Board {
	// Declare the public variables
	var $post = Array();

	function Post($postid, $board, $boardid, $is_inserting = false) {
		global $tc_db;

		$results = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."posts` WHERE `boardid` = '" . $boardid . "' AND `id` = ".$tc_db->qstr($postid)." LIMIT 1");
		if (count($results)==0&&!$is_inserting) {
			exitWithErrorPage('Invalid post ID.');
		} elseif ($is_inserting) {
			$this->Board($board, false);
		} else {
			foreach ($results[0] as $key=>$line) {
				if (!is_numeric($key)) $this->post[$key] = $line;
			}
			$results = $tc_db->GetAll("SELECT `cleared` FROM `".KU_DBPREFIX."reports` WHERE `postid` = ".$tc_db->qstr($this->post['id'])." LIMIT 1");
			if (count($results)>0) {
				foreach($results AS $line) {
					$this->post['isreported'] = ($line['cleared'] == 0) ? true : 'cleared';
				}
			} else {
				$this->post['isreported'] = false;
			}
			$this->post['isthread'] = ($this->post['parentid'] == 0) ? true : false;
			if (empty($this->board) || $this->board['name'] != $board) {
				$this->Board($board, false);
			}
		}
	}

	function Delete($allow_archive = false) {
		global $tc_db;
		$i = 0;
		if ($this->post['isthread'] == true) {
			if ($allow_archive && $this->board['enablearchiving'] == 1 && $this->board['loadbalanceurl'] == '') {
				$this->ArchiveMode(true);
				$this->RegenerateThreads($this->post['id']);
				$files = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."post_files` WHERE `boardid` = " . $this->board['id'] . " AND `id` = " . $this->post['id']);
				foreach ($files as $file) {
					@copy(KU_BOARDSDIR . $this->board['name'] . '/src/' . $file['file'] . '.' . $file['filetype'], KU_BOARDSDIR . $this->board['name'] . $this->archive_dir . '/src/' . $file['file'] . '.' . $file['filetype']);
					@copy(KU_BOARDSDIR . $this->board['name'] . '/thumb/' . $file['file'] . 's.' . $file['filetype'], KU_BOARDSDIR . $this->board['name'] . $this->archive_dir . '/thumb/' . $file['file'] . 's.' . $file['filetype']);
				}
			}
			$results = $tc_db->GetAll("SELECT `id` FROM `".KU_DBPREFIX."posts` WHERE `boardid` = '" . $this->board['id'] . "' AND `IS_DELETED` = 0 AND `parentid` = ".$tc_db->qstr($this->post['id'])."");
			foreach($results AS $line) {
				$i++;
				if ($allow_archive && $this->board['enablearchiving'] == 1) {
					$files = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."post_files` WHERE `boardid` = " . $this->board['id'] . " AND `id` = " . $this->post['id']);
					foreach ($files as $file) {
						@copy(KU_BOARDSDIR . $this->board['name'] . '/src/' . $file['file'] . '.' . $file['filetype'], KU_BOARDSDIR . $this->board['name'] . $this->archive_dir . '/src/' . $file['file'] . '.' . $file['filetype']);
						@copy(KU_BOARDSDIR . $this->board['name'] . '/thumb/' . $file['file'] . 's.' . $file['filetype'], KU_BOARDSDIR . $this->board['name'] . $this->archive_dir . '/thumb/' . $file['file'] . 's.' . $file['filetype']);
					}
				}
			}
			if ($allow_archive && $this->board['enablearchiving'] == 1) {
				$this->ArchiveMode(false);
			}
			@unlink(KU_BOARDSDIR.$this->board['name'].'/res/'.$this->post['id'].'.html');
			@unlink(KU_BOARDSDIR.$this->board['name'].'/res/'.$this->post['id'].'-100.html');
			@unlink(KU_BOARDSDIR.$this->board['name'].'/res/'.$this->post['id'].'+50.html');
			$this->DeleteFile(false, true);
			foreach($results AS $line) {
				$tc_db->Execute("UPDATE `".KU_DBPREFIX."posts` SET `IS_DELETED` = 1 , `deleted_timestamp` = '" . time() . "' WHERE `boardid` = '" . $this->board['id'] . "' AND `id` = '".$line['id']."' AND `parentid` = ".$tc_db->qstr($this->post['id']));
				clearPostCache($line['id'], $this->board['name']);
			}
			$tc_db->Execute("DELETE FROM `".KU_DBPREFIX."watchedthreads` WHERE `threadid` = ".$tc_db->qstr($this->post['id'])." AND `board` = '".$this->board['name']."'");
			$tc_db->Execute("UPDATE `".KU_DBPREFIX."posts` SET `IS_DELETED` = 1 , `deleted_timestamp` = '" . time() . "' WHERE `boardid` = '" . $this->board['id'] . "' AND `id` = ".$tc_db->qstr($this->post['id'])." ");
			$tc_db->Execute("UPDATE `".KU_DBPREFIX."post_files` SET `IS_DELETED` = 1 , `deleted_timestamp` = '" . time() . "' WHERE `boardid` = '" . $this->board['id'] . "' AND `id` = ".$tc_db->qstr($this->post['id'])." ");
			clearPostCache($this->post['id'], $this->board['name']);

			return $i.' ';
		} else {
			$this->DeleteFile();
			$tc_db->Execute("UPDATE `".KU_DBPREFIX."posts` SET `IS_DELETED` = 1 , `deleted_timestamp` = '" . time() . "' WHERE `boardid` = '" . $this->board['id'] . "' AND `id` = ".$tc_db->qstr($this->post['id']));
			$tc_db->Execute("UPDATE `".KU_DBPREFIX."post_files` SET `IS_DELETED` = 1 , `deleted_timestamp` = '" . time() . "' WHERE `boardid` = '" . $this->board['id'] . "' AND `id` = ".$tc_db->qstr($this->post['id']));
			clearPostCache($this->post['id'], $this->board['name']);

			return true;
		}
	}

	function DeleteFile($update_to_removed = true, $whole_thread = false) {
		global $tc_db;
		if ($whole_thread && $this->post['isthread']) {
			$results = $tc_db->GetAll("SELECT `id` FROM `".KU_DBPREFIX."posts` WHERE `boardid` = " . $this->board['id'] . " AND `IS_DELETED` = 0 AND `parentid` = ".$tc_db->qstr($this->post['id']));
			if (count($results)>0) {
				foreach($results AS $line) {
					$files = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."post_files` WHERE `boardid` = " . $this->board['id'] . " AND `id` = " . $line['id']);
					foreach ($files as $file) {
						if ($file['file'] != '' && $file['file'] != 'removed') {
							if ($this->board['loadbalanceurl'] != '') {
								$this->loadbalancer->Delete($file['file'], $file['file_type']);
							} else {
								@unlink(KU_BOARDSDIR.$this->board['name'].'/src/'.$file['file'].'.'.$file['file_type']);
								@unlink(KU_BOARDSDIR.$this->board['name'].'/src/'.$file['file'].'.pch');
								@unlink(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'s.'.$file['file_type']);
								@unlink(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'c.'.$file['file_type']);
								if ($file['file_type'] == 'mp3') {
									@unlink(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'s.jpg');
									@unlink(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'s.png');
									@unlink(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'s.gif');
								}
							}
						}
					}
					if ($update_to_removed) {
						$tc_db->Execute("UPDATE `".KU_DBPREFIX."post_files` SET `file` = 'removed', `file_md5` = '', `IS_DELETED` = 1, `deleted_timestamp`= '".time()."' WHERE `boardid` = '" . $this->board['id'] . "' AND `id` = ".$line['id']);
						clearPostCache($line['id'], $this->board['name']);
					}
				}
			}
			$this->DeleteFile($update_to_removed);
		} else {
			$files = $tc_db->GetAll("SELECT * FROM `".KU_DBPREFIX."post_files` WHERE `boardid` = " . $this->board['id'] . " AND `id` = " . $this->post['id']);
			foreach ($files as $file) {
				if ($file['file']!=''&&$file['file']!='removed') {
					if ($this->board['loadbalanceurl'] != '') {
						$this->loadbalancer->Delete($file['file'], $file['file_type']);
					} else {
							@unlink(KU_BOARDSDIR.$this->board['name'].'/src/'.$file['file'].'.'.$file['file_type']);
							@unlink(KU_BOARDSDIR.$this->board['name'].'/src/'.$file['file'].'.pch');
							@unlink(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'s.'.$file['file_type']);
							@unlink(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'c.'.$file['file_type']);
							if ($file['file_type'] == 'mp3') {
								@unlink(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'s.jpg');
								@unlink(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'s.png');
								@unlink(KU_BOARDSDIR.$this->board['name'].'/thumb/'.$file['file'].'s.gif');
							}
					}
					if ($update_to_removed) {
						$tc_db->Execute("UPDATE `".KU_DBPREFIX."post_files` SET `file` = 'removed', `file_md5` = '', `IS_DELETED` = 1, `deleted_timestamp`= '".time()."' WHERE `boardid` = '" . $this->board['id'] . "' AND `file` = ".$tc_db->qstr($file['file']));
						clearPostCache($file['file'], $this->board['name']);
					}
				}
			}
		}
	}

	function Insert($parentid, $name, $tripcode, $email, $subject, $message, $files, $password, $timestamp, $bumped, $ip, $posterauthority, $tag, $stickied, $locked, $boardid) {
		global $tc_db;
		$query = 'INSERT INTO `'.KU_DBPREFIX.'posts` ( `parentid` , `boardid`, `name` , `tripcode` , `email` , `subject` , `message` , `password`, `timestamp`, `bumped`, `ip`, `ipmd5`, `posterauthority`, `tag`, `stickied`, `locked`, `boardname`) VALUES ('.$tc_db->qstr($parentid).', '.$tc_db->qstr($boardid).', '.$tc_db->qstr($name).', '.$tc_db->qstr($tripcode).', '.$tc_db->qstr($email).', '.$tc_db->qstr($subject).', '.$tc_db->qstr($message).', '.$tc_db->qstr($password).', '.$tc_db->qstr($timestamp).', '.$tc_db->qstr($bumped).', '.$tc_db->qstr(md5_encrypt($ip, KU_RANDOMSEED)).', \''.md5($ip).'\', '.$tc_db->qstr($posterauthority).', '.$tc_db->qstr($tag).', '.$tc_db->qstr($stickied).', '.$tc_db->qstr($locked).', "'.$_POST['board'].'" )';		
		$tc_db->Execute($query);
		
		$id = $tc_db->lastInsertId();
		
		if(!$id || KU_DBTYPE == 'sqlite') {
			// Non-mysql installs don't return the insert ID after insertion, we need to manually get it.
			$id = $tc_db->GetOne("SELECT `id` FROM `".KU_DBPREFIX."posts` WHERE `boardid` = ".$tc_db->qstr($boardid)." AND timestamp = ".$tc_db->qstr($timestamp)." AND `ipmd5` = '".md5($ip)."' LIMIT 1");
		}

		if ($id == 1 && $this->board['start'] > 1) {
			$tc_db->Execute("UPDATE `".KU_DBPREFIX."posts` SET `id` = '".$this->board['start']."' WHERE `boardid` = ".$boardid);
			return $this->board['start'];
		}
		
		if($files) {
			foreach ($files as $file) {
				$filetype = (substr($file['file_type'], 0, 1) == '.') ? substr($file['file_type'], 1) : $file['file_type'];
				$embeds = array();
				$result = $tc_db->GetAll("SELECT HIGH_PRIORITY * FROM `" . KU_DBPREFIX . "embeds`");
				foreach ($result as $line) {
					$embeds[] = $line['filetype'];
				}
				if (in_array($filetype, $embeds)) {
					$filename = $file['file_name'];
					$file_md5 = md5($file['file_name']);
					$file_original = $file['file_name'];
					$image_w = '';
					$image_h = '';
					$filesize = 0;
					$thumb_w = '';
					$thumb_h = '';
				} elseif ($filetype == 'mp3') {
					$file_md5 = $file['file_md5'];
					if (!$file_md5) {
						continue;
					}
					$filename = $file['file_name'];				
					$file_original = $file['original_file_name'];
					$image_w = '';
					$image_h = '';
					$filesize = $file['file_size'];
					$thumb_w = '';
					$thumb_h = '';	
				} else {
					//If the file doesn't have an md5 it doesn't exist and md5 will always be unique and present if
					//the file does exist, then skip to the next or end.
					$file_md5 = $file['file_md5'];
					if (!$file_md5) {
						continue;
					}
					$filename = $file['file_name'];				
					$file_original = $file['original_file_name'];
					$image_w = $file['imgWidth'];
					$image_h = $file['imgHeight'];
					$filesize = $file['file_size'];
					$thumb_w = $file['imgWidth_thumb'];
					$thumb_h = $file['imgHeight_thumb'];	
				}
				if ($thumb_w == NULL) {
					$thumb_w = '42';
					if ($thumb_h == NULL) {
						$thumb_h = '42';
					}
				}
				$query = 'INSERT INTO `'.KU_DBPREFIX.'post_files` ( `id`, `boardid`, `file`, `file_md5`, `file_type`, `file_original`, `file_size`, `file_size_formatted`, `image_w`, `image_h`,  `thumb_w`, `thumb_h`) VALUES ( '.$tc_db->qstr($id).', '.$tc_db->qstr($boardid).', '.$tc_db->qstr($filename).', '.$tc_db->qstr($file_md5).', '.$tc_db->qstr($filetype).', '.$tc_db->qstr($file_original).', '.$tc_db->qstr($filesize).', '.$tc_db->qstr(ConvertBytes($filesize)).', '.$tc_db->qstr(intval($image_w)).', '.$tc_db->qstr(intval($image_h)).', '.$tc_db->qstr($thumb_w).', '.$tc_db->qstr($thumb_h).' )';
				$tc_db->Execute($query);
			}
		}
		$to = array();
		$to2 = array();
		$querying1 = $tc_db->GetAll('SELECT * FROM `'.KU_DBPREFIX.'email` WHERE `postemail` = 1');
		foreach ($querying1 as $emails) {
			$to = $emails['email'];
		}
		$querying3 = $tc_db->GetAll('SELECT * FROM `'.KU_DBPREFIX.'email` WHERE `postemail` = 1');
		foreach ($querying3 as $wat1) {
			$wat2[] = $wat1['email'];
		}
		if(count($to) == 1) {
			$message = '
			There has been a new post at '.KU_NAME.' please click <a href="'.KU_WEBPATH.'/manage.php">here</a> to be taken to management to review the post.
			';
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			mail($to, 'New post at '.KU_NAME, $message, $headers);
		}
		if(count($wat2) > 1) {
			$to2 = implode(' , ', $wat2);
			$message = '
			There has been a new post at '.KU_NAME.' please click <a href="'.KU_WEBPATH.'/manage.php">here</a> to be taken to management to review the post.
			';
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			mail($to2, 'New post at '.KU_NAME, $message, $headers);
		}
		return $id;
	}

	function Report() {
		global $tc_db;
		
		$tc_db->Execute("INSERT INTO `".KU_DBPREFIX."reports` ( `board` , `postid` , `when` , `ip`, `reason` ) VALUES ( " . $tc_db->qstr($this->board['name']) . " , " . $tc_db->qstr($this->post['id']) . " , ".time()." , '" . md5_encrypt($_SERVER['REMOTE_ADDR'], KU_RANDOMSEED) . "', " . $tc_db->qstr($_POST['reportreason']) . " )");
		
		$email = array();
		$email2 = array();
		$querying = $tc_db->GetAll('SELECT * FROM `'.KU_DBPREFIX.'email` WHERE `reportemail` = 1');
		foreach ($querying as $emails) {
			$email[] = $emails['email'];
		}
		$querying2 = $tc_db->GetAll('SELECT * FROM `'.KU_DBPREFIX.'email` WHERE `reportemail` = 1');
		foreach ($querying2 as $watwat) {
			$wat[] = $watwat['email'];
		}
		if (count($email) == 1) {
			$message = '
			<html>
			<head>
			<title>Post reported at '.KU_WEBPATH.'</title>
			</head>
			<body>
			There has been a report posted at '.KU_NAME.' please click <a href="'.KU_WEBPATH.'/manage.php">here</a> to view the report.
			</body>
			</html>
			';
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			mail($email, 'Post reported at '.KU_NAME, $message, $headers);
		}
		if (count($wat) > 1) {
			$email2 = implode(' , ', $wat);
			$message = '
			<html>
			<head>
			<title>Post reported at '.KU_WEBPATH.'</title>
			</head>
			<body>
			There has been a report posted at '.KU_NAME.' please click <a href="'.KU_WEBPATH.'/manage.php">here</a> to view the report.
			</body>
			</html>
			';
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			mail($email2, 'Post reported at '.KU_NAME, $message, $headers);
		}
	}
}

?>
