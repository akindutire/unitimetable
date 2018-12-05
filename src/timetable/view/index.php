<?php

use \zil\factory\View;
$data = View::getInfo();

$absPath = $data[0];

$absPathForLinks = $data[2];

$basehref = $data[3];

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>timetable</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="icon" type="text/css" href="<?php echo "{$absPathForLinks}view/asset/img/ui1.jpg"; ?>">

</head>

<body>

<!--An header-->
	<?php  include_once("{$absPath}view/asset/template/header.php"); ?>
<!--End header-->

<article class="w3-col l12 m12 s12 w3-padding" style="display: flex; align-items: center; justify-content: center; height: 600px;">
	


	<section class="w3-col l3 m5 s12">
		

		<form class='w3-form' action='<?php echo "{$basehref}login"; ?>' method='post'>
			
			<input type="text" class="w3-input w3-border" name="username" placeholder="Username"><br>
			<input type="password" class="w3-input w3-border" name="password" placeholder="Password"><br>

			<p class="w3-center">
				<button class="w3-btn w3-green w3-round">Enter</button>
			</p>

		</form>


	</section>

</article>

<!--Footer-->
	<?php include_once("{$absPath}view/asset/template/footer.php"); ?>

</body>
	
	<script type="text/javascript" src="<?php echo "{$absPathForLinks}view/asset/js/angularApp/timetable.js"; ?>"></script>


</html>
