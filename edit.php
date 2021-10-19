<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version details
 *
 * @package    local_progress_card
 * @author     Mayur_Pawar
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/progress_card/classes/form/edit.php');

global $DB;
$users= $DB->get_records('user');
$grades= $DB->get_records('grade_grades');
$items= $DB->get_records('grade_items');
$courses= $DB->get_records('course');


$PAGE->set_url(new moodle_url('/local/progress_card/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Edit');
$prog=false;

$prn="";

$itm=0;

$mform = new edit();


$res="";
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/local/progress_card/edit.php','You cancelled the operation');
} else if ($fromform = $mform->get_data()) {
  //In this case you process validated data. $mform->get_data() returns data posted in form.


    
    $prn=$fromform->prntext;
    $check="";
    foreach($users as $user){

      //using userid which we have stored 
      //saving username in the result.
      if($user->username == $prn){
        $res.='<b style="color:blue"> Name of the User: '. $user->lastname . '</b>';
       // $res.='<b>'. 'Activity Name -- Course Details --------------------------------------------- Marks Obtained -- Total Marks </b> <br>';
        $uid=$user->id;
        foreach($grades as $grade){

          if($grade->userid== $uid){
            $itm=$grade->itemid;

            foreach($items as $item){
              
              if($item->id==$itm){
                
                $cid=$item->courseid;
                foreach($courses as $course){
                  if($cid==$course->id){
                    $res.='<br><br><b style="color:black;">Course Name: </b><b style="color:darkred;">';
                    $res.=$course->fullname;
                    $res.='</b>';
                  }
                }
                if($item->itemname==NULL){
                  $res.='<br><b style="color:darkmagenta;">Activity Name: ' . 'Total Grades</b>';
            
                }else{
                  $res.='<br><b style="color:darkmagenta;">Activity Name: ' . $item->itemname . '</b>' ;
                  
                }
              }

            }
            $res.='<br><b style="color:darkgreen;">Grades: ';
            if(!$grade->finalgrade){
              $res.='0.00000' . ' out of ' . $grade->rawgrademax . '</b>';
            }
            else{
            $res.=$grade->finalgrade . ' out of ' . $grade->rawgrademax . '</b>';
            }

          }

        
        }


        $prog=true;
      }
    }
    
}
  

echo $OUTPUT->header();

$mform->display();

if($prog==true){
  echo $res;
  $prn="";
  $prog=false;
}
// $templatecontext =(object)[
//   'prn'=> $prn
// ];
//$renderable=new\local\progress_card\edit.php($prn);


//echo $OUTPUT->render_from_template('local_progress_card/edit',$prn);

echo $OUTPUT->footer();