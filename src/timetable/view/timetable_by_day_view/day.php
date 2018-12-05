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

			<span class="w3-col l3 m4 s12 w3-padding w3-card-2 w3-border w3-round">
				<a class="w3-col l12 m12 s12  w3-btn w3-round w3-border-blue-gray w3-blue-grey w3-padding" style="" href="<?php echo "{$data['ROUTER_LINK']}timetable"; ?>">Generate Timetable</a>
			</span>
			
		</div>

		<div class="w3-col l12 m12 s12 w3-padding-large" style="">

            <?php
			    $days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];

			        foreach($days as $id => $day){

                        $id++;
                		
                		echo "<div class='w3-col l3 m4 s12 w3-padding'>";
                        
                        echo "<div class='w3-col w3-round w3-card w3-padding'>";	
	                        
	                        echo "<p class='w3-large'>$day</p>";

	                        	echo "<div class=''>
	                        			
	                        			<span class='w3-margin-right'><a class='w3-small w3-bar-item w3-tag w3-white' style='text-decoration: none;' href='{$baselink}day/reset/$id'><i class='fa fa-cogs w3-text-red'></i>&nbsp;Reset</a></span>
		                        		
		                        		
		                        		<span class=''><a class='w3-small w3-bar-item w3-tag w3-white' target='_new' style='text-decoration: none;' href='{$baselink}day/open/$id'><i class='fa fa-eye'></i>&nbsp;View</a></span>
		                        		
		                        		<elon class='w3-clear'></elon>
	                        		
	                        		</div>";

	                    echo "</div>";
                        
                        echo "</div>";
                    
                    }

                    echo "<div class='w3-col l3 m4 s12 w3-padding'>";
                        
                        echo "<div class='w3-col w3-round w3-card w3-padding'>";	
	                        
	                        echo "<p class='w3-large'>Incomplete Allocation</p>";

	                        	echo "<div class=''>
	                        			
	                        			
		                        		<span class=''><a class='w3-small w3-bar-item w3-tag w3-white' target='_new' style='text-decoration: none;' href='{$baselink}day/unallocated'><i class='fa fa-eye'></i>&nbsp;View</a></span>
		                        		
		                        		<elon class='w3-clear'></elon>
	                        		
	                        		</div>";

	                    echo "</div>";
                        
					echo "</div>";
					
					echo "<div class='w3-col l3 m4 s12 w3-padding'>";
                        
                        echo "<div class='w3-col w3-round w3-card w3-padding'>";	
	                        
	                        echo "<p class='w3-large'>Forgotten Allocation</p>";

	                        	echo "<div class=''>
	                        			
	                        			
		                        		<span class=''><a class='w3-small w3-bar-item w3-tag w3-white' target='_new' style='text-decoration: none;' href='{$baselink}day/forgotten'><i class='fa fa-eye'></i>&nbsp;View</a></span>
		                        		
		                        		<elon class='w3-clear'></elon>
	                        		
	                        		</div>";

	                    echo "</div>";
                        
                    echo "</div>";

         				
            ?>

		</div>
	</section>

</article>

<!--Footer-->
	<?php include_once("{$absPath}view/asset/template/footer.php"); ?>


</body>

<script type="text/javascript" src="<?php echo "{$absPathForLinks}view/asset/js/angularApp/course.js"; ?>"></script>

</html>
