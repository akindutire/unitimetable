var app = angular.module('app', [ 'ngSanitize' ]);

app.controller('ctrl', function($scope, $http, $location, $sce, $compile) {
	$scope.retryCount = 0;
	$scope.gwork = {};

	$scope.open_my_department = function(event) {
		event.preventDefault();

		document.getElementById('dept_modal').style.display = 'block';

		data = {};
		fac_id = event.path[0].getAttribute('data-fac-id');
		url = $scope.absPath + 'process/proc1/explorefaculty.php';
		data.faculty = fac_id;

		if (fac_id != null || fac_id != undefined) {
			$scope.gwork.fac_id = fac_id;

			document.getElementById('Departmenttitle').innerHTML = event.path[0].innerHTML;
			document.getElementById('dept_wrap').innerHTML = 'Loading...';
			$http.post(url, data).then(
				function(response) {
					if (response.data.success == 1) {
						template = '';
						for (i in response.data.msg) {
							template +=
								'<li> <a data-dept-id=' +
								i +
								" data-sig=1 ng-click=open_edit_department($event) class='w3-col l1 m1 s2 w3-border w3-round w3-center w3-margin-right w3-padding-small' style='cursor:pointer !important;'><i class='fas fa-pencil-alt w3-text-green'></i></a>&nbsp;<a data-dept-id=" +
								i +
								" data-sig=1 ng-click=delete_department($event) class='w3-col l1 m1 s2 w3-border w3-round w3-center w3-margin-right w3-padding-small' style='cursor:pointer !important;'><i class='fas fa-trash w3-text-red'></i></a><elon>" +
								response.data.msg[i] +
								'</elon></li>';
						}

						document.getElementById('dept_wrap').innerHTML = template;

						$compile(document.getElementById('dept_wrap'))($scope);
					} else {
						document.getElementById('dept_wrap').innerHTML =
							"<p id='error' class='w3-large w3-center'>" + response.data.msg + '</p>';
					}
				},
				function(status) {
					console.log(status.response);
				}
			);
		}
	};

	$scope.open_my_option = function(event) {
		event.preventDefault();

		document.getElementById('add_option_modal').style.display = 'block';

		data = {};
		dept_id = event.path[0].getAttribute('data-dept-id');
		url = $scope.absPath + 'process/proc1/exploredepartment.php';
		data.dept = dept_id;

		if (dept_id != null || dept_id != undefined) {
			$scope.gwork.dept_id = dept_id;

			document.getElementById('Departmenttitle').innerHTML = event.path[0].innerHTML;
			document.getElementById('option_wrap').innerHTML = 'Loading...';
			$http.post(url, data).then(
				function(response) {
					console.log(response.data);

					if (response.data.success == 1) {
						template = '';
						for (i in response.data.msg) {
							template +=
								"<li class='w3-bar-item w3-padding w3-border-bottom'>" + response.data.msg[i] + '</li>';
						}

						document.getElementById('option_wrap').innerHTML = template;
					} else {
						document.getElementById('option_wrap').innerHTML =
							"<p id='error' class='w3-large w3-center'>" + response.data.msg + '</p>';
					}
				},
				function(status) {
					console.log(status.response);
				}
			);
		}
	};

	$scope.add_faculty = function(event) {
		url = $scope.absPath + 'process/proc1/faculty.php';
		data = {};

		if (typeof $scope.fac != undefined) {
			data.faculty = $scope.fac;

			data = JSON.stringify(data);
			event.path[0].closest('form').previousElementSibling.innerHTML = '<i>Processing...</i>';
			$http.post(url, data).then(
				function(response) {
					console.log(response.data);
					if (response.data.success == 1) {
						template =
							"<li class='w3-bar-item w3-border-bottom w3-padding'><a data-fac-id=" +
							response.data.id +
							" data-sig=1 ng-click=open_edit_faculty($event) class='w3-col l1 m1 s2' style='cursor:pointer !important;'><i class='fa fa-pencil w3-text-green'></i></a>&nbsp;<input type='checkbox' class='w3-col l1 m1 s2 w3-padding-right' name='faculty_checkbox' id='faculty_checkbox' data-fac-id='" +
							response.data.id +
							"' ng-click=select_faculty($event)>&nbsp;<a data-fac-id=" +
							response.data.id +
							" ng-click=open_my_department($event)  class='w3-col l9 m9 s7' style='cursor:pointer !important;'>" +
							$scope.fac +
							'</a></li>';

						document.getElementById('fac_wrap').insertAdjacentHTML('beforeEnd', template);
						$compile(document.getElementById('fac_wrap'))($scope);

						if (document.querySelector('p#nodata') != null) document.querySelector('p#nodata').remove();

						event.path[0].closest('form').previousElementSibling.innerHTML =
							"<i class='w3-text-green'>" + response.data.msg + '</i>';
						$scope.fac = '';
					} else {
						event.path[0].closest('form').previousElementSibling.innerHTML =
							"<i class='w3-text-red'>" + response.data.msg + '</i>';
					}
				},
				function(status) {}
			);
		} else {
			alert("Faculty can't be empty");
		}
	};

	$scope.add_department = function(event) {
		url = $scope.absPath + 'process/proc1/department.php';
		data = {};

		if (typeof $scope.dept != undefined) {
			data.department = $scope.dept;
			data.time = $scope.time;
			data.department_short_code = $scope.dept_short_code;
			data.faculty = $scope.gwork.fac_id;

			data = JSON.stringify(data);

			event.path[0].closest('form').previousElementSibling.innerHTML = '<i>Processing...</i>';
			$http.post(url, data).then(
				function(response) {
					//console.log(response.data);
					if (response.data.success == 1) {
						if (document.querySelector('ul#dept_wrap p#error') != null)
							document.querySelector('ul#dept_wrap p#error').remove();

						template =
							'<li><a data-dept-id=' +
							response.data.id +
							" data-sig=1 ng-click=open_edit_department($event) class='w3-col l1 m1 s2 w3-border w3-round w3-center w3-margin-right w3-padding-small' style='cursor:pointer !important;'><i class='fas fa-pencil-alt w3-text-green'></i></a>&nbsp;<a data-dept-id=" +
							response.data.id +
							" data-sig=1 ng-click=delete_department($event) class='w3-col l1 m1 s2 w3-border w3-round w3-center w3-margin-right w3-padding-small' style='cursor:pointer !important;'><i class='fas fa-trash w3-text-red'></i></a><elon>" +
							$scope.dept +
							'</elon></li>';
						document.getElementById('dept_wrap').insertAdjacentHTML('beforeEnd', template);

						event.path[0].closest('form').previousElementSibling.innerHTML =
							"<i class='w3-text-green'>" + response.data.msg + '</i>';
						$scope.dept = '';

						$compile(document.getElementById('dept_wrap'))($scope);
					} else {
						event.path[0].closest('form').previousElementSibling.innerHTML =
							"<i class='w3-text-red'>" + response.data.msg + '</i>';
					}
				},
				function(status) {}
			);
		} else {
			alert("Department can't be empty");
		}
	};

	$scope.add_department_option = function(event) {
		url = $scope.absPath + 'process/proc1/option.php';
		data = {};

		if (typeof $scope.option != undefined) {
			data.option = $scope.option;
			data.time = $scope.time;
			data.department = $scope.gwork.dept_id;

			data = JSON.stringify(data);
			event.path[0].closest('form').previousElementSibling.innerHTML = '<i>Processing...</i>';
			$http.post(url, data).then(
				function(response) {
					console.log(response.data);
					if (response.data.success == 1) {
						if (document.querySelector('ul#option_wrap p#error') != null)
							document.querySelector('ul#option_wrap p#error').remove();

						template = "<li class='w3-bar-item w3-padding w3-border-bottom'>" + $scope.option + '</li>';
						document.getElementById('option_wrap').insertAdjacentHTML('beforeEnd', template);

						event.path[0].closest('form').previousElementSibling.innerHTML =
							"<i class='w3-text-green'>" + response.data.msg + '</i>';
						$scope.option = '';
					} else {
						event.path[0].closest('form').previousElementSibling.innerHTML =
							"<i class='w3-text-red'>" + response.data.msg + '</i>';
					}
				},
				function(status) {}
			);
		} else {
			alert("Option can't be empty");
		}
	};

	$scope.addvenue = function(event) {
		url = $scope.absPath + 'process/proc1/venue.php';
		data = {};

		if (typeof $scope.venue != undefined && typeof $scope.capacity != undefined) {
			data.venue = $scope.venue;
			data.capacity = $scope.capacity;
			data.location = $scope.location;

			data = JSON.stringify(data);
			event.path[0].closest('form').previousElementSibling.innerHTML = '<i>Processing...</i>';
			$http.post(url, data).then(
				function(response) {
					console.log(response.data);
					if (response.data.success == 1) {
						template =
							"<tr><td><a data-v-id='" +
							response.data.id +
							"' ng-click='remove_venue($event)' class='w3-text-red w3-tag w3-white w3-border w3-round w3-padding-small w3-round w3-border'><i class='fa fa-trash'></i></a> <a ng-click='open_edit_venue($event)' class='w3-green w3-tag w3-white w3-border w3-round w3-text-green w3-padding-small w3-round w3-border' data-v-id='" +
							response.data.id +
							"'><i class='fas fa-pencil-alt'></i></a> </td><td>" +
							$scope.venue +
							'</td><td>' +
							$scope.capacity +
							'</td><td>' +
							$scope.location +
							'</td></tr>';
						document.querySelector('table tbody').insertAdjacentHTML('beforeEnd', template);

						event.path[0].closest('form').previousElementSibling.innerHTML =
							"<i class='w3-text-green'>" + response.data.msg + '</i>';
						$scope.venue = '';
						$scope.location = '';
						$scope.capacity = '';

						$compile(document.querySelector('table tbody'))($scope);
					} else {
						event.path[0].closest('form').previousElementSibling.innerHTML =
							"<i class='w3-text-red'>" + response.data.msg + '</i>';
					}
				},
				function(status) {}
			);
		} else {
			alert("Venue can't be empty");
		}
	};

	$scope.remove_venue = function(event) {
		if (window.confirm('Do you want to delete')) {
			url = $scope.absPath + 'process/proc1/removevenue.php';
			data = {};

			cur_e = event.path[0];

			if (cur_e.getAttribute('data-v-id') == null) {
				cur_e = event.path[1];
				if (cur_e.getAttribute('data-v-id') == null) cur_e = event.path[2];
			}

			if (cur_e.getAttribute('data-v-id') != null) {
				data.venue = cur_e.getAttribute('data-v-id');

				data = JSON.stringify(data);
				cur_e.setAttribute('disabled', 'disabled');

				$http.post(url, data).then(
					function(response) {
						console.log(response.data);

						if (response.data.success == 1) {
							cur_e.closest('tr').remove();
							alert('Deleted!');
						} else {
							alert(response.data.msg);
							cur_e.removeAttribute('disabled');
						}
					},
					function(status) {
						cur_e.removeAttribute('disabled');
					}
				);
			} else {
				alert('No Venue Selected');
			}
		}
	};

	$scope.suspend_venue = function(event) {
		if (window.confirm('Do you want to suspend')) {
			url = $scope.absPath + 'process/proc1/suspend_venue.php';
			data = {};

			cur_e = event.path[0];

			if (cur_e.getAttribute('data-v-id') == null) {
				cur_e = event.path[1];
				if (cur_e.getAttribute('data-v-id') == null) cur_e = event.path[2];
			}

			if (cur_e.getAttribute('data-v-id') != null) {
				data.venue = cur_e.getAttribute('data-v-id');

				data = JSON.stringify(data);
				cur_e.setAttribute('disabled', 'disabled');

				$http.post(url, data).then(
					function(response) {
						console.log(response.data);

						if (response.data.success == 1) {
							window.location.reload(true);
						} else {
							alert(response.data.msg);
							cur_e.removeAttribute('disabled');
						}
					},
					function(status) {
						cur_e.removeAttribute('disabled');
					}
				);
			} else {
				alert('No Venue Selected');
			}
		}
	};

	$scope.restore_venue = function(event) {
		url = $scope.absPath + 'process/proc1/restore_venue.php';
		data = {};

		cur_e = event.path[0];

		if (cur_e.getAttribute('data-v-id') == null) {
			cur_e = event.path[1];
			if (cur_e.getAttribute('data-v-id') == null) cur_e = event.path[2];
		}

		if (cur_e.getAttribute('data-v-id') != null) {
			data.venue = cur_e.getAttribute('data-v-id');

			data = JSON.stringify(data);
			cur_e.setAttribute('disabled', 'disabled');

			$http.post(url, data).then(
				function(response) {
					console.log(response.data);

					if (response.data.success == 1) {
						window.location.reload(true);
					} else {
						alert(response.data.msg);
						cur_e.removeAttribute('disabled');
					}
				},
				function(status) {
					cur_e.removeAttribute('disabled');
				}
			);
		} else {
			alert('No Venue Selected');
		}
	};

	$scope.mark_as_multisight = function(event) {
		url = $scope.absPath + 'process/proc1/mark_as_multisight_venue.php';
		data = {};

		cur_e = event.path[0];

		if (cur_e.getAttribute('data-v-id') == null) {
			cur_e = event.path[1];
			if (cur_e.getAttribute('data-v-id') == null) cur_e = event.path[2];
		}

		if (cur_e.getAttribute('data-v-id') != null) {
			data.venue = cur_e.getAttribute('data-v-id');
			data.to_mark_as = cur_e.getAttribute('data-v-to-mark-as');

			data = JSON.stringify(data);
			cur_e.setAttribute('disabled', 'disabled');

			$http.post(url, data).then(
				function(response) {
					console.log(response.data);

					if (response.data.success == 1) {
						window.location.reload(true);
					} else {
						alert(response.data.msg);
						cur_e.removeAttribute('disabled');
					}
				},
				function(status) {
					cur_e.removeAttribute('disabled');
				}
			);
		} else {
			alert('No Venue Selected');
		}
	};

	$scope.open_edit_venue = function(event) {
		url = $scope.absPath + 'process/proc1/removevenue.php';
		data = {};

		cur_element = event.path[0];

		if (cur_element.getAttribute('data-v-id') == null) {
			cur_element = event.path[1];
			if (cur_element.getAttribute('data-v-id') == null) cur_element = event.path[2];
		}

		$scope.gwork.cur_element = cur_element;
		v_id = cur_element.getAttribute('data-v-id');
		$scope.gwork.temp = v_id;
		document.getElementById('edited_venue_capacity').value =
			cur_element.parentElement.nextElementSibling.nextElementSibling.innerText;

		document.getElementById('edit_venue_modal').style.display = 'block';
	};

	$scope.edit_venue = function(event) {
		cur_element = event.path[0];
		if (cur_element.getAttribute('data-sig') == null) {
			cur_element = event.path[1];
			if (cur_element.getAttribute('data-sig') == null) {
				cur_element = event.path[2];
			}
		}

		edited_venue_capacity = document.getElementById('edited_venue_capacity').value;

		url = $scope.absPath + 'process/proc1/editvenue.php';
		data = {};

		data.venue_id = $scope.gwork.temp;
		data.venue_capacity = edited_venue_capacity;

		$http.post(url, data).then(
			function(response) {
				//console.log(response.data.success == 1);

				if (response.data.success == 1) {
					$scope.gwork.cur_element.parentElement.nextElementSibling.nextElementSibling.innerText = edited_venue_capacity;
					document.getElementById('edit_venue_modal').style.display = 'none';
					$scope.gwork.cur_element = null;
				} else {
					alert('Venue capacity not Changed');
				}
			},
			function(status) {}
		);
	};

	$scope.select_faculty = function(event) {
		cur_element = event.path[0];

		if (cur_element.checked == true) {
			if (document.getElementById('delete_faculty_btn').disabled == true) {
				document.getElementById('delete_faculty_btn').removeAttribute('disabled');
			}
		} else {
			checked_input_btn_array = document.querySelectorAll('li input#faculty_checkbox:checked');

			if (checked_input_btn_array.length < 1) {
				document.getElementById('delete_faculty_btn').setAttribute('disabled', true);
			}
		}
	};

	$scope.delete_faculty = function(event) {
		if (window.confirm('Do you want to delete')) {
			cur_element = event.path[0];

			if (cur_element.getAttribute('data-sig') == null) {
				cur_element = event.path[1];

				if (cur_element.getAttribute('data-sig') == null) {
					cur_element = event.path[2];
				}
			}

			input_selected = document.querySelectorAll('li input#faculty_checkbox:checked');

			data = {};

			if (input_selected.length > 0) {
				for (var i = 0; i < input_selected.length; i++) {
					data[i] = input_selected[i].getAttribute('data-fac-id');
				}
			}

			url = $scope.absPath + 'process/proc1/deletefaculty.php';

			cur_element.setAttribute('disabled', true);

			$http.post(url, data).then(
				function(response) {
					if (response.data.success == 1) {
						for (var i = 0; i < input_selected.length; i++) {
							input_selected[i].parentElement.remove();
						}
						alert('Deletion Complete!');
					} else {
						alert("Couldn't delete Faculty");
					}
				},
				function(status) {
					console.log(status);
				}
			);
		}
	};

	$scope.open_edit_faculty = function(event) {
		cur_element = event.path[0];
		if (cur_element.getAttribute('data-sig') == null) {
			cur_element = event.path[1];
			if (cur_element.getAttribute('data-sig') == null) {
				cur_element = event.path[2];
			}
		}

		$scope.gwork.cur_element = cur_element;
		fac_id = cur_element.getAttribute('data-fac-id');
		$scope.gwork.temp = fac_id;
		document.getElementById('edited_fac_name').value = cur_element.nextElementSibling.nextElementSibling.innerText;

		document.getElementById('edit_fac_modal').style.display = 'block';
	};

	$scope.edit_faculty = function(event) {
		cur_element = event.path[0];
		if (cur_element.getAttribute('data-sig') == null) {
			cur_element = event.path[1];
			if (cur_element.getAttribute('data-sig') == null) {
				cur_element = event.path[2];
			}
		}

		edited_faculty_name = document.getElementById('edited_fac_name').value;

		url = $scope.absPath + 'process/proc1/editfaculty.php';
		data = {};

		data.faculty_id = $scope.gwork.temp;
		data.faculty_name = edited_faculty_name;

		$http.post(url, data).then(
			function(response) {
				if (response.data.success == 1) {
					$scope.gwork.cur_element.nextElementSibling.nextElementSibling.innerText = edited_faculty_name;
					document.getElementById('edit_fac_modal').style.display = 'none';
					$scope.gwork.cur_element = null;
					//alert("Faculty name Changed");
				} else {
					alert('Faculty name not Changed');
				}
			},
			function(status) {}
		);
	};

	$scope.delete_department = function(event) {
		if (window.confirm('Do you want to delete')) {
			cur_element = event.path[0];

			if (cur_element.getAttribute('data-sig') == null) {
				cur_element = event.path[1];

				if (cur_element.getAttribute('data-sig') == null) {
					cur_element = event.path[2];
				}
			}

			data = {};
			data.department_id = cur_element.getAttribute('data-dept-id');
			url = $scope.absPath + 'process/proc1/deletedepartment.php';

			cur_element.setAttribute('disabled', true);

			$http.post(url, data).then(
				function(response) {
					console.log(response.data);

					if (response.data.success == 1) {
						cur_element.parentElement.remove();
					} else {
						alert("Couldn't delete Department");
					}
				},
				function(status) {
					console.log(status);
				}
			);
		}
	};

	$scope.open_edit_department = function(event) {
		cur_element = event.path[0];
		if (cur_element.getAttribute('data-sig') == null) {
			cur_element = event.path[1];
			if (cur_element.getAttribute('data-sig') == null) {
				cur_element = event.path[2];
			}
		}

		$scope.gwork.cur_element = cur_element;
		dept_id = cur_element.getAttribute('data-dept-id');
		$scope.gwork.temp = dept_id;
		document.getElementById('edited_dept_name').value = cur_element.nextElementSibling.nextElementSibling.innerText;

		document.getElementById('edit_dept_modal').style.display = 'block';
	};

	$scope.edit_department = function(event) {
		cur_element = event.path[0];
		if (cur_element.getAttribute('data-sig') == null) {
			cur_element = event.path[1];
			if (cur_element.getAttribute('data-sig') == null) {
				cur_element = event.path[2];
			}
		}

		edited_dept_name = document.getElementById('edited_dept_name').value;

		url = $scope.absPath + 'process/proc1/editdepartment.php';
		data = {};

		data.department_id = $scope.gwork.temp;
		data.department_name = edited_dept_name;

		$http.post(url, data).then(
			function(response) {
				if (response.data.success == 1) {
					$scope.gwork.cur_element.nextElementSibling.nextElementSibling.innerText = edited_dept_name;
					document.getElementById('edit_dept_modal').style.display = 'none';
					$scope.gwork.cur_element = null;
				} else {
					alert('Department name not Changed');
				}
			},
			function(status) {}
		);
	};
});
