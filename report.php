<?php


// by jmyeom
$board = $_GET['bo'];
$post = $_GET['no'];

include('config.php');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Report Post #<?php echo $post; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="<?php echo $cf['KU_WEBPATH']; ?>css/burichan.css">
		<style>fieldset { margin-right: 25px; }</style>
	</head>
<body>

<form action='board.php' method="POST">
<table width="100%">
<tr><td>

	<fieldset><legend>Report type</legend>
	<input type="radio" name="reportreason" value="Rule violation" checked>Rule violation<br/>
	<input type="radio" name="reportreason" value="Illegal content">Illegal content<br/>
	<input type="radio" name="reportreason" value="Spam/advertising/flooding">Spam
	</fieldset>
</td><td>
<!--<a href="#" onclick="javascript:document.getElementById('captchaimage').src = '<?php echo $cf['KU_WEBPATH']; ?>captcha.php?' + Math.random();return false;"><img id="captchaimage" src="<?php echo $cf['KU_WEBPATH']; ?>captcha.php" alt="Captcha image" border="0" height="25" width="90"></a>
</td>
<td>
<input name="captcha" size="28" maxlength="10" accesskey="c" type="text">
</td></tr>
</table>-->
<table width="100%"><tr><td width="240px"></td><td>
<input type="submit" value="Submit">
<!--
You are reporting post <b><?php echo $post; ?></b> on /<?php echo $board; ?>/.
-->
<input type="hidden" name="board" value="<?php echo $board; ?>">
<input type="hidden" name="post[]" value="<?php echo $post; ?>">
<input type="hidden" name="reportpost" value="true">
</td></tr></table>
</center>
</form>
<br>
<div class='rules'><u>Note</u>: Submitting frivolous reports will result in a ban. When reporting, make sure that the post in question violates the global/board rules, or contains content illegal in the United States.</div>

	</body>
</html>
</body></html>
