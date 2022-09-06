

var app = angular.module('app',['ngSanitize']);

app.controller('ctrl',function($scope,$http,$location,$sce,$compile){ 

	
	$scope.retryCount = 0;
	$scope.gwork = {};
	
	$scope.open_course_setting = function(event){
	
		cur_element = event.path[0];
        if (cur_element.getAttribute('data-sig') === null){
            cur_element = event.path[1];
            if(cur_element.getAttribute('data-sig') === null){
                cur_element = event.path[2];
            }
        }

        $scope.gwork.c_code = cur_element.getAttribute('data-c-code');
		
        $scope.load_venue_exemptions($scope.gwork.c_code);
        $scope.load_course_constraint($scope.gwork.c_code);

        document.getElementById('course_setting_modal').style.display = 'block';

        entire_row_nodes = cur_element.parentElement.parentElement.childNodes;

       	basic_details_form_nodes = document.getElementById('form_basic_course_details').childNodes;
       	
       	basic_details_form_nodes[3].value = entire_row_nodes[3].innerText;
       	basic_details_form_nodes[9].value = entire_row_nodes[11].innerText;
        basic_details_form_nodes[15].value = entire_row_nodes[13].innerText;
		
		course_constraint_form_nodes = document.getElementById('form_course_constraint').childNodes;
		course_constraint_form_nodes[3].value = entire_row_nodes[15].innerText;

		
		if(document.getElementById('no_of_class').value > 1)
			document.getElementById('base_constraint').style.display = 'block';
		else
			document.getElementById('base_constraint').style.display = 'none';
		
	};

	$scope.load_venue_exemptions = function(c_code){

		url = $scope.absPath+"process/proc1/get_venue_exemption.php";
		data = {};
		data.course_code = c_code;
		
		$http.post(url,data).then(

				function(response){
					
					if(response.data.success == 1){
						
						arr = response.data.msg;

						//console.log(arr);
						document.getElementById('venue_already_exempted').innerHTML = '';
						for(i in arr){
							
							document.getElementById('venue_already_exempted').insertAdjacentHTML('beforeEnd',"<p><a class='w3-text-red' data-v-id="+i+" ng-click=remove_exemption(\$event)>[-]</a>&nbsp;&nbsp;"+arr[i]+'</p>');
 
						};

						$compile(document.getElementById('venue_already_exempted'))($scope);
					}
				},

				function(status){


				}
			);

	};

	$scope.remove_exemption = function(event){

		cur_element = event.path[0];

		v = cur_element.getAttribute('data-v-id');
		url = $scope.absPath+"process/proc1/remove_venue_exemption.php";
		data = {};
		data.course_code = $scope.gwork.c_code;
		data.venue_id = v;
		
		$http.post(url,data).then(

				function(response){
					
					if(response.data.success == 1){
						
						cur_element.parentElement.remove();

					}
				},

				function(status){


				}
			);

	};

	$scope.load_course_constraint = function(c_code){

		url = $scope.absPath+"process/proc1/get_course_constraint.php";
		data = {};
		data.course_code = c_code;
		
		$http.post(url,data).then(

				function(response){
					
					if(response.data.success == 1){

						daybound = document.getElementById('daybound');
						lecturebound = document.getElementById('lecturebound');
						timebound = document.getElementById('timebound');
						
						
						if(response.data.msg.length != 0){

							arr = response.data.msg[0];

							if(arr[0] == 1)
								daybound.checked = true;
							
							if(arr[1] == 1)
								lecturebound.checked = true;
							
							if (arr[2] == 1)
								timebound.checked = true;

							
							$compile(document.getElementById('base_constraint'))($scope);
						
						}
					}
				},

				function(status){


				}
			);

	};

	$scope.add_course = function(event){

		url = $scope.absPath+"process/proc1/course.php";
		data = {};


		if(typeof($scope.dept_code) != undefined){

			data.department_code = $scope.dept_code;
			data.title = $scope.c_title;
			data.code = $scope.c_code;
			data.unit = $scope.c_unit;
			data.prac = $scope.c_prac;

			data = JSON.stringify(data);
			event.path[0].closest('form').previousElementSibling.innerHTML = "<i>Processing...</i>";
			$http.post(url,data).then(

				function(response){

					//console.log(response.data);
					if(response.data.success == 1){
						
						event.path[0].closest('form').previousElementSibling.innerHTML = "<i>Course Added, refresh to effect</i>";
						alert("Course Added, refresh to effect");

					}else{

						event.path[0].closest('form').previousElementSibling.innerHTML = "<i class='w3-text-red'>"+response.data.msg+"</i>";
					}
				},

				function(status){


				}
			);

		}else{

			alert("Department Code can\'t be empty");
		}

	};

	$scope.edit_basic_course_details = function(event){

		url = $scope.absPath+"process/proc1/course_basic_details.php";
		data = {};

		if(typeof($scope.gwork.c_code) != undefined){

			basic_details_form_nodes = document.getElementById('form_basic_course_details').childNodes;
       	
		
			data.course_code = $scope.gwork.c_code;
			data.title = basic_details_form_nodes[3].value;
			data.hours_req = basic_details_form_nodes[9].value;
			data.straight_hours =  basic_details_form_nodes[15].value;


			data = JSON.stringify(data);
			event.path[0].closest('form').previousElementSibling.innerHTML = "<i>Processing...</i>";
			$http.post(url,data).then(

				function(response){

					//console.log(response.data);
					if(response.data.success == 1){

						event.path[0].closest('form').previousElementSibling.innerHTML = "<i>Changes Saved, refresh to effect</i>";
						alert("Changes Saved, refresh to effect");

					}else{

						event.path[0].closest('form').previousElementSibling.innerHTML = "<i class='w3-text-red'>"+response.data.msg+"</i>";
					}
				},

				function(status){


				}
			);

		}else{

			alert("Department can\'t be empty");
		}

	};

	$scope.edit_course_constraint = function(event){

		url = $scope.absPath+"process/proc1/course_constraint.php";
		data = {};


		daybound = document.getElementById('daybound');
		lecturebound = document.getElementById('lecturebound');
		timebound = document.getElementById('timebound');
		no_of_class = document.getElementById('no_of_class').value;

		if(daybound.checked == true)
			daybound = 1;
		else
			daybound = 0;


		if(lecturebound.checked == true)
			lecturebound = 1;
		else
			lecturebound = 0;


		if (timebound.checked == true)
			timebound = 1;
		else
			timebound = 0;



		data.course_code = $scope.gwork.c_code;
		data.daybound = daybound;
 		data.timebound = timebound;
 		data.lecturebound = lecturebound;
 		data.no_of_class = no_of_class;


 		$http.post(url,data).then(

				function(response){

					//console.log(response.data);
					if(response.data.success == 1){

						event.path[0].closest('form').previousElementSibling.innerHTML = "<i>Changes Saved, refresh to effect</i>";
						alert("Changes Saved, refresh to effect");

					}else{

						event.path[0].closest('form').previousElementSibling.innerHTML = "<i class='w3-text-red'>"+response.data.msg+"</i>";
					}
				},

				function(status){


				}
			);


	};

	$scope.check_constraint = function(event){

		if(event.path[0].value > 1)
			document.getElementById('base_constraint').style.display = 'block';
		else
			document.getElementById('base_constraint').style.display = 'none';

		

	};

	$scope.add_venue_exemption = function(event){

		url = $scope.absPath+"process/proc1/course_venue_exemption.php";
		data = {};

		if(typeof($scope.gwork.c_code) != undefined){

			venue_exemption_form_nodes = document.getElementById('form_venue_exemption').childNodes;
       		
			selected_option = 	document.querySelector('form#form_venue_exemption select#venue_list').selectedOptions;

			data.venues = {};
			for(var i=0; i < selected_option.length; i++){

				data.venues[i] = selected_option[i].value;
			}

			data.course_code = $scope.gwork.c_code;
			
			data = JSON.stringify(data);

			event.path[0].closest('form').previousElementSibling.innerHTML = "<i>Processing...</i>";
			
			
			$http.post(url,data).then(

				function(response){

					//console.log(response.data);
					if(response.data.success == 1){

						event.path[0].closest('form').previousElementSibling.innerHTML = "<i>Changes Saved, refresh to effect</i>";
						alert("Changes Saved, refresh to effect");

					}else{

						event.path[0].closest('form').previousElementSibling.innerHTML = "<i class='w3-text-red'>"+response.data.msg+"</i>";
					}
				},

				function(status){


				}
			);


		}else{

			alert("Department can\'t be empty");
		}
	};


	$scope.remove_course = function(event){

		if(window.confirm("Do you want to delete?")){	
			cur_element = event.path[0];

			url = $scope.absPath+"process/proc1/remove_course.php";
			data = {};
			data.course_code = $scope.gwork.c_code;
					
			$http.post(url,data).then(

					function(response){
						
						if(response.data.success == 1){
							
							alert("Refresh to effect changes");
							document.getElementById('course_setting_modal').style.display = 'none';

						}else{

							alert("Couldn't remove course");
						}
					},

					function(status){


					}
				);
		
		}
			
	};


});