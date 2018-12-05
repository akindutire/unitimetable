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
		
		<div class="w3-col l12 m12 s12 w3-animate-zoom w3-padding w3-margin-top">		

			 <form class="w3-col l6 m12 s12 w3-border w3-card-2 w3-round w3-margin-right" style="" action="<?php echo "{$baselink}settings/user/update"; ?>" method="post">

                <span class="w3-col l4 m4 s12 w3-padding">

                    <input title="Old" placeholder="Old Password" type="password" name="opwd" class="w3-input w3-border w3-round">

                </span>
                <span class="w3-col l4 m4 s12 w3-padding">

                    <input title="New" placeholder="New Password" type="password"  name="npwd" class="w3-input w3-border w3-round">

                </span>

                <span class="w3-col l3 m4 s12 w3-padding" style="padding-right: 0px !important;">
                    <button class="w3-btn w3-blue-grey w3-round">Change Password</button>
                </span>

            </form>

			
		</div>


		<div class="w3-col l12 m12 s12 w3-padding-large" style="display: flex; justify-content: center; align-items: center; height: 400px;">

			<div class="w3-col l4 m5 s12">

				<p class="w3-col l12 m12 s12 w3-xlarge "> Create Account </p>
	           	
	           	<form class="w3-col l12 m12 s12" style="margin-top: 1%;" action="<?php echo "{$baselink}settings/user/create"; ?>" method="post">

	                    <input type="text" placeholder="Username" name="uname"  class="w3-input w3-border w3-round"><br>
	                    
	                    <input type="password" placeholder="Password" name="upass"  class="w3-input w3-border w3-round"><br>
	                    
	                    <p class="w3-center w3-col l12 m12 s12">
	                    	<button class="w3-btn w3-green w3-round">Add User</button>
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
