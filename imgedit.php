<?php
//include the config file, 
include('config.php');

// patch for lack of gd filter and rotate support
include(KU_ROOTDIR.'inc/gdless.inc.php');

require_once KU_ROOTDIR . 'inc/functions.php';
require_once KU_ROOTDIR . 'inc/classes/board-post.class.php';
global $tc_db, $CURRENTLOCALE;

// Is this allowed on the board?
$boardname = $_GET['B'];

// If not Kill it
if (!$tc_db->GetOne("SELECT `name` FROM `" . KU_DBPREFIX . "boards` WHERE `imgedit` = 1 AND `name` = ".$tc_db->qstr($boardname))) {
        exitWithErrorPage(_gettext('Image editing is not available for this board'));
}

//does not give the user a option to change, can only be one...
//set if you wish files to only be outputted in this format
//examples...
//$output = "JPEG";
//$output = "GIF";
//$output = "PNG";
$output = "";


//gives the user the option in how it outputs..
//allowed output types..(only works if $output is set to ""), split by |
//examples...
//$output = "PNG|GIF";
//$output = "PNG|JPEG";
//$output = "JPEG|GIF";
$outputs = "PNG|JPEG|GIF";

//fonts folder, with trailing slash
$fontfolder = "lib/fonts/";

//default font, not including .ttf extension
$fontD = 'monofont';

//folder to store gened images, with training slash
$tempimg = "imagetmp/";

//leave this how it is, for me only, so i can debug stuff... leave as 0 for it to work...
// 0 = no, 1 = show post data, 2 = show image
$debugging = 0;

$jquery = "jquery.js"; // name of the jquery lib you're using

/*dwoo hooks to load a custom header template so the forms display with the board's styles. we need a custom template because the provided header tpls
include the beginning of the postform which messes things up.*/

$board_class = new Board($boardname);
$board_class->InitializeDwoo();

$board_class->dwoo_data->assign('file_path', getCLBoardPath($board_class->board['name'], $board_class->board['loadbalanceurl_formatted'], ''));
$board_class->dwoo_data->assign('title', 'Image Edit');
$board_class->dwoo_data->assign('jquery', $jquery);
$board_class->dwoo_data->assign('board', $board_class->board['name']);
$board_class->dwoo_data->assign('ku_styles', explode(':', KU_STYLES));
$board_class->dwoo_data->assign('ku_defaultstyle', (!empty($board_class->board['defaultstyle']) ? ($board_class->board['defaultstyle']) : (KU_DEFAULTSTYLE)));
$board_class->dwoo_data->assign('replythread', 0);
$board_class->dwoo_data->assign('boardlist', $board_class->board['boardlist']);

$header = $board_class->dwoo->get(KU_TEMPLATEDIR . '/img_blank_header.tpl', $board_class->dwoo_data);
$footer = $board_class->dwoo->get(KU_TEMPLATEDIR . '/global_board_footer.tpl', $board_class->dwoo_data); 

// everything else dont touch...!!!!!!!!!!
// (haha - anon#5324e8)

$imagenumber = $_GET['I'];
$start = array();
$result2 = $tc_db->GetAll("SELECT `id` FROM `".KU_DBPREFIX."boards` WHERE `name` = ".$tc_db->qstr($boardname)." LIMIT 1");
foreach ($result2 as $line) {
$start = $line['id'];
}
$results = $tc_db->GetAll("SELECT `image_w`, `id`, `file_type`, `boardid`, `image_h` FROM `" . KU_DBPREFIX . "post_files` WHERE `file` = ".$tc_db->qstr($imagenumber)." LIMIT 1");
$thread1 = array();
$result = $tc_db->GetAll("SELECT `id` FROM `" . KU_DBPREFIX . "post_files` WHERE `file` = ".$tc_db->qstr($imagenumber)." AND `boardid` = ".$start." LIMIT 1");
foreach ($result as $line) {
	$thread1 = $line['id'];
}
$image_w = $results[0]['image_w'];
$image_h = $results[0]['image_h'];
$scalew = 0;
$scaleh = 0;
$file_type = $results[0]['file_type'];
$boardid = $results[0]['boardid'];
$board = $tc_db->GetAll("SELECT `name`, `enablecaptcha`  FROM `" . KU_DBPREFIX . "boards` WHERE `id` = $boardid");
$boardname = $board[0]['name'];
$boardcaptcha = $board[0]['enablecaptcha'];
$threads = array();
$results1 = $tc_db->GetAll("SELECT `parentid` FROM `" . KU_DBPREFIX . "posts` WHERE `id` = ".$thread1." AND `boardid` = ". $start." LIMIT 1");
foreach ($results1 as $line) {
	$threads = $line['parentid'];
}
if ($threads == 0) {
$thread5 = array();
$results1 = $tc_db->GetAll("SELECT `parentid` FROM `" . KU_DBPREFIX . "posts` WHERE `parentid` = ".$thread1." AND `boardid` = ". $start." LIMIT 1");
foreach ($results1 as $line) {
	$thread5 = $line['parentid'];
}
$threads = $thread5;
}

$filedir = KU_WEBFOLDER.$boardname."/src/";
$filedir1 = $boardname."/src/";
$KU_WEBPATH = KU_WEBPATH;
$KU_NAME = KU_NAME;
$KU_CGIPATH = KU_CGIPATH;
$KU_DEFAULTSTYLE = KU_DEFAULTSTYLE;

if($debugging == 1){
    print_r($boardid);
}

/*returns something as a js alert*/
function alert($v){
	echo('<script>alert("'.$v.'")</script>');
};
				
/*take a comma-delimited string of int values (sent to a form field by the color picker) and split into an array and test the values for rgb-safe range.
if true, return the array, otherwise return a default.*/

function testRGB($arr, $def){
		
	$rgb = explode(",", $arr);
			
	foreach($rgb as $key=>$value){
		if(filter_var( $rgb[$key], FILTER_VALIDATE_INT, array( 'min_range' => 0, 'max_range' => 255)) === false){
			return $def;
		}
	}
	return $rgb;
};

/*take a POST value and return it if it falls inside a certain range, else return a default.
has to be an integer*/
function testKeyRange($k, $min, $max, $def){
	
	
	if(isset($_POST[$k])){
		if(filter_var($_POST[$k], FILTER_VALIDATE_INT, array('options' => array( 'min_range' => $min, 'max_range' => $max))) == false){
				return $def;
		}
		else{
			return $_POST[$k];
		}
	
	}
	
	return $def;
};

// flip horizontal and vertical
define ( 'IMAGE_FLIP_HORIZONTAL', 1 );
define ( 'IMAGE_FLIP_VERTICAL', 2 );
define ( 'IMAGE_FLIP_BOTH', 3 );

//xafford	http://www.php.net/manual/en/function.imagecopy.php
function imageflip ( $imgsrc, $mode ){

    $width                        =    imagesx ( $imgsrc );
    $height                       =    imagesy ( $imgsrc );

    $src_x                        =    0;
    $src_y                        =    0;
    $src_width                    =    $width;
    $src_height                   =    $height;

    switch ( (int) $mode )
    {

        case IMAGE_FLIP_HORIZONTAL:
            $src_y                =    $height;
            $src_height           =    -$height;
        break;

        case IMAGE_FLIP_VERTICAL:
            $src_x                =    $width;
            $src_width            =    -$width;
        break;

        case IMAGE_FLIP_BOTH:
            $src_x                =    $width;
            $src_y                =    $height;
            $src_width            =    -$width;
            $src_height           =    -$height;
        break;

        default:
            return $imgsrc;

    }

    $imgdest    =    imagecreatetruecolor ( $width, $height );
					 imagealphablending($imgdest, false);
					 imagesavealpha($imgdest, true);
					 
    if ( imagecopyresampled ( $imgdest, $imgsrc, 0, 0, $src_x, $src_y, $width, $height, $src_width, $src_height ) )
    {
        return $imgdest;
    }

    return $imgsrc;

};

// I suck at math.
function getscale($int, $s){
	if(is_float($s)){
		return round($int*$s);
	}
	else return round($int*($s/100));
}

// return an image at $s scale
function imagescale($transformimg, $s){
	 $w = imagesx($transformimg);
	 $h = imagesy($transformimg);
		 
	 $scalew = getscale($w, $s);
	 $scaleh = getscale($h, $s);
		 
	 $imgdest = imagecreatetruecolor($scalew,$scaleh);
			   imagealphablending($imgdest,false);
			   imagesavealpha($imgdest,true);
				   
		if(imagecopyresampled ($imgdest, $transformimg, 0, 0, 0, 0, $scalew, $scaleh, $w, $h)){
			$transformimg = $imgdest;
		}
	
	return $transformimg;
}


/*enable all transformations (rotation, flip, filters)*/ 
function imagetransform($transformimg){
		
	// add image flip
	if(isset($_POST['flipx'])){
		$transformimg = imageflip($transformimg, IMAGE_FLIP_HORIZONTAL);
	};
			
	if(isset($_POST['flipy'])){
		$transformimg = imageflip($transformimg, IMAGE_FLIP_VERTICAL);
	};
	
	// add image scale/rotation if not using the demotivator
	
	if(isset($_POST['scaleimage']) &!($_POST['tool'] == 'motivator')){

		 $s = testKeyRange('scaleimage',50, 100, 100);
		 $transformimg = imagescale($transformimg, $s);
		 
		$rotation = testKeyRange('rotateimage', 90, 270, 0);
		$transformimg = imagerotate($transformimg, $rotation, 0);
	}
			
	/*apply image filters*/
	if(isset($_POST['setfilterdesat'])){
		imagefilter($transformimg, IMG_FILTER_GRAYSCALE);
	};
		
	if(isset($_POST['setfilternegative'])){
		imagefilter($transformimg, IMG_FILTER_NEGATE);
	};
	
	if(isset($_POST['setfiltercolorize'])){
		$filtervalues = testRGB($_POST['rgbcolorize'], array(0,0,0));
		imagefilter($transformimg, IMG_FILTER_COLORIZE, $filtervalues[0], $filtervalues[1], $filtervalues[2]);
	};
		
	return $transformimg;
}

			
if(isset($_POST['tool'])){
	
    if($debugging != 1){
        if($output == ""){
            $extension = $_POST['filetype'];
        }else{
            $extension = $output;
        }
        $case = $_POST['tool'];
        switch ($case) {
            case "motivator":
                $title = "motivator-".str_replace(" ", "-", $_POST['motivator_title']).".".strtolower($extension);
                break;
            case "macro":
                $title = "macro-".str_replace(" ", "-", $_POST['macro_top_text']).".".strtolower($extension);
                break;
        }
            
            //other stuff that will/might be used in all functions..
        if(isset($_POST['motivator_title']) && isset($_POST['motivator_text']) && $_POST['tool'] == "motivator"){
			
			// i should apply scaling to this...
            if($_POST['motivator_aspect'] == "landscape"){
                $canvasw = 710; $canvash = 660; $rectanglex = 659; $rectangley = 515; $cropx = 599; $cropy = 455; $text1 = 570; $text2 = 620;
            }else{
                $canvasw = 560; $canvash = 810; $rectanglex = 509; $rectangley = 659; $cropx = 450; $cropy = 600; $text1 = 715; $text2 = 765;
            }
			
            $canvas = imagecreatetruecolor( $canvasw, $canvash );
            $white = imagecolorallocate($canvas, 255, 255, 255);
            $black = imagecolorallocate($canvas, 0, 0, 0); 
			
            ImageFillToBorder($canvas, 0, 0, $black, $black);
            $shinameext = explode(".",$_POST['shi_filename']);
            if($shinameext[1] == "jpg"){
                $shinameext = "jpeg";
            }else{
                $shinameext = $shinameext[1];
            }
            $func = "imagecreatefrom".strtolower($shinameext);
            $cropped = $func( $_POST['filedir'].$_POST['shi_filename'] );
            $font = $fontfolder.$fontD.'.ttf';
            $bbox = imagettfbbox(30, 0, $font, $_POST['motivator_title']);
            $x = $bbox[0] + (imagesx($canvas) / 2) - ($bbox[4] / 2);
			
			//motivator text - leave this white
            imagettftext($canvas, 30, 0, $x, $text1, $white, $font, $_POST['motivator_title']);
            $bbox = imagettfbbox(24, 0, $font, $_POST['motivator_text']);
            $x = $bbox[0] + (imagesx($canvas) / 2) - ($bbox[4] / 2);
			
            imagettftext($canvas, 24, 0, $x, $text2, $white, $font, $_POST['motivator_text']);
            imagerectangle($canvas, 50, 50, $rectanglex, $rectangley, $white);
            imagerectangle($canvas, 51, 51, $rectanglex-1, $rectangley-1, $white);
		
			$cropped = imagetransform($cropped);
			
			/* this is not ready 
			$s = testKeyRange('scaleimage',50, 100, 100);
			$cropped = imagescale($cropped, $s); */
			
            imagecopyresized( $canvas, $cropped, 55,55, $_POST['x'], $_POST['y'], $cropx, $cropy, $_POST['w'], $_POST['h'] );
            $func = "image".strtolower($extension);
            if($debugging == 2){
                header("Content-type: image/".strtolower($extension)); 
                $func($canvas);
            }else{
                $func($canvas, $tempimg.$title);
                imagedestroy($canvas); 
            }

        }

        if(isset($_POST['macro_top_text']) && isset($_POST['macro_bottom_text']) && $_POST['tool'] == "macro"){

			// if scaling is enabled, scale the background
			if(isset($_POST['scaleimage'])){
				$s = testKeyRange('scaleimage',50, 100, 100);
				$W = getscale($_POST['w'], $s);
				$H = getscale($_POST['h'], $s);
			}
			else{
			
				$W = $_POST['w']; 
				$H = $_POST['h'];
			};
			
            $canvas = imagecreatetruecolor( $W, $H );
			
			// apply rotation to the canvas if necessary
			$rotation = testKeyRange('rotateimage', 90, 270, 0);
			
			if($rotation != 0){
				$canvas = imagerotate($canvas, $rotation, 0);
			};
	
            $white = imagecolorallocate($canvas, 255, 255, 255);
            $black = imagecolorallocate($canvas, 0, 0, 0); 
			
			$textfillrgb = testRGB($_POST['rgbfill'], array(255,255,255));
			$fillcolor = imagecolorallocate($canvas, $textfillrgb[0], $textfillrgb[1], $textfillrgb[2]);
		
			$textstrokergb = testRGB($_POST['rgbstroke'], array(0,0,0));
			$strokecolor = imagecolorallocate($canvas, $textstrokergb[0], $textstrokergb[1], $textstrokergb[2]);
            
			ImageFillToBorder($canvas, 0, 0, $black, $black);
			
            $shinameext = explode(".",$_POST['shi_filename']);
            if($shinameext[1] == "jpg"){
                $shinameext = "jpeg";
            }else{
                $shinameext = $shinameext[1];
            }
            $func = "imagecreatefrom".strtolower($shinameext);
            $cropped = $func( $_POST['filedir'].$_POST['shi_filename'] );
	
			// apply all transforms and filters to the image
			$cropped = imagetransform($cropped);
			
            imagecopy($canvas, $cropped, 0, 0, $_POST['x'], $_POST['y'], $W+$POST['w'], $H+$_POST['h']);

            function imagettfstroketext($image, $size, $x, $y, $fillcolor, $strokecolor, $fontfile, $text, $px) {

                for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++){
                    for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++){
                        $bg = imagettftext($image, $size, 0, $c1, $c2, $strokecolor, $fontfile, $text);
                    }
                }
                return imagettftext($image, $size, 0, $x, $y, $fillcolor, $fontfile, $text);
            }
            
            
            $align = $_POST['macro_top_align'];
			
			$fsize = testKeyRange('fontsizetop',10,99,99);
            $font = $fontfolder.$_POST['macro_top_font'].".ttf";
            $text = $_POST['macro_top_text'];
            $_b = imageTTFBbox($fsize,0,$font,$text);
            while(abs($_b[2]-$_b[0])+5 > $W){
                $fsize = $fsize - 1;
                $_b = imageTTFBbox($fsize,0,$font,$text);
            
            }
            $Y =  ($_b[7]) * -1;
            if($align == "left"){
                imagettfstroketext($canvas, $fsize, 0, $Y, $fillcolor, $strokecolor, $font, $text, 2);
            }elseif($align == "right"){
                    $_W = abs($_b[2]-$_b[0])+5;
                    $_X = $W-$_W;
                    imagettfstroketext($canvas, $fsize, $_X, $Y, $fillcolor, $strokecolor, $font, $text, 2);  
            }elseif($align == "center"){
                    $_W = abs($_b[2]-$_b[0]);
                    $_X = abs($W/2)-abs($_W/2);
                    imagettfstroketext($canvas, $fsize, $_X, $Y, $fillcolor, $strokecolor, $font, $text, 2);  
               
            }     

						
			$fsize = testKeyRange('fontsizebottom',10,99,99);
			$align = $_POST['macro_bottom_align'];
            $font = $fontfolder.$_POST['macro_bottom_font'].".ttf";
            $text = $_POST['macro_bottom_text'];
            
            $_b = imageTTFBbox($fsize,0,$font,$text);
            while(abs($_b[2]-$_b[0])+5 > $W){
                $fsize = $fsize - 1;
                $_b = imageTTFBbox($fsize,0,$font,$text);
            
            }
            $Y =  $H + ($_b[7] / 3);
            if($align == "left"){
                imagettfstroketext($canvas, $fsize, 0, $Y, $fillcolor, $strokecolor, $font, $text, 2);
            }elseif($align == "right"){
                $_W = abs($_b[2]-$_b[0])+5;
                $_X = $W-$_W;
                imagettfstroketext($canvas, $fsize, $_X, $Y, $fillcolor, $strokecolor, $font, $text, 2);  
            }elseif($align == "center"){
                $_W = abs($_b[2]-$_b[0]);
                $_X = abs($W/2)-abs($_W/2);
                imagettfstroketext($canvas, $fsize, $_X, $Y, $fillcolor, $strokecolor, $font, $text, 2);  
               
            }     
            $func = "image".strtolower($extension);
            if($debugging == 2){
                header("Content-type: image/".strtolower($extension)); 
                $func($canvas);
            }else{
                $func($canvas, $tempimg.$title);
                imagedestroy($canvas); 
            }

        }

        if($debugging == 0){
            echo $header.'

            <form name="postform" id="postform" action="board.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="board" value="'.$boardname.'" />
            <input type="hidden" name="replythread" value="'.$threads.'" />
			

            <input type="text" name="email" size="28" maxlength="75" value="" style="display: none;" />
            <table class="postform">
            <tbody>
            <tr>
	    <td class="postblock">
            Name</td>
            <td>
            <input type="text" name="name" size="28" maxlength="75" accesskey="n" />
            </td>
            </tr>
            ';

			if($boardcaptcha == 1){
				
				echo <<<captcha
   <tr><td class="postblock">
				<a href="#" onclick="javascript:document.getElementById('captchaimage').src = '$KU_CGIPATH/captcha.php?' + Math.random();return false;"><img id="captchaimage" src="$KU_CGIPATH/captcha.php" border="0" width="90" height="25" alt="Captcha image"></a>
			</td>
			<td>
				<input type="text" name="captcha" size="28" maxlength="10" accesskey="c" />
                                            </td>
                                            </tr>
captcha;
				
			};
			
			echo '
            <tr>
            <td class="postblock">
            Subject</td>
            <td>
            <input type="text" name="subject" size="35" maxlength="75" accesskey="s" />&nbsp;<input type="submit" value="Reply" accesskey="z" />&nbsp;(<span id="posttypeindicator">Reply to '.$threads.'</span>)

            </td>
            </tr>
            <tr>
            <td class="postblock">
            Message
            </td>
            <td>
            <textarea name="message" cols="48" rows="4" accesskey="m"></textarea>
            </td>
            </tr>
            <tr>
            <td class="postblock">
            File
            </td>
            <td>
			<img src="'.$KU_WEBPATH.'/'.$tempimg.$title.'"/>
            <input type="hidden" name="fileurl" value="'.$KU_WEBPATH.'/'.$tempimg.$title.'"/>
            </td>
            </tr>

            <tr>
            <td class="postblock">
            Password
            </td>
            <td>
            <input type="password" name="postpassword" size="13" accesskey="p" />&nbsp;(For post and file deletion)
			
            </td>
            </tr>
            <tr id="passwordbox"><td></td><td></td></tr>
            <tr>
            <td colspan="2" class="rules">
            Input your message like you would normally
            </td>
            </tr>
            </tbody>
		
            </table>
            </form>
            '.$footer;
        }
    }
}else{
    if($output == ""){
        $splitext = explode("|", $outputs);
        $filtypesallowed = '      <tr>
	<td class="postblock" colspan="2">File type
	<select name="filetype">';
        foreach ($splitext as $value) {
            $filtypesallowed .= '<option value="'.$value.'">'.strtolower($value).'</option>';
        }
        $filtypesallowed .= '</select></td>
      </tr>';
    }else{
        $filtypesallowed = '<input type="hidden" id="filetype" name="filetype" value="'.$output.'" />';
    }
    $fonts = "";
    
    if ($handle = opendir($fontfolder)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $file = explode(".", $file);
                $file1 = $file[0];
                $fonts .= '<option value="'.$file1.'">'.$file1.'</option>';
            }
        }
    }
	echo $header.'
	<link rel="stylesheet" href="'.$KU_WEBPATH.'/css/moorainbow.css" type="text/css" />
	<style type="text/css">
		.filterpanel{
			display: none;
		}
		
		td{
			border:1px solid #505050;
		}
		#inlinepreview{
			float:right;
		}
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/mootools/1.3.0/mootools-yui-compressed.js" type="text/javascript"></script>
	<script src="'.$KU_WEBPATH.'/lib/javascript/mootools-more.js" type="text/javascript"></script>
	<script src="'.$KU_WEBPATH.'/lib/javascript/moorainbow.js" type="text/javascript"></script>

	<script type="text/javascript">
	var image_width = '.$image_w.';
	var image_height = '.$image_h.';
	var imgpath = "'.$KU_WEBPATH.'/css/images/";
	</script>

<form method="POST">
  <input type="hidden" id="filedir" name="filedir" value="'.$filedir1.'"/>
  <input type="hidden" id="x" name="x" />
  <input type="hidden" id="y" name="y" />
  <input type="hidden" id="w" name="w" />
  <input type="hidden" id="h" name="h" />
  <input type="hidden" name="rgbcolorize" 	id="rgbcolorize" 	value="255,255,255">
  <input type="hidden" name="rgbfill" 		id="rgbfill" 		value="255,255,255">
  <input type="hidden" name="rgbstroke" 	id="rgbstroke" 		value="0,0,0">
  <input type="hidden" name="scaleimage"	id="scaleimage"		value="100">
  <table>
    <tr><td>
    <table>
      <tr>
	<td class="postblock" colspan="2">Preview</td>
      </tr>
      <tr>

	<td colspan="2">
	  <div style="width:200px;height:200px;overflow:hidden;margin-left:5px;" id="jcrop-preview-div">
	    <img src="'.$filedir.$imagenumber.'.'.$file_type.'" id="jcrop-preview" />
	  </div>
	  
	</td>
      </tr>
      <tr>
	<td class="postblock" colspan="2">Original image</td>

      </tr>
      <tr>
	<td colspan="2"><img id="jcrop-image" src="'.$filedir.$imagenumber.'.'.$file_type.'" width="'.$image_w.'" height="'.$image_h.'" /></td>
      </tr>
    </table>
    <table>
      <tr>
	<td class="postblock">Tool</td>

	<td>
	  <select name="tool" onchange="update_tool(event, this);">
	    <option value="motivator">(De) motivator</option>
	    <option value="macro">Macro</option>
	  </select>

	</td>
      </tr>
    </table>
	
	<table>	<tr>	
	<tr><td class="postblock">Filters: </td><td>
	<div id="setfilterselect">[<input type="checkbox"  name="setfilternegative"><label for="setfilternegative"> Negative</label>]</div>
	<div id="setfilterdesat">[<input type="checkbox" name="setfilterdesat" id="setfilterdesat"><label for="setfilterdesat"> Black and White</label>
	<span class="filterpanel" id="setfilterdesatpanel">
	</span> ]
	</div>
	<div id="setfiltercolorize">[<input type="checkbox"  name="setfiltercolorize" id="setfiltercolorize"><label for="setfiltercolorize"> Colorize</label>
	<span class="filterpanel" id="setfiltercolorizepanel"> 
			<span id="colorizepicker">
			 <a href="javascript:void(0)">Color Picker</a> <span id="rgbcolorizepreview"></span>
			</span>	

		</span> ]
	</div>
</td></tr></table>

<table>
	<td class="postblock">Global Transform</td>
	<td>
	[<input type="checkbox" name="flipx" id="flipx"><label for="flipx">Flip X</label>]
	[<input type="checkbox" name="flipy" id="flipy"><label for="flipy">Flip Y</label>]
	</td>
	</table>
	
    <table id="motivator-options" style="display:none;">
      <tr>
	<td class="postblock">Orientation</td>
	<td>
	  <input id="aspect1" type="radio" name="motivator_aspect" value="landscape" checked="" onclick="update_aspect(this);" />
	  <label for="aspect1">Landscape</label>
	  <input id="aspect2" type="radio" name="motivator_aspect" value="portrait" onclick="update_aspect(this);" />
	  <label for="aspect2">Portrait</label>
	</td>
      </tr>

      <tr>
	<td class="postblock">Title</td>
	<td><input name="motivator_title" value="" /></td>
      </tr>
      <tr>
	<td class="postblock">Comment</td>
	<td><textarea name="motivator_text"></textarea></td>

      </tr>
    </table>
    <table id="macro-options" style="display:none;">
	<td class="postblock" rowspan="2">Transform</td>
	<td>Rotate: 
	
	<select name="rotateimage" id="rotateimage">
	<option value="false" selected="true">None</option>
	<option value="90">90</option>
	<option value="180">180</option>
	<option value="270">270</option>
	</select>
	
	
	<TR><TD>
	Scale: <span id="scaleimagepreview"></span> <div id="slider" class="slider">
  <div class="knob"></div> 
</div>
	</td></tr>
      <tr>
	<td class="postblock" colspan="2">The text of the above</td>
      </tr>
      <tr>
	<td class="postblock">Message</td>

	<td><input name="macro_top_text" value="" /></td>
      </tr>
      <tr>
	<td class="postblock">Alignment</td>
	<td>
	  <select name="macro_top_align">
	    <option value="left">Left</option>
	    <option value="center" selected="True">Centered</option>
	    <option value="right">Right</option>
	  </select>
	</td>	
      </tr>
      <tr>
	<td class="postblock">Font</td>
	<td>
	  <select name="macro_top_font">
'.$fonts.'
	  </select>	
	  <input type="text" size="2" maxlength="2" name="fontsizetop" value="99"> 
	  
      <tr>
	<td class="postblock" colspan="2">The text below</td>
      </tr>

      <tr>
	<td class="postblock">Message</td>
	<td><input name="macro_bottom_text" value="" /></td>
      </tr>
      <tr>
	<td class="postblock">Alignment</td>
	<td>
	  <select name="macro_bottom_align">
	    <option value="left">Left</option>
	    <option value="center" selected="True">Centered</option>
	    <option value="right">Right</option>
	  </select>
	 
	</td>	
      </tr>
      <tr>
	<td class="postblock">Font</td>

	<td>
	  <select name="macro_bottom_font">
'.$fonts.'
	  </select> 
	  <input type="text" size="2" maxlength="2" name="fontsizebottom" value="99">
	</td>	
      </tr>
	  <tr>
	<td class="postblock">Colors</td><td>
	  <input type="radio" name="colortype" value="rgbstroke">Stroke:<span id="rgbstrokepreview"></span> <br>
	  <input type="radio" name="colortype" value="rgbfill" checked>Fill:<span id="rgbfillpreview"></span><BR>
	 <span id="fillstrokepicker"><a href="javascript:void(0)">Color Picker</a></span>
	</td>	
      </tr>
 		  
    </table>
    <table id="shi-options" style="display:none;">

      <tr>
	<td class="postblock">Dimensions</td>
	<td><input name="shi_width" id="shi-width" size="5" value="600" />×<input name="shi_height" id="shi-height" size="5" value="600" /></td>
      </tr>
      <tr>
	<td class="postblock">File name</td>
	<td><input name="shi_filename" value="'.$imagenumber.'.'.$file_type.'" /></td>

      </tr>
      <tr>
	<td class="postblock">Save Animation</td>
	<td><input name="shi_animation" type="checkbox" /></td>
      </tr>
    </table>
    <table>
'.$filtypesallowed.'
	  
      <tr>

	<td colspan="2"><input type="submit" name="draw" value="Draw"></td>
      </tr>
    </table>
    </td></tr>
  </table>
</form>
'.$footer;
}
?>
