<?php
	
	namespace src\timetable\model;

	use \zil\factory\Database;
	use \zil\factory\BuildQuery;
	use \zil\factory\Security;
	use \zil\factory\Session;
	use \zil\factory\Mailer;
	use \zil\factory\Redirect;
    use \zil\factory\Logger;
    use \zil\factory\ErrorTracer;
		
	use src\timetable\config\config as cfg;
	
	class timetablemodel{

		public $msg = null;

        private $forbiddenList = [];
        private $allocationCounts = [];
        private $time = ['8/9','9/10','10/11','11/12','12/1','1/2','2/3','3/4','4/5','5/6'];
        private $undistorted_time = ['8/9','9/10','10/11','11/12','12/1','1/2','2/3','3/4','4/5','5/6'];
        private $days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];

        private $data_bunch = null;
        private $secondChanceHit_f = false;

		public function __construct(){
            try{
                $cfg = new cfg;

                
                if(Session::getSession('App_Cert') == 1){

                    $this->data_bunch = json_decode(file_get_contents($cfg->getAppPath()."database/data.json"));
                                
                }else{
                    
                    new Redirect($cfg->getAppInit());

                }
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }

		}

        public function generatebyWeek($semester){
            try{

                if(\php_sapi_name() == 'cli-server')
                    \set_time_limit(400);

                $cfg = new cfg;
                


                $arr = [];

                $secondChanceHit = false;
                $this->secondChanceHit_f = $secondChanceHit;

                $knownSemester = [ 'harmathan', 'rain' ];

                if(!in_array(trim($semester), $knownSemester))
                    return $arr;
                
                $s = 2;

                if($semester == $knownSemester[0])
                    $s = 1;

                $this->data_bunch->current_running_semester = $s;
                
                file_put_contents($cfg->getAppPath()."database/data.json", json_encode($this->data_bunch,JSON_PRETTY_PRINT));
        

                $days = $this->days;

                
                generate:

                    foreach ($days as $day_id => $day_name) {
                        
                        $day_id += 1;
                        if($day_id == 5){
                            unset($this->time[4],$this->time[5],$this->time[6]);
                            $this->time = array_values($this->time);
                        }else{
                            $this->time = ['8/9','9/10','10/11','11/12','12/1','1/2','2/3','3/4','4/5','5/6'];
                        }
                        $this->run($day_id,$s);

                    }
                /**
                *   Run Second chance allocation for terminated allocation */
                

                if($secondChanceHit === false){
                    sleep(10);
                    
                    
                    $secondChanceHit = true;
                    $this->secondChanceHit_f = $secondChanceHit;

                    $days = array_reverse($days, true);

                    foreach ($days as $day_id => $day_name) {
                        
                        $day_id += 1;

                        if($day_id == 5){
                            unset($this->time[4],$this->time[5],$this->time[6]);
                            $this->time = array_values($this->time);
                        }else{
                            $this->time = ['8/9','9/10','10/11','11/12','12/1','1/2','2/3','3/4','4/5','5/6'];
                        }
                        $this->run($day_id,$s,$secondChanceHit);

                    }                
                    
                }

            
                return null;
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }


		public function run($day_id,$semester, $second_chance = false){
            try{
                $arr = [];

                /**
                 * Forbidden List is preset only on first chance and internally preset on second chance
                 */
                if(!$second_chance)
                    $this->presetForbiddenList($day_id);


                    $connect = (new Database())->connect();

                    $sql = new BuildQuery($connect);

                    if($second_chance){
                        /**
                         * Runs for terminated allocations and fix course individual
                         */
                        $tis = $sql->read('terminated_allocation',[ ['Day',$day_id] ],['Course_Code']);
                        if($tis->rowCount() != 0){
                            while(list($c_code) = $tis->fetch()){
                                $this->fix_course($c_code,$day_id,$this->getTolerance(), true);
                            }
                        }

                        $connect->query("TRUNCATE terminated_allocation");
                        goto end;
                    }else{
                        $connect->query("TRUNCATE terminated_allocation");
                    }

                    /**
                     * Process Course with fixed timings
                     */

                    $ris = $sql->read('fixed_allocation', [ [ 'Day', $day_id, 'OR'], ['Day', 0] ], ['Course_Code', 'Venue_ID', 'Time', 'Day', 'All_Venue']);
                    if($ris->rowCount() != 0){

                        while(list($c_code, $venue_id, $time, $day, $all_venue_exception) = $ris->fetch()){

                            if($venue_id == 0 && $time == 0 && $day == 0 && $all_venue_exception == 0)
                                continue;

                            $priority = ['v' => false, 't' => false, 'd' => false, 'a_v_e' => false];

                            if($this->isNotForSemester($semester,$c_code))
                                continue;

                            $rs = $sql->read('course',[ ['Code', $c_code] ],['ScheduleLimit','Hour_required','No_Of_Occurence']);
                            list($straight_hours, $total_hour_required, $no_of_class) = $rs->fetch();

                            if($this->isMaxAllocationHit($c_code) === true)
                                    continue;
                            
                            /** Handling fixing differences*/
                            
                            if($venue_id == 0)
                                $priority['v'] = true;
                            
                            if($time == 0)
                                $priority['t'] = true;
                            
                            if($day == 0)
                                $priority['d'] = true;

                            if($all_venue_exception == 1)
                                $priority['a_v_e'] = true;


                            $this->allocate_fixed_course_venue_time($c_code, $day_id, $priority);

                        }
                    }
                    

                    /**	Process Course with Day Constraint Next*/

                        
                        /**Multi Sight */
                        $ris = $connect->query("SELECT c.Code, c.ScheduleLimit,c.Hour_required,c.No_Of_Occurence FROM course AS c JOIN course_constraint AS cc ON c.Code=cc.Course_Code WHERE c.No_Of_Occurence>1 AND c.Allocation_Hit=0 AND c.Practical<>1 AND cc.Lecture_Bound=1 AND cc.Multisight_Bound=1 ORDER BY RAND()");
                        while(list($c_code,$straight_hours,$total_hour_required,$no_of_class) = $ris->fetch()){
                            
                            if($this->isNotForSemester($semester,$c_code))
                                continue;

                                    
                            $course_package = $this->prepareCourseForAllocation($c_code,$straight_hours,$total_hour_required,$no_of_class, $day_id);
                            if($course_package === false)
                                continue;

                            list($c_code, $RecursiveSchedule, $highest_time_index, $lecture_bounded_in_same_hour, $fit_capacity, $straight_hours, $day_id) = $course_package;
                            
                            $this->allocate_constrained_course_venue_time($c_code, $RecursiveSchedule, $highest_time_index, $lecture_bounded_in_same_hour, $fit_capacity, $straight_hours, $day_id);                        
                        }
                    
                        
                        /**Repitition for early hours non multisight*/
                        $ris = $connect->query("SELECT c.Code, c.ScheduleLimit,c.Hour_required,c.No_Of_Occurence FROM course AS c JOIN course_constraint AS cc ON c.Code=cc.Course_Code WHERE c.No_Of_Occurence>1 AND c.Allocation_Hit=0 AND c.Practical<>1 AND cc.Multisight_Bound=0 AND cc.Time_Bound=1 ORDER BY RAND()");
                        while(list($c_code,$straight_hours,$total_hour_required,$no_of_class) = $ris->fetch()){
                            
                            if($this->isNotForSemester($semester,$c_code))
                                continue;
    
                            $course_package = $this->prepareCourseForAllocation($c_code,$straight_hours,$total_hour_required,$no_of_class, $day_id);
                            if($course_package === false)
                                continue;

                            list($c_code, $RecursiveSchedule, $highest_time_index, $lecture_bounded_in_same_hour, $fit_capacity, $straight_hours, $day_id) = $course_package;
                            
                            $this->allocate_constrained_course_venue_time($c_code, $RecursiveSchedule, $highest_time_index, $lecture_bounded_in_same_hour, $fit_capacity, $straight_hours, $day_id);
                        }

                        
                        
                        /**Repitition for all hours non multisight*/
                        $ris = $connect->query("SELECT c.Code, c.ScheduleLimit,c.Hour_required,c.No_Of_Occurence FROM course AS c JOIN course_constraint AS cc ON c.Code=cc.Course_Code WHERE c.No_Of_Occurence>1 AND c.Allocation_Hit=0 AND c.Practical<>1  AND cc.Multisight_Bound=0 AND cc.Time_Bound=0 ORDER BY RAND()");
                        while(list($c_code,$straight_hours,$total_hour_required,$no_of_class) = $ris->fetch()){
                        

                            if($this->isNotForSemester($semester,$c_code))
                                continue;
                            
                            

                            $course_package = $this->prepareCourseForAllocation($c_code,$straight_hours,$total_hour_required,$no_of_class, $day_id);
                        

                            if($course_package === false)
                                continue;

                            

                            list($c_code, $RecursiveSchedule, $highest_time_index, $lecture_bounded_in_same_hour, $fit_capacity, $straight_hours, $day_id) = $course_package;


                            $this->allocate_constrained_course_venue_time($c_code, $RecursiveSchedule, $highest_time_index, $lecture_bounded_in_same_hour, $fit_capacity, $straight_hours, $day_id);
                        }

                        
                    
                    /**
                    *	Process Courses Without Constraint
                    */
                
                        /**Non repitive course in early hours */
                        $ris = $connect->query("SELECT c.Code, c.ScheduleLimit,c.Hour_required,c.No_Of_Occurence FROM course AS c JOIN course_constraint AS cc ON c.Code=cc.Course_Code WHERE c.No_Of_Occurence=1 AND c.Allocation_Hit=0 AND c.Practical<>1 AND c.Allocation_Hit=0 AND (cc.Lecture_Bound=0 OR cc.Lecture_Bound=0) AND cc.Time_Bound=1 ORDER BY RAND()");
                        // $ris = $sql->read('course',[ ['Practical','<>',1],['No_Of_Occurence',1],['Allocation_Hit',0] ],['Code','ScheduleLimit','Hour_required','No_Of_Occurence'], ['ORDER BY RAND()']);
                    
                        while(list($c_code, $straight_hours, $total_hour_required, $no_of_class) = $ris->fetch()){
                        
                            /**
                            *   Assert courses picked are for the chosen semester alone
                            */

                            if ($this->isNotForSemester($semester,$c_code))
                                continue;

                            $rs = $sql->read('course_constraint', [ ['Course_Code',$c_code],['Time_Bound', 1] ], [ 'Time_Bound'] );
                            if($rs->rowCount() == 0)
                                continue;

                            $course_package = $this->prepareCourseForAllocationForUnconstrained($c_code,$straight_hours,$total_hour_required,$no_of_class, $day_id);
                            if($course_package === false)
                                continue;

                            list($c_code, $highest_time_index, $fit_capacity, $straight_hours, $day_id) = $course_package;
                            
                            $this->allocate_unconstrained_course_venue_time($c_code, $highest_time_index, $fit_capacity, $straight_hours, $day_id);
                        }

                        /**Non repitive course in all hours */
                        $ris = $sql->read('course',[ ['Practical','<>',1],['No_Of_Occurence',1],['Allocation_Hit',0] ],['Code','ScheduleLimit','Hour_required','No_Of_Occurence'], ['ORDER BY RAND()']);
                    
                        while(list($c_code, $straight_hours, $total_hour_required, $no_of_class) = $ris->fetch()){
                            
                            /**
                            *   Assert courses picked are for the chosen semester alone
                            */

                            if ($this->isNotForSemester($semester,$c_code))
                                continue;

                            $rs = $sql->read('course_constraint', [ ['Course_Code',$c_code],['Time_Bound', 1] ], [ 'Time_Bound'] );
                            if($rs->rowCount() > 0)
                                continue;

                            $course_package = $this->prepareCourseForAllocationForUnconstrained($c_code,$straight_hours,$total_hour_required,$no_of_class, $day_id);
                            if($course_package === false)
                                continue;

                            list($c_code, $highest_time_index, $fit_capacity, $straight_hours, $day_id) = $course_package;
        
                            $this->allocate_unconstrained_course_venue_time($c_code, $highest_time_index, $fit_capacity, $straight_hours, $day_id);
                        }

                    /**
                     * Timings that are forbidded for courses are cleared at the end of each day
                     */
                    $this->resetForbiddenList();

                return $arr;
            
                    
                end:
                    return null;
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        private function prepareCourseForAllocation($c_code,$straight_hours,$total_hour_required,$no_of_class, $day_id){
            try{
                $connect = (new Database())->connect();

                $sql = new BuildQuery($connect);

                /**Skip if course is already fixed */
                $rs = $sql->read('fixed_allocation',[ ['Course_Code',$c_code] ],['ID']);
                if($rs->rowCount() > 0)
                    return false;
                
                $no_registered = 0;
                    
                $rs = $sql->read('course_offered',[ ['Course_Code',$c_code] ],['Capacity']);
            
                list($no_registered) = $rs->fetch();
                                            
                $rs = $sql->read('course_constraint',[ ['Course_Code',$c_code] ], ['Time_Bound', 'Lecture_Bound', 'Day_Bound'] );
                
                list($t_bool, $l_bool, $d_bool) = $rs->fetch();
                unset($rs);

                if($this->isMaxAllocationHit($c_code) === true)
                    return false;
                
                   

                $time = $this->time;

                /** No. schedule repeat for a day */
                $RecursiveSchedule	=	1;

                /** Check if Course is taken on same day */
                if ($d_bool == 1)
                    $RecursiveSchedule	=	$no_of_class;
      
                /** Fit Course Capacity to System Tolerance Value e.g System tolerate 2000 students as 1600
                *   with a tolerance of 20% loss of course capacity */
                
                    $fit_capacity = ceil(round( ( (100 - $this->getTolerance() )  / 100), 1 ) * $no_registered);

                /**	Check if Course has completed its today Allocation */
                $rsp = $sql->read('allocation',[ ['Course_Code',$c_code],['Day',$day_id] ], ['ID']);

                if ($rsp->rowCount() == $RecursiveSchedule)
                    return false;

                   
                unset($rsp);

                /** Get the Time Range of the Schedule */
                
                $highest_time_index	=	count($time)-1;

                if ($t_bool == 1 ) 
                    $highest_time_index	=	floor( (count($time)-1) / 2 ) + 2;
                

                $lecture_bounded_in_same_hour =   false;
                
                if ($l_bool == 1 ) 
                    $lecture_bounded_in_same_hour =   true;

                return [$c_code, $RecursiveSchedule, $highest_time_index, $lecture_bounded_in_same_hour, $fit_capacity, $straight_hours, $day_id];

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        private function prepareCourseForAllocationForUnconstrained($c_code, $straight_hours, $total_hour_required, $no_of_class, $day_id){
            
            try{

                $connect = (new Database())->connect();

                $sql = new BuildQuery($connect);

                
                /**Skip if course is already fixed */
                $rs = $sql->read('fixed_allocation',[ ['Course_Code',$c_code] ],['ID']);
                if($rs->rowCount() > 0)
                    return false;

                if ($this->isMaxAllocationHit($c_code))	
                    return false;
                
                $time = $this->time;             
                
                /**	No. schedule repeat for a day*/
                $RecursiveSchedule	=	1;

                /** Fit Course Capacity to System Tolerance Value e.g System tolerate 2000 students to be treated as 1600
                *   with a tolerance of 20% loss of course capacity,  */

                $rs = $sql->read('course_offered',[ ['Course_Code',$c_code] ],['Capacity']);
                list($no_registered) = $rs->fetch(); 
                $fit_capacity = ceil( ( (100 - $this->getTolerance() )  / 100)* $no_registered);

                /**	Check if Course has completed its today Allocation*/

                $rs = $sql->read('allocation',[ ['Course_Code',$c_code],['Day',$day_id] ], []);
                if ($rs->rowCount() == $RecursiveSchedule)
                    return false;

                /**	Get the Time Range of the Schedule*/
                
                $highest_time_index	=	count($time)-1;
                $rs = $sql->read('course_constraint',[ ['Course_Code',$c_code] ], [ 'Time_Bound' ] );
                    
                list($t_bool) = $rs->fetch();
                unset($rs);

                if ($t_bool == 1 ) 
                    $highest_time_index =   floor( (count($time)-1) / 2 ) + 2;

                return [$c_code, $highest_time_index, $fit_capacity, $straight_hours, $day_id];

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        private function isNotForSemester($semester,$c_code){
            try{

                /**
                 * The sixth digit in course code determines it semester, if odd then harmathan, if even then rain
                 */
                $sem_determinant = $c_code[5];

                if($sem_determinant <= 1)
                    $sem_course_output = 1;
                else
                    $sem_course_output = ($sem_determinant%2)==0 ? 2 : 1;

                /**assert its a sesson and not for this semester evaluate to false */
                if(isset($this->data_bunch->sessional_course_code->{$c_code}))
                    return false;

                if ($semester != $sem_course_output) {
                    return true;;
                }

                return false;
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }

        }

        private function isMaxAllocationHit(string $c_code){

            try{

                /**
                 * This method checks if maximum allocation of a course has been reached and then 
                 * updates if it has.
                 
                */

                $connect = (new Database())->connect();
                $sql	=	new BuildQuery($connect);

                $rs 	=	$sql->read('course', [   ['Code',$c_code] ], ['Hour_required','ScheduleLimit' ,'No_Of_Occurence']);
                list($total_no_of_occurence_in_a_week, $straight_hours, $repition_in_a_day) = $rs->fetch();
                
                $to_be_allocated_counts = $repition_in_a_day * $straight_hours * $total_no_of_occurence_in_a_week;

                $rs 	=	$sql->read('allocation', [   ['Course_Code',$c_code] ], ['ID']);
                $allocated_counts = $rs->rowCount();
                
                if( $allocated_counts < $to_be_allocated_counts){
                    return false;
                }else{
                    return true;
                }
              

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        private function update_allocation_hit($c_code){
            try{
                $connect = (new Database())->connect();
                $sql	= new BuildQuery($connect);

                $rse = $sql->read('course', [ ['Code', $c_code] ], ['Allocation_Hit']);
                $rse = $sql->update('course', [ ['Code', $c_code] ],  [ ['Allocation_Hit', 1] ]);
                
                if($rse->rowCount() > 0)
                    unset($this->allocationCounts[$c_code]);

            }catch(\Thowable $t){
                new ErrorTracer($t);
            }
        }
        
        private function allocate_fixed_course_venue_time($c_code, $day_id, $priority){
            try{

                $connect = (new Database())->connect();
                $sql	= new BuildQuery($connect);

                $time = $this->time;
                $costMultiplier = 2;

                $constrained = false;
                $recursiveSchedule = 1;
                $highest_time_index	=	count($time)-1;

                $rs = $sql->read('course_offered',[ ['Course_Code',$c_code] ],['Capacity']);
                list($no_registered) = $rs->fetch();
                
                $fit_capacity = ceil(round( ( (100 - $this->getTolerance() )  / 100), 1 ) * $no_registered);
                $rs = $sql->read('course_constraint', [ ['Course_Code', $c_code] ], ['Time_Bound', 'Lecture_Bound',  'Day_Bound']);
                
                if($rs->rowCount() > 0){
                    $constrained = true;
                

                    list($t_bool, $l_bool, $d_bool) = $rs->fetch();
                    
                    $rs = $sql->read('course', [ ['Code', $c_code] ], ['ScheduleLimit','Hour_required','No_Of_Occurence']);
                    list($straight_hours, $total_hour_required, $no_of_class) = $rs->fetch();

                    if($d_bool)
                        $recursiveSchedule = $no_of_class;
                    
                
                    /**Get course latest hour allowed, aliased early hours or not */
                    if ($t_bool == 1 ) 
                        $highest_time_index =   floor( (count($time)-1) / 2 ) + 2;

                }

                $remainingSchedule = $recursiveSchedule;
                
                startAllocation:
                
                /**
                 * System uses her current day when this course has day issue(i.e not fixed) or not
                */
                $day = $day_id;

                if($priority['t']){
                    
                    /**Course want system to decide her time */
                    $time_allocation_index = rand(1, $highest_time_index);
                    $time_picked = $time[$time_allocation_index];
                    
                }else{
                    /** Course has her own timings, then get it*/

                    if($priority['d']){
                        /**System doesn't know which specific day the timing was made for, so she will chose by rand */
                        $rs = $sql->read('fixed_allocation', [ ['Course_Code', $c_code] ], ['Time'], ['ORDER BY RAND()']);
                        list($time_picked) = $rs->fetch();
                    }else{
                        
                        /**The system find the specific time with the current day, if time cant be reached  then allocation is not today, though it should find it */
                        $rs = $sql->read('fixed_allocation', [ ['Course_Code', $c_code], ['Day', $day_id] ], ['Time']);
                        list($time_picked) = $rs->fetch();
                        
                        /**This line would never be reached */
                        if(empty($time_picked))
                            goto stopAllocation;
                    }
                }

                if($priority['a_v_e']){
                    /**
                     * Override no of class
                     */
                    $rs = $sql->read('venue', [ [ 'Not_In_Use', 0] ],['ID']);
                    while(list($venue_id) = $rs->fetch()){
                        $sql->create('allocation',['null', $c_code, $venue_id, $time_picked, $day_id, 0, "{$c_code}{$venue_id}{$time_picked}{$day_id}0", $this->getTolerance() ]);
                    }
                    
                    if(!isset($this->allocationCounts[$c_code]))
                        $this->allocationCounts[$c_code] = 1;
                    else
                        $this->allocationCounts[$c_code] += 1;

                    if($this->allocationCounts[$c_code] == $total_hour_required){ 
                        $this->update_allocation_hit($c_code);
                    }

                    goto updateForbiddenList;
            

                }else{

                    if($priority['v']){
                        /**Course want system to decide her venue */

                        $VenueTrialCost = 0;
                        pickVenue:
                        if ( ($VenueTrialCost  *  $costMultiplier) > 4){
                            // Logger::Init();
                            //     Logger::Log("{$c_code} couldn't get a venue after several request, recommend adjusting your venues or course capacity\n");
                            // Logger::kill();
                            goto nextAllocation;
                        }
                            if($constrained)
                                $venue_id = $this->pick_free_venue_for_constrained_course($c_code,$fit_capacity,$remainingSchedule, $day_id, $time_picked);
                            else
                                $venue_id = $this->pick_free_venue($c_code, $fit_capacity, $day, $time_picked);

                            if($venue_id == 0){
                                $VenueTrialCost += 1;
                                goto pickVenue;
                            }
                    }else{
                        /**Course has her own venue for use */
                        if($priority['d']){
                            /**Course has day issue (current day is used instead), so she doesn't know which day for which venue, the system would pick one of the course venue */
                            $rs = $sql->read('fixed_allocation', [ ['Course_Code', $c_code] ], ['Venue_ID'], ['ORDER BY RAND()']);
                            list($venue_id) = $rs->fetch();
                        }else{
                            /**Course is aware of day she wants a venue */
                            $rs = $sql->read('fixed_allocation', [ ['Course_Code', $c_code], ['Day', $day] ], ['Venue_ID']);
                            list($venue_id) = $rs->fetch();

                            /**If no venue found though it should be found, then allocation of this course is not today */
                            if(empty($venue_id))
                                goto stopAllocation;
                        }                    
                    }

                }

                allocationFinale:
                
                 /**
                *	Save Allocation, neglect conflict with for hours > 1 on lectures of 'n' direct hours, n>1 
                */
                if($straight_hours > 1){
                
                    $straight_index = 0;

                    while( $straight_index < $straight_hours){

                        $sql->create('allocation',['null', $c_code, $venue_id, $time_picked, $day_id, 0, "{$c_code}{$venue_id}{$time_picked}{$day_id}0", $this->getTolerance()]);
                        /**
                         * Automatically Assign next hour as continuation of lecture during direct hours
                         * Note, course conflict is not checked
                         */
                        $straight_index += 1;

                        $time_allocation_index += 1; 

                        if (isset($time[$time_allocation_index])) 
                            $time_picked = $time[$time_allocation_index];
                        else
                            break;
                    }
                }else{
                    $sql->create('allocation',['null',$c_code,$venue_id,$time_picked,$day_id, 0, "{$c_code}{$venue_id}{$time_picked}{$day_id}0"]);
                }

                
                nextAllocation:
                if($constrained){
                    if($remainingSchedule > 0){
                        
                        /**
                         * Get remaining course capacity and schedules left
                         */
                        $v_rs = $sql->read('venue', [ [ 'ID',$venue_id ] ], [ 'Capacity' ]);
                        list($venue_capacity) = $v_rs->fetch();
            
                        $remainingSchedule -= 1;

                        /*
                        Reset Venue trial after successful allocation to a time and venue
                        If remaining schedule is not zero yet, then it will assume a new trial
                        for a new time and venue, thus time trial, venue trial 
                        will be reset at this point.    
                        */
                        $VenueTrialCost = 0;

                        if($fit_capacity > 0){
                            $e = $this->data_bunch->min_venue_capacity_req;
                            if($e > 100)
                                $e = 100;
                            $min_percent_of_last_venue = ($e/100 ) * $venue_capacity;
                            if(abs($venue_capacity - $fit_capacity) > $min_percent_of_last_venue){
                                $fit_capacity -= $venue_capacity;
                                goto startAllocation;
                            }else{

                                if(!isset($this->allocationCounts[$c_code]))
                                    $this->allocationCounts[$c_code] = 1;
                                else
                                    $this->allocationCounts[$c_code] += 1;

                                if($this->allocationCounts[$c_code] == $total_hour_required){ 
                                    $this->update_allocation_hit($c_code);
                                }

                                goto updateForbiddenList;
                            }
                        }else{
                            
                            if(!isset($this->allocationCounts[$c_code]))
                                $this->allocationCounts[$c_code] = 1;
                            else
                                $this->allocationCounts[$c_code] += 1;

                            if($this->allocationCounts[$c_code] == $total_hour_required){ 
                                $this->update_allocation_hit($c_code);
                            }
                            goto updateForbiddenList;
                        }
                    }
                }

                updateAllocationHit:
                $e = $this->data_bunch->min_venue_capacity_req;
                if($e > 100)
                    $e = 100;
    
                $min_venue_capacity_req = ($e / 100) * $venue_capacity;
            
                if( abs($venue_capacity - $fit_capacity) <= $min_venue_capacity_req ){
                    
                    $rs = $sql->read('course', [ ['Code', $c_code] ], ['Hour_required']);
                    list($total_hour_required) = $rs->fetch();

                    if(!isset($this->allocationCounts[$c_code]))
                        $this->allocationCounts[$c_code] = 1;
                    else
                        $this->allocationCounts[$c_code] += 1;

                    if($this->allocationCounts[$c_code] == $total_hour_required){
                        $this->update_allocation_hit($c_code);
                    }
                }

                updateForbiddenList:
                    /**Update forbidden list, since it is forced, it is put first not to clash with others */
                    $this->updateForbiddenList($c_code,$time_picked);
                    
                stopAllocation:
                
                return;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
            
        }

        private function allocate_constrained_course_venue_time($c_code, $recursiveSchedule, $highest_time_index, $lecture_bounded_in_same_hour, $fit_capacity, $straight_hours, $day_id){

            try{
            
                $connect = (new Database())->connect();
                $sql	= new BuildQuery($connect);

                $time = $this->time;
                $costMultiplier = 1;

                $remainingSchedule = $recursiveSchedule;


                if($fit_capacity == '*' || $fit_capacity == NULL){
                    /**
                     * skip allocation
                     */
                    goto end;
                }

                // if($fit_capacity < 40)
                //     goto end;

                if($straight_hours > 1)
                    $highest_time_index -= $straight_hours;
                
                
                $VenueTrialCost = 0;
                $TimeTrialCost = 0;
                startAllocation:
            
                
                if( $TimeTrialCost > ( 2 *  $costMultiplier) )
                    goto terminateAllocation;

                  
                    /** 
                     * This block means that this is the first time allocation for this course, 
                     * even though there are more allocations ahead.
                    */

                    /**
                     * Ensure  recursive lecture schedule expected to run on same timings implicating different venues
                     */
                    $multi_sight = false;
                    if($lecture_bounded_in_same_hour){
                        /**
                         * Retrieve time picked for previous lectures of this course, 
                         * This goes for multi sight or multi lecturer courses
                         */
                        
                       
                        $rs = $sql->read('course_constraint', [['Course_Code', $c_code], ['Multisight_Bound', 1] ], ['Multisight_Bound']);
                        if($rs->rowCount() > 0)
                            $multi_sight = true;

                        $rs = $connect->query("SELECT Time FROM allocation WHERE Course_Code='$c_code' AND Day='$day_id' ");
                        if($rs->rowCount() != 0){
                            list($time_picked) = $rs->fetch();
                        }else{
                            $time_allocation_index = rand(0,$highest_time_index);
                            $time_picked = $time[$time_allocation_index];
                        }
                        /**
                         * If time retrieved for Course is forbidden, terminate it  or Else check if venue is free
                         */
                        
                         if($this->isCourseForbidden($c_code, $time_picked) == true){
                            goto terminateAllocation;
                         }else{
                            $VenueTrialCost = 0;
                            goto pickVenue;
                         }
                    }else{

                        /**
                         * This block runs for classes on different time allocation 
                         */

                        /** The picktimeSnapShot LABEL is same as the picktime LABEL, it's repeated to avoid extra code scanning
                        * Constraint: TimeTrialCost is used and it's on reset at every successful(recursive) allocation
                        *  
                        */    
                        
                        picktimeSnapShot:    
                        if( $TimeTrialCost  > (5 *  $costMultiplier) )
                            goto terminateAllocation;
                        
                        /**
                         * This block runs if the course schedules should be on different timings 
                         * not necessarily next to each other on timeline.
                         *     
                         */
                        
                        $prev_time_index = $this->getLastPrevTimingsForConstrained($c_code, $day_id);
                        $time_allocation_index = rand(0,$highest_time_index);
                        if(!is_null($prev_time_index)){
            
                            if(abs($prev_time_index - $time_allocation_index) < 2)
                                goto picktimeSnapShot;    
            
                        }
                        
                        $time_picked = $time[$time_allocation_index];
                        

                        $rs = $connect->query("SELECT Time FROM allocation WHERE Course_Code='$c_code' AND Day='$day_id' AND Time='$time_picked' ");
                        
                        if($rs->rowCount() > 0){
                        
                            /**
                             * Recursive check to ensure current course are not placed on same time allocations
                             */
                            $TimeTrialCost += 1;
                            goto picktimeSnapShot;
                        }
                    }

                courseMutualExclusionCheck:
                if($this->isCourseForbidden($c_code, $time_picked) == true){
                    
                    /**
                     * This block ensure the mutual exclusion feature of individual courses with respected to
                     * their timings.
                     */

                    $TimeTrialCost =+ 1;
                    goto startAllocation;
                }
                
                $VenueTrialCost = 0;
                pickVenue:
                if ( $VenueTrialCost > (4 *  $costMultiplier) ){
                    // Logger::Init();
                    //     Logger::Log("{$c_code} couldn't get a venue after several request, recommend adjusting your venues or course capacity on allocation-{$remainingSchedule} \n");
                    // Logger::kill();
                    goto terminateAllocation;
                }
            
                $venue_id = $this->pick_free_venue_for_constrained_course($c_code,$fit_capacity,$remainingSchedule, $day_id, $time_picked, $multi_sight);

                if($venue_id == 0){
                    $VenueTrialCost += 1;
                    goto pickVenue;
                }
   
                allocationFinale:
	
        			/**
        			*	Save Allocation, neglect conflict  for hours > 1 on lectures of 'n' direct hours, n>1 
        			*/
                    if($straight_hours > 1){
                        
                        $straight_index = 0;

                        if( $straight_index < $straight_hours){

            			    $sql->create('allocation',['null', $c_code, $venue_id, $time_picked, $day_id, 0, "{$c_code}{$venue_id}{$time_picked}{$day_id}0", $this->getTolerance()]);
                            /**
                             * Automatically Assign next hour as continuation of lecture during direct hours
                             * Note, course conflict is not checked
                             */
                            $straight_index += 1;

                            $time_allocation_index += 1; 

                            if (isset($time[$time_allocation_index])) {
                                $time_picked = $time[$time_allocation_index];
                                
                                if($this->isCourseForbidden($c_code, $time_picked) == true){
                                    $sql->delete('allocation', [ ['Course_Code', $c_code], ['Day', $day_id] ]); 
                                    goto terminateAllocation;
                                }

                            }else{
                                $sql->delete('allocation', [ ['Course_Code', $c_code], ['Day', $day_id] ]); 
                                goto terminateAllocation;
                            }   
                            
                        }

                    }else{
                        if($time_picked !== null)
                            $sql->create('allocation',['null',$c_code,$venue_id,$time_picked,$day_id, 0, "{$c_code}{$venue_id}{$time_picked}{$day_id}0", $this->getTolerance()]);
                    }

                    $v_rs = $sql->read('venue', [ [ 'ID',$venue_id ] ], [ 'Capacity' ]);
                    list($venue_capacity) = $v_rs->fetch();
                    
                    $rs = $sql->read('course', [ ['Code', $c_code] ], ['Hour_required']);
                    list($total_hour_required) = $rs->fetch();

                    unset($v_rs, $rs);
                    /**
                     * Get remaining course capacity and schedules left
                     */
                    $remainingSchedule -= 1;
                    
                    

                    if ($remainingSchedule > 0){
                        
                        /*
                            Reset Venue trial after successful allocation to a time and venue
                            If remaining schedule is not zero yet, then it will assume a new trial
                            for a new time and venue, thus time trial, venue trial, and internaltimetrial 
                            will be reset at this point.    
                        */
                        $VenueTrialCost = 0;
                        $TimeTrialCost = 0;
                        
                        if($fit_capacity > 0){

                            $fit_capacity -= $venue_capacity;
                            goto startAllocation;


                            /**The following block truncate allocation before its required no of class due to venue trade off,
                             * A venue might accept a min_venue_req i.e request (certain percent loss or gain) for a leftover 
                             * population thereby truncating further allocation since it's a good trade off
                            */
                            // $e = $this->data_bunch->min_venue_capacity_req;
                            
                            // if($e > 100)
                            //     $e = 100;

                            // if($e == 0)
                            //     $min_percent_of_last_venue = 0;
                            // else
                            //     $min_percent_of_last_venue = ($e/100 ) * $venue_capacity;
                            
                            // /**
                            //  * If a trade off to the last allocation is bearable, the allocation stop before the proposed no. of classes. e.g if a 800  capacity is assigned to 816 population with $e val of 2, the system will ignore the next allocation hoping it is a good trade off
                            //  */

                           
                            // if(abs($venue_capacity - $fit_capacity) > $min_percent_of_last_venue){
                                /**Non bearable */
                            //     $fit_capacity -= $venue_capacity;
                            //     goto startAllocation;
                            // }else{
                                    /**Bearable */
                            //     if(!isset($this->allocationCounts[$c_code]))
                            //         $this->allocationCounts[$c_code] = 1;
                            //     else
                            //         $this->allocationCounts[$c_code] += 1;

                            //     if($this->allocationCounts[$c_code] == $total_hour_required){
                            //         $this->update_allocation_hit($c_code);
                            //     }

                            //     goto updateForbiddenList;

                            // }
                        }else{
                            goto updateForbiddenList;
                        }
                    }else{
                       
                        /**update Allocation hit*/
                                       
                        if(!isset($this->allocationCounts[$c_code]))
                            $this->allocationCounts[$c_code] = 1;
                        else
                            $this->allocationCounts[$c_code] += 1;

                        if($this->allocationCounts[$c_code] == $total_hour_required){
                            $this->update_allocation_hit($c_code);
                        }
                        
                        goto updateForbiddenList;
                        
                    }
                
                updateForbiddenList:
                    /**Update forbidden list, since it is forced, it is put first not to clash with others */
                    $this->updateForbiddenList($c_code,$time_picked);
                    /**Update allocation history */
                    
                    goto end;

                terminateAllocation:
                    /**
                     * Clearly incomplete allocation for the day and Stage terminated allocation for second chance
                     */
                    
                    $sql->delete('allocation', [ [ 'Course_Code', $c_code], ['Day', $day_id] ]);
                    $sql->create('terminated_allocation',  ['', $c_code, $day_id]);
                
                
                end:
                    return null;
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }

        } 


        private function allocate_unconstrained_course_venue_time($c_code, $highest_time_index, $fit_capacity, $straight_hours, $day_id){

            try{
                /**
                 * Connect to database using generic params in src\timetable\config
                 */
                $connect = (new Database())->connect();

                $sql	= new BuildQuery($connect);

                /**
                 * Time Central Buffer
                 */
                $time = $this->time;

                $costMultiplier = 1;
                /**
                 * schedule goes hour by hour
                 */
                $remainingSchedule = $straight_hours;

                if($fit_capacity == NULL || $fit_capacity == '*')
                    goto end;


                /**
                 * Adjust highest possible time index to ensure boundedness of time e.g time picked may fall at last hour 
                 * for a 2 direct hours course, |HTI - 2| + 2 = HTI
                 */
                if($straight_hours > 1)
                    $highest_time_index -= $straight_hours;

                
                startAllocation:

                $time_picked = null;

                    $TimeTrialCost = 0;
                    picktime:

                    /**
                     * Controls recursive time checks
                     */
                    if( ($TimeTrialCost  *  $costMultiplier) > 2)
                        goto terminateAllocation;

                    /**
                     * Pick a random time index between 0 and HTI
                     */
                    $time_allocation_index = rand(0,$highest_time_index);
                    
                    $time_picked = $time[$time_allocation_index];

                    /**
                     * Check if time picked does not forbids this course 
                     */
                    if($this->isCourseForbidden($c_code, $time_picked) == true){
                        $TimeTrialCost += 1;
                        goto picktime;
                    }
                    
                    $VenueTrialCost = 0;
                    pickVenue:
                    if ( ($VenueTrialCost  *  $costMultiplier) > 4){
                        
                        goto terminateAllocation;
                    }
                    $venue_id = $this->pick_free_venue($c_code, $fit_capacity, $day_id, $time_picked);
                    if($venue_id == 0){
                        $VenueTrialCost += 1;
                        goto pickVenue;
                    }

                    allocationFinale:      			
                    /**
                    *	Save Allocation
                    */
                    if($straight_hours > 1){

                        $straight_index = 0;

                        while( $straight_index < $straight_hours){

                            $sql->create('allocation',['null', $c_code, $venue_id, $time_picked, $day_id, 0, "{$c_code}{$venue_id}{$time_picked}{$day_id}0", $this->getTolerance()]);
                            
                            $straight_index += 1;

                            $time_allocation_index += 1; 

                            if (isset($time[$time_allocation_index])) 
                                $time_picked = $time[$time_allocation_index];
                            else
                                break;
                        }
                    }else{
                    
                        $sql->create('allocation',['null', $c_code, $venue_id, $time_picked, $day_id, 0, "{$c_code}{$venue_id}{$time_picked}{$day_id}0", $this->getTolerance()]);

                    }

                    updateAllocationHit:
                    
                    $rs = $sql->read('course', [ ['Code', $c_code] ], ['Hour_required']);
                    list($total_hour_required) = $rs->fetch();

                    if(!isset($this->allocationCounts[$c_code]))
                        $this->allocationCounts[$c_code] = 1;
                    else
                        $this->allocationCounts[$c_code] += 1;
                    

                    if($this->allocationCounts[$c_code] == $total_hour_required){
                        $this->update_allocation_hit($c_code);
                    }
                
                    
                    unset($rs);
                    
                    updateForbiddenList:
                        /**Update forbidden list, since it is forced, it is put first not to clash with others */
                        
                        $v_rs = $sql->read('venue', [ [ 'ID',$venue_id ] ], [ 'Capacity' ]);
                        list($venue_capacity) = $v_rs->fetch();
                        
                        if($fit_capacity > $venue_capacity){
                            /**pos */
                            $v_tol_sign = -1;
                        }else{
                            /**neg */
                            $v_tol_sign = 1;
                        }
                        $this->processAllocationHistory($c_code, $venue_id, $v_tol_sign,  $day_id);
                        $this->updateForbiddenList($c_code,$time_picked);
                        goto end;

                    terminateAllocation:
                        /**Stage terminated allocation for second chance */

                        $sql->create('terminated_allocation',  ['', $c_code, $day_id]);
                
                    end:
                        return null;
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        } 

        private function getLastPrevTimingsForConstrained($Course_Code, $today_id){
            try{
                $connect = (new Database())->connect();

                $sql    = new BuildQuery($connect);

                /**
                *   Read the last time that was allocated to this course
                */
                
                $rs = $sql->read('allocation', [ ['Course_Code',$Course_Code], ['Day', $today_id] ], ['Time'], ['ORDER BY ID DESC']);   
                if($rs->rowCount() > 0){

                    list($t) = $rs->fetch();
                    /**Find index of the time on its lookup */
                    return array_keys($this->undistorted_time, "$t")[0];
                } else{
                    return null;
                } 
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }     
          
        }   

        private function pick_free_venue_for_constrained_course($Course_Code, $fit_capacity, $remainingSchedule, $day, $time, $multisight = false){

            try{

                $connect = (new Database())->connect();

                $sql    = new BuildQuery($connect);

                $cfg = new cfg;

                /**
                *   Check Venue Exemption
                */
                
                $arr = [];
                $rs = $sql->read('venue_exemption', [ ['Course_Code',$Course_Code] ], ['Venue_ID'], []);          
                while(list($v) = $rs->fetch()){ 
                    array_push($arr, $v);
                }
                
                $venue_id_picked = 0;
            
                $data_bunch = $this->data_bunch;
            
                unset($rs);

                
                
                /**
                 * Check if an allocation plan exist
                 */
                $plan_exists = false; $plan = [];
                $rs = $sql->read('course_allocation_plan', [ ['Course_Code', $Course_Code] ], ['Proportion']);
                if($rs->rowCount() == 1){
                    $plan_exists = true;
                    list($props) = $rs->fetch();
                    $plan = explode(';', $props);
                }

                $cost = $remainingSchedule;
                $trials = 1;
                $min_venue_request = $fit_capacity;
                AdjustMinRequestCost:
            
                $min_venue_request = intdiv($min_venue_request , $cost);

                //Logger::Log("trials is $trials, Minimum request is $min_venue_request and fit capacity is $fit_capacity");
                if($multisight){
                    /**Future decision: should plans should  be on multisight venues? */
                    $rs = $connect->query("SELECT ID,Capacity FROM venue WHERE Not_In_Use=0 AND Multisight=1");
                }else{
                    if($plan_exists){
                        
                        $rs = $sql->read('course_offered',[ ['Course_Code',$Course_Code] ],['Capacity']);
                        list($no_registered) = $rs->fetch();
                        $real_fit_capacity = ceil(round( ( (100 - $this->getTolerance() )  / 100), 1 ) * $no_registered);

                        $props = ($plan[$remainingSchedule-1]  / 100 ) * $real_fit_capacity;
                        $upper_bound_of_props = $props + (0.1 * $props);
                        
                        $rs = $connect->query("SELECT ID,Capacity FROM venue WHERE Not_In_Use=0 AND ( Capacity>='$props' AND Capacity<='$upper_bound_of_props')");
                    }else{
                        $rs = $connect->query("SELECT ID,Capacity FROM venue WHERE Not_In_Use=0 AND Capacity>'$min_venue_request'");
                    }
                }
                if($rs->rowCount() < 2){
                    $cost += 1;

                    if($trials < 20){
                        $trials += 1;
                        goto AdjustMinRequestCost;
                    }else{
                        return 0;
                    }
                    
                }else{
                    $e = $data_bunch->min_venue_capacity_req;
                    if($data_bunch->min_venue_capacity_req > 100)
                        $e = 100;
        
                    $highest_free_capacity = 0;            
                    $rsd = $connect->prepare("SELECT ID FROM allocation WHERE Venue_ID = ? AND Day=? AND Time = ?");
                    while(list($id,$venue_capacity) = $rs->fetch()){

                        if(!in_array($id, $arr)){
                            /** Get the highest free venue and fit the student*/
                            
                            if($fit_capacity > $venue_capacity){
                                if( $venue_capacity > $highest_free_capacity){
                                    $rsd->execute([$id, $day, $time]);
        
                                    if($rsd->rowCount() == 0){
                                        $highest_free_capacity = $venue_capacity;
                                        $venue_id_picked = $id;
                                    }else{
                                        continue;
                                    }
                                }
                            }else{
                                if($e == 0)
                                    $min_venue_capacity_req = 0;
                                else
                                    $min_venue_capacity_req = ($e / 100) * $venue_capacity;

                                if( ($venue_capacity - $fit_capacity) <= $min_venue_capacity_req)
                                    $venue_id_picked = $id;
                                else
                                    continue;
                            }
    
                        }
                    }
                }
            
                return $venue_id_picked;
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        private function pick_free_venue($Course_Code,$fit_capacity, $day, $time, $multisight=false){

            try{

                $connect = (new Database())->connect();
                $sql	= new BuildQuery($connect);
                
                /**
                *	Check Venue Exemption
                */

                $arr = [];
                
                $rs 	=	$sql->read('venue_exemption', [ ['Course_Code',$Course_Code] ], ['Venue_ID'], []);       	
                while(list($v) = $rs->fetch()){
                    
                    array_push($arr, $v);
                }
                
                $data_bunch = $this->data_bunch;

                if($this->secondChanceHit_f){
                    $history = $this->getHistorybunch();
                    if( isset($history->{$Course_Code}) && isset($history->{$Course_Code}->{$day}->v_n) ){
                        $venue_id = $history->{$Course_Code}->{$day}->v_n;
                        $rs = $sql->read('venue', [ ['ID', $venue_id] ], ['Capacity']);
                        if($rs->rowCount() == 1){
                            list($venue_capacity) = $rs->fetch();

                            $rsd = $connect->prepare("SELECT ID FROM allocation WHERE Venue_ID = ? AND Day=? AND Time = ?");
                            $rsd->execute([$venue_id, $day, $time]);

                            if($rsd->rowCount() == 0){
                            
                                return $venue_id;
                            }

                        }
                    }
                }

                $e = $data_bunch->min_venue_capacity_req;
                if($data_bunch->min_venue_capacity_req > 100)
                    $e = 100;

                $venue_id_picked = 0;
                $trials = 0;
                checkIfVenueIsFree:
                if($trials > 11)
                    return $venue_id_picked;

                $trials += 1;

                if($multisight)
                    $rs = $connect->query("SELECT ID,Capacity FROM venue WHERE Not_In_Use=0 AND Multisight=1");
                else
                    $rs = $connect->query("SELECT ID,Capacity FROM venue WHERE Not_In_Use=0");
                

                $rsd = $connect->prepare("SELECT ID FROM allocation WHERE Venue_ID = ? AND Day=? AND Time = ?");

                while(list($id,$venue_capacity) = $rs->fetch()){

                    
                    if(!in_array($id, $arr)){
                        if($e == 0)
                            $min_venue_capacity_req = 0;
                        else
                            $min_venue_capacity_req = ($e / 100) * $venue_capacity;
                        
                        if( abs($venue_capacity - $fit_capacity) <= $min_venue_capacity_req){
                            $rsd->execute([$id, $day, $time]);
                            if($rsd->rowCount() == 0){
                                $venue_id_picked = $id;
                                break;
                            }else{
                                continue;
                            }
                            
                        }else{
                            continue;
                        }
                    }
                }

                return $venue_id_picked;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        private function isCourseForbidden($c_code,$time){

            try{

                $connect = (new Database())->connect();

                $sql = new BuildQuery($connect);

                $rs = $sql->read('participant', [ ['Course_Code', $c_code] ],['Participant_Array']);
                list($participantString) = $rs->fetch();

                /**check for electives */
                if($c_code[3] == 0 && $c_code[4] == 0){
                    /**Electives must not be held same hours */
                    
                    if($participantString == '*' || $participantString == null || strlen($participantString) == 0){
                        if(isset($this->forbiddenList[$time]['SE'][1]))
                            return true;
                    }
                }
                
                $rs = $sql->read('course', [ [ 'Code',$c_code ] ], ['Host_dept'] );

                list($Host_dept) = $rs->fetch();    

                /*empty host_Dept is assumed not clash with any other course must not be empty else its forbidden*/
                if(empty($Host_dept))
                    return false;

                    if ($participantString == '*')
                    return false;
                

                if ($participantString == NULL) 
                    return false;

                if($rs->rowCount() == 0)
                    return false;

                /**
                 * Cleaning host participant String
                 */
                $participantString = preg_replace('/(&)+/','&',$participantString);
                $participantString = preg_replace('/(\()+/','(',$participantString);
                $participantString = preg_replace('/(\))+/',')',$participantString);

                if( strlen($participantString) > 0  ){
                
                    $a = explode('&', rtrim($participantString,'&'));

                    $halt = false;
                    foreach ($a as  $part) {

                        $part = ltrim($part,'(');
                        $part = trim(rtrim($part,')'));

                        $b = explode(',', $part);
                        $dept = @trim($b[0]);
                        $level = @intval($b[1]);

                        if(isset($this->forbiddenList[$time][$dept][$level])){
                            $halt = true;
                            break;
                        }
                    }
                }

                return $halt;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        private function  processAllocationHistory($Course_Code, $Venue_id, $v_tol_sign, $Day_id){
            
            try{

                $data = $this->getHistorybunch();

                $c_tol = $this->data_bunch->tolerance;
                $v_tol = $this->data_bunch->min_venue_capacity_req;

                /**normalize the tolerances */
                $accuracy = 1 - ( ( ($c_tol + ($v_tol * $v_tol_sign) ) - 0) / (100 - 0) );

                if(  (@$data->{$Course_Code}->{$Day_id}->accuracy < $accuracy) || !@isset($data->{$Course_Code}->{$Day_id})  ){
                    $d=['v_n' => $Venue_id, 'accuracy' => $accuracy] ;
                    @$data->{$Course_Code}->{$Day_id} = $d;
                }

                $this->saveHistorybunch($data);

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        private function getHistorybunch(){
            try{
                return json_decode(file_get_contents((new  cfg())->getAppPath()."database/allocationhistory.json"));  
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }          
        }

        private function saveHistorybunch($data_bunch){
            try{
                $AbsPath  =   (new cfg())->getAppPath();
                file_put_contents("{$AbsPath}database/allocationhistory.json", json_encode($data_bunch,JSON_PRETTY_PRINT));

                return true;   
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }      
        }

        private function presetForbiddenList($day_id){

            try{
                $connect = (new Database())->connect();

                $sql = new BuildQuery($connect);

                $this->forbiddenList = [];

                $rs = $sql->read('allocation', [ ['Day',$day_id] ], ['Course_Code','Time']);

                while(list($c_code,$time) = $rs->fetch()){

                $this->updateForbiddenList($c_code,$time);

                }
            
                return null;
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        private function updateForbiddenList($c_code,$time){

            try{

                $connect = (new Database())->connect();

                $sql = new BuildQuery($connect);

                if(!isset($this->forbiddenList[$time]))
                    $this->forbiddenList[$time] = [];

                    /**Identify Elective */
                    if($c_code[3] == 0 && $c_code[4] == 0){
                        /**Electives must not be held same hours */
                            
                        /**check for elective participant array */
                            $rs = $sql->read('participant', [ ['Course_Code', $c_code] ],['Participant_Array']);
                            list($participantString) = $rs->fetch();

                            if($participantString == '*' || $participantString == null || strlen($participantString) == 0){
                                $this->forbiddenList[$time]['SE'][1] = true;
                                return true;
                            } 
                    }

                $rs = $sql->read('participant', [ [ 'Course_Code',$c_code] ], [ 'Participant_Array' ] );

                list($participantString) = $rs->fetch();

                if ($participantString == '*')
                    return true;
                

                if ($participantString == NULL) 
                    return true;

                if($rs->rowCount() == 0)
                    return true;

                /**
                 * Cleaning participant String
                 */
                $participantString = preg_replace('/(&)+/','&',$participantString);
                $participantString = preg_replace('/(\()+/','(',$participantString);
                $participantString = preg_replace('/(\))+/',')',$participantString);

                if( strlen($participantString) > 0  ){
                
                    $a = explode('&', rtrim($participantString,'&'));


                    foreach ($a as  $part) {

                        $part = ltrim($part,'(');
                        $part = trim(rtrim($part,')'));

                        $b = explode(',', $part);

                        if(isset($b[0]) && isset($b[1])){
                            $details = [ trim($b[0]), intval($b[1]) ];
                            $this->forbiddenList[$time][$details[0]][$details[1]] = true;
                        }else{
                            Logger::Log('Broken participant format'.$c_code);
                        } 
                    }
                }

                return true;
            
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        private function resetForbiddenList(){

            $this->forbiddenList = [];
            return null;
        }

        public function open($day){
            try{

                $connect = (new Database())->connect();
                $sql = new BuildQuery($connect);
                $arr = [];
                $op = $connect->query("SELECT DISTINCT Location, Name FROM `venue` ORDER BY Capacity DESC, Location, Name ASC");

                while(list($v_l, $e) = $op->fetch()){
                    $oo = $sql->read('venue',  [ ['Location', $v_l] ], ['ID', 'Name'], ['ORDER BY Name']);

                    while(list($v_id, $v_n) = $oo->fetch()){
                        $rs   =   $sql->read('allocation',[ ['Day',$day], ['Venue_ID', $v_id] ], ['ID','Course_Code','Time'] ); 
                        while( list($id,$c_code, $time) = $rs->fetch()){

                            //$op = $sql->read('venue',[ ['ID',$v_id] ], ['Name','Location'] );
            
                            $arr[$v_n]['location'] = $v_l;
                            
                            $key = array_search($time, $this->time);
            
                            // $arr[$v_n]['course'][$key] = $c_code;
                            
                            $arr[$v_n]['course'][$key] = [ 'allocationIndex'=>$id, 'courseCode'=>$c_code ];
                            
                        
                        }
                    }
                }
                
                return $arr;
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }
        
        public function getTolerance(){
            try{
                return intval($this->data_bunch->tolerance);    
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }       
        }

        public function getVenueTolerance(){
            try{
                return intval($this->data_bunch->min_venue_capacity_req);     
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }      
        }

        public function setTolerance($tolerance){
            try{
                $AbsPath  =   (new cfg())->getAppPath();
                $data_bunch = $this->data_bunch;
                
                $data_bunch->tolerance = intval($tolerance);

                file_put_contents("{$AbsPath}database/data.json", json_encode($data_bunch,JSON_PRETTY_PRINT));

                return true;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function saveDataBunch($data_bunch){
            try{
                
                $AbsPath  =   (new cfg())->getAppPath();
                file_put_contents("{$AbsPath}database/data.json", json_encode($data_bunch,JSON_PRETTY_PRINT));

                return true;
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function setVenueTolerance($tolerance){
            try{
               
                $AbsPath  =   (new cfg())->getAppPath();
                $data_bunch = $this->data_bunch;
                
                $data_bunch->min_venue_capacity_req = intval($tolerance);
    
                file_put_contents("{$AbsPath}database/data.json", json_encode($data_bunch,JSON_PRETTY_PRINT));
    
                return true;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function resetADay($day_id){
            try{
                $connect = (new Database())->connect();

                $sql = new BuildQuery($connect);

                $day_id = stripslashes(strip_tags($day_id));

                $query   =   $connect->prepare("SELECT Course_Code FROM allocation WHERE Day= ? AND Fixed=? ");
                
                $query->execute([$day_id,0]);
                
                while( list($code) = $query->fetch()){
                    $rs0 = $connect->query("UPDATE course SET Allocation_Hit='0' WHERE Code='$code' ");
                    $rs0 = $connect->query("DELETE FROM allocation WHERE Course_Code='$code' AND Day='$day_id'");
                }
                
                if($query->rowCount() > 0)
                    return true;
                
                return false;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function reset(){
            try{
                $connect = (new Database())->connect();

                $sql = new BuildQuery($connect);

                $current_working_semester = $this->whichSemesterOnAllocation();
                if($current_working_semester === false)
                    return false;
                
                $rs = $sql->read('course', [], [ 'Code' ]);
                $rsd = $connect->prepare("UPDATE course SET Allocation_Hit=? WHERE Code=?");
                while(list($c_code) = $rs->fetch()){
                    if($this->isNotForSemester($current_working_semester, $c_code))
                        continue; 

                    $rsd->execute([0, $c_code]);
                }

                $rs   =   $connect->query("TRUNCATE allocation");
                unset($this->data_bunch->current_running_semester);

                if ($rs->rowCount() == 0) {

                    return false;
                }
            
                return true;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function open_unallocated(){
            try{
                $connect = (new Database())->connect();

                $sql = new BuildQuery($connect);

                $current_working_semester = $this->whichSemesterOnAllocation();
                if($current_working_semester === false)
                        return [];
                
                $rs   =   $sql->read('course',[ ['Allocation_Hit',0],['Practical','<>',1] ], ['Name','Code','ScheduleLimit','No_Of_Occurence','Hour_required'] );

                $arr = [];
                
                while( list($name, $code, $straight_hours, $no_of_class, $Hour_required) = $rs->fetch() ){
                    
                    if($this->isNotForSemester($current_working_semester, $code))
                        continue;

                    $t = $sql->read('allocation', [ ['Course_Code',$code] ],[]);

                    $occurence_in_allocation = $t->rowCount();

                    if ($occurence_in_allocation != 0) {

                        $occurence_in_allocation /= ($no_of_class * $straight_hours);
                    }

                    $rsi = $sql->read('course_offered',[ ['Course_Code',$code] ],['Capacity']);
                    list($no_registered) = $rsi->fetch();

                    if( ($occurence_in_allocation < 5)  ){
                        if($occurence_in_allocation < $Hour_required)
                            $arr[$code] = [$name,$no_of_class,$Hour_required, intval($occurence_in_allocation), $no_registered];
                        
                    }
                } 

                unset($rs, $rsi);
                return $arr;
            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function open_forgotten(){
            try{
                $connect = (new Database())->connect();
    
                $sql = new BuildQuery($connect);
    
                $current_working_semester = $this->whichSemesterOnAllocation();
                if($current_working_semester === false)
                        return [];
                
                $rs   =   $sql->read('course',[ ['Allocation_Hit',2],['Practical','<>',1] ], ['Name','Code','ScheduleLimit','No_Of_Occurence','Hour_required'] );
    
                $arr = [];
                
                while( list($name, $code, $straight_hours, $no_of_class, $Hour_required) = $rs->fetch() ){
                    
                    if($this->isNotForSemester($current_working_semester, $code))
                        continue;
    
                    $t = $sql->read('allocation', [ ['Course_Code',$code] ],[]);
    
                    $occurence_in_allocation = $t->rowCount();
    
                    if ($occurence_in_allocation != 0) {
    
                        $occurence_in_allocation /= ($no_of_class * $straight_hours);
                    }
    
                    $rsi = $sql->read('course_offered',[ ['Course_Code',$code] ],['Capacity']);
                    list($no_registered) = $rsi->fetch();
    
                    if( ($occurence_in_allocation < 5)  ){
                        if($occurence_in_allocation < $Hour_required)
                            $arr[$code] = [$name,$no_of_class,$Hour_required, intval($occurence_in_allocation), $no_registered];
                        
                    }
                } 
    
                unset($rs, $rsi);
                return $arr;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }
 
        public function whichSemesterOnAllocation(){
            try{
                $connect = (new Database())->connect();
                $sql = new BuildQuery($connect);

                $rs = $sql->read('allocation', [], ['Course_Code'],['ORDER BY RAND() LIMIT 1']);
            
                if($rs->rowCount() == 0)
                    return false;

                return $this->data_bunch->current_running_semester;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }

        }

        public function fix_course(string $c_code, int $day_id, int $tolerance, bool $check_clashes){
            
            try{

                $connect = (new Database())->connect();

                $sql = new BuildQuery($connect);
                
                if($check_clashes)
                    $this->presetForbiddenList($day_id);
                
                $rs = $sql->read('allocation', [ ['Course_Code',$c_code],['Day',$day_id] ]);
                if($rs->rowCount() > 0){
                    $this->msg = "$c_code is currently fixed here";
                    return false;
                }

                $rs   =   $sql->read('course',[ ['Code',$c_code] ], ['No_Of_Occurence','Hour_required','ScheduleLimit'] );
                list($no_of_class,$total_hour_required,$StraightHours) = $rs->fetch();
            

                if($day_id == 5){
                    unset($this->time[4],$this->time[5],$this->time[6]);
                    $this->time = array_values($this->time);
                }else{
                    $this->time = ['8/9','9/10','10/11','11/12','12/1','1/2','2/3','3/4','4/5','5/6'];;
                }

                $time = $this->time;

                $highest_time_index	=	count($time)-1;          	
                                
                $rs = $sql->read('course_offered',[ ['Course_Code',$c_code] ],['Capacity']);
                
                list($no_registered) = $rs->fetch();
            
                $rs = $sql->read('allocation',[ ['Course_Code',$c_code], ['Day', $day_id] ],['Venue_ID']);
                $allocated_capacity = 0;
                if($rs->rowCount() > 0){
                    
                    while(list($v_id) = $rs->fetch()){
                        $c = $sql->read('venue',[ ['ID',$v_id] ],['Capacity']);
                        list($d) = $c->fetch();
                        $allocated_capacity += $d;
                    }
                    unset($v_id, $c, $d);
                }
            
                $no_registered -= $allocated_capacity;

                /**
                *   Fit Course Capacity to System Tolerance Value e.g System tolerate 2000 students as 1600
                *   with a tolerance of 20% loss of course capacity
                */

                $fit_capacity = ceil(round( ( (100 - $tolerance )  / 100), 1 ) * $no_registered);
            
                if($this->isMaxAllocationHit($c_code) === true){
                    $this->msg = "Couldn't fix course, max. allocation per week reach";
                    return false;
                }
            
                $rs = $sql->read('allocation',[ ['Course_Code',$c_code] ],['ID']);
                $occurence_in_allocation = $rs->rowCount();
                if ($occurence_in_allocation != 0) {
                    $occurence_in_allocation /= ($no_of_class * $StraightHours);
                }
                $this->allocationCounts[$c_code] = floor($occurence_in_allocation);

                $trial = 0;

                tryAllocation:
                $trial += 1;

                if($no_of_class > 1){

                    $rs = $sql->read('course_constraint',[ ['Course_Code',$c_code] ], ['Time_Bound', 'Lecture_Bound', 'Day_Bound'] );
                    list($t_bool, $l_bool, $d_bool) = $rs->fetch();

                    $RecursiveSchedule = 1;
                    if( $d_bool == 1)
                        $RecursiveSchedule = $no_of_class;

                    $lecture_bounded_in_same_hour =   false;
                                
                    if ($l_bool == 1 ) 
                        $lecture_bounded_in_same_hour =   true;

                    if ($t_bool == 1 ) 
                        $highest_time_index	=	floor( (count($time)-1) / 2 ) + 2;
                    
                    $this->allocate_constrained_course_venue_time($c_code, $RecursiveSchedule, $highest_time_index, $lecture_bounded_in_same_hour, $fit_capacity, $StraightHours, $day_id);
                }else{
                    $this->allocate_unconstrained_course_venue_time($c_code, $highest_time_index, $fit_capacity, $StraightHours, $day_id);
                }

                
                $rs = $sql->read('allocation', [ ['Course_Code',$c_code],['Day',$day_id] ]);

                if($rs->rowCount() > 0){
                    $this->msg = "Course fixed";
                    return true;
                }else{
                    if($trial < 20){
                        goto tryAllocation;
                    }
                    $this->msg = "Couldn't fix course, retry";
                    return false;
                }

                $this->resetForbiddenList();
                unset($this->allocationCounts[$c_code]);

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function override_course($c_code){
            try{
                //Forgets course and assume its allocation has hit :2 means forgotten allocation
                $connect = (new Database())->connect();

                $sql = new BuildQuery($connect);

                $rs = $sql->update('course', [ ['Code', $c_code] ], [ ['Allocation_Hit', 2] ]);
                
                if($rs->rowCount() > 0)
                    return true;
            
                return false;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function undo_override_course($c_code){
            try{
                //Rollback course and assume its allocation remains :0 means incomplete allocation
                $connect = (new Database())->connect();

                $sql = new BuildQuery($connect);

                $rs = $sql->update('course', [ ['Code', $c_code] ], [ ['Allocation_Hit', 0] ]);
                
                if($rs->rowCount() > 0)
                    return true;
            
                return false;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function allocationReflection(int $allocationIndex, string $c_code){

            try{
                
                $connect = (new Database())->connect();
                $sql = new BuildQuery($connect);

                $rs = $sql->read('allocation', [  ['Course_Code', $c_code] ], [], ['ORDER BY ID']);
                $arr = [  ];

                while( list($id, $c, $venue_id, $time_index, $day_id, $e, $e, $tol) = $rs->fetch() ){
                    
                    $d = $this->days[$day_id - 1];
                   

                    $v_rs = $sql->read('venue', [ [ 'ID', $venue_id ] ], ['Name', 'Location', 'capacity', 'Multisight']);
                    list( $v_name, $v_loc, $v_cap, $v_is_multisight) = $v_rs->fetch();

                    array_push( $arr, [ 'day'=>$d, 'time'=>$time_index, 'tolerance_loss'=>$tol,'venue'=>[ 'name'=>$v_name, 'location'=>$v_loc, 'capacity'=>$v_cap, 'is_multisight'=>$v_is_multisight] ] );
                } 

                $rs = $sql->read('course_offered', [  ['Course_Code', $c_code] ], ['Capacity']);
                list($capacity) = $rs->fetch();
                
                $arr['cap'] = $capacity;

                return $arr;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
        }
	} 

?>