<!-- /#wrapper -->
<!-- jQuery -->

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
<!--weather icon -->
<script src="../../plugins/bower_components/skycons/skycons.js"></script>
<!--Morris JavaScript -->
<script src="../../plugins/bower_components/raphael/raphael-min.js"></script>
<script src="../../plugins/bower_components/morrisjs/morris.js"></script>
<!-- jQuery for carousel -->
<script src="../../plugins/bower_components/owl.carousel/owl.carousel.min.js"></script>
<script src="../../plugins/bower_components/owl.carousel/owl.custom.js"></script>
<!-- Sparkline chart JavaScript -->
<script src="../../plugins/bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="../../plugins/bower_components/jquery-sparkline/jquery.charts-sparkline.js"></script>
<!--Counter js -->
<script src="../../plugins/bower_components/waypoints/lib/jquery.waypoints.js"></script>
<script src="../../plugins/bower_components/counterup/jquery.counterup.min.js"></script>
<!-- Custom Theme JavaScript -->
<script src="../js/custom.min.js"></script>
<script src="../js/widget.js"></script>
<!--Style Switcher -->
<script src="../../plugins/bower_components/styleswitcher/jQuery.style.switcher.js"></script>
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