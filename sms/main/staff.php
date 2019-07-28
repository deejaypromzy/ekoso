<?PHP include("../includes/main_class.php");?>
<?php include_once '../includes/header.php'?>
<?php
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }

        $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

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

// mysql_select_db($database_top_ridge_db_connection, $top_ridge_db_connection);$
$query_rs_staff = "SELECT staff.id, concat(first_name,' ',last_name) AS staff_name FROM staff  WHERE staff.staff_status = 1 ORDER BY id desc";
$rs_staff = mysqli_query($top_ridge_db_connection,$query_rs_staff) or die(mysqli_error($top_ridge_db_connection));
$row_rs_staff = mysqli_fetch_assoc($rs_staff);
$totalRows_rs_staff = mysqli_num_rows($rs_staff);

// mysql_select_db($database_top_ridge_db_connection, $top_ridge_db_connection);
$query_rs_subjects = "SELECT subjects.subject_id, subjects.subject_name FROM subjects  WHERE subjects.subject_status  = 1 ORDER BY subject_name ASC ";
$rs_subjects = mysqli_query($top_ridge_db_connection,$query_rs_subjects) or die(mysqli_error($top_ridge_db_connection));
$row_rs_subjects = mysqli_fetch_assoc($rs_subjects);
$totalRows_rs_subjects = mysqli_num_rows($rs_subjects);

// mysql_select_db($database_top_ridge_db_connection, $top_ridge_db_connection);
$query_rs_classes = "SELECT classes.class_id, classes.class_name FROM classes WHERE classes.class_status = 1 ORDER BY class_id ASC";
$rs_classes = mysqli_query($top_ridge_db_connection,$query_rs_classes) or die(mysqli_error($top_ridge_db_connection));
$row_rs_classes = mysqli_fetch_assoc($rs_classes);
$totalRows_rs_classes = mysqli_num_rows($rs_classes);

$maxRows_rs_staff_info = 100;
$pageNum_rs_staff_info = 0;
if (isset($_GET['pageNum_rs_staff_info'])) {
    $pageNum_rs_staff_info = $_GET['pageNum_rs_staff_info'];
}
$startRow_rs_staff_info = $pageNum_rs_staff_info * $maxRows_rs_staff_info;

// mysql_select_db($database_top_ridge_db_connection, $top_ridge_db_connection);
$query_rs_staff_info = "SELECT staff.title, staff.id, staff.staff_id, CONCAT(first_name, ' ', last_name) AS staff, staff.phone_number, staff.job_type FROM staff  WHERE staff.staff_status  = 1  ORDER BY id desc";
$query_limit_rs_staff_info = sprintf("%s LIMIT %d, %d", $query_rs_staff_info, $startRow_rs_staff_info, $maxRows_rs_staff_info);
$rs_staff_info = mysqli_query($top_ridge_db_connection,$query_limit_rs_staff_info) or die(mysqli_error($top_ridge_db_connection));
$row_rs_staff_info = mysqli_fetch_assoc($rs_staff_info);

if (isset($_GET['totalRows_rs_staff_info'])) {
    $totalRows_rs_staff_info = $_GET['totalRows_rs_staff_info'];
} else {
    $all_rs_staff_info = mysqli_query($top_ridge_db_connection,$query_rs_staff_info);
    $totalRows_rs_staff_info = mysqli_num_rows($all_rs_staff_info);
}
$totalPages_rs_staff_info = ceil($totalRows_rs_staff_info/$maxRows_rs_staff_info)-1;

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2"))
{


    $class_ids = $_POST['class_ids'];

    $staff_num = mysql_prep($_POST['staff_id']);
    $subject_id = mysql_prep($_POST['subject_id']);

    if(count($class_ids) == 0  )
    {
        header('location:staff.php?class=null');

    }else{



        for($count= 0; $count< count($class_ids); $count++)
        {
            if(!empty($class_ids[$count]))
            {
                $class_id = $class_ids[$count];

                $query1 = mysqli_query($top_ridge_db_connection,"INSERT INTO class_teachers (staff_id, class_id, subject_id) VALUES ($staff_num, $class_id, $subject_id)");
                confirm_query($query1);


            }
        }


        header('location:staff.php?allocation=1');

    }
}














if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {


    $picName= $_FILES['picture']['name'];
    $picSize= $_FILES['picture']['size'];
    $picType= $_FILES['picture']['type'];
    $picTemp= $_FILES['picture']['tmp_name'];

    $target = "pictures/".$picName;
    move_uploaded_file($picTemp,$target);

    $staff_id = "TOP". date('y'). rand(100,999);
    $insertSQL = sprintf("INSERT INTO staff (staff_id, first_name, last_name,title, job_type, appointment_date, gender, phone_number, address, email, picture) VALUES ('$staff_id',%s, %s,%s, %s, %s, %s, %s, %s, %s, '$target')",
        GetSQLValueString($_POST['first_name'], "text"),
        GetSQLValueString($_POST['last_name'], "text"),
        GetSQLValueString($_POST['title'], "text"),
        GetSQLValueString($_POST['job_type'], "int"),
        GetSQLValueString($_POST['appointment_date'], "text"),
        GetSQLValueString($_POST['gender'], "text"),
        GetSQLValueString($_POST['phone_number'], "text"),
        GetSQLValueString($_POST['address'], "text"),
        GetSQLValueString($_POST['email'], "text"));
    /// GetSQLValueString($_POST['picture'], "text")

    // mysql_select_db($database_top_ridge_db_connection, $top_ridge_db_connection);
    $Result1 = mysqli_query($top_ridge_db_connection,$insertSQL) or die(mysqli_error($top_ridge_db_connection));

    ///users table
    $id = $top_ridge->last_inserted_staff_record();
    $password  = sha1("staff");
    $logon_info = mysqli_query($top_ridge_db_connection,"INSERT INTO users (staff_id, username, password) VALUES ($id, '$staff_id', '$password') ");
    confirm_query( $logon_info);

    $_SESSION['staff_full_name'] = mysql_prep($_POST['first_name']). ' '. mysql_prep($_POST['last_name']);

    if($Result1){header('location:staff.php?reg=1');}

}
?>

<?php
if(isset($_GET['reg']) && isset($_SESSION['staff_full_name']))
{
    $message = $_SESSION['staff_full_name'].  " Was Registered Successful!";
    echo $top_ridge->print_message(1,$message);
}



if(isset($_GET['class']) == 'null')
{
    $message = "Subject allocation failed. Please select at least one class.";
    echo $top_ridge->print_message(0, $message);
}


if(isset($_GET['allocation']) == 1)
{
    $message = "Subject was successfully allocated.";
    echo $top_ridge->print_message(1, $message);
}

if(isset($_GET['deleting']) && isset($_SESSION['deleted_staff_name']))
{
    $message=  $_SESSION['deleted_staff_name'].  " was deleted successfully.";
    $top_ridge->print_message(1, $message);
}


if(isset($_GET['delete_staff']))
{
    $staff_id = mysql_prep($_GET['delete_staff']);
    $staff_name = mysql_prep($_GET['staff_name']);
    $_SESSION['deleted_staff_name'] = $staff_name;

    /////delete teaching subjects
    $del_teaching_subjects = mysqli_query($top_ridge_db_connection,"DELETE FROM class_teachers WHERE staff_id  = {$staff_id}");
    confirm_query($del_teaching_subjects);




    $user_id = mysqli_query($top_ridge_db_connection,"SELECT user_id FROM users WHERE staff_id = {$staff_id}");
    confirm_query($user_id);
    $b= mysqli_fetch_assoc($user_id);
    $user_table_id = $b['user_id'];

    /////delete from login_info table
    $del_user_id = mysqli_query($top_ridge_db_connection,"DELETE FROM login_info WHERE user_id  = {$user_table_id}");
    confirm_query($del_user_id);


    /////delete from users table
    $del_user = mysqli_query($top_ridge_db_connection,"DELETE FROM users WHERE staff_id  = {$staff_id}");
    confirm_query($del_user);



    $top_ridge->update_record_status($table_name ='staff' ,$status_name =  'staff_status', $primary_key_field = 'id', $primary_key_value  = $staff_id, $record_name = $staff_name , $title='staff');
    header("location:staff.php?deleting=true");

}

?>

<script type="text/javascript">
    function showUser(str)
    {
        if (str=="")
        {
            document.getElementById("txtHint").innerHTML="";
            return;
        }
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                document.getElementById("tint").innerHTML=xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","load_page.php?subject_id="+str,true);
        xmlhttp.send();
    }






</script>
<div id="page-wrapper">
        <div class="container-fluid">
            <div class="row bg-title">
                <div class="col-m-3">
                    <ol class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Staff</a></li>
                    </ol>                </div>
                <!-- /.col-lg-12 -->
            </div>
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

                <div class="col-md-2"></div>
                    <section class="col-md-8">
                        <div class="sttabs tabs-style-shape">
                            <nav>
                                <ul>
                                    <li><a href="#staff"><span>Staff Details</span></a></li>
                                    <li><a href="#class_allocation"><span>Class Allocation</span></a></li>
                                </ul>
                            </nav>

                            <div class="content-wrap">
                                <section id="staff">
                                        <div class="white-box">
                                            <h3 class="box-title m-b-0 text-right">
                                                <button style="margin-bottom: 20px" onclick="print_info3('staff_reg.php');" class="btn btn-success">Add New Staff</button>
                                                </h3>
                                            <div class="table-responsive">
                                                        <table id="myTable" class="table table-striped">
                                                            <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>STAFF ID</th>
                                                        <th>STAFF NAME</th>
                                                        <th>PHONE NUMBER</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <?php $a=  ($startRow_rs_staff_info + 1) ?>
<tbody>

<?php do { ?>
    <tr>
        <td class="rightAlign"><?php echo $a; ?>.</td>
        <td><?php echo $row_rs_staff_info['staff_id']; ?></td>
        <td class='capitalize'><?php echo $row_rs_staff_info['title'];  ?>. <?php echo strtoupper($row_rs_staff_info['staff']); ?></td>
        <td><?php echo $row_rs_staff_info['phone_number']; ?></td>
        <td ><a href="" class="print_info" title="View <?php echo $row_rs_staff_info['staff']; ?> teaching subjects " onclick="print_info3('teaching_subjects.php?staff_id=<?php echo $row_rs_staff_info['id']; ?>');"><img src="images/prew_active.png" width="19" height="19" alt="img" /> subjects</a>
      <a href="" class="print_info" title="View <?php echo $row_rs_staff_info['staff']; ?> teaching subjects " onclick="print_info3('teaching_subjects.php?staff_id=<?php echo $row_rs_staff_info['id']; ?>');"><img src="images/prew_active.png" width="19" height="19" alt="img" /> subjects</a></td>



    </tr>
    <?php $a++; }
while ($row_rs_staff_info = mysqli_fetch_assoc($rs_staff_info)); ?>

</tbody>
                                                </table>
                                            </div>
                                        </div>
                                </section>
                                <section id="class_allocation">
                                        <div class="white-box">
                                            <h3 class="box-title m-b-0 text-right">
                                                <button style="margin-bottom: 20px" onclick="print_info3('staff_reg.php');" class="btn btn-success">Add New Staff</button>
                                                </h3>
                                            <form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2">
                                                  <p>  Staff Name:
                                                    <select name="staff_id" class="form-control" required>
                                                        <option value="" selected="selected">Select</option>
                                                        <?php
                                                        do {
                                                            ?>
                                                            <option value="<?php echo $row_rs_staff['id']?>"><?php echo $row_rs_staff['staff_name']?></option>
                                                            <?php
                                                        } while ($row_rs_staff = mysqli_fetch_assoc($rs_staff));
                                                        $rows = mysqli_num_rows($rs_staff);
                                                        if($rows > 0) {
                                                            // mysql_data_seek($rs_staff, 0);
                                                            $row_rs_staff = mysqli_fetch_assoc($rs_staff);
                                                        }
                                                        ?>
                                                    </select>

                                                  </p>

<p>
                                                Select Subject:
                    <select name="subject_id" class="form-control" onchange="showUser(this.value);" required>
                        <option value="">Select</option>
                        <?php
                        do {
                            ?>
                            <option value="<?php echo $row_rs_subjects['subject_id']?>"><?php echo $row_rs_subjects['subject_name']?></option>
                            <?php
                        } while ($row_rs_subjects = mysqli_fetch_assoc($rs_subjects));
                        $rows = mysqli_num_rows($rs_subjects);
                        if($rows > 0) {
                            // mysql_data_seek($rs_subjects, 0);
                            $row_rs_subjects = mysqli_fetch_assoc($rs_subjects);
                        }
                        ?>
                    </select>
</p>


                                                    <div class="text-center"   id="tint">


                                                    </div>

<div class="box-title m-b-0 text-center">
    <input type="submit" class="btn btn-success btn-lg text-center" value="Save Details""/>


</div>

                                                <input type="hidden" name="MM_insert" value="form2" />
                                            </form>




                                        </div>
                                </section>
</div>

                            </div>
                            <!-- /content -->
                        </div>
                        <!-- /tabs -->






            </div>
            <!-- /.row -->
            <!-- /.row -->

        </div>
        <!-- /.container-fluid -->
        <footer class="footer text-center"> 2017 &copy; Elite Admin brought to you by themedesigner.in </footer>
    </div>
    <!-- /#page-wrapper -->
</div>

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
        $('#myTable').DataTable();
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

            // Order by the grouping
            $('#example tbody').on('click', 'tr.group', function() {
                var currentOrder = table.order()[0];
                if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
                    table.order([2, 'desc']).draw();
                } else {
                    table.order([2, 'asc']).draw();
                }
            });
        });
    });
    $('#example23').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
</script>

</body>

</html>
