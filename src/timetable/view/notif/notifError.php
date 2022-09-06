<?php
use \zil\factory\View;
$data = View::getInfo();

	$absPath = $data[0];
	$absPathForLinks = $data[2];
	$message = $data[3];
	

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Notif</title>
</head>

<body>



<!--An header-->
	<?php  //include_once("{$absPath}view/asset/template/header.php"); ?>
<!--End header-->

<article class="w3-col l12 m12 s12 w3-padding" style="margin-top: 0px !important; padding-left: 32px !important; padding-right: 32px !important;">
	
	<!--Sidebar here-->
		<?php //include_once("{$absPath}view/asset/template/sidebar.php"); ?>
	

	<section class="w3-col l12 m12 s12" style="display: flex; justify-content: center; align-items: center; height: 400px;">
		
		<div class="w3-col l5 m6 s12 w3-center w3-small w3-border w3-card-2 w3-center w3-round w3-padding-large w3-text-red">
			<?php echo $message[0]; ?>
		</div>

		

	</section>

</article>

<!--Footer-->
	<?php //include_once("{$absPath}view/asset/template/footer.php"); ?>


</body>


</html>
