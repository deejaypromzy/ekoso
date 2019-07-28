
	<?php 
	
	 if(isset($_POST['class_work'])	  ||  isset($_GET['class_work_results']) 	)  
	{

			if(isset($_POST['class_work']))
			{
				$class_id = mysql_prep($_POST['class_id']);
				$subject_id = mysql_prep($_POST['subject_id']);
				
				
				$term_id = mysql_prep($_POST['term_id']);
				
			
			}else if(isset($_GET['class_work_results'])){
				
				$class_id = mysql_prep($_GET['class_id']);
				$subject_id = mysql_prep($_GET['subject_id']);
				$term_id = mysql_prep($_GET['term_id']);
				}

	?> 
	<?php echo $top_ridge->total_class_students($class_id);?>
	<table width="100%" border="0" class="heading">
	<tr>
	<td width="27%">ACADEMIC TERM: <b id="green"><?php echo $top_ridge->term_details($term_id);?></b></td>
	<td width="14%">CLASS:  <b id="green"><?php echo $top_ridge->class_name($class_id);?></b></td>
	
	<td width="35%">SUBJECT: <b id="green"><?php echo $top_ridge->subject_name($subject_id);?></b></td>
	</tr>
	</table>
	
	<p>
	<?php
	echo "<form method='POST' action='academics.php' >";
	echo "<table border=0 width='100%'>
	<tr>
		<td>";
	
	
	
	if($top_ridge->get_class_total_count($class_id) > 0)
		{
		  echo "<input type='submit' title='Click to save all records'  class='submit_button save_button ui-corner-all' value='Save Record' name='class_work_results' /> ";
		
		}
		echo "</td>
	</tr>
</table>";
	
	?>
	<table width='100%' border=0  class='seperator1'>
			<tr>
					<td>		M		A		L		E		S</td>
			</tr>
	</table>
	<table id='' cellpadding='0' width='100%' class='academics collapse_border' border="1" >
	<thead>
	<tr class="heading collapse_border">
	<th axis='number' align='center' width='5%'>No.</th>
	<th axis='string' width='20%'>STUDENT NAME</th>
	<th axis='string' width='10%' class='center'>GROUP WORK <br/>(15)</th>
<!--	<th axis='string' width='10%' class='center'>CW 2 <br/>(10)</th>-->
<!--	-->
<!--	<th axis='string' width='10%' class='center'>CW 3 <br/>(10)</th>-->
<!--	<th axis='string' width='10%' class='center'>CW 4 <br/>(10)</th>-->
	<th axis='string' width='10%' class='center'>SUB TOTAL <br/> (15)</th>
	

	</tr>
	</thead>
	
	
	
	
	
	
	<?php
	
	echo " <input type='hidden' name='class_id' value=$class_id />
	<input type='hidden' name='term_id' value=$term_id />
	<input type='hidden' name='subject_id' value=$subject_id />";
	
	
	$home_work_results = mysqli_query($top_ridge_db_connection,"SELECT students.id, 
	CONCAT(students.last_name,' ',first_name) AS STUDENT_NAME,class_work_one, (class_work_one ) AS TOTAL 
	FROM continous_assessment,students WHERE students.id = continous_assessment.student_id AND 
	continous_assessment.term_id='$term_id' AND continous_assessment.subject_id={$subject_id}
	AND continous_assessment.class_id = {$class_id} AND gender='M'  ORDER BY students.last_name ASC, students.first_name ASC
	");
	confirm_query($home_work_results);
	$count = 1;
	
	$marks_out_of_bounds_exception = false;
	if(mysqli_num_rows($home_work_results) > 0)
	{
	while($rows = mysqli_fetch_assoc($home_work_results))
	{
		
	   $student_id = $rows['id'];
		$student_name = strtoupper($rows['STUDENT_NAME']);
		$class_work_one =$rows['class_work_one'];
//		$class_work_two = $rows['class_work_two'];
//		$class_work_three = $rows['class_work_three'];
//		$class_work_four = $rows['class_work_four'];
		//$TOTAL = round($rows['TOTAL']);
		$TOTAL = $top_ridge->get_format_number( round($rows['TOTAL'],2));
		//if($TOTAL > 40 ){$marks_out_of_bounds_exception = true;}
		$twenty_percent = 0.2 * $TOTAL; 
		echo " <input type='hidden' name='student_id[]' value=$student_id />";
		
		echo "  <tr class='hover_background'>
		
		
	
		
		<td align='center'>$count.</td>
		<td class='capitalize' style='text-align:left;  padding-left:10px;text-transform:capitalize'>$student_name </td>
		
		<td class='center'>	<input ";
		if($class_work_one < 0 || $class_work_one >15){
			echo " style='background-color: red' ";}
		echo "type='text'  name='class_work_one[]' value=$class_work_one ></td>".
//		<td class='center'><input type='text' name='class_work_two[]' value=$class_work_two /></td>
//		<td class='center'><input type='text' name='class_work_three[]' value=$class_work_three /></td>
//		<td class='center'><input type='text' name='class_work_four[]' value=$class_work_four /></td>
		"<td class='center' style='vertical-align:middle'><b>$TOTAL</b></td>

		
		</tr>";
		
		
		$count++;
	
	}	 
	}
	
	echo "</table>";
	
	?>
	
	
	<table width='100%' border=0  class='seperator2'>
			<tr>
					<td>	F 		E		M		A		L		E		S</td>
			</tr>
	</table>
	<table id='' cellpadding='0' width='100%' class='academics collapse_border' border="1" >
	<thead>
	<tr class="heading collapse_border">
	<th axis='number' align='center' width='5%'>No.</th>
	<th axis='string' width='20%'>STUDENT NAME</th>
	<th axis='string' width='10%' class='center'>GROUP WORK  <br/>(15)</th>
<!--	<th axis='string' width='10%' class='center'>CW 2 <br/>(10)</th>-->
<!--	-->
<!--	<th axis='string' width='10%' class='center'>CW 3 <br/>(10)</th>-->
<!--	<th axis='string' width='10%' class='center'>CW 4 <br/>(10)</th>-->
	<th axis='string' width='10%' class='center'>SUB TOTAL <br/> (15)</th>
	
	</tr>
	</thead>
	
	<?php
	$home_work_results = mysqli_query($top_ridge_db_connection,"SELECT students.id, 
	CONCAT(students.last_name,' ',first_name) AS STUDENT_NAME,class_work_one, (class_work_one) AS TOTAL 
	FROM continous_assessment,students WHERE students.id = continous_assessment.student_id AND 
	continous_assessment.term_id='$term_id' AND continous_assessment.subject_id={$subject_id}
	AND continous_assessment.class_id = {$class_id} AND gender='F'  ORDER BY students.last_name ASC, students.first_name ASC
	");
	confirm_query($home_work_results);
	$count = 1;
	if(mysqli_num_rows($home_work_results) > 0)
	{
	while($rows = mysqli_fetch_assoc($home_work_results))
	{

	   $student_id = $rows['id'];
		$student_name = strtoupper($rows['STUDENT_NAME']);
		$class_work_one =$rows['class_work_one'];
//		$class_work_two = $rows['class_work_two'];
//		$class_work_three = $rows['class_work_three'];
//		$class_work_four = $rows['class_work_four'];
		//$TOTAL = round($rows['TOTAL']);
		$TOTAL = $top_ridge->get_format_number( round($rows['TOTAL'],2));
		//if($TOTAL > 40 ){$marks_out_of_bounds_exception = true;}
		$twenty_percent = 0.2 * $TOTAL; 
		echo " <input type='hidden' name='student_id[]' value=$student_id />";
		
		echo "  <tr class='hover_background'>
		
		
	
		
		<td align='center'>$count.</td>
		<td class='capitalize' style='text-align:left;  padding-left:10px;text-transform:capitalize'>$student_name </td>

		<td class='center'>			
<input ";
		if($class_work_one < 0 || $class_work_one >15){
			echo " style='background-color: red' ";}
		echo "type='text'  name='class_work_one[]' value=$class_work_one ></td>"

//		<td class='center'><input type='text' name='class_work_two[]' value=$class_work_two /></td>
//		<td class='center'><input type='text' name='class_work_three[]' value=$class_work_three /></td>
//		<td class='center'><input type='text' name='class_work_four[]' value=$class_work_four /></td>
		."<td class='center' style='vertical-align:middle'><b>$TOTAL</b></td>

		
		</tr>";

		
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
		echo "<input type='submit' title='Click to save all records'  class='submit_button save_button ui-corner-all' value='Save Record' name='class_work_results' /> ";
	}

	echo "</td>
	</tr>
</table>";
		echo "</form>";
	
	}
	
	
	/* if($marks_out_of_bounds_exception == true)
	{
		echo "<script type='text/javascript'>
			alert('ERROR: Total marks entered exceeds the maximum of 40 for some records.Please check and re-submit. Neglecting error can have severe inconsistences on your final report');
		</script>"; 
	}*/
	
	
	
	
	
	?> 
	
	</p> 

