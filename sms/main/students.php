<?PHP include("../includes/main_class.php");?>
<?php include_once '../includes/header.php'?>
<?php
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }

        $theValue = function_exists("mysql_real_escape_string") ? mysql_prep($theValue) : mysql_prep($theValue);

        switch ($theType) {
            case "text":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "long":
            case "int":
                $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                break;
            case "double":
                $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
                break;
            case "date":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "defined":
                $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                break;
        }
        return $theValue;
    }
}

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

    $picName= $_FILES['picture']['name'];
    $picSize= $_FILES['picture']['size'];
    $picType= $_FILES['picture']['type'];
    $picTemp= $_FILES['picture']['tmp_name'];

    $target = "pictures/".$picName;
    move_uploaded_file($picTemp,$target);
    $student_num = "TOP/". date('y'). '/'.rand(1000,9999);



    $insertSQL = sprintf("INSERT INTO students (student_id,first_name, last_name, gender, date_of_birth, class_id, last_school_attended, parent_id, picture) VALUES ('$student_num', %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['first_name'], "text"),
        GetSQLValueString($_POST['last_name'], "text"),
        GetSQLValueString($_POST['gender'], "text"),
        GetSQLValueString($_POST['date_of_birth'], "text"),
        GetSQLValueString($_POST['class_id'], "int"),
        GetSQLValueString($_POST['last_school_attended'], "text"),
        GetSQLValueString($_POST['parent_id'], "int"),
        GetSQLValueString($target, "text"));





    // mysql_select_db($database_top_ridge_db_connection, $top_ridge_db_connection);  $Result1 = mysqli_query($top_ridge_db_connection,$insertSQL, $top_ridge_db_connection) or die(mysqli_error($top_ridge_db_connection));

    $_SESSION['student_name'] = $_POST['first_name'].' '. $_POST['last_name'];

    if(isset($top_ridge->current_term_id))
    {
       /// /INSERT STUDENT DETAILS INTO Student_term_total table
        $top_ridge->current_term_registration($_POST['class_id']);
        header("location:student.php?student_reg=1");



    }else{

        if($Result1){header("location:student.php?reg=1");}
    }


    // if($Result1){header("location:student.php?reg=1");}
}

$maxRows_rs_parents = 10;
$pageNum_rs_parents = 0;
if (isset($_GET['pageNum_rs_parents'])) {
    $pageNum_rs_parents = $_GET['pageNum_rs_parents'];
}
$startRow_rs_parents = $pageNum_rs_parents * $maxRows_rs_parents;

// mysql_select_db($database_top_ridge_db_connection, $top_ridge_db_connection);
$query_rs_parents = "SELECT parents.parent_id, concat(first_name, ' ',last_name) AS parent_name, parents.phone_number, parents.title FROM parents WHERE parents.parent_status = 1 ORDER BY first_name  ASC";
$query_limit_rs_parents = sprintf("%s LIMIT %d, %d", $query_rs_parents, $startRow_rs_parents, $maxRows_rs_parents);
$rs_parents = mysqli_query($top_ridge_db_connection,$query_limit_rs_parents) or die(mysqli_error($top_ridge_db_connection));
$row_rs_parents = mysqli_fetch_assoc($rs_parents);

if (isset($_GET['totalRows_rs_parents'])) {
    $totalRows_rs_parents = $_GET['totalRows_rs_parents'];
} else {
    $all_rs_parents = mysqli_query($top_ridge_db_connection,$query_rs_parents);
    $totalRows_rs_parents = mysqli_num_rows($all_rs_parents);
}
$totalPages_rs_parents = ceil($totalRows_rs_parents/$maxRows_rs_parents)-1;

// mysql_select_db($database_top_ridge_db_connection, $top_ridge_db_connection);
$query_rs_parent_names = "SELECT parents.parent_id, CONCAT(parents.title ,'.',parents.first_name,' ',parents.last_name) AS parent_name FROM parents  WHERE parents.parent_status = 1 order by first_name asc";
$rs_parent_names = mysqli_query($top_ridge_db_connection,$query_rs_parent_names) or die(mysqli_error($top_ridge_db_connection));
$row_rs_parent_names = mysqli_fetch_assoc($rs_parent_names);
$totalRows_rs_parent_names = mysqli_num_rows($rs_parent_names);

// mysql_select_db($database_top_ridge_db_connection, $top_ridge_db_connection);$
$query_rs_all_classes = "SELECT classes.class_id, classes.class_name FROM classes WHERE classes.class_status = 1 order by class_id ASC";
$rs_all_classes = mysqli_query($top_ridge_db_connection,$query_rs_all_classes) or die(mysqli_error($top_ridge_db_connection));
$row_rs_all_classes = mysqli_fetch_assoc($rs_all_classes);
$totalRows_rs_all_classes = mysqli_num_rows($rs_all_classes);

$maxRows_rs_old_students = 10;
$pageNum_rs_old_students = 0;
if (isset($_GET['pageNum_rs_old_students'])) {
    $pageNum_rs_old_students = $_GET['pageNum_rs_old_students'];
}
$startRow_rs_old_students = $pageNum_rs_old_students * $maxRows_rs_old_students;

// mysql_select_db($database_top_ridge_db_connection, $top_ridge_db_connection);
$query_rs_old_students = "SELECT students.id, concat( students.first_name,' ', students.last_name) as student, students.gender FROM students WHERE students.student_status = 2 ORDER BY first_name asc";
$query_limit_rs_old_students = sprintf("%s LIMIT %d, %d", $query_rs_old_students, $startRow_rs_old_students, $maxRows_rs_old_students);
$rs_old_students = mysqli_query($top_ridge_db_connection,$query_limit_rs_old_students) or die(mysqli_error($top_ridge_db_connection));
$row_rs_old_students = mysqli_fetch_assoc($rs_old_students);

if (isset($_GET['totalRows_rs_old_students'])) {
    $totalRows_rs_old_students = $_GET['totalRows_rs_old_students'];
} else {
    $all_rs_old_students = mysqli_query($top_ridge_db_connection,$query_rs_old_students);
    $totalRows_rs_old_students = mysqli_num_rows($all_rs_old_students);
}
$totalPages_rs_old_students = ceil($totalRows_rs_old_students/$maxRows_rs_old_students)-1;

$queryString_rs_parents = "";
if (!empty($_SERVER['QUERY_STRING'])) {
    $params = explode("&", $_SERVER['QUERY_STRING']);
    $newParams = array();
    foreach ($params as $param) {
        if (stristr($param, "pageNum_rs_parents") == false &&
            stristr($param, "totalRows_rs_parents") == false) {
            array_push($newParams, $param);
        }
    }
    if (count($newParams) != 0) {
        $queryString_rs_parents = "&" . htmlentities(implode("&", $newParams));
    }
}
$queryString_rs_parents = sprintf("&totalRows_rs_parents=%d%s", $totalRows_rs_parents, $queryString_rs_parents);
?>

<div id="page-wrapper">
        <div class="container-fluid">
            <div class="row bg-title">
                <div class="col-m-3">
                    <ol class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Staff</a></li>
                    </ol>


                    <?php
                    if(isset($_GET['reg']) && isset($_SESSION['student_name']))
                    {
                        $message = $_SESSION['student_name'].  " Was Registered Successful.";
                        echo $top_ridge->print_message(1,$message);
                    }else if(isset($_GET['student_reg']) && isset($_SESSION['student_name'])){
                        $message = $_SESSION['student_name'].  " Was Registered Successful In  ".
                            $top_ridge->CURRENT_TERM. ' Academic Year';
                        echo $top_ridge->print_message(1,$message);
                    }




                    if(isset($_GET['delete_student']))
                    {
                        $student_name  = mysql_prep($_GET['student_name']);

                        $student_id  = mysql_prep($_GET['student_id']);

                        if(isset($top_ridge->current_term_id))

                        {
                            $term_id = $top_ridge->current_term_id;

                            //continous_assessment
                            $continous_assessment  = mysqli_query($top_ridge_db_connection,"DELETE FROM continous_assessment WHERE student_id = '$student_id' AND term_id = '$term_id' ");
                            confirm_query($continous_assessment);



                           // lower_primary_assessment
                            /* $lower_primary_assessment  = mysqli_query($top_ridge_db_connection,"DELETE FROM lower_primary_assessment WHERE student_id = '$student_id' AND term_id = '$term_id' ");
                            confirm_query($lower_primary_assessment); */


                           // student_remarks
                            $student_remarks  = mysqli_query($top_ridge_db_connection,"DELETE FROM student_remarks WHERE 	student_id = '$student_id' AND term_id = '$term_id' ");
                            confirm_query($student_remarks);

                            //student_term_total
                            $student_term_total  = mysqli_query($top_ridge_db_connection,"DELETE FROM student_term_total WHERE 	student_id = '$student_id' AND term_id = '$term_id' ");
                            confirm_query($student_term_total);


                        }






                        $query = mysqli_query($top_ridge_db_connection,"UPDATE students SET student_status = 0 WHERE id = '$student_id'  ");
                        confirm_query($query);








                        if($query)
                        {
                            header("location:student.php?student_name=$student_name&&status=0");
                        }
                    }

                    if(isset($_GET['status']))
                    {
                        $student_name  = mysql_prep($_GET['student_name']);
                        $message = $student_name. ' was deleted successfully.';
                        echo $top_ridge->print_message(1,$message);
                    }
                    ?>

                </div>
                <!-- /.col-lg-12 -->
            </div>


            <?php
            $query = mysqli_query($top_ridge_db_connection,"SELECT class_name, class_id FROM classes LIMIT 1");
            confirm_query($query);
            $a = mysqli_fetch_assoc($query);
            $first_class=$a['class_name'];
            $first_class_id=$a['class_id'];
            ?>

            <!-- .row -->
            <div class="row">

<!--                <div class="col-sm-3">-->
<!--                            <div class="panel panel-info">-->
<!--                                <div class="panel-heading">Academic Calender-->
<!--                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"></a> </div>-->
<!--                                </div>-->
<!--                                <div class="panel-wrapper collapse in" aria-expanded="true">-->
<!--                                    <div class="panel-body">-->
<!--                                        <div class="table-responsive">-->
<!--                                            <table class="table">-->
<!--                                                <thead>-->
<!--                                                <tr class="cal">-->
<!--                                                    <td><label class="cap">Academic Year:</label>  --><?php //echo $top_ridge->academicYear; ?><!--</td>-->
<!--                                                </tr>-->
<!--                                                </thead>-->
<!--                                                <tbody>-->
<!---->
<!--                                                <tr class="cal">-->
<!--                                                    <td> <label class="cap">Term:</label>   --><?php //echo $top_ridge->academicTerm; ?><!--</td>-->
<!--                                                </tr>-->
<!--                                                <tr class="cal">-->
<!--                                                    <td><label class="cap">Number of Weeks: </label>--><?php //echo $top_ridge->number_of_weeks; ?><!--</td>-->
<!--                                                </tr>-->
<!--                                                <tr class="cal">-->
<!--                                                    <td><label class="cap">Start Date:</label> --><?php //echo $top_ridge->current_term_start_date; ?><!--</td>-->
<!--                                                </tr>-->
<!--                                                <tr class="cal">-->
<!--                                                    <td><label class="cap">Vacation Date: </label> --><?php //echo $top_ridge->date_of_vacation; ?><!--</td>-->
<!--                                                </tr>-->
<!--                                                <tr class="cal">-->
<!--                                                    <td><label class="cap">Resumption Date: </label>  --><?php //echo $top_ridge->date_of_resumption; ?><!--</td>-->
<!--                                                </tr>-->
<!--                                                </tbody>-->
<!--                                            </table>-->
<!---->
<!--                                                --><?php //if($top_ridge->staff_job_type == 1) {?>
<!---->
<!--                                                                <div><a href=" " onclick="print_info('calender_edit.php')" title="Edit Academic Calender" >-->
<!--                                                                    --><?php //if(isset($top_ridge->current_term_id)) { echo "<b>Edit Calender "; }?><!-- </b></a>-->
<!---->
<!--                                                                    <a style="margin-left: 100px" href=""  onclick="print_info('new_calender.php')"   title="Add Academic Calender" ><b>Add New</u></b></a>-->
<!---->
<!--                                                                </div>-->
<!---->
<!---->
<!--                                                --><?php //} ?>
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        <div class="panel panel-info">-->
<!--                            <div class="panel-heading">Academic Calender-->
<!--                                <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"></a> </div>-->
<!--                            </div>-->
<!--                            <div class="panel-wrapper collapse in" aria-expanded="true">-->
<!--                                <div class="panel-body">-->
<!--                                        <div class="table-responsive">-->
<!--                                            <table class="table">-->
<!--                                                <tbody>-->
<!--                                                <tr class="cal">-->
<!--                                                    <td align="left"  > <a href="academics.php?classwork=1" title="Add Group Work Results" class="reg">Group Work </a></td>-->
<!--                                                </tr>-->
<!---->
<!---->
<!---->
<!--                                                <tr class="cal">-->
<!--                                                    <td align="left"  > <a href="academics.php?classtest=1" title="Add Class Test Results" class="reg">CAT I &  II </a></td>-->
<!--                                                </tr>-->
<!---->
<!--                                                <tr class="cal">-->
<!--                                                    <td align="left"  > <a href="academics.php?exams=1" title="Add Exams Results" class="reg">Examinations</a></td>-->
<!--                                                </tr>-->
<!--                                                <tr class="cal">-->
<!--                                                  <td align="left"  > <a href='academics.php?mock=1' title="Add Mock Results" class="reg">Mock Exams: J.H S 3</a></td>-->
<!--                                                </tr>-->
<!---->
<!--                                                </tbody>-->
<!---->
<!--                                            </table>-->
<!---->
<!---->
<!--                                        </div>-->
<!---->
<!---->
<!---->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--</div>-->

                <div class="col-md-12">
                    <button style="margin-bottom: 20px" onclick="print_info3('student_reg.php');" class="btn btn-success">Add New Staff</button>

                    <?php  $top_ridge->table_count('Students', 'students', 'student_status'); ?>
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $first_class_id;?>">
                                        <?php echo $first_class;?>
                                        <span style="align-content:center"><?php   $top_ridge->total_class_students($first_class_id);?></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="<?php echo $first_class_id;?>" class="panel-collapse collapse in">
                                <div class="panel-body">


                                    <div class="table-responsive">
                                                                                <table class="table table-striped myTable  table-hover">
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th >No.</th>
                                                                                        <th >ID</th>
                                                                                        <th >NAME</th>
                                                                                        <th >GENDER</th>
                                                                                        <th >PARENT / GUARDIAN</th>
                                                                                        <th >Actions</th>
                                        
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                        
                                                                                    <?php
                                                                                    $class_students = mysqli_query($top_ridge_db_connection,"SELECT students.student_id,students.id, CONCAT(students.first_name,' ', students.last_name) AS student_name, students.gender, CONCAT(parents.title,'.',parents.first_name,' ',parents.last_name) AS parent FROM students,parents WHERE students.class_id={$first_class_id} AND students.student_status = 1 AND students.parent_id = parents.parent_id ORDER BY students.first_name ASC, students.gender ASC");
                                                                                    confirm_query($class_students);
                                                                                    $counter= 1;
                                                                                    if(mysqli_num_rows($class_students)){
                                        
                                                                                        while($result = mysqli_fetch_assoc($class_students))
                                                                                        {
                                                                                            $student_name = strtoupper($result['student_name']);
                                                                                            $gender = $result['gender'];
                                                                                            $parent = $result['parent'];
                                                                                            $student_id = $result['id'];
                                                                                            $id = $result['student_id'];
                                                                                            ?>
                                        
                                                                                       <tr>  <th ><?php echo $counter; ?></th>
                                                                                            <th><?php echo $id; ?></th>
                                                                                            <th><?php echo strtoupper($student_name); ?></th>
                                                                                            <th><?php echo strtoupper($gender); ?></th>
                                                                                            <th><?php echo strtoupper($parent); ?></th>
                                                                                           <th ><a href="" class="print_info" title="View <?php echo $student_name; ?> details " onclick="print_info('student_details.php?student_id=<?php echo $student_id; ?>');">details</a>
                                                                                          ||<a href="" class="print_info" title="Click to edit <?php echo $student_name; ?><!-- information " onclick="print_info('student_edit.php?student_id=-<?php echo $student_id; ?>');"><img src="images/user_edit.png" width="19" height="19" alt="img" />edit</a>
                                                                                          ||<a href="students.php?delete_student=<?php echo $student_name; ?>&&student_name=<?php echo $student_name; ?>&&student_id=<?php echo $student_id; ?>" title="Click to delete <?php echo $student_name; ?>--" class="print_info" onclick="return confirm('Are you sure you want to delete <?php echo $student_name; ?>?');"><img src="images/trash.png" width="16" height="16" alt="img" />delete</a></th>


                                               </tr>
                                                   <?php $counter++; }} ?>

                                                                                    </tbody>
                                                                                </table>
                                                                        </div>
                                
                                
                                
                                
                                </div>
                            </div>
                        </div>


                        <?php
                        $remaining_classes = mysqli_query($top_ridge_db_connection,'SELECT class_id, class_name FROM classes  WHERE class_status = 1 ORDER BY class_id ASC LIMIT 100 OFFSET 1');
                        confirm_query($remaining_classes);
                        if(mysqli_num_rows($remaining_classes) > 0 ){
                        while($row= mysqli_fetch_assoc($remaining_classes))
                        {
                        $class_id =$row['class_id'];
                        $class_name =$row['class_name'];
                        
                                                ?>
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $class_id;?>
">
                                        <?php echo $class_name;?>
                                        <?php $top_ridge->total_class_students($class_id);?>

                                    </a>
                                </h4>
                            </div>
                            <div id="<?php echo $class_id;?>" class="panel-collapse collapse">
                                
                                
                                
                                <div class="panel-body">


                                    <div class="table-responsive">
                                                                                <table class="table table-striped myTable  table-hover">
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th >No.</th>
                                                                                        <th >ID</th>
                                                                                        <th >NAME</th>
                                                                                        <th >GENDER</th>
                                                                                        <th >PARENT / GUARDIAN</th>
                                                                                        <th >Actions</th>

                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>


                                                                                    <?php
                                                                                    $class_students = mysqli_query($top_ridge_db_connection,"SELECT students.student_id,students.id, CONCAT(students.first_name,' ', students.last_name) AS student_name, students.gender, CONCAT(parents.title,'.',parents.first_name,' ',parents.last_name) AS parent FROM students,parents WHERE students.class_id={$class_id} AND students.student_status = 1 AND students.parent_id = parents.parent_id ORDER BY students.first_name ASC, students.gender ASC");
                                                                                    confirm_query($class_students);
                                                                                    $count= 1;
                                                                                    if(mysqli_num_rows($class_students)){

                                                                                        while($result = mysqli_fetch_assoc($class_students))
                                                                                        {
                                                                                            $student_name = strtoupper($result['student_name']);
                                                                                            $gender = $result['gender'];
                                                                                            $parent = $result['parent'];
                                                                                            $student_id = $result['id'];
                                                                                            $id = $result['student_id'];
                                                                                            ?>



                                                                                            <tr>  <th ><?php echo $counter; ?></th>
                                                                                                <th><?php echo $id; ?></th>
                                                                                                <th><?php echo strtoupper($student_name); ?></th>
                                                                                                <th><?php echo strtoupper($gender); ?></th>
                                                                                                <th><?php echo strtoupper($parent); ?></th>
                                                                                                <th ><a href="" class="print_info" title="View <?php echo $student_name; ?> details " onclick="print_info('student_details.php?student_id=<?php echo $student_id; ?>');">details</a>
                                                                                                    ||  <a href="" class="print_info" title="Click to edit <?php echo $student_name; ?>information " onclick="print_info('student_edit.php?student_id=<?php echo $student_id; ?>');"><img src="images/user_edit.png" width="19" height="19" alt="img" />edit</a>
                                                                                                    ||  <a href="students.php?delete_student=<?php echo $student_name; ?>&&student_name=<?php echo $student_name; ?>&&student_id=<?php echo $student_id; ?>" title="Click to delete<?php echo $student_name; ?>" class="print_info" onclick="return confirm('Are you sure you want to delete <?php echo $student_name; ?>?');"><img src="images/trash.png" width="16" height="16" alt="img" />delete</a></th>


                                                                                            </tr>
                                                                                            <?php $counter++; }} ?>

                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                
                                
                                </div>
                            </div>
                        </div>
                        <?php }} ?>
                      </div></div>                    </div>
<!--


            </div>
            <!-- /.row -->
            <!-- /.row -->

        </div></div>
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
