var app = angular.module('app', [ 'ngSanitize' ]);

app.controller('ctrl', function($scope, $http, $location, $sce, $compile) {
	$scope.retryCount = 0;
	$scope.gwork = {};
	$scope.gwork.changes = [];
	$scope.gwork.isChanges = false;
	$scope.gwork.courseparticipants = [];
	$scope.gwork.fixedtimings = [];

	$scope.open_course_setting = function(event, code = null) {
		if (code === null) {
			cur_element = event.path[0];
			if (cur_element.getAttribute('data-sig') == null) {
				cur_element = event.path[1];
				if (cur_element.getAttribute('data-sig') == null) {
					cur_element = event.path[2];
				}
			}

			$scope.gwork.c_code = cur_element.getAttribute('data-c-code');
		} else {
			$scope.gwork.c_code = code;
		}

		$scope.gwork.changes = [];

		document.getElementById('course_setting_modal').style.display = 'block';

		$scope.gwork.saved = false;

		$scope.load_venue_exemptions($scope.gwork.c_code);
		$scope.load_course_constraint($scope.gwork.c_code);

		$scope.load_basic_details($scope.gwork.c_code);

		if (document.getElementById('no_of_class').value > 1)
			document.getElementById('base_constraint').style.display = 'block';
		else document.getElementById('base_constraint').style.display = 'none';
	};

	$scope.open_course_participants = function(e) {
		$scope.load_course_participants($scope.gwork.c_code);
		document.querySelector('form#course_participant_frm').reset();
		document.getElementById('course_participant_modal').style.display = 'block';
		document.getElementById('course_setting_modal').style.display = 'none';
	};

	$scope.open_course_fixing_modal = function(e) {
		$scope.load_fix_timings($scope.gwork.c_code);
		document.querySelector('form#fixed_allocation_frm').reset();
		document.getElementById('fixed_allocation_modal').style.display = 'block';
	};

	$scope.load_fix_timings = function(c_code) {
		url = $scope.absPath + 'process/proc1/get_fix_timings.php';
		data = {};
		data.course_code = c_code;
		$http.post(url, data).then(
			function(response) {
				//console.log(response.data);
				if (response.data.success === 1) {
					arr = response.data.msg;

					template = '';

					document.querySelector('div#fixed_timings_view').innerHTML = '';

					for (var i in arr) {
						item = arr[i];
						// console.log(item);
						key = item.venue_id + '|' + item.day_id + '|' + item.time_id + '|' + 0;
						$scope.gwork.fixedtimings.push(key);

						v_txt = '? System decides venue ';
						d_txt = '? System decides day';
						t_txt = '? System decides time';

						if (item.venue_id != 0) v_txt = item.venue;

						if (item.day_id != 0) d_txt = item.day;

						if (item.time_id != 0) t_txt = item.time;

						template =
							"<div class='w3-margin-top w3-padding w3-card-2 w3-round w3-col l12 m12 s12'><h4 class='w3-col l12 m12 s12 w3-text-blue-grey'>" +
							d_txt +
							"</h4><p class='w3-col l12 m12 s12'><a class='w3-text-red' data-p-id=" +
							key +
							' ng-click=logFixedTimingsRemoval($event)>[-]</a>&nbsp;&nbsp;' +
							v_txt +
							'( ' +
							t_txt +
							')</p></div>';

						document.querySelector('div#fixed_timings_view').insertAdjacentHTML('beforeEnd', template);
					}

					$compile(document.querySelector('div#fixed_timings_view'))($scope);

					$scope.loading_notification = '';
				}
			},
			function(status) {}
		);
	};

	$scope.load_course_participants = function(c_code) {
		$scope.gwork.courseparticipants = [];

		url = $scope.absPath + 'process/proc1/get_course_participant.php';
		data = {};
		data.course_code = c_code;

		$http.post(url, data).then(
			function(response) {
				document.getElementById('course_participant_view').innerHTML = 'Loading...';

				if (response.data.success == 1) {
					if (response.data.msg.length > 0) {
						arr = response.data.msg;

						template = '';

						document.querySelector('div#course_participant_view').innerHTML = '';

						arr.forEach(function(item) {
							key = item.code + '' + item.level;
							$scope.gwork.courseparticipants.push(key);

							template =
								"<p class='w3-margin-top w3-col l12 m12 s12'><a class='w3-text-red' data-p-id=" +
								key +
								' ng-click=logCourseParticipantRemoval($event)>[-]</a>&nbsp;&nbsp;' +
								item.department +
								'(Part ' +
								item.level +
								')</p>';

							document
								.querySelector('div#course_participant_view')
								.insertAdjacentHTML('beforeEnd', template);
						});

						$compile(document.querySelector('div#course_participant_view'))($scope);
					} else {
						document.querySelector('div#course_participant_view').innerHTML = '';
						alert('No Participant');
					}
				} else if (response.data.success == 2) {
					document.querySelector('div#course_participant_view').innerHTML = '';
					alert('Participant list is undefined (maybe large or small)');
				} else {
					document.getElementById('course_participant_view').innerHTML = '';
					console.log(response.data);
				}
			},
			function(status) {}
		);
	};

	$scope.load_basic_details = function(c_code) {
		$scope.loading_notification = '  Loading Details... ';

		url = $scope.absPath + 'process/proc1/get_basic_details.php';
		data = {};
		data.course_code = c_code;

		$http.post(url, data).then(
			function(response) {
				//console.log(response.data);
				if (response.data.success === 1) {
					arr = response.data.msg;

					basic_details_form_title = document.querySelector('form#form_basic_course_details input#title');
					basic_details_form_hrsReq = document.querySelector('form#form_basic_course_details select#hrsReq');
					basic_details_form_strHrs = document.querySelector('form#form_basic_course_details select#strHrs');
					basic_details_form_timeBound = document.querySelector(
						'form#form_basic_course_details input#timebound'
					);

					basic_details_form_capacity = document.querySelector(
						'form#form_basic_course_details input#course_capacity'
					);

					course_constraint_input = document.querySelector('form#form_course_constraint input#no_of_class');

					basic_details_form_title.value = arr.title;
					basic_details_form_hrsReq.value = arr.hoursReq;
					basic_details_form_strHrs.value = arr.straigthHrs;
					basic_details_form_capacity.value = arr.totalRegistered;

					course_constraint_input.value = arr.class;

					if (arr.class > 1) document.getElementById('base_constraint').style.display = 'block';
					else document.getElementById('base_constraint').style.display = 'none';

					if (arr.timebound == 1) {
						basic_details_form_timeBound.checked = true;
					} else {
						basic_details_form_timeBound.checked = false;
					}

					$compile(document.getElementById('venue_already_exempted'))($scope);

					$scope.loading_notification = '';
				}
			},
			function(status) {}
		);
	};

	$scope.load_venue_exemptions = function(c_code) {
		url = $scope.absPath + 'process/proc1/get_venue_exemption.php';
		data = {};
		data.course_code = c_code;

		$http.post(url, data).then(
			function(response) {
				if (response.data.success == 1) {
					arr = response.data.msg;

					//console.log(arr);
					document.getElementById('venue_already_exempted').innerHTML = '';

					for (i in arr) {
						$scope.gwork.changes.push(i);

						document
							.getElementById('venue_already_exempted')
							.insertAdjacentHTML(
								'beforeEnd',
								"<p class='w3-margin-top w3-col l12 m12 s12'><a class='w3-text-red' data-v-id=" +
									i +
									' ng-click=logVenueExemptionRemoval($event)>[-]</a>&nbsp;&nbsp;' +
									arr[i] +
									'</p>'
							);
					}

					$compile(document.getElementById('venue_already_exempted'))($scope);
				}
			},
			function(status) {}
		);
	};

	$scope.load_course_constraint = function(c_code) {
		url = $scope.absPath + 'process/proc1/get_course_constraint.php';
		data = {};
		data.course_code = c_code;

		$http.post(url, data).then(
			function(response) {
				if (response.data.success == 1) {
					daybound = document.getElementById('daybound');
					lecturebound = document.getElementById('lecturebound');
					multisightbound = document.getElementById('multisightbound');

					if (response.data.msg.length != 0) {
						arr = response.data.msg[0];

						if (arr[0] == 1) daybound.checked = true;
						else daybound.checked = false;

						if (arr[3] == 1) {
							multisightbound.checked = true;
							document.querySelector('a#multiresource').style.display = 'inline';
						} else {
							multisightbound.checked = false;
							document.querySelector('a#multiresource').style.display = 'none';
						}

						if (arr[1] == 1) lecturebound.checked = true;
						else lecturebound.checked = false;

						$compile(document.getElementById('base_constraint'))($scope);
					}
				}
			},
			function(status) {}
		);
	};

	$scope.logFixedTimingsRemoval = function(e) {
		event.stopImmediatePropagation();

		cur_element = event.path[0];

		allocationkey = cur_element.getAttribute('data-p-id');

		if (typeof allocationkey !== undefined || typeof allocationkey !== null) {
			i = $scope.gwork.fixedtimings.indexOf(allocationkey);

			$scope.gwork.fixedtimings.splice(i, 1);

			cur_element.parentElement.parentElement.remove();
		} else {
			alert('Couldnt remove participant');
		}

		console.log($scope.gwork.fixedtimings);
	};

	$scope.logCourseParticipantRemoval = function(e) {
		event.stopImmediatePropagation();

		cur_element = event.path[0];

		participantkey = cur_element.getAttribute('data-p-id');

		if (typeof participantkey !== undefined || typeof participantkey !== null) {
			i = $scope.gwork.courseparticipants.indexOf(participantkey);

			$scope.gwork.courseparticipants.splice(i, 1);

			cur_element.parentElement.remove();
		} else {
			alert('Couldnt remove participant');
		}
	};

	$scope.logVenueSelection = function() {
		selected_option = document.querySelector('form#form_venue_exemption select#venue_list').selectedOptions;

		for (var i = 0; i < selected_option.length; i++) {
			if ($scope.gwork.changes.indexOf(selected_option[i].value) < 0) {
				$scope.gwork.changes.push(selected_option[i].value);

				template =
					"<p class='w3-margin-top w3-col l12 m12 s12'><a class='w3-text-red' data-v-id=" +
					selected_option[i].value +
					' ng-click=logVenueExemptionRemoval($event)>[-]</a>&nbsp;&nbsp;' +
					selected_option[i].innerHTML +
					'</p>';

				document.querySelector('div#venue_already_exempted').insertAdjacentHTML('beforeEnd', template);
			}
		}

		$compile(document.querySelector('div#venue_already_exempted'))($scope);
	};

	$scope.logVenueExemptionRemoval = function(event) {
		event.stopImmediatePropagation();

		cur_element = event.path[0];

		venueId = cur_element.getAttribute('data-v-id');

		if (venueId > 0) {
			i = $scope.gwork.changes.indexOf(venueId);

			if (i >= 0) $scope.gwork.changes.splice(i, 1);

			cur_element.parentElement.remove();
		} else {
			alert("Couldn't Remove Exemption");
		}
	};

	$scope.fix_course_to_slot = function(e) {
		venue = document.querySelector('form#fixed_allocation_frm select#venue_list');
		day = document.querySelector('form#fixed_allocation_frm select#day');
		time = document.querySelector('form#fixed_allocation_frm select#time');
		if (venue.value != 0 || day.value != 0 || time.value != 0) {
			$scope.check_course_affordability($scope.gwork.c_code, venue.value, time.value, day.value);
		} else {
			alert('Please choose at least a Venue or Day or Time');
		}
		// console.log($scope.gwork.fixedtimings);
	};

	$scope.check_course_affordability = function(c_code, venue, time, day) {
		url = $scope.absPath + 'process/proc1/check_course_affordance.php';
		data = {};
		data.course_code = c_code;
		data.venue_id = venue;
		data.time_id = time;
		data.day_id = day;
		data.size_of_uncommitted_allocation = $scope.gwork.fixedtimings.length;

		$http.post(url, data).then(
			function(response) {
				console.log(response.data);
				if (response.data.success == 1) {
					if (response.data.msg[0] === false) {
						if (response.data.msg[1] !== undefined) alert(response.data.msg[1]);

						if (response.data.msg[2] !== undefined) alert(response.data.msg[2]);

						if (response.data.msg[3] !== undefined) alert(response.data.msg[3]);

						return false;
					} else {
						venue = document.querySelector('form#fixed_allocation_frm select#venue_list');
						day = document.querySelector('form#fixed_allocation_frm select#day');
						time = document.querySelector('form#fixed_allocation_frm select#time');

						key = venue.value + '|' + day.value + '|' + time.value + '|' + 0;
						if ($scope.gwork.fixedtimings.indexOf(key) < 0) {
							$scope.gwork.fixedtimings.push(key);

							v_txt = '? System decides venue ';
							d_txt = '? System decides day';
							t_txt = '? System decides time';

							if (venue.value != 0) v_txt = venue.options[venue.selectedIndex].text;

							if (day.value != 0) d_txt = day.options[day.selectedIndex].text;

							if (time.value != 0) t_txt = time.options[time.selectedIndex].text;

							template =
								"<div class='w3-margin-top w3-padding w3-card-2 w3-round w3-col l12 m12 s12'><h4 class='w3-col l12 m12 s12 w3-text-blue-grey'>" +
								d_txt +
								"</h4><p class='w3-col l12 m12 s12'><a class='w3-text-red' data-p-id=" +
								key +
								' ng-click=logFixedTimingsRemoval($event)>[-]</a>&nbsp;&nbsp;' +
								v_txt +
								'( ' +
								t_txt +
								')</p></div>';

							document.querySelector('div#fixed_timings_view').insertAdjacentHTML('afterBegin', template);

							$compile(document.querySelector('div#fixed_timings_view'))($scope);
						}
					}
				}
			},
			function(status) {}
		);
	};

	$scope.add_course_participant = function(e) {
		departmentSelectedBox = document.querySelector('form#course_participant_frm select#dept').selectedOptions;
		level = document.querySelector('form#course_participant_frm select#level').value;

		for (var i = 0; i < departmentSelectedBox.length; i++) {
			key = departmentSelectedBox[i].value + '' + level;
			if ($scope.gwork.courseparticipants.indexOf(key) < 0) {
				if (departmentSelectedBox[i].value != 0 && level != 0) {
					$scope.gwork.courseparticipants.push(key);

					template =
						"<p class='w3-margin-top w3-col l12 m12 s12'><a class='w3-text-red' data-p-id=" +
						key +
						' ng-click=logCourseParticipantRemoval($event)>[-]</a>&nbsp;&nbsp;' +
						departmentSelectedBox[i].innerHTML +
						'(Part ' +
						level +
						')</p>';

					document.querySelector('div#course_participant_view').insertAdjacentHTML('afterBegin', template);

					$compile(document.querySelector('div#course_participant_view'))($scope);
				} else {
					alert('Please Choose a department and level');
				}
			}
		}

		console.log($scope.gwork.courseparticipants);
	};

	$scope.close_fixed_allocation = function() {
		document.getElementById('fixed_allocation_modal').style.display = 'none';
	};

	$scope.close_course_participants = function() {
		document.getElementById('course_setting_modal').style.display = 'block';
		document.getElementById('course_participant_modal').style.display = 'none';
	};

	$scope.monitor_changes = function() {
		$scope.gwork.isChanges = true;
	};

	$scope.edit_course_fixings = function() {
		url = $scope.absPath + 'process/proc1/edit_course_fixings.php';
		data = {};

		if (typeof $scope.gwork.c_code != undefined && $scope.gwork.fixedtimings.length > 0) {
			data.course_code = $scope.gwork.c_code;

			data.timings = {};

			for (var i = 0; i < $scope.gwork.fixedtimings.length; i++) {
				data.timings[i] = $scope.gwork.fixedtimings[i];
			}

			data = JSON.stringify(data);

			$scope.loading_notification = 'Processing Course Fixings...';

			$http.post(url, data).then(
				function(response) {
					console.log(response.data);

					if (response.data.success === 1) {
						$scope.loading_notification = 'Course Fixings Saved';
					} else {
						$scope.loading_notification = '';
					}
				},
				function(status) {}
			);
		}
	};

	$scope.edit_course_participant = function() {
		url = $scope.absPath + 'process/proc1/edit_course_participant.php';
		data = {};

		if (typeof $scope.gwork.c_code != undefined) {
			data.course_code = $scope.gwork.c_code;

			data.participants = {};

			if ($scope.gwork.courseparticipants.length > 0) {
				for (var i = 0; i < $scope.gwork.courseparticipants.length; i++) {
					data.participants[i] = $scope.gwork.courseparticipants[i];
				}
			}

			data = JSON.stringify(data);

			$scope.loading_notification = 'Processing Course Participants...';

			$http.post(url, data).then(
				function(response) {
					//console.log(response.data);

					if (response.data.success === 1) {
						$scope.loading_notification = 'Course Participants Saved';
					} else {
						$scope.loading_notification = '';
					}
				},
				function(status) {}
			);
		}
	};

	$scope.edit_basic_course_details = function() {
		url = $scope.absPath + 'process/proc1/course_basic_details.php';
		data = {};

		if (typeof $scope.gwork.c_code != undefined) {
			basic_details_form_nodes = document.getElementById('form_basic_course_details').childNodes;

			basic_details_form_title = document.querySelector('form#form_basic_course_details input#title');
			basic_details_form_hrsReq = document.querySelector('form#form_basic_course_details select#hrsReq');
			basic_details_form_strHrs = document.querySelector('form#form_basic_course_details select#strHrs');
			basic_details_form_timeBound = document.querySelector('form#form_basic_course_details input#timebound');
			basic_details_form_capacity = document.querySelector(
				'form#form_basic_course_details input#course_capacity'
			);

			data.course_code = $scope.gwork.c_code;
			data.title = basic_details_form_title.value;
			data.hours_req = basic_details_form_hrsReq.value;
			data.straight_hours = basic_details_form_strHrs.value;
			data.totalRegistered = basic_details_form_capacity.value;

			if (basic_details_form_timeBound.checked == true) data.timebound = 1;
			else data.timebound = 0;

			data = JSON.stringify(data);

			$scope.loading_notification = 'Processing Course Details...';

			$http.post(url, data).then(
				function(response) {
					//console.log(response.data);
					if (response.data.success == 1) {
						$scope.loading_notification = 'Basic Details Saved';
					} else {
						$scope.loading_notification = '';
						//alert("Basic Details:: "+response.data.msg);
					}
				},
				function(status) {}
			);
		} else {
			alert("Department can't be empty");
		}
	};

	$scope.edit_course_constraint = function() {
		url = $scope.absPath + 'process/proc1/course_constraint.php';
		data = {};

		daybound = document.getElementById('daybound');
		lecturebound = document.getElementById('lecturebound');
		multisightbound = document.getElementById('multisightbound');
		no_of_class = document.getElementById('no_of_class').value;

		if (daybound.checked == true) daybound = 1;
		else daybound = 0;

		if (lecturebound.checked == true) lecturebound = 1;
		else lecturebound = 0;

		if (multisightbound.checked == true) multisightbound = 1;
		else multisightbound = 0;

		data.course_code = $scope.gwork.c_code;
		data.daybound = daybound;
		data.lecturebound = lecturebound;
		data.multisightbound = multisightbound;
		data.no_of_class = no_of_class;

		$scope.loading_notification = 'Processing Course Constraints...';
		$http.post(url, data).then(
			function(response) {
				//console.log(response.data);
				if (response.data.success == 1) {
					$scope.loading_notification = 'Course Constraints Saved';
				} else {
					$scope.loading_notification = '';
					//alert("Course Constraints:: "+response.data.msg);
				}
			},
			function(status) {}
		);
	};

	$scope.check_constraint = function(event) {
		$scope.monitor_changes();

		if (event.path[0].value > 1) document.getElementById('base_constraint').style.display = 'block';
		else document.getElementById('base_constraint').style.display = 'none';
	};

	$scope.edit_venue_exemption = function() {
		url = $scope.absPath + 'process/proc1/course_venue_exemption.php';
		data = {};

		if (typeof $scope.gwork.c_code != undefined) {
			venue_exemption_form_nodes = document.getElementById('form_venue_exemption').childNodes;

			selected_option = document.querySelector('form#form_venue_exemption select#venue_list').selectedOptions;

			data.venues = {};

			for (var i = 0; i < $scope.gwork.changes.length; i++) {
				data.venues[i] = $scope.gwork.changes[i];
			}

			data.course_code = $scope.gwork.c_code;

			data = JSON.stringify(data);

			$scope.loading_notification = 'Processing Venue Exemption...';

			$http.post(url, data).then(
				function(response) {
					//console.log(response.data);
					if (response.data.success == 1) {
						$scope.loading_notification = 'Venue Exemption Saved';
					} else {
						$scope.loading_notification = '';
						//alert("Venue Exemption:: "+response.data.msg);
					}
				},
				function(status) {}
			);
		} else {
			alert("Department can't be empty");
		}
	};

	$scope.save_all_changes = function(event) {
		if (confirm('Save All Changes')) {
			/**
       *	Basic Course Details
       */

			$scope.gwork.saved = true;
			$scope.gwork.isChanges = false;

			$scope.edit_basic_course_details();
			$scope.edit_course_constraint();
			$scope.edit_venue_exemption();
			$scope.edit_course_participant();
			$scope.edit_course_fixings();
		}
	};

	$scope.close_setting = function() {
		if ($scope.gwork.isChanges === true && $scope.gwork.saved === false) {
			if (confirm('Do you want to save changes')) {
				$scope.save_all_changes();
				$scope.gwork.changes = [];
				$scope.gwork.courseparticipants = [];

				/**
         *	Refresh Page, been the last Async Call
         */
				$scope.refreshPage();
			}
		}

		document.getElementById('course_setting_modal').style.display = 'none';
	};

	$scope.refreshPage = function() {
		if (confirm('Refresh this page?')) {
			/**
       *	Reload page from server, an args false will reload it from cache
       */

			window.location.reload(true);
		}
	};

	$scope.add_course = function(event) {
		url = $scope.absPath + 'process/proc1/course.php';
		data = {};

		if (typeof $scope.c_dept != undefined) {
			data.department_code = $scope.c_dept;
			data.title = $scope.c_title;
			data.code = $scope.c_code;
			data.unit = $scope.c_unit;

			$scope.gwork.temp_c_code = $scope.c_code;

			c_prac = document.querySelector('input#c_prac');

			if (c_prac.checked == true) data.prac = 1;
			else data.prac = 0;

			data = JSON.stringify(data);
			event.path[0].closest('form').previousElementSibling.innerHTML = '<i>Processing...</i>';
			$http.post(url, data).then(
				function(response) {
					//console.log(response.data);
					if (response.data.success == 1) {
						event.path[0].closest('form').previousElementSibling.innerHTML =
							'<i>Course Added, refresh to effect</i>';

						document.getElementById('add_course_modal').style.display = 'none';

						$scope.open_course_setting(null, $scope.gwork.temp_c_code);

						$scope.gwork.temp_c_code = undefined;
					} else {
						event.path[0].closest('form').previousElementSibling.innerHTML =
							"<i class='w3-text-red'>" + response.data.msg + '</i>';
					}
				},
				function(status) {}
			);
		} else {
			alert("Department Code can't be empty");
		}
	};

	$scope.remove_course = function(event) {
		if (window.confirm('Do you want to delete?')) {
			cur_element = event.path[0];

			url = $scope.absPath + 'process/proc1/remove_course.php';
			data = {};
			data.course_code = $scope.gwork.c_code;

			$http.post(url, data).then(
				function(response) {
					if (response.data.success == 1) {
						$scope.refreshPage();
						document.getElementById('course_setting_modal').style.display = 'none';
					} else {
						alert("Couldn't remove course");
					}
				},
				function(status) {}
			);
		}
	};

	$scope.fix_course = function(event) {
		url = $scope.absPath + 'process/proc1/fixcourse.php';
		data = {};

		if (document.querySelector('input#c_clash').checked) {
			data.check_clashes = true;
		} else {
			if (confirm("Attention: You didn't avoid course clash \n \tDo you want continue")) {
				data.check_clashes = false;
			} else {
				return false;
			}
		}

		data.tolerance = parseInt($scope.tolerance);

		if (data.tolerance < 0) {
			alert('Tolerance cant be negative');
			return false;
		}

		if ((data.tolerance = 1)) {
			data.tolerance = 1;
		}

		data.day = $scope.day;

		c_code = document.getElementById('CourseCodeId').innerHTML;

		data.c_code = c_code;
		document.querySelector('p#error').style.color = 'green';
		$scope.tolerance_feedback = 'Processing...';

		if (typeof c_code != undefined) {
			event.target.setAttribute('disabled', 'disabled');
			$http.post(url, data).then(
				function(response) {
					//console.log(response.data);

					if (response.data.success == 1) {
						document.querySelector('p#error').style.color = 'green';
						$scope.tolerance_feedback = 'Course has been fixed';
						alert('Refresh to see changes');
					} else {
						document.querySelector('p#error').style.color = 'red';
						$scope.tolerance_feedback = response.data.msg;
					}

					event.target.removeAttribute('disabled');
				},
				function(status) {
					console.log(status);
				}
			);
		} else {
			alert('An Error Occured, refresh to continue');
		}
	};

	$scope.override_course = function(e) {
		if (confirm('Do you want to forget this course') !== true) return false;

		url = $scope.absPath + 'process/proc1/overridecourse.php';
		data = {};
		data.c_code = e.target.getAttribute('data-code');

		if (typeof c_code != undefined) {
			e.target.innerHTML = 'Forgetting...';
			e.target.setAttribute('disabled', 'disabled');
			$http.post(url, data).then(
				function(response) {
					//console.log(response.data);

					if (response.data.success == 1) {
						e.target.parentElement.parentElement.remove();
						$scope.unallocated -= 1;
					} else {
						alert(response.data.msg);
					}
					e.target.innerHTML = 'Forget';
					e.target.removeAttribute('disabled');
				},
				function(status) {}
			);
		} else {
			alert('An Error Occured, refresh to continue');
		}
	};

	$scope.undo_override_course = function(e) {
		url = $scope.absPath + 'process/proc1/undooverridecourse.php';
		data = {};
		data.c_code = e.target.getAttribute('data-code');

		if (typeof c_code != undefined) {
			e.target.innerHTML = '...';
			e.target.setAttribute('disabled', 'disabled');
			$http.post(url, data).then(
				function(response) {
					//console.log(response.data);

					if (response.data.success == 1) {
						e.target.parentElement.parentElement.remove();
						$scope.forgotten -= 1;
					} else {
						alert(response.data.msg);
					}
					e.target.innerHTML = 'Undo';
					e.target.removeAttribute('disabled');
				},
				function(status) {}
			);
		} else {
			alert('An Error Occured, refresh to continue');
		}
	};
});
