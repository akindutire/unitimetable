<?php

/**
 * Protected Course Modals, not a public asset
 */
?>

<div class="w3-modal" id="course_setting_modal" style="display: none; padding-top: 40px;">
    <div class="w3-modal-content w3-white" style="width: 65% !important;">

        <div class="w3-display-container" style="top: 5%;">
            <a class="w3-display-topleft w3-padding w3-large">Edit {{ gwork.c_code }}  </a>
            
            <span class="w3-display-topmiddle">
                <button class="w3-btn w3-red w3-round" type="button" ng-click=remove_course($event)>Delete Course</button>
                <button class="w3-btn w3-pale-red  w3-round" type="button" ng-click=remove_allocation_pathway($event)>Delete Allocation Pathways</button>
           </span>

            <a class="w3-display-topright w3-tag w3-red w3-padding" ng-click="close_setting()"><i class="fa fa-times"></i></a>
        </div>

        <hr class="w3-col">

        <div class="w3-container" style="padding-top: 5% !important;">

         

            <section id="notif" class="w3-col l12 m12 s12">
                <span class="w3-text-green w3-animate-fading w3-margin-left">{{ loading_notification }}</span>
            </section>

            <section class="w3-col l6 m6 s12 w3-padding">

                <div class="w3-padding w3-border w3-round w3-card-2 w3-col" style="">

                    <p class="w3-col l12 m12 s12">Basic Details</p>
                    <p class="w3-col l12 m12 s12"></p>
                    <form class="w3-col l12 m12 s12" id="form_basic_course_details">

                        <label>Title</label>
                        <input type="text" id="title" ng-keyup="monitor_changes()" class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom"><br><br>

                        <label>Hours Required in a week</label>
                        <select id="hrsReq" ng-focus="monitor_changes()" class="w3-select w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom">

                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            
                        </select><br><br>

                        <label>Straight Hours Required in a day</label>
                        <select id="strHrs" ng-focus="monitor_changes()" class="w3-select w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom">

                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select><br><br>

                        <label>Total Registered</label>
                        <input type="number" ng-keyup="monitor_changes()" min="1" id="course_capacity" class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom"><br><br>

                        <input type="checkbox" class='w3-input' style="width: auto; margin-right: 8px; display: inline;" id="timebound">Course Should be held at early hours alone?<br><br>

                        <div class="w3-center w3-margin-top">

                            <div class="w3-padding w3-col l6 m12 s12">
                                <button ng-click=open_course_participants($event) type="button" class="w3-btn w3-blue-grey  w3-ripple w3-round w3-col l12 m12 s12">View Participants</button>
                            </div>                        
                                
                            <div class="w3-padding w3-col l6 m12 s12">
                                <button ng-click=open_course_fixing_modal($event) type="button" id="fixcourse" class="w3-btn w3-orange w3-padding w3-ripple w3-round w3-col l12 m12 s12">Force Fix Course</button>
                            </div> 
                            
                            
                        </div>

                    </form>
                </div>

            </section>

            <section class="w3-col l6 m6 s12 w3-padding">
                <div class="w3-padding w3-border w3-round w3-card-2 w3-col">

                    <p class="w3-col l12 m12 s12"><span ng-show="!course_constraint_strict_mode">Course Contraints</span> <span ng-show="course_constraint_strict_mode">Strict Mode</span>&nbsp; <a id="multiresource" style="display: none;" class="w3-badge w3-round  w3-blue w3-small">multi sight</a>
                    
                    <a  title="Strict mode" ng-if="!course_constraint_strict_mode"  ng-click=enter_course_constraint_strict_mode($event) class="w3-right w3-text-blue"><i class="fas fa-cog"></i></a>

                    <a  title="Exit Strict mode" ng-if="course_constraint_strict_mode"  ng-click=leave_course_constraint_strict_mode($event) class="w3-right w3-text-blue"><i class="fas fa-arrow-left"></i></a>

                    </p>
                    
                    <div ng-if="!course_constraint_strict_mode">
                        <p><h5>Note:</h5> <span>1. All Classes would be held the same day. For manipulations use the <span class="w3-text-blue">fix course</span>  button</span></p>
                    </div>

                    <div ng-if="course_constraint_strict_mode">
                        <p><h5>Note:</h5> <span>1. You can set proportions of classes you want here</span></p>
                    </div>

                    <form ng-show="!course_constraint_strict_mode" class="w3-col l12 m12 s12" id="form_course_constraint">

                        <label>Max. No. of Classes</label>
                        <input type="text" id="no_of_class"  class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-keyup=check_constraint($event) id="no_of_class"><br><br>

                        <elon id='base_constraint' style='display: none;'>
                            
                            <input type="checkbox" class='w3-input' style="width: auto; margin-right: 8px; display: inline;" id="lecturebound">Classes Should be held same hour?
                            
                            <br><input type="checkbox" class='w3-input' style="width: auto; margin-right: 8px; display: inline;" id="multisightbound" onclick="document.getElementById('lecturebound').checked=true;">Multi Sight?

                            
                            <br><span style="display: none;"><input type="checkbox" class='w3-input' style="width: auto; margin-right: 8px; display: inline;" id="daybound" checked>Classes Should be held same day?</span>

                        </elon>

                        <!--<p class="w3-center w3-margin-top"><button ng-click=edit_course_constraint($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Save</button></p>-->

                    </form>

                    <form class="w3-col l12 m12 s12 w3-border-pale-red" ng-show="course_constraint_strict_mode" id="form_course_constraint_strict_mode">

                        <div id="form_course_constraint_strict_mode_editable">
                        
                        </div>

                       

                        <p class="w3-center w3-margin-top">
                            <button ng-disabled="gwork.class_proportion_overflow" ng-click=edit_course_allocation_plan($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Save</button>

                            <button ng-disabled="!gwork.course_allocation_plan_exists" ng-click=remove_course_allocation_plan($event) type="button" class="w3-btn w3-red w3-padding w3-ripple w3-round">Thrash</button>

                        </p>

                    </form>

                </div>

            </section>

            <section class="w3-col l6 m6 s12 w3-padding">
                <div class="w3-padding w3-border w3-round w3-card-2 w3-col">

                    <p class="w3-col l12 m12 s12">Venues to be Exempted on Allocation</p>
                    <p class="w3-col l12 m12 s12"></p>

                    <form class="w3-col l12 m12 s12" id="form_venue_exemption">

                        <label></label>
                        <select class="w3-select w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" id='venue_list' multiple="multiple">
                            <?php

                            foreach ($venue as $id => $v) {
                                echo "<option value='{$id}'>{$v[0]}</option>";
                            }
                            ?>

                        </select><br><br>

                        <p class="w3-center w3-margin-top"><button ng-click="logVenueSelection()" type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Add to Box</button></p>
                    </form>

                </div>

            </section>


            <section class="w3-col l12 m12 s12 w3-padding">
                <div class="w3-padding w3-border w3-round w3-card-2 w3-col">

                    <p class="w3-col l12 m12 s12">Venues Already Exempted on Allocation</p>
                    
                    <div id='venue_already_exempted' class='w3-col l12 m12 s12'>


                    </div>

                </div>

            </section>


            <section class="w3-col l12 m12 s12">
                <p class="w3-col l12 m12 s12 w3-center">
                    <button class="w3-btn w3-green w3-margin-right w3-round" type="button" ng-click="save_all_changes($event)">Save</button>
                    <button class="w3-btn w3-red w3-margin-right w3-round" type="button" ng-click="close_setting()">Close</button>
                </p>
            </section>

        </div>
    </div>
</div>


<div class="w3-modal" id="course_participant_modal" style="display: none;">
    <div class="w3-modal-content w3-white" style="width: 60% !important;">

        <div class="w3-display-container" style="top: 5%;">
            <a class="w3-display-topleft w3-padding w3-large"> {{ gwork.c_code }}  Prticipants</a>

            <a class="w3-display-topright w3-tag w3-red w3-padding" ng-click=close_course_participants()><i class="fa fa-times"></i></a>
        </div>

        <hr class="w3-col">

        <div class="w3-container" style="padding-top: 8% !important;">
           
            <div class="w3-col l6 m6 s12 w3-padding">
                
                <div class="w3-col l12 m12 s12 w3-padding w3-border w3-round w3-card-2">

                    <p></p>
                    <form class="w3-col l12 m12 s12 w3-form" id="course_participant_frm">


                        <label>Department (ctrl + <i class="fas fa-arrow-down"></i> for multiple dept.)</label><br>
                        <select title="Select Department" ng-focus="monitor_changes()" name="dept" id="dept"  class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom"  multiple="multiple">

                            <option value=0 ng-click="selectAllVenuesof('form#course_participant_frm select#dept')" style="width: auto;" class="w3-pale-blue">--All Dept.--</option>
                            <?php

                            foreach ($alldept as $faculty_name => $dept_arr) {

                                foreach ($dept_arr as $dkey => $data) {
                                    echo "<option value={$data[1]} class='w3-bar-item w3-padding w3-border-bottom'>{$data[0]}</option>";
                                }
                            }

                            ?>
                        </select><br>

                        <label>Part</label><br>
                        <select class="w3-select w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-focus="monitor_changes()" id="level">
                            <option value=0>--Select Level--</option>
                            <option value=1>1</option>
                            <option value=2>2</option>
                            <option value=3>3</option>
                            <option value=4>4</option>
                            <option value=5>5</option>
                            <option value=6>6</option>
                            <option value=7>7</option>
                        </select><br><br>

                        

                        <p class="w3-center w3-margin-top"><button ng-click=add_course_participant($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Add</button></p>

                    </form>

                </div>
            </div>


            <div class="w3-col l6 m6 s12 w3-padding">
                <div class="w3-col l12 m12 s12 w3-padding w3-border w3-round w3-card-2" id="course_participant_view" style="max-height: 400px; overflow: scroll; overflow-x: unset;">
                    
                </div>
            </div>

             <!--<p class="w3-col l12 m12 s12 w3-center">
                    <button class="w3-btn w3-red w3-margin-right w3-round" type="button" ng-click="close_course_participants()">Close</button>
            </p>-->
        </div>
    </div>
</div>

<div class="w3-modal" id="fixed_allocation_modal" style="display: none;">
    <div class="w3-modal-content w3-white" style="width: 60% !important;">

        <div class="w3-display-container" style="top: 5%;">
            <a class="w3-display-topleft w3-padding w3-large"> {{ gwork.c_code }} Fixed Allocations</a>

            <a class="w3-display-topright w3-tag w3-red w3-padding" ng-click=close_fixed_allocation()><i class="fa fa-times"></i></a>
        </div>

        <hr class="w3-col">

        <div class="w3-container" style="padding-top: 8% !important;">
           
            <div class="w3-col l6 m6 s12 w3-padding">
                
                <div class="w3-col l12 m12 s12 w3-padding w3-border w3-round w3-card-2">

                    <p><h5>Note:</h5> <span>1.If no value is selected in a field below then the system decides</span><br><span>2. Timings fixed would effect while system generate timetable</span></p>
                    <form class="w3-col l12 m12 s12 w3-form" id="fixed_allocation_frm">


                        <label>Venue</label><br>
                        <select class="w3-select w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-focus="monitor_changes()" id='venue_list' multiple="multiple">

                            <option value=0 id="all" style="width: 100%;" class="w3-pale-blue" ng-click="selectAllVenuesof('form#fixed_allocation_frm select#venue_list')">--All Venue--</option>
                            <?php

                                foreach ($venue as $id => $v) {
                                    echo "<option value='{$id}'>{$v[0]}</option>";
                                }

                            ?>

                        </select><br>

                        <label>Day</label><br>
                        <select class="w3-select w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-focus="monitor_changes()" id="day">
                            <option value=0>--Select Day--</option>
                            <option value=1>Monday</option>
                            <option value=2>Tuesday</option>
                            <option value=3>Wednesday</option>
                            <option value=4>Thursday</option>
                            <option value=5>Friday</option>
                        </select><br>
        
                        <label>Time</label><br>
                        <select class="w3-select w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-focus="monitor_changes()" id="time">
                            <option value=0>--Select Time--</option>
                            <option value='8/9'>8am-9am</option>
                            <option value='9/10'>9am- 10am</option>
                            <option value='10/11'>10am - 11am</option>
                            <option value='11/12'>11am - 12pm</option>
                            <option value='12/1'>12pm - 1pm</option>
                            <option value='1/2'>1pm - 2pm</option>
                            <option value='2/3'>2pm - 3pm</option>
                            <option value='3/4'>3pm - 4pm</option>
                            <option value='4/5'>4pm - 5pm</option>
                            <option value='5/6'>5pm - 6pm</option>
                        </select><br>
                        <br>

                        

                        <p class="w3-center w3-margin-top"><button ng-click=fix_course_to_slot($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Fix</button></p>

                    </form>

                </div>
            </div>


            <div class="w3-col l6 m6 s12 w3-padding">
                <div class="w3-col l12 m12 s12 w3-padding w3-border w3-round w3-card-2" id="fixed_timings_view" style="max-height: 400px; overflow: scroll; overflow-x: unset;">
                    
                </div>
            </div>

             <!--<p class="w3-col l12 m12 s12 w3-center">
                    <button class="w3-btn w3-red w3-margin-right w3-round" type="button" ng-click="close_course_participants()">Close</button>
            </p>-->
        </div>
    </div>
</div>


