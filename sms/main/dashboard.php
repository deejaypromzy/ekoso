<?PHP include("../includes/main_class.php");?>
<?php include_once '../includes/header.php'?>

    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row bg-title">
                <div class="col-m-3">
                    <h4 class="page-title">Dashboard</h4>
                </div>
                <div class="col-md-9">
                    <ol class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                    </ol>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- .row -->
            <div class="row">

                <div class="col-sm-3">
                            <div class="panel panel-info">
                                <div class="panel-heading">Academic Calender
                                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"></a> </div>
                                </div>
                                <div class="panel-wrapper collapse in" aria-expanded="true">
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr class="cal">
                                                    <td><label class="cap">Academic Year:</label>  <?php echo $top_ridge->academicYear; ?></td>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                <tr class="cal">
                                                    <td> <label class="cap">Term:</label>   <?php echo $top_ridge->academicTerm; ?></td>
                                                </tr>
                                                <tr class="cal">
                                                    <td><label class="cap">Number of Weeks: </label><?php echo $top_ridge->number_of_weeks; ?></td>
                                                </tr>
                                                <tr class="cal">
                                                    <td><label class="cap">Start Date:</label> <?php echo $top_ridge->current_term_start_date; ?></td>
                                                </tr>
                                                <tr class="cal">
                                                    <td><label class="cap">Vacation Date: </label> <?php echo $top_ridge->date_of_vacation; ?></td>
                                                </tr>
                                                <tr class="cal">
                                                    <td><label class="cap">Resumption Date: </label>  <?php echo $top_ridge->date_of_resumption; ?></td>
                                                </tr>
                                                </tbody>
                                            </table>

                                                <?php if($top_ridge->staff_job_type == 1) {?>

                                                                <div><a href=" " onclick="print_info('calender_edit.php')" title="Edit Academic Calender" >
                                                                    <?php if(isset($top_ridge->current_term_id)) { echo "<b>Edit Calender "; }?> </b></a>

                                                                    <a style="margin-left: 100px" href=""  onclick="print_info('new_calender.php')"   title="Add Academic Calender" ><b>Add New</u></b></a>

                                                                </div>


                                                <?php } ?>







                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="panel panel-info">
                            <div class="panel-heading">Academic Calender
                                <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"></a> </div>
                            </div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tbody>
                                                <tr class="cal">
                                                    <td align="left"  > <a href="academics.php?classwork=1" title="Add Group Work Results" class="reg">Group Work </a></td>
                                                </tr>



                                                <tr class="cal">
                                                    <td align="left"  > <a href="academics.php?classtest=1" title="Add Class Test Results" class="reg">CAT I &  II </a></td>
                                                </tr>

                                                <tr class="cal">
                                                    <td align="left"  > <a href="academics.php?exams=1" title="Add Exams Results" class="reg">Examinations</a></td>
                                                </tr>
                                                <tr class="cal">
                                                  <td align="left"  > <a href='academics.php?mock=1' title="Add Mock Results" class="reg">Mock Exams: J.H S 3</a></td>
                                                </tr>

                                                </tbody>
                                            </table>


                                        </div>



                                    </div>
                                </div>
                            </div>
</div>

                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-lg-6 col-sm-6 col-xs-12">
                            <div class="white-box">
                                <h3 class="box-title">TOTAL STAFF</h3>
                                <ul class="list-inline two-part">
                                    <li><i class="icon-people text-info"></i></li>
                                    <li class="text-right"><span class="counter">
                                            <?php   $top_ridge->table_records('staff', 'staff_status'); ?>
</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-6 col-xs-12">
                            <div class="white-box">
                                <h3 class="box-title">TOTAL STUDENTS</h3>
                                <ul class="list-inline two-part">
                                    <li><i class="icon-folder text-purple"></i></li>
                                    <li class="text-right"><span class="counter">
                                            <?php   $top_ridge->table_records('students', 'student_status'); ?>
</span></li>
                                </ul>

                            </div>
                        </div>


                       </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="white-box">
                                <div class="row row-in">
                                    <div class="col-lg-12">
                                        <div class=" row">


                                            <div class="col-md-6 col-sm-6 col-xs-6"> <i class="ti-receipt"></i>
                                                <h5 class="text-muted vb">MALE STAFF</h5> </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <h3 class="counter text-right m-t-15 text-warning">
                                                    <?php   $top_ridge->table_records_by_gender('staff', 'staff_status','M'); ?>
                                                </h3> </div>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> <span class="sr-only">40% Complete (success)</span> </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="white-box">
                                <div class="row row-in">
                                    <div class="col-lg-12">
                                        <div class=" row">
                                            <div class="col-md-6 col-sm-6 col-xs-6"> <i class="ti-receipt"></i>
                                                <h5 class="text-muted vb">FEMALE STAFF</h5> </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <h3 class="counter text-right m-t-15 text-warning">
                                                    <?php   $top_ridge->table_records_by_gender('staff', 'staff_status','F'); ?>
                                                </h3> </div>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> <span class="sr-only">40% Complete (success)</span> </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="white-box">
                                <div class="row row-in">
                                    <div class="col-lg-12">
                                        <div class=" row">
                                            <div class="col-md-6 col-sm-6 col-xs-6"> <i class="ti-receipt"></i>
                                                <h5 class="text-muted vb">MALE STUDENTS</h5> </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <h3 class="counter text-right m-t-15 text-warning">
                                                    <?php   $top_ridge->table_records_by_gender('students', 'student_status','M'); ?>
                                                </h3> </div>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> <span class="sr-only">40% Complete (success)</span> </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="white-box">
                                <div class="row row-in">
                                    <div class="col-lg-12">
                                        <div class=" row">
                                            <div class="col-md-6 col-sm-6 col-xs-6"> <i class="ti-receipt"></i>
                                                <h5 class="text-muted vb">FEMALE STUDENTS</h5> </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <h3 class="counter text-right m-t-15 text-warning">
                                                    <?php   $top_ridge->table_records_by_gender('students', 'student_status','F'); ?>
                                                </h3> </div>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> <span class="sr-only">40% Complete (success)</span> </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





                <div class="col-sm-3">
                    <div class="white-box">

                    <div class="text-center">
                        <h5 class="box-title m-t-30">Calender</h5>
                        <center>
                            <div id="datepicker-inline"></div>
                        </center>
                    </div></div>
                    <div class="news-slide m-b-15">
                        <div class="vcarousel slide">
                            <!-- Carousel items -->
                            <div class="carousel-inner">
                                <div class="active item">
                                    <div class="overlaybg"><img src="../../plugins/images/news/slide1.jpg" /></div>
                                    <div class="news-content"><span class="label label-danger label-rounded">Primary</span>
                                        <h2>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</h2> <a href="#">Read More</a></div>
                                </div>
                                <div class="item">
                                    <div class="overlaybg"><img src="../../plugins/images/news/slide1.jpg" /></div>
                                    <div class="news-content"><span class="label label-primary label-rounded">Primary</span>
                                        <h2>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</h2> <a href="#">Read More</a></div>
                                </div>
                                <div class="item">
                                    <div class="overlaybg"><img src="../../plugins/images/news/slide1.jpg" /></div>
                                    <div class="news-content"><span class="label label-success label-rounded">Primary</span>
                                        <h2>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</h2> <a href="#">Read More</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <!-- /.row -->

        </div>
        <!-- /.container-fluid -->
        <footer class="footer text-center"> 2017 &copy; Elite Admin brought to you by themedesigner.in </footer>
    </div>
    <!-- /#page-wrapper -->
</div>
<script src="../../plugins/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="../bootstrap/dist/js/tether.min.js"></script>
<script src="../bootstrap/dist/js/bootstrap.min.js"></script>
<script src="../../plugins/bower_components/bootstrap-extension/js/bootstrap-extension.min.js"></script>
<!-- Menu Plugin JavaScript -->
<script src="../../plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>
<!--slimscroll JavaScript -->
<script src="../js/jquery.slimscroll.js"></script>
<!--Wave Effects -->
<script src="../js/waves.js"></script>
<!-- Custom Theme JavaScript -->
<script src="../js/custom.min.js"></script>
<!-- Plugin JavaScript -->
<script src="../../plugins/bower_components/moment/moment.js"></script>
<!-- Clock Plugin JavaScript -->
<script src="../../plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.js"></script>
<!-- Color Picker Plugin JavaScript -->
<script src="../../plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asColor.js"></script>
<script src="../../plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asGradient.js"></script>
<script src="../../plugins/bower_components/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js"></script>
<!-- Date Picker Plugin JavaScript -->
<script src="../../plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<!-- Date range Plugin JavaScript -->
<script src="../../plugins/bower_components/timepicker/bootstrap-timepicker.min.js"></script>
<script src="../../plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script>
    // Clock pickers
    $('#single-input').clockpicker({
        placement: 'bottom',
        align: 'left',
        autoclose: true,
        'default': 'now'

    });

    $('.clockpicker').clockpicker({
            donetext: 'Done',

        })
        .find('input').change(function() {
        console.log(this.value);
    });

    $('#check-minutes').click(function(e) {
        // Have to stop propagation here
        e.stopPropagation();
        input.clockpicker('show')
            .clockpicker('toggleView', 'minutes');
    });
    if (/mobile/i.test(navigator.userAgent)) {
        $('input').prop('readOnly', true);
    }
    // Colorpicker

    $(".colorpicker").asColorPicker();
    $(".complex-colorpicker").asColorPicker({
        mode: 'complex'
    });
    $(".gradient-colorpicker").asColorPicker({
        mode: 'gradient'
    });
    // Date Picker
    jQuery('.mydatepicker, #datepicker').datepicker();
    jQuery('#datepicker-autoclose').datepicker({
        autoclose: true,
        todayHighlight: true
    });

    jQuery('#date-range').datepicker({
        toggleActive: true
    });
    jQuery('#datepicker-inline').datepicker({

        todayHighlight: true
    });

    // Daterange picker

    $('.input-daterange-datepicker').daterangepicker({
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-danger',
        cancelClass: 'btn-inverse'
    });
    $('.input-daterange-timepicker').daterangepicker({
        timePicker: true,
        format: 'MM/DD/YYYY h:mm A',
        timePickerIncrement: 30,
        timePicker12Hour: true,
        timePickerSeconds: false,
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-danger',
        cancelClass: 'btn-inverse'
    });
    $('.input-limit-datepicker').daterangepicker({
        format: 'MM/DD/YYYY',
        minDate: '06/01/2015',
        maxDate: '06/30/2015',
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-danger',
        cancelClass: 'btn-inverse',
        dateLimit: {
            days: 6
        }
    });
</script>
<!--Style Switcher -->
<script src="../../plugins/bower_components/styleswitcher/jQuery.style.switcher.js"></script>

<?php include_once '../includes/footer.php'?>



</body>

</html>
