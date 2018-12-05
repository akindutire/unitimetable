<?php
use \zil\factory\View;
$data = View::getInfo();

$absPath = $data[0];
$absPathForLinks = $data[2];
$baselink = $data[3];

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>timetable</title>
</head>

<body ng-app="app" ng-controller="ctrl" ng-init=" absPath='<?php echo $absPathForLinks; ?>'; ">



<!--An header-->
	<?php  include_once("{$absPath}view/asset/template/header.php"); ?>
<!--End header-->

<article class="w3-col l12 m12 s12 w3-padding" style="margin-top: 6% !important; padding-left: 0px !important;">
	
	<!--Sidebar here-->
		<?php include_once("{$absPath}view/asset/template/sidebar.php"); ?>
	

	<section class="w3-col l10 m9 s12">
		<!--<p class="w3-col l12 m12 s12 w3-bar w3-animate-zoom">
			<span class="w3-col l5 m6 s12 w3-padding">
				<input type="text" class="w3-input w3-col l10 m8 s7 w3-border" ng-model="searchdept" placeholder="Search Department"><button class="w3-col l2 m4 s5  w3-button w3-border w3-border-blue-gray w3-blue-grey w3-padding-large" style="" type="button"><i class="fa fa-search"></i></button>
			</span>
			
		</p>-->

		<div class="w3-col l3 m4 s12 w3-padding" style="display: flex; justify-content: center;">

            <?php
			   
            ?>

		</div>
	</section>

</article>

<!--Footer-->
	<?php include_once("{$absPath}view/asset/template/footer.php"); ?>


</body>

<script type="text/javascript" src="<?php echo "{$absPathForLinks}view/asset/js/angularApp/course.js"; ?>"></script>

</html>
