




	<?php 
/*	if(isset($_POST['home_work']))
	{
	$class_id = mysql_prep($_POST['class_id']);
	$subject_id = mysql_prep($_POST['subject_id']);

	if($top_ridge->staff_job_type == 1)
	{
	$term_id = mysql_prep($_POST['term_id']);
	}else{
	$term_id = $top_ridge->current_term_id;
	}*/

	
	 if(isset($_POST['home_work'])	  ||  isset($_GET['home_work_results']) 	)  
	{
			
			if(isset($_POST['home_work']))
			{
				$class_id = mysql_prep($_POST['class_id']);
				$subject_id = mysql_prep($_POST['subject_id']);




				/* if($class_id > $top_ridge->jhs_3_class_id())
					{
					  header("location:academics.php?class_no_assignment=true&&no_class_id=$class_id");
					  exit();
					}
				 */
				/*
				if($top_ridge->staff_job_type == 1)
				{ */
				$term_id = mysql_prep($_POST['term_id']);
				/* }else{
				$term_id = $top_ridge->current_term_id;
				} */

			}else if(isset($_GET['home_work_results'])){
				
				$class_id = mysql_prep($_GET['class_id']);
				$subject_id = mysql_prep($_GET['subject_id']);
				 $term_id = mysql_prep($_GET['term_id']);
				}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*  if($class_id != 1 || $class_id != 2 )
	 { */
	
	?> 
<hr>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<!--		<a class="btn btn-default btn-outline m-b-20" id="unblockbtn4">	--><?php //echo $total_class_students1 =  $top_ridge->total_class_students($class_id);?>
<!--		</a>-->
		<div class="panel panel-primary block4">
			<div class="panel-heading">
				<b>ACADEMIC TERM: </b><?php echo $top_ridge->term_details($term_id);?><br>

				<b>CLASS:</b> <?php echo $top_ridge->class_name($class_id);?><br>

				<b>SUBJECT:</b><?php echo $top_ridge->subject_name($subject_id);?><br>
			</div>
		</div>
		<?php
		echo "<form method='POST' action='academics.php' >";
		echo " <input type='hidden' name='class_id' value=$class_id />
	<input type='hidden' name='term_id' value=$term_id />
	<input type='hidden' name='subject_id' value=$subject_id />";

		?>
		<?php

		if($top_ridge->get_class_total_count($class_id) > 0)
		{
			echo "<input type='submit' title='Click to save all records'  class='btn btn-lg btn-danger save_button ui-corner-all' value='Save Record' name='home_work_results' /> ";
		}
		?>
<hr>
		<div class="panel panel-success block4">
			<div class="panel-heading">
			<label style="font-size: xx-large"> M		A		L		E		S</label>
				<div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"><i class="ti-close"></i></a> </div>
			</div>
			<div class="panel-wrapper collapse in" aria-expanded="true">
				<div class="panel-body text-right">

					<div class="table-responsive">
						<table id="myTable" class="table table-striped">
							<thead>
							<tr style="align-content: center" class="text-center">
								<th >No.</th>
								<th >STUDENT NAME</th>
								<th >PROJECT WORK</th>
								<td style="align-content: center"><b>SUB TOTAL <br/>(15)</b></td>
							</tr>
							</thead>
							<tbody>

							<?php


							$home_work_results = mysqli_query($top_ridge_db_connection,"SELECT students.id, 
	CONCAT(students.last_name,' ',first_name) AS STUDENT_NAME,home_work_one, (home_work_one) AS TOTAL 
	FROM continous_assessment,students WHERE students.id = continous_assessment.student_id AND 
	continous_assessment.term_id='$term_id' AND continous_assessment.subject_id={$subject_id}
	AND continous_assessment.class_id = {$class_id} AND students.gender = 'M'  ORDER BY students.last_name ASC
	");
							confirm_query($home_work_results);
							$count = 1;

							//$marks_out_of_bounds_exception = false;

							if(mysqli_num_rows($home_work_results) > 0)
							{
								while($rows = mysqli_fetch_assoc($home_work_results))
								{

									$student_id = $rows['id'];
									$student_name = strtoupper($rows['STUDENT_NAME']);
									$home_work_one =$rows['home_work_one'];
//		$home_work_two = $rows['home_work_two'];
//		$home_work_three = $rows['home_work_three'];
//		$home_work_four = $rows['home_work_four'];
									$TOTAL = $top_ridge->get_format_number( round($rows['TOTAL'],2));


									//if($TOTAL > 20 ){$marks_out_of_bounds_exception = true;}

									$twenty_percent = 0.2 * $TOTAL;
									echo " <input type='hidden' name='student_id[]' value=$student_id />";

									echo "  <tr style=\"align-content: center\" class=\"text-center\">
		
		
	
		
		<td align='center'>$count.</td>
		<td style='text-align:left;'>$student_name </td>
		<td  style='width: 20%' ><input  ";
									if($home_work_one < 0 || $home_work_one >15){
										echo " style='background-color: red' ";}

									echo "class='form-control' type='text'  id='hw[]' name='home_work_one[]'  value=$home_work_one ></td>
		<td style='alignment:center'><b>$TOTAL</b></td>
		
		</tr>";


									$count++;

								}
							}

							?>

							</tbody>

						</table>

		</div></div>
		</div>
<hr>

		<div class="panel panel-warning block4">
			<div class="panel-heading">
			<label style="font-size: xx-large">F    E    M		A		L		E		S</label>
				<div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"><i class="ti-close"></i></a> </div>
			</div>
			<div class="panel-wrapper collapse in" aria-expanded="true">
				<div class="panel-body text-center">

					<div class="table-responsive">
						<table id="myTable" class="table table-striped">
							<thead>
							<tr style="align-content: center" class="text-center">
								<th >No.</th>
								<th >STUDENT NAME</th>
								<th >PROJECT WORK</th>
								<td style="align-content: center"><b>SUB TOTAL <br/>(15)</b></td>
							</tr>
							</thead>
							<tbody>

							<?php


							$home_work_results = mysqli_query($top_ridge_db_connection,"SELECT students.id, 
	CONCAT(students.last_name,' ',first_name) AS STUDENT_NAME,home_work_one, (home_work_one) AS TOTAL 
	FROM continous_assessment,students WHERE students.id = continous_assessment.student_id AND 
	continous_assessment.term_id='$term_id' AND continous_assessment.subject_id={$subject_id}
	AND continous_assessment.class_id = {$class_id} AND students.gender = 'F'  ORDER BY students.last_name ASC
	");
							confirm_query($home_work_results);
							$count = 1;

							//$marks_out_of_bounds_exception = false;

							if(mysqli_num_rows($home_work_results) > 0)
							{
								while($rows = mysqli_fetch_assoc($home_work_results))
								{

									$student_id = $rows['id'];
									$student_name = strtoupper($rows['STUDENT_NAME']);
									$home_work_one =$rows['home_work_one'];
//		$home_work_two = $rows['home_work_two'];
//		$home_work_three = $rows['home_work_three'];
//		$home_work_four = $rows['home_work_four'];
									$TOTAL = $top_ridge->get_format_number( round($rows['TOTAL'],2));


									//if($TOTAL > 20 ){$marks_out_of_bounds_exception = true;}

									$twenty_percent = 0.2 * $TOTAL;
									echo " <input type='hidden' name='student_id[]' value=$student_id />";

									echo "  <tr style=\"align-content: center\" class=\"text-center\">
		
		
	
		
		<td align='center'>$count.</td>
		<td style='text-align:left;'>$student_name </td>
		<td  style='width: 20%' ><input  ";
									if($home_work_one < 0 || $home_work_one >15){
										echo " style='background-color: red' ";}

									echo "class='form-control' type='text'  id='hw[]' name='home_work_one[]'  value=$home_work_one ></td>
		<td style='alignment:center'><b>$TOTAL</b></td>
		
		</tr>";


									$count++;

								}
							}

							?>

							</tbody>

						</table>

					</div></div>


		</div></div>
		</div>

	

<?php


	if($top_ridge->get_class_total_count($class_id) > 0)
	{
		echo "<input type='submit' title='Click to save all records'  class='btn btn-lg btn-danger save_button ui-corner-all' value='Save Record' name='home_work_results' /> ";
	}

	echo "</form>";
	
	//}
	}
	
	
	if(isset($marks_out_of_bounds_exception))
	{
		 echo "<script type='text/javascript'>
			alert('ERROR: Total marks entered exceeds the maximum of 20 for some records.Please check and re-submit.');
		</script>";
	}


	?> 
