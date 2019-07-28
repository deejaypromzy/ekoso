<?PHP include("main_class.php");

if(isset($_GET['class_id']))
{
		$class_id = $_GET['class_id'];	
		$staff_id = $top_ridge->staffid;
		if($top_ridge->staff_job_type == 1)
		{
			$query = mysqli_query($top_ridge_db_connection,"SELECT subjects.subject_name, subjects.subject_id FROM subjects,class_subjects 
			WHERE class_subjects.subject_id = subjects.subject_id AND class_subjects.status = 1 AND class_subjects.class_id 
			 = {$class_id} ORDER BY subject_name ASC ");	
			 confirm_query($query);
		}else{
			$query = mysqli_query($top_ridge_db_connection,"SELECT subjects.subject_name, subjects.subject_id FROM subjects,class_teachers
			WHERE class_teachers.subject_id = subjects.subject_id AND subjects.subject_status = 1 AND class_teachers.class_id 
			 = {$class_id} AND class_teachers.staff_id={$staff_id} ORDER BY subject_name ASC ");	
			 confirm_query($query);
			
			}
}
?>
<div class="row form-group">

<strong>SELECT SUBJECT: </strong>
  <select name="subject_id" class="form-control capitalize">
    <?php
                  		if(mysqli_num_rows($query) > 0 )
						{
							while ($row = mysqli_fetch_assoc($query))
							{
								$subject_name = strtoupper ($row['subject_name']);
								$subject_id = $row['subject_id'];
								echo "<option value=$subject_id>$subject_name</option>";
							}	
						}
				  ?>
  </select>
</div>