<?php
	require_once 'config.php';
	require_once KU_ROOTDIR . 'inc/functions.php';
	require_once KU_ROOTDIR . 'lib/dwoo.php';
	global $tc_db, $dwoo_data;
	if (file_exists("install.php")) {
		die('You are seeing this message because either you haven\'t ran the install file yet, and can do so <a href="install.php">here</a>, or already have, and <strong>must delete it</strong>.');
	}
	$dwoo_tpl = new Dwoo_Template_File(KU_TEMPLATEDIR . '/mainpage.tpl');
	// News/FAQ/Rules
	if ($_GET['view'] == '') {
		$entries = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "front` WHERE `page` = 0 ORDER BY `timestamp` DESC LIMIT 5 OFFSET ".($_GET['page'] * 5));
	} elseif ($_GET['view'] == 'faq') {
		$entries = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "front` WHERE `page` = 1 ORDER BY `timestamp` DESC");
	} elseif ($_GET['view'] == 'rules') {
		$entries = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "front` WHERE `page` = 2 ORDER BY `order` ASC");
	}
	$pages = $tc_db->GetOne("SELECT COUNT(*) FROM `" . KU_DBPREFIX . "front` WHERE `page` = 0");
	$sections = Array();
	$results_boardsexist = $tc_db->GetAll("SELECT `id` FROM `".KU_DBPREFIX."boards` LIMIT 1");
	if (count($results_boardsexist) > 0) {
		$sections = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "sections` ORDER BY `order` ASC");
		foreach($sections AS $key=>$section) {
			$results = $tc_db->GetAll("SELECT * FROM `" . KU_DBPREFIX . "boards` WHERE `section` = '" . $section['id'] . "' ORDER BY `order` ASC, `name` ASC");
			foreach($results AS $line) {
				$sections[$key]['boards'][] = $line;
			}
		}
	}

// Recent Posts
	$limit = 5; // How many post you want to show
	$disallowedboard = ''; //Boards you rather not show recent post in IE 'test'.. TODO:Make user able to add more than one board
	//end configuration
	if ($disallowedboard != '') {
		$boardid = $tc_db->GetOne('SELECT `id` FROM `'.KU_DBPREFIX.'boards` WHERE `name` = "'.$disallowedboard.'"');
	}
	$query = $tc_db->GetAll('SELECT * FROM `'.KU_DBPREFIX.'posts` WHERE `IS_DELETED` = 0 ORDER BY `timestamp` DESC LIMIT '.$limit.'');
	
//last images
	$lastimages = $tc_db->GetAll("SELECT p.id, p.file, p.file_type, x.parentid, b.name
	FROM `post_files` p LEFT 
	JOIN `boards` b
	ON p.boardid = b.id LEFT JOIN `posts` x ON p.id=x.id and p.boardid =x.boardid
	WHERE p.IS_DELETED = 0 AND x.IS_DELETED = 0 
	AND p.file_type IN ('gif', 'jpg', 'png') AND p.file_md5 != ''
	AND b.name != '3' AND b.name != '".$disallowedboard."'
	ORDER BY p.timestamp
	DESC LIMIT 20");
	$rrcount = 4;
	$rrid = 0;
	$lastimagesok = array();
	foreach ((array)$lastimages as $rrval) {
	$lastimagesok[($rrid++) % $rrcount][] = $rrval;
	}	

//******************************************
	$dwoo_data->assign('lastimages', $lastimagesok);
	$dwoo_data->assign('recentposts', $query);
	$dwoo_data->assign('pages', ($pages/5));
	$dwoo_data->assign('totalposts', $totalposts);
	$dwoo_data->assign('currentusers', $currentusers);
	$dwoo_data->assign('activecontent', $activecontent);
	$dwoo_data->assign('boards', $sections);
	$dwoo_data->assign('entries', $entries);
	$dwoo_data->assign('section', $boardsection);
	$dwoo_data->assign('ku_webpath', getCWebPath());
	$styles = explode(':', KU_MENUSTYLES);
	$dwoo_data->assign('styles', $styles);	
	$dwoo->output($dwoo_tpl, $dwoo_data);
?>
