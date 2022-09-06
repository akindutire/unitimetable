<?php
	
	namespace src\timetable\model;

	
	use \zil\factory\Database;
	use \zil\factory\BuildQuery;
	use \zil\config\Config;
	use \zil\factory\Security;
	use \zil\factory\Session;
	use \zil\factory\Mailer;
	use \zil\factory\Redirect;
	use \zil\factory\Logger;
		
	use src\timetable\config\config as cfg;
	
	class datamanagementmodel{

		public $msg = null;

		public function __construct(){

			$cfg = new cfg;


			if(Session::getSession('App_Cert') == 1){
				$con = (new Database())->connect();
			}else{

				new Redirect($cfg->getAppInit());
			}
		}

		public function addfaculty($fac){

			$connect = (new Database())->connect();
			$sql = new BuildQuery($connect);

			$feedback = $sql->create('faculty',['null',$fac]);
			$this->msg[0]=$connect->lastInsertId();

			if($feedback == 1){

				$this->msg[1] = "Faculty added";
				return true;

			}else{


				$this->msg = "Couldn't add Faculty, retry";
				return false;

			}

		}

		public function delete_faculty($faculty_id_class){

			$connect = (new Database())->connect();
			$sql = new BuildQuery($connect);
			
			$faculty_id_array = (array)$faculty_id_class;
			if(count($faculty_id_array) > 0){

				foreach ($faculty_id_array as  $k => $f) {
					
					$ty=$sql->delete('faculty',[['ID',$f]]);
					$rs=$sql->read('department',[['Faculty',$f]],['ID'],['LIMIT 1']);
					if($rs->rowCount() > 0){
						list($dp) = $rs->fetch();
						$sql->delete('department_option',[['Department_ID',$dp]]);
						$sql->delete('department',[['Faculty',$f]]);
					}
				}

				if($ty->rowCount() > 0){
					return true;
				}else{
					return false;
				}	
			}else{
				return false;
			}			
		}

        public function delete_department($d_id){

            
           $connect = (new Database())->connect();
            $sql = new BuildQuery($connect);


            $rs = $sql->delete('department_option',[['Department_ID',$d_id]]);
            $sql->delete('department',[['ID',$d_id]]);

            return true;
        }

        public function edit_faculty($f_id,$f_nm){

		    
		    $connect = (new Database())->connect();

		    $sql = new BuildQuery($connect);

		    $rs = $sql->update('faculty',[ ['ID',$f_id] ],[ ['Name',$f_nm] ]);

		    if($rs->rowCount() > 0)
		        return true;

		    return false;
		}

        public function edit_department($dept_id,$dept_name){

            
           $connect = (new Database())->connect();
            $sql = new BuildQuery($connect);

            $rs = $sql->update('department',[ ['ID',$dept_id] ],[ ['Name',$dept_name] ]);

            if($rs->rowCount() > 0)
                return true;

            return false;
        }

        public function edit_venue($v_id,$v_cp){

            
           $connect = (new Database())->connect();
            $sql = new BuildQuery($connect);

            $rs = $sql->update('venue',[ ['ID',$v_id] ],[ ['Capacity',$v_cp] ]);

            if($rs->rowCount() > 0)
                return true;

            return false;
        }

		public function adddepartment($fac,$department,$time_range,$short_code){

			
			$connect = (new Database())->connect();
			$sql = new BuildQuery($connect);


                $feedback = $sql->create('department',['null',$department,$fac,$time_range,$short_code]);
                $this->msg[0]=$connect->lastInsertId();

			if($feedback == 1){

				$this->msg[1] = "Department added";
				return true;

			}else{


				$this->msg = "Couldn't add Department, Query Error, Retry";
				return false;

			}

		}

		public function adddepartmentoption($dpt,$opt,$time_range){

			
			$connect = (new Database())->connect();
			$sql = new BuildQuery($connect);

			$feedback = $sql->create('department_option',[$dpt,'null',$opt]);
			$this->msg[0]=$connect->lastInsertId();

			if($feedback == 1){

				$this->msg[1] = "Option added";
				return true;

			}else{

				$this->msg = "Couldn't add Option, retry";
				return false;

			}

		}


		public function addvenue($v,$c,$l, $not_in_use, $multisight){

			
			$connect = (new Database())->connect();
			$sql = new BuildQuery($connect);

			$feedback = $sql->create('venue',['null',$v,$c,$l, $not_in_use, $multisight]);
			$this->msg[0]=$connect->lastInsertId();

			if($feedback == 1){

				$this->msg[1] = "Venue added";
				return true;

			}else{


				$this->msg = "Couldn't add Venue, retry";
				return false;

			}
		}

		public function removevenue($v){

			
			$connect = (new Database())->connect();
			$sql = new BuildQuery($connect);

			$feedback = $sql->delete('venue',[['ID',$v]]);
			
			if($feedback->rowCount() == 1){

				$this->msg = "Venue removed";
				return true;

			}else{


				$this->msg = "Couldn't remove Venue, retry";
				return false;

			}
		}

		public function suspendvenue($v){

			$connect = (new Database())->connect();
			$sql = new BuildQuery($connect);

			$feedback = $sql->update('venue',[['ID',$v]], [['Not_In_Use', 1]]);
			
			if($feedback->rowCount() == 1){

				$this->msg = "Venue suspended";
				return true;

			}else{

				$this->msg = "Couldn't suspended Venue, retry";
				return false;
			}
		}

		public function restorevenue($v){

			$connect = (new Database())->connect();
			$sql = new BuildQuery($connect);

			$feedback = $sql->update('venue',[['ID',$v]], [['Not_In_Use', 0]]);
			
			if($feedback->rowCount() == 1){

				$this->msg = "Venue restored";
				return true;
			}else{

				$this->msg = "Couldn't restore Venue, retry";
				return false;
			}
		}

		public function markasmultisightvenue($v, $bool){

			$connect = (new Database())->connect();
			$sql = new BuildQuery($connect);

			$feedback = $sql->update('venue',[['ID',$v]], [['Multisight', $bool]]);
			
			if($feedback->rowCount() == 1){

				$this->msg = "Venue marked";
				return true;
			}else{

				$this->msg = "Couldn't mark Venue, retry";
				return false;
			}
		}

		public function getAllvenue(){

			
			$connect = (new Database())->connect();
			$sql = new BuildQuery($connect);

			$venue_op_disabled = false;
				
			$rs = $sql->read('allocation',[],['ID'],['LIMIT 1']);
			if($rs->rowCount() > 0)
				$venue_op_disabled = true;

			$arr = [];
			$arr['venue_op_disabled'] = $venue_op_disabled;

			$rs = $sql->read('venue',[],['ID','Capacity','Location','Name', 'Not_In_Use', 'Multisight'], ['ORDER BY Capacity DESC, Name ASC']);
			$arr['f_arr_venue'] = [];
			$arr['v_arr_venue'] = [];
			
			while(list($id,$c,$l,$n, $not_in_use, $multisight) = $rs->fetch()){

				$arr['f_arr_venue'][$id] = [$n,$c,$l,$not_in_use, $multisight];
				
				if($not_in_use == 0)
					$arr['v_arr_venue'][$id] = [$n,$c,$l,$not_in_use, $multisight];
			}

			$rs = $sql->read('venue',[['Not_In_Use', 0]],['COUNT(ID)']);
			list($v_in_use) = $rs->fetch();

			$arr['v_in_use'] = $v_in_use;

			return $arr;
		}


		public function getfaculty(){

			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);
	
			$rs = $sql->read('faculty',[['id','!=',0]],[],['ORDER BY ID']);
			$f_arr = [];

			while (list($id,$f) = $rs->fetch()) {
				$f_arr[$id] = ucwords($f);	
			}

			return $f_arr;
		}

		public function getdepartment($faculty_id,$all = false){

			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);
		
				$rs = $sql->read('department',[['Faculty',$faculty_id]], ['ID','Name']);
				$f_arr = [];

				while (list($id,$d) = $rs->fetch()) {
					$f_arr[$id] = ucwords($d);	
				}

				return $f_arr;		
		}

		public function getdepartmentoptions($dept_id,$all = false){

			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);
		
				$rs = $sql->read('department_option',[['Department_ID',$dept_id]], ['Option_ID','Option_Name']);
				$f_arr = [];

				while (list($id,$o) = $rs->fetch()) {
					$f_arr[$id] = ucwords($o);	
				}

				return $f_arr;		
		}

		public function getAlldepartment1(){

			
			$connect = (new Database())->connect();
			$sql = new BuildQuery($connect);
			
			$fd_arr = [];

				$rs = $sql->read('faculty',[['ID','!=',0]], ['ID','Name']);
				while( list($f,$n) = $rs->fetch()){

					$rs1 = $sql->read('department',[ ['Faculty',$f] ],['ID','Name','code'],['ORDER BY Name']);
					while(list($D,$dname,$code) = $rs1->fetch()){

						$fd_arr[$n][$D] = [ucwords($dname),strtoupper($code)];
					}	
				}

				return $fd_arr;
		}

		public function getAlldepartment(){

			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);
			
			$fd_arr = [];

				
					$rs1 = $sql->read('department',[ ],['ID','Name','code'],['ORDER BY Name']);
					while(list($D,$dname,$code) = $rs1->fetch()){

						$fd_arr[0][$D] = [ucwords($dname),strtoupper($code)];
					}	

				return $fd_arr;
		}

		public function addcourse($dept_code,$title,$code,$unit,$practical){

			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			$t = ucwords($title);
			$c = strtoupper($code);
			$c = str_replace(' ', null, $c);
			$d = strtoupper($dept_code);

			
			if ($practical != true)
				$practical = '';
			else
				$practical = 1;

			
			$rs = $sql->create('course',[$t,$c,$unit,$unit,1,1,$d,$practical,0]);

			$this->msg = $rs;
			return $rs;

		}



		public function get_course($dept_code){

			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			$rs = $sql->read('department',[ ['code',$dept_code] ],['Name']);
			list($dept_name) = $rs->fetch();

			
			$arr = [];
			$arr['Harmathan'] = [];
			$arr['Rain'] = [];
			$arr['dept_info'] = [$dept_code,$dept_name];

			$rs = $sql->read('course',[ ['Host_dept',strtoupper($dept_code)] ],['Name','Code','Unit','Hour_required','ScheduleLimit','No_Of_Occurence']);
			while(list($n,$c,$u,$h,$s,$nc) = $rs->fetch()){

				$e = $c[3];
				if($c[3] < 1)
					$e = 1;

				$l = $e.'00L';

				if($c[5] > 1)
					$semes = $c[5]%2==0?'Rain':'Harmathan';
				else
					$semes = 'Harmathan';

				
				$arr[$semes][$l][$c] = [$n,$u,$h,$s,$nc];
			}

			return $arr;

		}


		public function edit_basic_course_details($c_code, $c_title, $c_hrs_req, $c_str_hrs, $timebound, $totalRegistered){


			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			$rs = $sql->read('course',[ ['Code',$c_code],['No_Of_Occurence','>',1] ],[]);

			/*
			if($rs->rowCount() == 1 && $c_str_hrs > 1){
				
				$this->msg = "Couldn't Edit Constrained Course to be held more than 1 hour at a Stretch";
				return false;

			}
			*/

			$t = ucwords($c_title);
			$rs = $sql->update('course',[ ['Code',$c_code] ],[ ['Name',$t],['Hour_required',$c_hrs_req],['ScheduleLimit',$c_str_hrs] ]);

			$rs = $sql->read('course_offered', [ ['Course_Code',$c_code] ], []);
			
			if($rs->rowCount() == 0){

				$sql->create('course_offered', [$c_code,$totalRegistered]);
			
			}else{

				$sql->update('course_offered', [ ['Course_Code',$c_code] ], [ ['Capacity',$totalRegistered] ]);
			
			}

			$rs= $sql->read('course_constraint', [ ['Course_Code',$c_code] ]);
			
			if ($rs->rowCount() == 0) {
			
				$rs =$sql->create('course_constraint', [ $c_code, $timebound, 0, 0, 0]);

				if($rs == 1){
				
					$this->msg = 1;
					return true;
				}
			
			}else{

				$rs = $sql->update('course_constraint', [ ['Course_Code',$c_code] ], [ [ 'Time_Bound',$timebound] ]);

				if($rs->rowCount() > 0){
					
					$this->msg = 1;
					return true;
				}
			}		

			$this->msg = 0;
			return false;

		}

		public function add_exemption_venue($c_code,$venues){

			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			$v = array($venues);

			$r = false;

			$sql->delete('venue_exemption',[ ['Course_Code',$c_code] ]);
			
			foreach ($v[0] as $k => $vs) {
				

				$rs = $sql->read('venue_exemption',[ ['Course_Code',$c_code],['Venue_ID',$vs] ]);
				
				if($rs->rowCount() == 0){
					$r=$sql->create('venue_exemption',[$c_code,$vs]);
				}
			
			}

			$this->msg = $r;
			return $r;

		}

		public function get_venue_exemption($c_code){

			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			$arr = [];

			$rs = $sql->read('venue_exemption',[ ['Course_Code',$c_code] ],['Venue_ID']);
			while(list($v) = $rs->fetch()){

				$e = $sql->read('venue',[ ['ID',$v] ] ,['Name']);
				list($vn) = $e->fetch();
				$arr[$v] = $vn;
			}
			$this->msg = $arr;
			return true;
		}

		public function remove_venue_exemption($c_code,$v_id){

			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			$sql->delete('venue_exemption', [ ['Course_Code',$c_code],['Venue_ID',$v_id] ]);
			$this->msg = 1;
			return true;

		}

		public function set_course_allocation_plan($course_code, $allocation_plan){
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);
			$plan = (new \ArrayObject($allocation_plan))->getIterator();

			$prop = '';
			foreach($plan as $p){
				$prop .= "{$p};";
			}
			$prop = rtrim($prop, ';');
			
			$sql->delete('course_allocation_plan', [ ['Course_Code',$course_code] ]);
			$rs=$sql->create('course_allocation_plan', [$course_code, $prop]);
			
			if($rs){
				$this->msg = 1;	
				return true;
			}

			return false;
		}

		public function get_course_allocation_plan($course_code){
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);
			$rs = $sql->read('course_allocation_plan', [ ['Course_Code',$course_code] ], ['Proportion']);

			if($rs->rowCount() > 0)
				list($this->msg) = $rs->fetch();
			else
				$this->msg = '';
				
			return true;
		}

		public function remove_course_allocation_plan($course_code){
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);
			$sql->delete('course_allocation_plan', [ ['Course_Code',$course_code] ]);
			$this->msg = 1;
			return true;
		}

		public function set_course_constraint($c_code, $daybound, $lecturebound, $multisightbound, $no_of_class){

			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);


			$sql->update('course',[ ['Code',$c_code] ],[ ['No_Of_Occurence',$no_of_class] ]);

			$rs = $sql->read('course',[ ['Code',$c_code ],['ScheduleLimit','>',1] ], []);

			$rs = $sql->read('course_constraint',[ ['Course_Code',$c_code] ], ['Time_Bound']);
			list($p_t_bool) = $rs->fetch();

			if($lecturebound == 0 && $p_t_bool == 0){
				$connect->query("DELETE FROM course_constraint WHERE Course_Code='$c_code' ");
				$this->msg = 0;	
				return true;
			}

			
			if($multisightbound == 1)
				$lecturebound = 1;

			if($no_of_class == 1){
				$sql->update('course_constraint',[ ['Course_Code',$c_code] ], [ ['Lecture_Bound',$lecturebound],['Day_Bound',1], ['Multisight_Bound', $multisightbound] ]);
			}

			$e = null;

			$s = null;

			if($rs->rowCount() == 0)
				$e = $sql->create('course_constraint',[$c_code,0,$lecturebound,$daybound,$multisightbound]);
			else
				$s=$sql->update('course_constraint',[ ['Course_Code',$c_code] ], [ ['Lecture_Bound',$lecturebound],['Day_Bound',$daybound],['Multisight_Bound',$multisightbound] ]);


			if($e == true){
				$this->msg = 1;
				return true;
			}

			if($s != null && $s->rowCount()==1){
				$this->msg = 1;
				return true;
			}
			
			$this->msg = 0;
			return false;

			endg:
				$this->msg = 0; 
				return true;
		}

		public function get_course_constraint($c_code){

			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			$arr = [];

			$rs = $sql->read('course_constraint',[ ['Course_Code',$c_code] ],['Time_Bound','Lecture_Bound','Day_Bound', 'Multisight_Bound']);
			if($rs->rowCount() > 0){
				
				list($t,$l,$d,$m) = $rs->fetch();

				$arr[0] = [$d,$l,$t,$m];

			}

			$this->msg = $arr;
			return true;

		}

		public function set_course_participant($c_code,$participants){
			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);
			
			$r = false;
			
			$p = (new \ArrayObject($participants))->getIterator();
			
			if(count($p) > 0){
			
				$string = null;

				foreach ($p as $participant) {

					$code = substr($participant, 0,3);
					$level = substr($participant, 3);
				
					$string .= "({$code},{$level})&";	
				
				}
			
			}else{
				$r = $sql->update('participant',[ ['Course_Code',$c_code] ], [ ['Participant_Array','*'] ]);
				$r = $r->rowCount() > 0 ? true : false;
				return $r;
			
			}

			$r = $sql->read('participant',[ ['Course_Code',$c_code] ]);

			if($r->rowCount() > 0){
				
				$r = $sql->update('participant',[ ['Course_Code',$c_code] ], [ ['Participant_Array',$string] ]);
				
				$r = $r->rowCount() > 0 ? true : false;

			}else{
			
				$r = $sql->create('participant',[ $c_code, $string ]);
				$r = $r->rowCount() > 0 ? true : false;
			}

			$this->msg = $r;

			return $r;

		}


		public function get_course_participant($c_code){

			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			$arr = [];

			$rs = $sql->read('participant',[ ['Course_Code',$c_code] ],['Participant_Array']);
			if($rs->rowCount() > 0){
				
				list($participantString) = $rs->fetch();
				
				$a = explode('&', rtrim($participantString,'&'));
				
				if(is_array($a) && sizeof($a) > 0){
					foreach ($a as  $part) {
						if($part == '*' || $part == null || strlen(trim($part)) == 0)
							break;
						$part = ltrim($part,'(');
						$part = trim(rtrim($part,')'));

						$b = explode(',', $part);

						$rs = $sql->read('department', [ ['code',trim($b[0])] ], [ 'Name' ]);
						list($dept_name) = $rs->fetch();
						
						$details = [ "code" => trim($b[0]), "department" => ucwords($dept_name), "level" => intval($b[1]) ];

						array_push($arr, $details);
						
					}
				}

			}

			$this->msg = $arr;
			return true;
		}

		public function get_course_fixed_timings($c_code){

			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			$arr = [];

			$rs = $sql->read('fixed_allocation',[ ['Course_Code',$c_code], ['Fixed', 1] ],['ID', 'Venue_ID', 'Time', 'Day', 'All_Venue'], ['ORDER BY Day']);
			if($rs->rowCount() > 0){
				
				/**#
				 * days count start from 1
				 */
                $time = [null,'8/9' => '8am - 9am','9/10'=>'9am - 10am','10/11'=>'10am - 11am','11/12'=>'11am - 12pm','12/1'=>'12pm - 1pm','1/2'=>'1pm - 2pm','2/3'=>'2pm - 3pm','3/4'=>'3pm - 4pm','4/5'=>'4pm - 5pm','5/6'=>'5pm - 6pm'];
				$days = [null,'Monday','Tuesday','Wednesday','Thursday','Friday'];

				$venue_stmt = $connect->prepare("SELECT Name FROM venue WHERE ID=?");

                while(list($id, $v_id, $time_id, $day_id, $all_venue_constraint) = $rs->fetch()) {

					$venue_stmt->execute([$v_id]);

					list($v_name) = $venue_stmt->fetch();
                                        
                    $arr[$id] = [ "venue" => $v_name, "venue_id" => $v_id, "day" => $days[$day_id], "day_id" => $day_id, "time" => $time[$time_id], "time_id" => $time_id, "all_venue_constraint" => $all_venue_constraint ];
				 
                }
			}

			$this->msg = $arr;
			return true;
		}

		public function check_course_affordance($course_code, $venue_id, $time_id,  $day_id, $size_of_uncommited_fixed_allocation, $v_f_exception){
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);
			$this->msg = [true];
			/**
			 * Is venue exempted
			 */

			$rs = $sql->read('venue', [ ['ID', $venue_id], ['Not_In_Use', 1] ], ['ID']);
			if($rs->rowCount() > 0){
				$this->msg[0] = false;
				return true;
			}

			$rs = $sql->read('venue_exemption', [ ['Course_Code', $course_code], ['Venue_ID', $venue_id] ], ['Venue_ID']);
			if($rs->rowCount() > 0){
				$this->msg[0] = false;
				$this->msg[1] = "This venue was exempted from allocation of this course ($course_code)";
			}
			
			$rs = $sql->read('fixed_allocation', [ ['Venue_ID', $venue_id], ['Time', $time_id], ['Day', $day_id] ], ['Course_Code']);

			if($rs->rowCount() > 0){
				list($used_by) = $rs->fetch();
				$this->msg[0] = false;
				if(isset($this->msg[1]))
					$this->msg[2] = "{$used_by} has been fixed here";
				else
					$this->msg[1] = "{$used_by} has been fixed here";
			}else{
				$rs = $sql->read('allocation', [ ['Course_Code', $course_code], ['Day', $day_id] ], ['Course_Code']);
				if($rs->rowCount() > 0){
					
					$this->msg[0] = false;
					if(isset($this->msg[1]))
						$this->msg[2] = "{$course_code} has been allocated on this day";
					else
						$this->msg[1] = "{$course_code} has been allocated on this day";
				}else{
					$rs = $sql->read('allocation', [ ['Venue_ID', $venue_id], ['Time', $time_id], ['Day', $day_id] ], ['Course_Code']);
					if($rs->rowCount() > 0){
						list($used_by) = $rs->fetch();
						$this->msg[0] = false;
						if(isset($this->msg[1]))
							$this->msg[2] = "{$used_by} has been allocated here";
						else
							$this->msg[1] = "{$used_by} has been allocated here";
					}
				}
			}
			
			$rs = $sql->read('course',[['Code', $course_code] ],['ScheduleLimit', 'No_Of_Occurence', 'Hour_required']);
			list($no_of_class, $straight_hours, $total_hour_required) = $rs->fetch();

			$rs = $sql->read('fixed_allocation', [   ['Course_Code',$course_code]	], []);
			$total_times_allocated = $rs->rowCount();
			
			$rs = $sql->read('allocation', [   ['Course_Code',$course_code]	], []);
        	$total_times_allocated += $rs->rowCount();
			$total_times_allocated += $size_of_uncommited_fixed_allocation;

			unset($rs);
			
			if($v_f_exception){
				$rs = $sql->read('venue', [ ['Not_In_Use', 0] ], ['ID']);
				$vc = $rs->rowCount();

				$rs = $sql->read('fixed_allocation', [   ['Course_Code',$course_code]	], []);
				$total_times_allocated = $rs->rowCount() * $vc;

				$rs = $sql->read('allocation', [   ['Course_Code',$course_code]	], []);
				$total_times_allocated += $rs->rowCount();
				$total_times_allocated += $size_of_uncommited_fixed_allocation * $vc;
			
				unset($vc);
			}
			
			
			

			$times_of_allocation = floor($total_times_allocated / ($no_of_class * $straight_hours) );
			
			
        	if($times_of_allocation >= $total_hour_required){
				$this->msg[0] = false;
				if(isset($this->msg[1])){
					if(isset($this->msg[2])){
						$this->msg[3] = "Maximum allocation has been reached for this course ($course_code)";
					}else{
						$this->msg[2] = "Maximum allocation has been reached for this course ($course_code)";
					}
				}else{
					$this->msg[1] = "Maximum allocation has been reached for this course ($course_code)";
				}
					
			}
			return true;
		}

		public function fix_course_forcefully($course_code, $timings, $thrash){
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			
			$connect->query("DELETE FROM fixed_allocation WHERE Course_Code='$course_code' AND Fixed=1");
			
			$rs = $sql->read('course', [ ['Code', $course_code] ], ['No_Of_Occurence']);
			list($no_of_class) = $rs->fetch();

			foreach($thrash  as $thrashed_allocation){
				$e = explode('|', $thrashed_allocation);

			
				if(sizeof($e) == 5){
					$sql->delete('fixed_allocation', [ ['Venue_ID', $e[0]], ['Time', $e[2]], ['Day', $e[1]], ['Fixed', 1], ['All_Venue', $e[4]] ]);
				}

				
			}
			
			unset($thrash, $e, $thrashed_allocation);

			foreach($timings as $time){
				$time_arr = explode('|',$time);

				if(sizeof($time_arr) == 5){

					$venue = $time_arr[0];
					$day = $time_arr[1];
					$time = $time_arr[2];
					
					$v_exception = boolval($time_arr[4]) == true ? 1 : 0;
					
					$rs = $sql->read('fixed_allocation', [ ['Venue_ID', $venue], ['Time', $time], ['Day', $day] ], ['Fixed']);
					
					if($rs->rowCount() == 0){
						if($sql->create('fixed_allocation', ['', $course_code, $venue, $time, $day, 1, $v_exception])){
							
							continue;
						}
					}
					
				}
			}
			
		}


		public function remove_course($c_code){

			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			$sql->delete('course_constraint',[ ['Course_Code',$c_code] ]);
			$sql->delete('course',[ ['Code',$c_code] ]);
			$sql->delete('venue_exemption',[ ['Course_Code',$c_code] ]);

			$this->msg = true;
			return true;
			
		}

		public function remove_course_allocation_pathways($c_code){
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);

			$sql->delete('allocation',[ ['Course_Code',$c_code] ]);
			$sql->update('course',[ ['Code',$c_code] ], [ ['Allocation_Hit', 0] ]);

			$this->msg = true;
			return true;
			
		}

        public function getRandomCourse($limit){

            $connect = (new Database())->connect();

            $sql = new BuildQuery($connect);

            $arr = [];


            $rs = $sql->read('course',[ ],['Name','Code','Unit','Hour_required','ScheduleLimit','No_Of_Occurence','Host_dept'],["ORDER BY RAND() LIMIT 0,{$limit}"]);
            while(list($n,$c,$u,$h,$s,$nc,$hd) = $rs->fetch()){

                $ds = $sql->read('department',[ ['code',$hd] ], [  'Name'  ]);
                list($dept) = $ds->fetch();

				$e = $c[3];
				if($c[3] < 1)
					$e = 1;
                $l = $e.'00L';

                $semes = $c[5]%2==0?'Rain':'Harmathan';

                $arr[$c] = [$n,$u,$h,$s,$nc,$dept,$semes,$l];
            }

            return $arr;
		}

		public  function getAllCourseBySemester($semesterIndex)
        {

            $connect = (new Database())->connect();

            $sql = new BuildQuery($connect);

            $arr = [];


            $rs = $sql->read('course', [], ['Name', 'Code', 'Unit', 'Hour_required', 'ScheduleLimit', 'No_Of_Occurence', 'Host_dept'], ["ORDER BY Code"]);
            while (list($n, $c, $u, $h, $s, $nc, $hd) = $rs->fetch()) {

            	if($c[5] > 1)
					$semes = $c[5] % 2 == 0 ? 'rain' : 'harmathan';
				else
					$semes = 'harmathan';

                


                if ($semes != $semesterIndex) {
                    continue;
                }

				$e = $c[3];
				if($c[3] < 1)
					$e = 1;
                $l = $e . '00L';

                if( strlen($hd) > 0) {

                    $ds = $sql->read('department', [['code', $hd]], ['Name']);
                    list($dept) = $ds->fetch();

                }else{

                    $hd = "Unclassified";
                    $dept = "Unclassified";

                }


                $arr[$hd]['name'] = $dept;
                $arr[$hd][$c] = [$n, $u, $h, $s, $nc, $dept, $semes, $l];

            }

            return $arr;
        }

        public function get_course_basic_details($course_code){

            $connect = (new Database())->connect();

            $sql = new BuildQuery($connect);

            $rs = $sql->read('course', [ ['Code',$course_code] ], [  'Name', 'Hour_required', 'ScheduleLimit', 'No_Of_Occurence']);

            if($rs->rowCount() > 0 ) {

            	list($title, $hrs, $strhrs, $no_class) = $rs->fetch();

                $rs = $sql->read('course_constraint', [ ['Course_Code',$course_code] ], [  'Time_Bound' ]);

                list($timebound) = $rs->fetch();

                if($rs->rowCount() == 0)
                    $timebound = 0;

                $rs = $sql->read('course_offered', [ ['Course_Code',$course_code] ], [  'Capacity' ]);

                list($totalRegistered) = $rs->fetch();

                if($rs->rowCount() == 0)
                    $totalRegistered = 0;

                
                $this->msg = ["title"=>$title, "hoursReq"=>$hrs, "straigthHrs"=>$strhrs, "class"=>$no_class,"timebound"=>$timebound, "totalRegistered"=>$totalRegistered];

                return true;

            }else{

                return false;

            }

        }

        
        public function sandbox(){

        	$connect = (new Database())->connect();
        	
        	$sql = new BuildQuery($connect);

        	$rs = $sql->read('participant',[] , ['Course_Code','Participant_Array']);
        	
        	while(list($code,$str) = $rs->fetch()){

        		$l = $code[3];

        		if($l == 0){
        			$sql->update('participant', [ [ 'Course_Code', $code] ], [ ['Participant_Array','*'] ] );

        		}
        		
        		
        		
        	}
        }

		
	} 

?>