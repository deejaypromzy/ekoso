
	<?php 
	
	
	///JHS 3 class_id
		/*$JHS_3_class_id = mysql_query("SELECT class_id  FROM classes  ORDER BY class_id  DESC LIMIT 1");
		confirm_query($JHS_3_class_id);
		$a =   mysql_fetch_assoc($JHS_3_class_id);
		$jhs_3_class_id = $a['class_id'];*/
		
		$jhs_3_class_id  = $top_ridge->jhs_3_class_id();
		
		
		
	$marks_out_of_bounds_exception = false;
	
	
	if(isset($_POST['submit_mock'])	  ||  isset($_GET['mock_results']) 	)  
	{
			
			if(isset($_POST['submit_mock']))
			{
				 $term_id = mysql_prep($_POST['term_id']);
				  $subject_id = mysql_prep($_POST['subject_id']);
				$mock_id = mysql_prep($_POST['mock_id']);
				$class_id = mysql_prep($_POST['class_id']);
			
			}else if(isset($_GET['mock_results'])  ){
				
				$term_id = mysql_prep($_GET['term_id']);
				$subject_id = mysql_prep($_GET['subject_id']);
				$mock_id = mysql_prep($_GET['mock_id']);
				$class_id = mysql_prep($_GET['class_id']);
			
			}
			
			
	

	
	?> 
	<?php //echo $top_ridge->total_class_students($jhs_3_class_id);
			
	?>
	<table width="100%" border="0" class="heading" >
	
	<tr>
			<td COLSPAN=5 class='center bold' style='font-size:20px;color:blue'>APTITUDE TEST RESULTS FOR <label style='color:red;'> <?PHP 
			echo $top_ridge->get_class_name($class_id);
			?></label></td>
	</tr>
	
	<tr>
	<td width="27%">ACADEMIC TERM: <b id="green" style='font-size:14px;color:blue'><?php echo $top_ridge->term_details($term_id);?></b></td>
	<td width="14%">TEST NUMBER:  <b id="green" style='font-size:14px;color:blue'><?php echo $top_ridge->MOCK_NUMBER($mock_id);?></b></td>
	
	<td width="35%">SUBJECT: <b id="green" style='font-size:14px;color:blue'><?php echo $top_ridge->subject_name($subject_id);?></b></td>
	
	<td width="35%">TOTAL STUDENTS: <b id="green" style='font-size:14px;color:blue'><?php echo $top_ridge->get_total_students_for_this_mock($mock_id);?></b></td>
	
	
	</tr>
	</table>
	
	<p>
	<?php
	echo "<form method='POST' action='academics.php' >";
	
	echo "<input type='hidden'  name='class_id'  value='$class_id'  />";
	
	
	echo "<table border=0 width='100%'>
	<tr>
		<td>
	<input type='submit' title='Click to save all records'  class='submit_button save_button ui-corner-all' value='Save Record' name='final_mock_results' /> 
		</td>
	</tr>
</table>";
	
	?>
	
	<table width='100%' border=0  class='seperator1'>
			<tr>
					<td>			M		A		L		E		S</td>
			</tr>
	</table>
	<table  cellpadding='0' width='100%' class='academics collapse_border' border="1">
	<thead>
	<tr class="heading collapse_border">
	<th axis='number' align='center' width='3%'>No.</th>
	<th axis='string' width='37%'>STUDENT NAME</th>
	<th axis='string' width='15%' class='center'>TEST SCORE <br/> (100)</th>
	<th  width='15%' class="center" >GRADE</th>
	<th  width='15%' class="center" >POSTION</th>
	<th  width='15%' class="center" >REMARKS</th>
	
	</tr>
	</thead>
	
	
	
	
	
	
	<?php
	echo "<form method='POST' action='academics.php' >";
	echo " <input type='hidden' name='class_id' value=$class_id />
	<input type='hidden' name='term_id' value=$term_id />
	<input type='hidden' name='subject_id' value=$subject_id />
	<input type='hidden' name='mock_id' value=$mock_id />";
	
	$MOCK_RESULTS = mysqli_query($top_ridge_db_connection,"SELECT students.id, 
	CONCAT(students.last_name,' ',first_name) AS STUDENT_NAME,marks,position	
	FROM  std_mock_results,students WHERE students.id =  std_mock_results.student_id AND 
	 std_mock_results.mock_id={$mock_id} AND std_mock_results.subject_id={$subject_id}
	AND  gender='M' ORDER BY students.last_name ASC, students.first_name ASC
	");
	confirm_query($MOCK_RESULTS);
	$count = 1;
	if(mysqli_num_rows($MOCK_RESULTS) > 0)
	{
	while($rows = mysqli_fetch_assoc($MOCK_RESULTS))
	{
		
	   $student_id = $rows['id'];
		$student_name = $rows['STUDENT_NAME'];
		$marks =  $rows['marks'];
		if($marks > 100 ){$marks_out_of_bounds_exception = true;}
		$position =  $rows['position'];
		
		$pos = $top_ridge->subject_grade($marks);
		$grade = $pos[1];
		$remarks = $pos[0];
		
		
		echo " <input type='hidden' name='student_id[]' value=$student_id />";
		
		echo "  <tr class='hover_background'>
		
		
	
		
		<td align='center'>$count.</td>
		<td class='capitalize' style='text-align:left;  padding-left:10px;text-transform:capitalize'>$student_name </td>
		<td class='center'>	<input type='text'  name='marks[]' value=$marks /> </td>
		<td class='center grade'>$grade </td>
		<td class='center position'>$position </td>
		<td style='text-align:left;padding-left:3%' class='remarks'>$remarks </td>
		
		
		
		</tr>";
		
		
		$count++;
	
	}	 
	}
	echo "</table>";
	?>
		<table width='100%' border=0  class='seperator2'>
			<tr>
					<td>	F	E		M		A		L		E		S</td>
			</tr>
	</table>
	<table  cellpadding='0' width='100%' class='academics collapse_border' border="1">
	<thead>
	<tr class="heading collapse_border">
	<th axis='number' align='center' width='3%'>No.</th>
	
	<th axis='string' width='37%'>STUDENT NAME</th>
	<th axis='string' width='15%' class='center'>TEST SCORE <br/> (100)</th>
	<th  width='15%' class="center" >GRADE</th>
	<th  width='15%' class="center" >POSTION</th>
	<th  width='15%' class="center" >REMARKS</th>
	
	
	</tr>
	</thead>
	
	
	<?php
	$MOCK_RESULTS = mysqli_query($top_ridge_db_connection,"SELECT students.id, 
	CONCAT(students.last_name,' ',first_name) AS STUDENT_NAME,marks,position	
	FROM  std_mock_results,students WHERE students.id =  std_mock_results.student_id AND 
	 std_mock_results.mock_id={$mock_id} AND std_mock_results.subject_id={$subject_id}
	AND  gender='F' ORDER BY students.last_name ASC, students.first_name ASC
	");
	confirm_query($MOCK_RESULTS);
	$count = 1;
	if(mysqli_num_rows($MOCK_RESULTS) > 0)
	{
	while($rows = mysqli_fetch_assoc($MOCK_RESULTS))
	{
		
	   $student_id = $rows['id'];
		$student_name = $rows['STUDENT_NAME'];
		$marks =  $rows['marks'];
		if($marks > 100 ){$marks_out_of_bounds_exception = true;}
		
		$position =  $rows['position'];
		$pos = $top_ridge->subject_grade($marks);
		$grade = $pos[1];
		$remarks = $pos[0];
		
		
		echo " <input type='hidden' name='student_id[]' value=$student_id />";
		
		echo "  <tr class='hover_background'>
		
		
	
		
		<td align='center'>$count.</td>
		<td class='capitalize' style='text-align:left;  padding-left:10px;text-transform:capitalize'>$student_name </td>
		<td class='center'>	<input type='text'  name='marks[]' value=$marks /> </td>
		<td class='center grade'>$grade </td>
		<td class='center position'>$position </td>
		<td style='text-align:left;padding-left:3%' class='remarks'>$remarks </td>
		
		
		</tr>";
		
		
		$count++;
	
	}	 
	}
	echo "</table>";
	echo "<table align='center' style='margin-top:1%'><tr class='no_background'>  <td align='align_right'> </td> </tr></table></form>";
	
	
	}
	
	
	if($marks_out_of_bounds_exception == true)
	{
		echo "<script type='text/javascript'>
			alert('ERROR: Total marks entered exceeds the maximum of 100 for some records.Please check and re-submit. Neglecting error can have severe inconsistences on your final report');
		</script>";
	}
	?> 
	
	</p> 