<?php if(isset($_POST['exams'])	  ||  isset($_GET['exams_results']) 	)  
	{
			
			if(isset($_POST['exams']))
			{
				$class_id = mysql_prep($_POST['class_id']);
				$subject_id = mysql_prep($_POST['subject_id']);
				
				$term_id = mysql_prep($_POST['term_id']);
				
			
			}else if(isset($_GET['exams_results'])  ){
				
				$class_id = mysql_prep($_GET['class_id']);
				$subject_id = mysql_prep($_GET['subject_id']);
				$term_id = mysql_prep($_GET['term_id']);
				}
			
			
	

	
	?> 
	<?php echo $top_ridge->total_class_students($class_id);?>
	<table width="100%" border="0" class="heading" >
	<tr>
	<td width="27%">ACADEMIC TERM: <b id="green"><?php echo $top_ridge->term_details($term_id);?></b></td>
	<td width="14%">CLASS:  <b id="green"><?php echo $top_ridge->class_name($class_id);?></b></td>
	
	<td width="35%">SUBJECT: <b id="green"><?php echo $top_ridge->subject_name($subject_id);?></b></td>
	</tr>
	</table>
	
    <?php 
	$marks_out_of_bounds_exception = false;
	
   //	if($class_id > 0 )
	//{ 
	
	?>
	<p>
	<?php
	echo "<form method='POST' action='academics.php' >";
	echo "<table border=0 width='100%'>
	<tr>
		<td>
	<input type='submit' title='Click to save all records'  class='submit_button save_button ui-corner-all' value='Save Record' name='final_exams_results' /> 
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
	<th axis='string' width='50%'>STUDENT NAME</th>
	 
	<th axis='string' width='27%' class='center'> EXAMS SCORE <br/> (100%)</th>
	<th axis='string' width='20%' class='center'> B<br>50%</th>
	
	</tr>
	</thead>
	
	
	
	
	
	
	<?php
	echo "<form method='POST' action='academics.php' >";
	echo " <input type='hidden' name='class_id' value=$class_id />
	<input type='hidden' name='term_id' value=$term_id />
	<input type='hidden' name='subject_id' value=$subject_id />";
	
	
	$home_work_results = mysqli_query($top_ridge_db_connection,"SELECT students.id, 
	CONCAT(students.last_name,' ',first_name) AS STUDENT_NAME,exams_score	
	FROM continous_assessment,students WHERE students.id = continous_assessment.student_id AND 
	continous_assessment.term_id='$term_id' AND continous_assessment.subject_id={$subject_id}
	AND continous_assessment.class_id = {$class_id} AND gender='M' ORDER BY students.last_name ASC
	");
	confirm_query($home_work_results);
	$count = 1;
	if(mysqli_num_rows($home_work_results) > 0)
	{
	while($rows = mysqli_fetch_assoc($home_work_results))
	{
		
	   $student_id = $rows['id'];
		$student_name = $rows['STUDENT_NAME'];
		$exams_score =  $rows['exams_score'];
		
		$fifty_percent = round((0.5 * $exams_score),2);
	//if($total_exams_score > 70 ){$marks_out_of_bounds_exception = true;}
		echo " <input type='hidden' name='student_id[]' value=$student_id />";
		
		echo "  <tr class='hover_background'>
		
		
	
		</tr>
		<td align='center'>$count.</td>
		<td class='capitalize' style='text-align:left;  padding-left:10px;text-transform:capitalize'>$student_name </td>";
		
		
			echo "<td class='center'> <input ";

		if($exams_score < 0 || $exams_score >100){
			echo " style='background-color: red' ";}

		echo " type='text'  name='exams_score[]' value=$exams_score > </td>

		
		<td class='center'>$fifty_percent </td>";
		
		
		
		echo "</tr>";
		
		
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
	<th axis='string' width='50%'>STUDENT NAME</th>
	 
	<th axis='string' width='27%' class='center'> EXAMS SCORE <br/> (100%)</th>
	<th axis='string' width='20%' class='center'> B <br>50%</th>
	
	</tr>
	</thead>
	
	
	<?php
	$home_work_results = mysqli_query($top_ridge_db_connection,"SELECT students.id, 
	CONCAT(students.last_name,' ',first_name) AS STUDENT_NAME,exams_score
	FROM continous_assessment,students WHERE students.id = continous_assessment.student_id AND 
	continous_assessment.term_id='$term_id' AND continous_assessment.subject_id={$subject_id}
	AND continous_assessment.class_id = {$class_id} AND gender='F' ORDER BY students.last_name ASC
	");
	confirm_query($home_work_results);
	$count = 1;
	if(mysqli_num_rows($home_work_results) > 0)
	{
	while($rows = mysqli_fetch_assoc($home_work_results))
	{
		
	   $student_id = $rows['id'];
		$student_name = $rows['STUDENT_NAME'];
		$exams_score =  $rows['exams_score'];
		$fifty_percent = round((0.5 * $exams_score),2);
	//if($total_exams_score > 70 ){$marks_out_of_bounds_exception = true;}
		
		echo " <input type='hidden' name='student_id[]' value=$student_id />";
		
		echo "  <tr class='hover_background'>
		
		
	
		
			<td align='center'>$count.</td>
		<td class='capitalize' style='text-align:left;  padding-left:10px;text-transform:capitalize'>$student_name </td>
		<td class='center'><input ";
		if($exams_score < 0 || $exams_score >100){
			echo " style='background-color: red' ";}
		echo " type='text'  name='exams_score[]' value=$exams_score ></td>

		
		<td class='center'>$fifty_percent </td>";
		
		
		echo "</tr>";
		
		
		$count++;
	
	}	 
	}




	echo "</table>";
	echo "<table align='center' style='margin-top:1%'><tr class='no_background'>  <td align='align_right'> </td> </tr></table>";
	echo "<table border=0 width='100%'>
	<tr>
		<td>";



	if($top_ridge->get_class_total_count($class_id) > 0)
	{
		echo "<input type='submit' title='Click to save all records'  class='submit_button save_button ui-corner-all' value='Save Record' name='final_exams_results' /> ";
	}

	echo "</td>
	</tr>
</table>";
		echo "</form>";

	
}
	
	
	
/* 	
	if($marks_out_of_bounds_exception == true)
	{
		echo "<script type='text/javascript'>
			alert('ERROR: Total marks entered exceeds the maximum of 100 for some records.Please check and re-submit. Neglecting error can have severe inconsistences on your final report');
		</script>";
	}			 echo $top_ridge->print_message(2,"Opps. <b style='color: red'>$exams_score</b>   must be between 0-100");

	 */
	?> 
	
	</table>