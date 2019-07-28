<?PHP include("../includes/main_class.php");?>
<?php include_once '../includes/header.php'?>

<?php
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }

        //$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

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

$maxRows_rs_parents = 20;
$pageNum_rs_parents = 0;
if (isset($_GET['pageNum_rs_parents'])) {
    $pageNum_rs_parents = $_GET['pageNum_rs_parents'];
}
$startRow_rs_parents = $pageNum_rs_parents * $maxRows_rs_parents;

// mysql_select_db($database_top_ridge_db_connection, $top_ridge_db_connection);
if(isset($_SESSION['PARENT_ID']))
{
    $p_id = $_SESSION['PARENT_ID'];
    $query_rs_parents = "SELECT parent_number, parents.parent_id, concat( parents.title, '.  ',first_name, ' ',last_name) AS parent_name, parents.phone_number  FROM parents WHERE parents.parent_status = 1 and parents.parent_id='$p_id'   ";
}else{


    $query_rs_parents = "SELECT parent_number, parents.parent_id, concat( parents.title, '.  ',first_name, ' ',last_name) AS parent_name, parents.phone_number  FROM parents WHERE parents.parent_status = 1 ORDER BY parent_id  desc";
}



$query_limit_rs_parents = sprintf("%s LIMIT %d, %d", $query_rs_parents, $startRow_rs_parents, $maxRows_rs_parents);
$rs_parents = mysqli_query($top_ridge_db_connection,$query_limit_rs_parents) or die(mysqli_error($top_ridge_db_connection));
//$row_rs_parents = mysqli_fetch_assoc($rs_parents);

if (isset($_GET['totalRows_rs_parents'])) {
    $totalRows_rs_parents = $_GET['totalRows_rs_parents'];
} else {
    $all_rs_parents = mysqli_query($top_ridge_db_connection,$query_rs_parents);
    $totalRows_rs_parents = mysqli_num_rows($all_rs_parents);
}
$totalPages_rs_parents = ceil($totalRows_rs_parents/$maxRows_rs_parents)-1;

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




<?php
if(isset($_GET['parent_id']))
{
    $parent_id = mysql_prep($_GET['parent_id']);
    $query = mysqli_query($top_ridge_db_connection,"UPDATE parents SET parent_status = 0 WHERE parent_id = {$parent_id}");
    confirm_query($query);
    if($query)
    {
        header("location:parents.php?id=$parent_id");
    }

}
if(isset($_GET['id']))
{
    $parent_id = mysql_prep($_GET['id']);
    $message =  $top_ridge->parent_name($parent_id) . ' was deleted successfully' ;
    $top_ridge->print_message(1,$message );
}

?>


<div id="page-wrapper">
        <div class="container-fluid">
            <div class="row bg-title">
                                <div class="col-m-12">
                    <ol class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Parents</a></li>
                    </ol>

                    </div>

                <div class="col-md-12">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <?php

                        if(!isset($_SESSION['PARENT_ID']))
                        {  ?>
                            <a onclick="print_info('parent_reg.php?page=1');" title="Click to Add New Parent"  class="btn btn-primary  m-b-20 m-r-20" style="float:left"> Add New Parent</b></a>

                            <?php
                        }

                        ?>
                        <a class="btn btn-default btn-outline m-b-20" id="unblockbtn4">Unblock Panel</a>


                        <div class="panel panel-info block4">
                            <div class="panel-heading">

                                <?php

                                    if(!isset($_SESSION['PARENT_ID']))
                                    {
                                        $top_ridge->table_count('Parents', 'parents', 'parent_status');

                                    }

                                    ?>

                                <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"><i class="ti-close"></i></a> </div>
                            </div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped myTable  table-hover">
                                            <thead>
                                            <tr>

                                                <th >No.</th>
                                                <th >ID</th>
                                                <th >PARENT / GUARDIAN NAME</th>
                                                <th >PHONE NUMBER</th>
                                                <th >Actions</th>

                                            </tr>
                                            </thead>
                                            <?php $count =  ($startRow_rs_parents + 1) ?>
                                            <tbody>
                                            <?php while ($row_rs_parents = mysqli_fetch_assoc($rs_parents))  { ?>
                                                <tr>
                                                    <td ><?php echo  $count; ?>.</td>
                                                    <td ><?php echo  $row_rs_parents['parent_number'];?></td>
                                                    <td ><?php echo $row_rs_parents['parent_name']; ?></td>
                                                    <td> <?php echo $row_rs_parents['phone_number']; ?> </td>
                                                    <td > <a onclick="print_info('parent_details.php?parent_id=<?php echo $row_rs_parents['parent_id'];?>');"><img src="images/fish_bloger.png" width="12" height="16" alt="img" /> Details</a>

                                                   ||   <a onclick="print_info('parent_kids.php?parent_id=<?php echo $row_rs_parents['parent_id'];?>');">  <img src="images/user_edit.png" width="16" height="16" alt="ig" />  Children</a>
                                                    <?php
                                                    if(!isset($_SESSION['PARENT_ID']))
                                                    {
                                                        ?>
                                                       ||   <a onclick="print_info3('parent_edit.php?parent_id=<?php echo $row_rs_parents['parent_id'];?>');"> <img src="images/pencil.gif" width="16" height="16" alt="img" />  Edit</a>

                                                        ||  <a onclick="return confirm('Are you sure you want to delete <?php echo $row_rs_parents['parent_name']; ?>?');"
                                                               href="parents.php?parent_id=<?php echo $row_rs_parents['parent_id'];?>">
                                                        <img src="images/error.png" width="16" height="16" alt="img" />  Delete</a>  <?php } ?></td>
                                                </tr>
                                                <?php $count++; } ?>

</tbody>

</table>




                                    </div>
                            </div>
                        </div>
                        <hr> </div>

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
