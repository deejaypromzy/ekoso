<?PHP include("../includes/main_class.php");

if(isset($_GET['subject_id']))
{
		$subject_id = $_GET['subject_id'];	
?>


   
                  <td  align="left" nowrap="nowrap">Select Class:</td>
                  <td align="left"><?php  //$top_ridge->subjects(5, 0);
			$query = mysqli_query($top_ridge_db_connection,"SELECT classes.class_id, classes.class_name FROM classes,class_subjects
			 WHERE classes.class_id = class_subjects.class_id AND class_subjects.subject_id = {$subject_id}  AND 
			 classes.class_status = 1 AND class_subjects.status=1 ORDER BY classes.class_id ASC ");	
				confirm_query($query);
				if(mysqli_num_rows($query) > 0)
				{
						while($rows = mysqli_fetch_assoc($query))
						{
							$class_id = $rows['class_id'];	
							$class_name = $rows['class_name'];
						echo "	<span class=\"form-check\">
		<label class=\"custom-control custom-checkbox\">
			<input type=\"checkbox\" class=\"custom-control-input\" required>
			<span class=\"custom-control-indicator\"></span>
			<span class=\"custom-control-description\">$class_name</span>
		</label>
	</span>";
//							echo "<input class='form-control' type='checkbox' name='class_ids[]' value=$class_id />$class_name<br/>";
						}
				}
			
			?></td>

                  
   <?php }?>        
              
  