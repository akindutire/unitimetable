<?php
use \zil\factory\View;
$data = View::getInfo();

$absPath = $data[0];
$absPathForLinks = $data[2];

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
		
		<div class="w3-col l12 m12 s12 w3-animate-zoom w3-padding w3-margin-top">		

			 			
		</div>


		<div class="w3-col l12 m12 s12 w3-padding-large" style="display: flex; justify-content: center; align-items: center; height: 400px;">

			<div class="w3-col l5 m6 s12">

				<p class="w3-col l12 m12 s12">
	                <a href="<?php echo $absPathForLinks.'database/coursecapacityformatdoc.csv'; ?>" download>Download course capacity list format</a>
	             </p>

				<p class="w3-col l12 m12 s12 w3-large "> Import Course Capacity (csv file required)</p>
	           	
	           	<form class="w3-col l12 m12 s12" style="margin-top: 1%;" action="<?php echo "{$absPathForLinks}settings/import/coursecapacity"; ?>" method="post" enctype="multipart/form-data">

	                    
	                    <input type="file" name="file" id="" class="w3-input w3-border w3-round"><br>
	                    
	                    <p class="w3-center w3-col l12 m12 s12">
	                    	<button class="w3-btn w3-green w3-round">Import</button>
	                    </p>
	                
	            </form>
	        
	        </div>

		</div>
	</section>

</article>

<!--Footer-->
	<?php include_once("{$absPath}view/asset/template/footer.php"); ?>


</body>

<script type="text/javascript" src="<?php echo "{$absPathForLinks}view/asset/js/angularApp/course.js"; ?>"></script>

</html>
