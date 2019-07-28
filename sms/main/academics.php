<?PHP include("../includes/main_class.php");?>
<?php include_once '../includes/header.php';?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>





<script type="text/javascript">
    $(document).ready(function(){
        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#myTab a[href="' + activeTab + '"]').tab('show');
            $("#myTab").trigger("click");

        }
    });
</script>


<script type="text/javascript">
    function loadpage(str) {
        if (str == "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        }
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "../includes/home_work.php?class_id=" + str, true);
        xmlhttp.send();
    }
</script>


<script type="text/javascript">
    function load_class_work(str) {
        if (str == "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        }
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("class_work").innerHTML = xmlhttp.responseText;
            }
        } ;


        xmlhttp.open("GET", "../includes/home_work.php?class_id=" + str, true);
        xmlhttp.send();
    }
</script>


<script type="text/javascript">
    function load_class_test(str) {

        if (str == "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        }
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("class_test").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "../includes/home_work.php?class_id=" + str, true);
        xmlhttp.send();
    }
</script>


<script type="text/javascript">
    function load_exams(str) {

       if (str == "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        }
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("exams").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "../includes/home_work.php?class_id=" + str, true);
        xmlhttp.send();
    }
</script>


<script type="text/javascript">
    function LOAD_MOCK_NUMBER(str) {
        if (str == "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        }
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("mock_loaded").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "../includes/load_mock.php?term_id=" + str, true);
        xmlhttp.send();
    }
</script>


<script type="text/javascript">
    function load_mocks(str) {
        if (str == "") {
            document.getElementById("aptitude").innerHTML = "";
            return;
        }
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("aptitude").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "../includes/load_mock.php?class_id=" + str, true);
        xmlhttp.send();
    }

</script>

<?php

$jhs_class_id = $top_ridge->jhs_3_class_id();


if (isset($_POST['home_work_results'])) {
$subject_id = mysql_prep($_POST['subject_id']);
$class_id = mysql_prep($_POST['class_id']);
$term_id = mysql_prep($_POST['term_id']);
$err = null;
if (isset($_POST['errors'])=='yes'){
$err=$_POST['errors'];
}
$student_id = $_POST['student_id'];

$home_work_one = $_POST['home_work_one'];
$home_work_two = 0;
$home_work_three = 0;
$home_work_four =0;

//    if ($home_work_one>=0 && $home_work_one<=15) {
$top_ridge->process_results('home_work_results', $subject_id, $class_id, $term_id, $student_id, $home_work_one, $home_work_two, $home_work_three, $home_work_four,$err);
//    }else{
//        echo $top_ridge->print_message(2, "Error. Home Work Results must be between 0-15. Check Entries and try again");
//
//    }



}


if (isset($_POST['class_work_results'])) {
$subject_id = mysql_prep($_POST['subject_id']);
$class_id = mysql_prep($_POST['class_id']);
$term_id = mysql_prep($_POST['term_id']);

$err = null;
if (isset($_POST['errors'])){
$err=$_POST['errors'];
}
$student_id = $_POST['student_id'];

$class_work_one = $_POST['class_work_one'];
$class_work_two = 0;
$class_work_three = 0;
$class_work_four = 0;

//    if ($class_work_one>=0 && $class_work_one<=15) {
$top_ridge->process_results('class_work_results', $subject_id, $class_id, $term_id, $student_id, $class_work_one, $class_work_two, $class_work_three, $class_work_four,$err);
//    }else{
//        echo $top_ridge->print_message(2, "Error. Project Work Results must be between 0-15. Check Entries and try again");
//
//    }

}


if (isset($_POST['class_test_results'])) {
$subject_id = mysql_prep($_POST['subject_id']);
$class_id = mysql_prep($_POST['class_id']);
$term_id = mysql_prep($_POST['term_id']);
$err = null;
if (isset($_POST['errors'])){
$err=$_POST['errors'];
}

$student_id = $_POST['student_id'];

$class_test_one = $_POST['class_test_one'];
$class_test_two = $_POST['class_test_two'];
$class_test_three = 0;
$array = array();
//    if ($class_test_one >= 0 && $class_test_one <= 15 && $class_test_two >= 0 && $class_test_two <= 15) {
$top_ridge->process_results('class_test_results', $subject_id, $class_id, $term_id, $student_id, $class_test_one, $class_test_two, $class_test_three, $array,$err);
//    } else {
//        echo $top_ridge->print_message(2, "Error.  CAT Results must be between 0-15. Check Entries and try again");
//
//    }
}
if (isset($_POST['final_exams_results'])) {
$subject_id = mysql_prep($_POST['subject_id']);
$class_id = mysql_prep($_POST['class_id']);
$term_id = mysql_prep($_POST['term_id']);
$err = null;
if (isset($_POST['errors'])){
$err=$_POST['errors'];
}

$student_id = $_POST['student_id'];


$reopening_exams =0;
$mgt_exams = 0;
$exams_score = $_POST['exams_score'];


$array = array();
//    if ($exams_score>=0 && $exams_score<=100) {
$top_ridge->process_results('exams_results', $subject_id, $class_id, $term_id, $student_id, $reopening_exams, $mgt_exams, $exams_score, $array,$err);
//    }else{
//        echo $top_ridge->print_message(2, "Error.  Examinations Results must be between 0-100. Check Entries and try again");
//    }
}


if (isset($_POST['lower_primary'])) {
$subject_id = mysql_prep($_POST['subject_id']);
$class_id = mysql_prep($_POST['class_id']);
$term_id = mysql_prep($_POST['term_id']);


$student_id = $_POST['student_id'];

$marks_obtained = $_POST['marks_obtained'];

$array = array();

$top_ridge->process_results('lower_primary', $subject_id, $class_id, $term_id, $student_id, $marks_obtained, $class_test_two, $class_test_three, $array);

}


if (isset($_POST['final_mock_results'])) {
$subject_id = mysql_prep($_POST['subject_id']);
$mock_id = mysql_prep($_POST['mock_id']);
$term_id = mysql_prep($_POST['term_id']);
$class_id = mysql_prep($_POST['class_id']);
$marks = $_POST['marks'];


$student_id = $_POST['student_id'];


$total_subjects_offered = $top_ridge->total_subject_offered_per_term($term_id, $class_id);
$total_records = count($student_id);


for ($count = 0; $count < $total_records; $count++) {
$id = $student_id[$count];
$mock_score = round($marks[$count]);

$update_scoress = mysqli_query($top_ridge_db_connection, "UPDATE std_mock_results SET marks = '$mock_score' WHERE mock_id = {$mock_id}
AND student_id = {$id} AND subject_id = {$subject_id}  ");
confirm_query($update_scoress);
}


/////subject positions array
$student_details = mysqli_query($top_ridge_db_connection, "SELECT * FROM std_mock_results WHERE subject_id= {$subject_id}
AND mock_id = {$mock_id} ORDER BY marks DESC  ");
confirm_query($student_details);


$size = mysqli_num_rows($student_details);
$array1 = array();

while ($row = mysqli_fetch_assoc($student_details)) {
$student_id = $row['student_id'];
$marks_obtained = $row['marks'];
array_push($array1, $marks_obtained);

}


$array2 = array();
$array2[0] = 1;

for ($index = 1; $index <= $size; $index++) {
if ($array1[$index] == $array1[$index - 1]) {

$array2[$index] = $array2[$index - 1];
} else {
$array2[$index] = ($index + 1);

}

}

/////end subject positions array


////////main subject positions
$student_pos = mysqli_query($top_ridge_db_connection, "SELECT * FROM std_mock_results WHERE subject_id= {$subject_id}
AND mock_id = {$mock_id} ORDER BY marks DESC  ");
confirm_query($student_pos);

$counter = 0;

while ($row = mysqli_fetch_assoc($student_pos)) {
$student_id = $row['student_id'];

$position = $array2[$counter];
$code = $top_ridge->position_code($position);
$position = $position . $code;

$update_pos = mysqli_query($top_ridge_db_connection, "UPDATE std_mock_results SET  position = '$position'
WHERE student_id = {$student_id} AND  subject_id= {$subject_id}
AND mock_id = {$mock_id} ");
confirm_query($update_pos);


//$count++;
$counter++;

}


/////ends main subject positions


/////OVERALL CLASS POSITION


$all_students_in_class = mysqli_query($top_ridge_db_connection, "SELECT DISTINCT student_id FROM std_mock_results
WHERE mock_id = {$mock_id} ");
confirm_query($all_students_in_class);


$total_subjects = mysqli_query($top_ridge_db_connection, "select distinct `subject_id` from  std_mock_results
WHERE `mock_id`= {$mock_id} ");
$total_subjects_for_term = mysqli_num_rows($total_subjects);


WHILE ($QUERY_RESULT = mysqli_fetch_assoc($all_students_in_class)) {
$id = $QUERY_RESULT['student_id'];
$subject_scores = mysqli_query($top_ridge_db_connection, "SELECT SUM(marks) AS TOTAL_SCORE FROM std_mock_results
WHERE mock_id = {$mock_id}  AND  student_id = {$id}  ");
confirm_query($subject_scores);


$a = mysqli_fetch_assoc($subject_scores);
$OVERALL_SCORE = round($a['TOTAL_SCORE']);
$AVERAGE_SCORE = round(($OVERALL_SCORE / $total_subjects_for_term), 2);


$update_mock_total = mysqli_query($top_ridge_db_connection, "UPDATE  std_mock_total_results SET total_score = $OVERALL_SCORE,
average_score = $AVERAGE_SCORE
WHERE mock_id={$mock_id}  AND student_id = {$id}	");
confirm_query($update_mock_total);

}

$score_sheet = array();


$score_sheet_data = mysqli_query($top_ridge_db_connection, "SELECT  total_score FROM  std_mock_total_results
WHERE mock_id = {$mock_id} ORDER BY total_score DESC");
confirm_query($score_sheet_data);
$class_size = mysqli_num_rows($score_sheet_data);


WHILE ($RESULTS = mysqli_fetch_assoc($score_sheet_data)) {
$OVERALL_SCORE = $RESULTS['total_score'];
array_push($score_sheet, $OVERALL_SCORE);

}


$overall_class_postion = array();
$overall_class_postion[0] = 1;


for ($index = 1; $index < $class_size; $index++) {
if ($score_sheet[$index] == $score_sheet[$index - 1]) {

$overall_class_postion[$index] = $overall_class_postion[$index - 1];
} else {
$overall_class_postion[$index] = ($index + 1);

}

}

$student_overall_position = mysqli_query($top_ridge_db_connection, "SELECT student_id FROM std_mock_total_results
WHERE mock_id = {$mock_id}
ORDER BY total_score DESC  ");
confirm_query($student_overall_position);

$initial_count = 0;
while ($row = mysqli_fetch_assoc($student_overall_position)) {
$student_id = $row['student_id'];
$OVERALL_POSITION = $overall_class_postion[$initial_count];
$CODE = $top_ridge->position_code($OVERALL_POSITION);
$OVERALL_POSITION = $OVERALL_POSITION . $CODE;

$update_overall_position = mysqli_query($top_ridge_db_connection, "UPDATE std_mock_total_results SET  overall_position = '$OVERALL_POSITION'
WHERE mock_id={$mock_id}  AND student_id = {$student_id} ");
confirm_query($update_overall_position);
$initial_count++;
}

/////end of OVERALL CLASS POSITION


////CALCUALATION OF BEST SIX
$MY_BEST_SIX = mysqli_query($top_ridge_db_connection, "SELECT distinct(student_id)   FROM std_mock_total_results
where mock_id = '$mock_id' ");

confirm_query($MY_BEST_SIX);

while ($rows_SET = mysqli_fetch_assoc($MY_BEST_SIX)) {
$stu_id = $rows_SET['student_id'];


$total_best_six = 0;


$score = mysqli_query($top_ridge_db_connection, "SELECT marks FROM std_mock_results WHERE
student_id = {$stu_id} AND mock_id = {$mock_id} AND
subject_id in (159, 175, 162, 167)    ");
confirm_query($score);


while ($a = mysqli_fetch_assoc($score)) {
$four_core_subjects = round($a['marks']);
$total_best_six += $four_core_subjects;

}


///other best 2 subjects here
$other_best_two_subjects = mysqli_query($top_ridge_db_connection, "SELECT subject_id, marks FROM std_mock_results
WHERE student_id = {$stu_id} AND mock_id = {$mock_id}
AND subject_id not in  (159, 175, 162, 167)
order by   marks DESC   LIMIT 2 ");
confirm_query($other_best_two_subjects);

while ($result_data = mysqli_fetch_assoc($other_best_two_subjects)) {

$best_two_subjects = $result_data['marks'];
$total_best_six += $best_two_subjects;

}

echo $total_best_six . '<br/>';


$update_best_six_subjects = mysqli_query($top_ridge_db_connection, "UPDATE  std_mock_best_six_results
SET best_six_total ='$total_best_six'   WHERE mock_id=$mock_id  AND student_id='$stu_id'  ");
confirm_query($update_best_six_subjects);


}


///////setting best six subjects positions
$my_best_six_subjects = mysqli_query($top_ridge_db_connection, " SELECT best_six_total  FROM std_mock_best_six_results  WHERE mock_id=$mock_id ORDER BY best_six_total  DESC ");
confirm_query($my_best_six_subjects);


$total_students = mysqli_num_rows($my_best_six_subjects);

$score_sheet_best_six = array();
WHILE ($RESULTS = mysqli_fetch_assoc($my_best_six_subjects)) {
$OVERALL_SCORE = $RESULTS['best_six_total'];
array_push($score_sheet_best_six, $OVERALL_SCORE);

}


$overall_class_postion = array();
$overall_class_postion[0] = 1;


for ($index = 1; $index < $total_students; $index++) {
if ($score_sheet_best_six[$index] == $score_sheet_best_six[$index - 1]) {

$overall_class_postion[$index] = $overall_class_postion[$index - 1];
} else {
$overall_class_postion[$index] = ($index + 1);

}

}

$student_overall_position = mysqli_query($top_ridge_db_connection, "SELECT student_id FROM std_mock_best_six_results
WHERE mock_id = {$mock_id}
ORDER BY best_six_total DESC  ");
confirm_query($student_overall_position);

$initial_count = 0;
while ($row = mysqli_fetch_assoc($student_overall_position)) {
$student_id = $row['student_id'];
$OVERALL_POSITION = $overall_class_postion[$initial_count];
$CODE = $top_ridge->position_code($OVERALL_POSITION);
$OVERALL_POSITION = $OVERALL_POSITION . $CODE;

$update_overall_position = mysqli_query($top_ridge_db_connection, "UPDATE std_mock_best_six_results SET  position ='$OVERALL_POSITION' WHERE mock_id={$mock_id}  AND student_id = {$student_id} ");
confirm_query($update_overall_position);
$initial_count++;
}


if ($update_overall_position) {
header("location:academics.php?mock_results=true&&class_id=$class_id&&subject_id=$subject_id&&mock_id=$mock_id&&term_id=$term_id");
}


}


?>

<div id="page-wrapper">
        <div class="container-fluid">
            <div class="row bg-title">
                                <div class="col-md-12">



                    <ol class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Academics</a></li>
                    </ol>

                                </div>
                <?php
                /////print error message if class is lily or does not do home_work
                if (isset($_GET['class_no_assignment']) == 'true') {
                    $class_id = mysql_prep($_GET['no_class_id']);
                    $class = $top_ridge->get_class_name($class_id);
                    echo $top_ridge->print_message(1, "Sorry. $class do not offer home work.");
                }


                if (isset($_GET['home_work_results'])=='true') {
                    $class_id = mysql_prep($_GET['class_id']);
                    $class = $top_ridge->get_class_name($class_id);
                    if (isset($_GET['err']) && $_GET['err']=='yes'){
                        echo $top_ridge->print_message(0, "Warning. $class Project Work Results contains invalid values ..please check and try again");

                    }else{
                        echo $top_ridge->print_message(1, "Congrats. $class Project Work Results were entered successfully.");

                    }
                }

                if (isset($_GET['class_work_results'])) {
                    $class_id = mysql_prep($_GET['class_id']);
                    $class = $top_ridge->get_class_name($class_id);
                    if (isset($_GET['err']) && $_GET['err']=='yes'){
                        echo $top_ridge->print_message(0, "Warning. $class Project Work Results contains invalid values ..please check and try again");

                    }else{
                        echo $top_ridge->print_message(1, "Congrats.  $class  Project Work Results were entered successfully.");

                    }
                }

                if (isset($_GET['class_test_results'])) {
                    $class_id = mysql_prep($_GET['class_id']);
                    $class = $top_ridge->get_class_name($class_id);
                    if (isset($_GET['err']) && $_GET['err']=='yes'){
                        echo $top_ridge->print_message(0, "Warning. $class CAT Results contains invalid values ..please check and try again");

                    }else{
                        echo $top_ridge->print_message(1, "Congrats. $class CAT Results were entered successfully.");
                    }
                }


                if (isset($_GET['exams_results'])) {
                    $class_id = mysql_prep($_GET['class_id']);
                    $class = $top_ridge->get_class_name($class_id);
                    if (isset($_GET['err']) && $_GET['err']=='yes'){
                        echo $top_ridge->print_message(0, "Warning. $class Examinations Results contains invalid values ..please check and try again");

                    }else{
                        echo $top_ridge->print_message(1, "Congrats. $class Examinations Results were entered successfully.");

                    }


                }

                
                
                
                

                if (isset($_GET['mock_results'])) {
                    $class_id = mysql_prep($_GET['class_id']);
                    $class = $top_ridge->get_class_name($class_id);
                    if (isset($_GET['err']) && $_GET['err']=='yes'){
                        echo $top_ridge->print_message(0, "Warning. $class Aptitude Results contains invalid values ..please check and try again");

                    }else{
                        echo $top_ridge->print_message(1, "Congrats. $class  Aptitude test Results were entered successfully.");

                    }
                }

                ?>
                <div class="col-md-2"></div>
                <?php
                //if($top_ridge->staff_job_type == 1  ||  $top_ridge->jhs_3_teacher() == true) {
                $term_id = $top_ridge->current_term_id;
                //CURRENT_TERM MOCKS
                $CURRENT_TERM_MOCKS = mysqli_query($top_ridge_db_connection, "SELECT mock_id, mock_number, class_id FROM std_mock_settings 
				WHERE  term_id =  '$term_id' ORDER BY mock_id DESC");
                confirm_query($CURRENT_TERM_MOCKS);

                //if(mysqli_num_rows($CURRENT_TERM_MOCKS) > 0){
                ?>
                <!--		<li -->
                <?php // if(isset($_GET['mock']) ||  isset($_GET['mock_results'])) { ?><!--  class="selected" -->
                <?php // } ?><!-->
                <!--			<a href="#tab5"><em>-->
                <!--		APTITUDE TEST-->
                <!--		-->
                <!--		</em></a></li>-->
                <?PHP //}

                //}

                ?>
                <div class="white-box col-md-8">
                     <section>
                                        <div class="sttabs tabs-style-shape">
                                            <nav>
                                                <ul class="nav nav-tabs" id="myTab">
                                                    <li><a href="#section-project_wrk"><span>Project Work</span></a></li>
                                                    <li ><a data-toggle="tab" href="#section-group_wrk"><span>Group Work</span></a></li>
                                                    <li><a data-toggle="tab" href="#section-test"><span>Class Test I & II</span></a></li>
                                                    <li><a data-toggle="tab" href="#section-exams"><span>Examination</span></a></li>

                                                    <?php
                                                    if($top_ridge->staff_job_type == 1 ||  $top_ridge->jhs_3_teacher() == true) {
                                                    if (mysqli_num_rows($CURRENT_TERM_MOCKS) > 0){
                                                    ?>
                                                    <li><a href="#section-mock"><span>Mock</span></a></li>
                                                    <?php }} ?>

                                                </ul>
                                            </nav>



                                            <div class="content-wrap text-center">
                                                <section id="section-project_wrk">

                                                    <div id="academics">
                                                        <form id="form1" name="form1" method="post" action="academics.php">
                                                            <?php if (isset($top_ridge->CURRENT_TERM)){ ?>
                                                            <div class="row form-group">
                                                                    <strong>Select Class:</strong>
	  <select name="class_id" class="form-control" id="class_id" onchange="loadpage(this.value);">
          <option>---Select---</option>
          <?php echo $top_ridge->academic_class(); ?>
      </select>
                         </div>

         <div class="row form-group">
             <strong>ACADEMIC YEAR:</strong>
             <select name="term_id" class="form-control" id="term_id">
                 <option
                     value="<?php echo $top_ridge->current_term_id; ?>"><?php echo $top_ridge->CURRENT_TERM; ?></option>
                 <?php if ($top_ridge->staff_job_type == 1) { ?>
                     <option
                         value="<?php echo $top_ridge->immedaite_past_term_id; ?>"><?php echo $top_ridge->immedaite_past_academic_term; ?></option>
                 <?php } ?>

    </select>
                                                    </div>
                                                                        <div id="txtHint"></div>

                                                            <button name="home_work" type="submit" class="btn btn-lg btn-success ui-corner-all"
                                                                               id="submit" >Submit</button>
                                                        </form>

                                                        <?php } ?>
                                                    </div>
                                                    <?php include("../includes/home_work_page.php"); ?>


                                                </section>
                                                <section id="section-group_wrk">
                                                    <div id="academics">


                                                        <form id="form1" name="form1" method="post" action="academics.php?classwork=1">
                                                            <?php if (isset($top_ridge->CURRENT_TERM)){ ?>
                                                            <div class="row form-group">
                                                                <strong>Select Class:</strong>

                                                                <select name="class_id" class="form-control" id="class_id" onchange="load_class_work(this.value);">
                                                                    <option>---Select---</option>
                                                                    <?php $top_ridge->academic_class(); ?>
                                                                </select>
</div>


                                                            <div class="row form-group">


                                                            <strong>ACADEMIC YEAR:</strong>
                                                            <select name="term_id" class="form-control" id="term_id">
                                                                            <option
                                                                                value="<?php echo $top_ridge->current_term_id; ?>"><?php echo $top_ridge->CURRENT_TERM; ?></option>
                                                                            <?php if ($top_ridge->staff_job_type == 1) { ?>
                                                                                <option
                                                                                    value="<?php echo $top_ridge->immedaite_past_term_id; ?>"><?php echo $top_ridge->immedaite_past_academic_term; ?></option>
                                                                            <?php } ?>
                                                                        </select>

</div>


                                                                        <div id="class_work"></div>




                                                            <button name="class_work" type="submit" class="btn btn-success btn-lg ui-corner-all"
                                                                               id="submit" >Submit</button>
                                                        </form>

                                                        <?php } ?>
                                                    </div>
                                                    <?php include("../includes/class_work_page.php"); ?>


                                                </section>
                                                <section id="section-test">
                                                    <div id="academics">

                                                        <form id="form1" name="form1" method="post" action="academics.php?classtest=1">
                                                            <?php if (isset($top_ridge->CURRENT_TERM)){ ?>
                                                            <div class="row form-group"><strong>Select Class:</strong>
	  <select name="class_id" class="form-control" id="class_id" onchange="load_class_test(this.value);">
          <option>---Select---</option>
          <?php $top_ridge->academic_class(); ?>
      </select>
                                                                </div>

                                                            <div class="row form-group">

                                                                <strong>ACADEMIC YEAR:</strong>
                            <select name="term_id" class="form-control" id="term_id">
                                <option
                                    value="<?php echo $top_ridge->current_term_id; ?>"><?php echo $top_ridge->CURRENT_TERM; ?></option>

                                <?php if ($top_ridge->staff_job_type == 1) { ?>
                                    <option
                                        value="<?php echo $top_ridge->immedaite_past_term_id; ?>"><?php echo $top_ridge->immedaite_past_academic_term; ?></option>
                                <?php } ?>
                            </select>
                   </div>



                                                                        <div id="class_test"></div>



                                                                    <button name="class_test" type="submit" class="btn btn-lg btn-success ui-corner-all"
                                                                               id="submit" >Submit</button>
                                                        </form>

                                                        <?php } ?>
                                                    </div>

                                                    <?php include("../includes/class_test_page.php"); ?>

                                                </section>



                                                <section id="section-exams">
                                                    <div id="academics">


                                                        <form id="form1" name="form1" method="post" action="academics.php?exams=1">
                                                            <?php if (isset($top_ridge->CURRENT_TERM)){ ?>
                                                            <div class="row form-group">

                                                            <strong>Select Class:</strong>
	  <select name="class_id" class="form-control" id="class_id" onchange="load_exams(this.value);" required>
          <option>---Select---</option>
          <?php $top_ridge->all_academic_class(); ?>
      </select>
                                  </div>

                                                            <div class="row form-group">

                                                            <strong>ACADEMIC YEAR:</strong>
                                                                        <select name="term_id" class="form-control" id="term_id">
                                                                            <option
                                                                                value="<?php echo $top_ridge->current_term_id; ?>"><?php echo $top_ridge->CURRENT_TERM; ?></option>

                                                                            <?php if ($top_ridge->staff_job_type == 1) { ?>
                                                                                <option
                                                                                    value="<?php echo $top_ridge->immedaite_past_term_id; ?>"><?php echo $top_ridge->immedaite_past_academic_term; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                               </div>


                                                                        <div id="exams"></div>


                                                                    <button name="exams" type="submit" class="btn btn-lg btn-success ui-corner-all"
                                                                               id="submit" >Submit</button>

                                                        </form>

                                                        <?php } ?>
                                                    </div>
                                                    <?php include("../includes/exams_page.php"); ?>
                                                </section>


                                                    <?php
                                                    if($top_ridge->staff_job_type == 1 ||  $top_ridge->jhs_3_teacher() == true) {
                                                        if (mysqli_num_rows($CURRENT_TERM_MOCKS) > 0){
                                                            ?>
                                                            <section id="section-mock">

                                                                <form id="form1" name="form1" method="post" action="academics.php?mock=1">
                                                                    <?php if (isset($top_ridge->CURRENT_TERM)){ ?>

                                                                    <div class="row form-group">

                                                                    <strong>Select Class:</strong>
                                                                        <select name="class_id" class="textfield" id="class_id"  onchange="load_mocks(this.value);">
                                                                                    <option>---Select---</option>
                                                                                    <?php $top_ridge->all_academic_class();?>
                                                                                </select>
</div>
                                                                    <div id="aptitude"></div>


                                                                    <div class="row form-group">
                                                                        <strong>ACADEMIC YEAR:</strong>
                                                                        <select name="term_id" class="form-control" id="term_id"   onchange="LOAD_MOCK_NUMBER(this.value);">
                                                                                    <option value="<?php  echo $top_ridge->current_term_id; ?>">
                                                                                        <?php echo $top_ridge->CURRENT_TERM; ?></option>

                                                                                    <?php if($top_ridge->staff_job_type == 1) {?>
                                                                                        <option value="<?php  echo $top_ridge->immedaite_past_term_id; ?>">
                                                                                            <?php echo $top_ridge->immedaite_past_academic_term; ?></option>
                                                                                    <?php } ?>
                                                                                </select></div>

                                                                            <button name="submit_mock" type="submit" class="btn btn-lg btn-success ui-corner-all" id="submit" >Submit</button>


                                                                </form>

                                                                <?php  } ?>

                                                                <p><?php include("../includes/mock_page.php"); ?></p>

                                                            </section>
                                                            <?php

                                                        }
                                                    } ?>


<script>
    if (location.hash) {
        $('a[href=\'' + location.hash + '\']').tab('show');
    }
    var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        $('a[href="' + activeTab + '"]').tab('show');
    }

    $('body').on('click', 'a[data-toggle=\'tab\']', function (e) {
        e.preventDefault()
        var tab_name = this.getAttribute('href')
        if (history.pushState) {
            history.pushState(null, null, tab_name)
        }
        else {
            location.hash = tab_name
        }
        localStorage.setItem('activeTab', tab_name)

        $(this).tab('show');
        return false;
    });
    $(window).on('popstate', function () {
        var anchor = location.hash ||
            $('a[data-toggle=\'tab\']').first().attr('href');
        $('a[href=\'' + anchor + '\']').tab('show');
    });
</script>

                        </div>
            </div>
            <!-- /.row -->
            <!-- /.row -->

        </div></div></div>
        <!-- /.container-fluid -->
        <footer class="footer text-center"> 2017 &copy; Elite Admin brought to you by themedesigner.in </footer>

    <!-- /#page-wrapper -->

<script src="../js/cbpFWTabs.js"></script>

<script type="text/javascript">
    (function() {

        [].slice.call(document.querySelectorAll('.sttabs')).forEach(function(el) {
            new CBPFWTabs(el);
        });

    })();
</script>

<?php include_once '../includes/footer.php'?>

<script src="../../plugins/bower_components/datatables/jquery.dataTables.min.js"></script>
<!-- start - This is for export functionality only -->
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<!-- end - This is for export functionality only -->
<script>
    $(document).ready(function() {
        $('.myTable').DataTable();
        $(document).ready(function() {
            var table = $('#example').DataTable({
                "columnDefs": [{
                    "visible": false,
                    "targets": 2
                }],
                "order": [
                    [2, 'asc']
                ],
                "displayLength": 25,
                "drawCallback": function(settings) {
                    var api = this.api();
                    var rows = api.rows({
                        page: 'current'
                    }).nodes();
                    var last = null;

                    api.column(2, {
                        page: 'current'
                    }).data().each(function(group, i) {
                        if (last !== group) {
                            $(rows).eq(i).before(
                                '<tr class="group"><td colspan="5">' + group + '</td></tr>'
                            );

                            last = group;
                        }
                    });
                }
            });


        });
    });
</script>

</body>

</html>
