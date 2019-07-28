<?PHP include("../includes/main_class.php");?>


			<?PHP include("../includes/header.php");?>
         
           
  
   
   
 
  <div id="main_content" class="main">
  
  <?php
  
  
  
  
		if(isset( $_GET['edit']) == 'true' )
		{
		
		
		$message = "Congrats. Academic calender was updated successfully!";
		$top_ridge->print_message(1,$message);
		$top_ridge->reload_opener_page_and_close();
		}
		
		if(isset( $_GET['term_update']) == 'failed' )
		{
		
		
		$message = "Sorry! An error occured. Contact your system administrator";
		$top_ridge->print_message(0,$message);
		}
  
  
  
  if(isset( $_GET['editing']) == 'false' )
		{
		
		
		$message = "Update failed. Academic calender already exist.";
		$top_ridge->print_message(0,$message);
		}
  
  
  
  
  
  
  	if(isset($_POST['update_calender']))
		{
			 $new_academic_year = mysql_prep($_POST['academic_year']);
			 $new_academic_term = mysql_prep($_POST['academic_term']);
			$new_vacation_date = mysql_prep($_POST['vacation_date']);
			$new_resumption_date = mysql_prep($_POST['resumption_date']);
			 $current_term_id = mysql_prep($_POST['current_term_id']);
			 $start_date = mysql_prep($_POST['start_date']);
			 
			 
			$new_weeks = mysql_prep($_POST['weeks']);
			/////check for double registration
			$check_reg = mysqli_query($top_ridge_db_connection,"SELECT term_id FROM term_settings WHERE academic_year = '$new_academic_year' AND academic_term='$new_academic_term' AND term_id != {$current_term_id} ");
			confirm_query($check_reg);
			if(mysqli_num_rows($check_reg) > 0)
			{
				header('location:calender_edit.php?editing=false');
			}else{
				$update_calender = mysqli_query($top_ridge_db_connection,"UPDATE term_settings SET start_date='$start_date'    ,academic_year='$new_academic_year' , academic_term='$new_academic_term', vacation_date='$new_vacation_date', resumption_date='$new_resumption_date', number_of_weeks = {$new_weeks}  WHERE term_id={$current_term_id}  ");
				confirm_query($update_calender);
				if($update_calender){
					
					header('location:calender_edit.php?edit=true');
					}else{
						
							header('location:calender_edit.php?term_update=failed');
						}
				
				}
		}?>
  
  
  
  
  
  
  
  
  <div id="term_settings">
    <fieldset class="ui-corner-all print_info_fieldset">
      <legend>Academic Calender Edit</legend>
      <form id="form1" name="form1" method="post" action="calender_edit.php">
        <input type="hidden" name="current_term_id" value='<?php echo $top_ridge->current_term_id; ?>'  />
        <table width="459" border="0">
          <tr>
            <td width="147" align="right">Academic Year:</td>
            <td width="296"><span id="sprytextfield1">
              <input name="academic_year" type="text" class="textfield ui-corner-all" value="<?php echo $top_ridge->academicYear; ?>" id="academic_year" />
              <span class="textfieldRequiredMsg"><br />
             Please select academic year.</span></span></td>
          </tr>
          <tr>
            <td align="right">Academic Term:</td>
            <td><span id="spryselect1">
              <select name="academic_term" class="textfield ui-corner-all" id="academic_term" style="width:86%;">
             <option value="<?php echo $top_ridge->academicTerm; ?>"> <?php echo $top_ridge->academicTerm; ?></option>
                <option value="1ST">1ST</option>
                <option value="2ND">2ND</option>
                <option value="3RD">3RD</option>
              </select>
              <span class="selectRequiredMsg"><br />
              Please select academic term</span></span></td>
          </tr>
          <tr>
            <td align="right">Number of Weeks:</td>
            <td><span id="sprytextfield4">
            <input name="weeks" type="text" value='<?php echo $top_ridge->number_of_weeks; ?>' class="textfield ui-corner-all"  id="weeks" />
            <br />
            <span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Invalid format.</span><span class="textfieldMinValueMsg">The entered value is less than the minimum required.</span></span></td>
          </tr>
          <tr>
            <td align="right">Start Date</td>
            <td><input type="text" size="30" name="start_date" class="textfield ui-corner-all" value='<?php echo $top_ridge->current_term_start_date; ?>' id="jQueryUICalendar3"/>
              
              <script type="text/javascript">
// BeginWebWidget jQuery_UI_Calendar: jQueryUICalendar3
jQuery("#jQueryUICalendar3").datepicker();

// EndWebWidget jQuery_UI_Calendar: jQueryUICalendar3
              </script></td>
          </tr>
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		            <tr>
            <td align="right">Resumption Date:</td>
            <td><span id="sprytextfield3">
              <input type="text" class="textfield ui-corner-all"  id="jQueryUICalendar2" name='resumption_date' value='<?php echo $top_ridge->date_of_resumption; ?>' size="30"/>
              <br />
              <span class="textfieldRequiredMsg">Resumption date is required.</span></span>
             
              <script type="text/javascript">
// BeginWebWidget jQuery_UI_Calendar: jQueryUICalendar2
jQuery("#jQueryUICalendar2").datepicker();

// EndWebWidget jQuery_UI_Calendar: jQueryUICalendar2
              </script></td>
          </tr>
		  
          <tr>
            <td align="right">Vacation Date:</td>
            <td><span id="sprytextfield2">
              <input type="text" class="textfield ui-corner-all" id="jQueryUICalendar1" name='vacation_date'  value='<?php echo $top_ridge->date_of_vacation; ?>'  size="30"/>
              <span class="textfieldRequiredMsg"><br />
              Vacation date is required.</span></span>
              
              <script type="text/javascript">
// BeginWebWidget jQuery_UI_Calendar: jQueryUICalendar1
jQuery("#jQueryUICalendar1").datepicker();

// EndWebWidget jQuery_UI_Calendar: jQueryUICalendar1
              </script></td>
          </tr>

          <tr>
            <td align="right">&nbsp;</td>
            <td><input name="update_calender" type="submit" class="submit_button ui-corner-all" id="update_calender" value="Update" /></td>
          </tr>
        </table>
      </form>
    
    </fieldset></div>
  </div>
  


 <div id="footer"><?php echo $top_ridge->footer;   ?></div>

<?php
include_once '../includes/footer.php';
ob_end_flush();  ?>