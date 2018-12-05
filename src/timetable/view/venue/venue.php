<?php
use \zil\factory\View;

$data = View::getInfo();
$absPath = $data[0];

$absPathForLinks = $data[2];

$venue = $data[3];

$venue_operation_disabled = $data['venue_op_disabled'];

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>timetable</title>
</head>

<body ng-app="app" ng-controller="ctrl" ng-init=" absPath='<?php echo $absPathForLinks; ?>'; ">



<div class="w3-modal" id="add_venue_modal" style="display: none;">
	<div class="w3-modal-content w3-white" style="width: 50% !important;">
		
		<div class="w3-display-container" style="top: 5%;">
			<a class="w3-display-topleft w3-padding w3-large">Add Venue</a>
			
			<a class="w3-display-topright w3-tag w3-red w3-padding" onclick="document.getElementById('add_venue_modal').style.display='none';"><i class="fa fa-times"></i></a>
		</div>

		<hr class="w3-col">

		<div class="w3-container" style="padding-top: 8% !important;">
			<p id="error"></p>
			<form class="w3-col l9 m9 s12 w3-form">

				<label>Venue</label><br>
				<input type="text" required="required" class="w3-input w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="venue"><br><br>
				<label>Capacity</label><br>
				<input type="number" required="required" min="1" class="w3-input w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="capacity"><br><br>
				<label>Location</label><br>
				<input type="text" class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="location"><br><br>
				<p class="w3-center w3-margin-top"><button type="button" ng-click=addvenue($event) class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Save</button></p>

			</form>

		</div>
	</div>
</div>

<div class="w3-modal" id="edit_venue_modal" style="display: none;">
    <div class="w3-modal-content w3-white" style="width: 50% !important;">

        <div class="w3-display-container" style="top: 5%;">
            <a class="w3-display-topleft w3-padding w3-large">Edit Venue</a>

            <a class="w3-display-topright w3-tag w3-red w3-padding" onclick="document.getElementById('edit_venue_modal').style.display='none';"><i class="fa fa-times"></i></a>
        </div>

        <hr class="w3-col">

        <div class="w3-container" style="padding-top: 8% !important;">

            <p></p>
            <form class="w3-col l9 m9 s12 w3-form">

                <label>Capacity</label><br>
                <input type="number" required="required" min="1"  class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" id="edited_venue_capacity"><br><br>
                <p class="w3-center w3-margin-top"><button data-sig=1 ng-click=edit_venue($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Change</button></p>

            </form>

        </div>
    </div>
</div>

<!--An header-->
	<?php  include_once("{$absPath}view/asset/template/header.php"); ?>
<!--End header-->

<article class="w3-col l12 m12 s12 w3-padding" style="margin-top: 6% !important; padding-left: 0px !important;">
	
	<!--Sidebar here-->
		<?php include_once("{$absPath}view/asset/template/sidebar.php"); ?>
	

	<section class="w3-col l10 m12 s12">
		<p class="w3-col l12 m12 s12 w3-bar w3-animate-zoom w3-padding w3-small">

			<span class="w3-bar-item w3-padding w3-border w3-round w3-card-2 w3-margin-right">
				<button onclick="document.getElementById('add_venue_modal').style.display='block';" class="w3-btn w3-blue-grey w3-round" type="button"><i class="fa fa-plus"></i>&nbsp;Add</button>
			</span>

			<span class="w3-bar-item w3-padding w3-border w3-round w3-card-2">
				<a class="w3-btn w3-blue-grey w3-round" href="<?php echo "{$data['ROUTER_LINK']}venue/import"; ?>">Import Venue List</a>
			</span>
			
		</p>

		<div class="w3-col l12 m12 s12 w3-padding">
			<?php

				if(count($venue) == 0)
					goto nodata;
			
				function pluralize($str, $det){
					if($det > 1)
						return $str.'s';
					else
						return $str;
				}
			?>

			<p class="w3-center w3-xlarge"><?php echo $data['venue_in_use'].' '.pluralize('venue', sizeof($venue)).' in use';?></p><br>
			<table class="w3-table w3-col l12 m12 s12 w3-responsive">
				
				<thead class="w3-light-gray w3-small">
					<tr>
						<th>#</th>
						<th>Venue</th>
						<th>Capacity</th>
						<th>Location</th>

					</tr>				
				</thead>

				<tbody class="w3-small">

					<?php 

						foreach ($venue as $id => $params) {

							$op = '';
							if(!$venue_operation_disabled){
								$pending = '';
								if($params[3] == 1){
									//venue is or not in use
									$pending = 'w3-pale-red';
									$not_or_in_use_handle = "<a ng-click='restore_venue(\$event)' class='w3-green w3-tag w3-white w3-border w3-round w3-text-green w3-padding-small w3-round w3-border' data-v-id='{$id}' title='Restore for allocation'><i class='fas fa-check'></i></a>";

								}else{
									$not_or_in_use_handle = "<a ng-click='suspend_venue(\$event)' class='w3-green w3-tag w3-white w3-border w3-round w3-text-red w3-padding-small w3-round w3-border' data-v-id='{$id}' title='Suspend from allocation'><i class='fas fa-ban'></i></a>";
								}

								if($params[4]){
									//is multisight option
									$multisight_handle = "<a ng-click='mark_as_multisight(\$event)' class='w3-green w3-tag w3-white w3-border w3-round w3-text-blue w3-padding-small w3-round w3-border' data-v-id='{$id}' data-v-to-mark-as='0' title='Unmark from multisight'><i class='far fa-eye-slash'></i></a>";
								}else{
									$multisight_handle = "<a ng-click='mark_as_multisight(\$event)' class='w3-green w3-tag w3-white w3-border w3-round w3-text-black w3-padding-small w3-round w3-border' data-v-id='{$id}' data-v-to-mark-as='1' title='Mark as multisight'><i class='far fa-eye'></i></a>";
								}

								$op = "
								<a ng-click='remove_venue(\$event)' class='w3-text-red w3-tag w3-white w3-border w3-round w3-padding-small w3-round w3-border' data-v-id='{$id}'><i class='fas fa-trash'></i></a>
								<a ng-click='open_edit_venue(\$event)' class='w3-green w3-tag w3-white w3-border w3-round w3-text-green w3-padding-small w3-round w3-border' data-v-id='{$id}'><i class='fas fa-pencil-alt'></i></a>
								
								{$not_or_in_use_handle}

								{$multisight_handle}

								";
							}

							echo "
								<tr class='w3-border-bottom {$pending}'>
									<td>{$op}</td>
									<td>{$params[0]}</td>
									<td>{$params[1]}</td>
									<td>{$params[2]}</td>
								</tr>
							";
						}
					?>
					
				</tbody>

			</table>

			<?php
				goto nop;
				nodata:echo "<p class='w3-large'>No venue found</p>";

				nop:
			?>


		</div>
	</section>

</article>

<!--Footer-->
	<?php include_once("{$absPath}view/asset/template/footer.php"); ?>


</body>

<script type="text/javascript" src="<?php echo "{$absPathForLinks}view/asset/js/angularApp/timetable.js"; ?>"></script>

</html>

