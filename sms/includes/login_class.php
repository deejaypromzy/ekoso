<?php ob_start();
include ("functions.php");
include ("top_ridge_db_connection.php");



class  login
{
public $title = "iSchool";
public $footer = "&copy Copyright | Ekoso Presby JHS | That they all may be one";
public $main_title = "School Mangt. System";
public $slogan = "That they all may be one";
		
		
		function __construct()
		{
				date_default_timezone_set("Africa/Accra"); 
				if(!isset($_SESSION)){session_start();}
		}
		
		
	function  error_message($message)
	{

		echo "<div class='alert alert-danger alert-dismissable'>
                                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
							$message </div>";
	}
	
	
		function  success_message($message)
	{

		echo "<div class='alert alert-success alert-dismissable'>
                                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
							$message </div>";

//		echo "<p>
//				 <div class='sucess_message'>   $message
//				 <a href='#' class='close' data-dismiss='alert'>×</a></div>
//				</p>";
	}
	
		function login($username, $password,$conn)
		{
			$user_info = mysqli_query($conn,"SELECT  staff.id, staff.staff_id, concat(staff.first_name, ' ', 
			staff.last_name)AS staff_name ,staff.job_type, users.username,  users.user_id AS USER_LOGIN_ID FROM users,
			 staff WHERE staff.id = users.staff_id  
			AND users.username= '$username' AND users.password = '$password'  and staff.staff_status=1 ");
			confirm_query($user_info);

			if(mysqli_num_rows ($user_info) == 0)
			{
				//CHECK IF USER IS A PARENT
				$user_info = mysqli_query($conn,"SELECT  parents.parent_id, parents.parent_number, concat(parents.first_name, ' ', parents.last_name)AS parent_name ,users.username,  users.user_id AS USER_LOGIN_ID 
				FROM users, parents WHERE parents.parent_id = users.parent_id  
				AND users.username= '$username' AND users.password = '$password'  and parents.parent_status=1 ");
				confirm_query($user_info);
				if(mysqli_num_rows ($user_info) == 0)
				{
					header("location:index.php?login=failed");
				}else{
				  $rows = mysqli_fetch_assoc($user_info);
					$_SESSION['PARENT_ID'] =  $rows['parent_id'];
					$parent_id=  $rows['parent_id'];
					$_SESSION['PARENT_NUMBER'] =  $rows['parent_number'];
					$_SESSION['PARENT_NAME'] =  $rows['parent_name'];
					$_SESSION['USER_LOGIN_ID'] =  $rows['USER_LOGIN_ID'];
					$_SESSION['USERNAME'] =  $rows['username'];
					$user_id =  $rows['USER_LOGIN_ID'];
					
					$_SESSION['JOB_TYPE'] = '';
					$_SESSION['STAFF_ID'] ='';
					$_SESSION['STAFFID'] ='';
					 $_SESSION['STAFF_NAME']='';
					
					
					//check last login
					$last_logon = mysqli_query($conn,"SELECT *
					 FROM login_info WHERE parent_id = {$parent_id}");
					confirm_query($last_logon);
					$date_set  = date('D dS M, Y ');
					$time_set = date('g:i A');
					$current_logon_date = $date_set." ". $time_set;
					
						if(mysqli_num_rows($last_logon) > 0 )
						{
							$record = mysqli_fetch_assoc($last_logon);
							
							 $_SESSION['LAST_LOGON'] =  $record['last_logon_date_time'];
							 $_SESSION['CURRENT_LOGON'] =  $record['current_logon_date_time'];
							 /////update current logon date and time
							 $update_logon = mysqli_query($conn,"UPDATE  login_info SET current_logon_date_time = '$current_logon_date'  
							 WHERE parent_id =  {$parent_id}");
							confirm_query($update_logon);
								
						}else{
							/////insert into login for the first time
							$first_time_logon = mysqli_query($conn,"INSERT INTO login_info (parent_id,current_logon_date_time ) 
							VALUES ('$parent_id', '$current_logon_date')  ");
							confirm_query($first_time_logon);
							
							}
				  header("location:sms/parent_module.php");
				
				}
			
			}else{
				
					$rows = mysqli_fetch_assoc($user_info);
					$_SESSION['STAFF_ID'] =  $rows['staff_id'];
					$_SESSION['STAFFID'] =  $rows['id'];
					$_SESSION['USER_LOGIN_ID'] =  $rows['USER_LOGIN_ID'];
					$_SESSION['USERNAME'] =  $rows['username'];
					$_SESSION['STAFF_NAME'] =  $rows['staff_name'];
					$_SESSION['JOB_TYPE'] =  $rows['job_type'];
					
					$user_id =  $rows['USER_LOGIN_ID'];
					
					
					//check last login
					$last_logon = mysqli_query($conn,"SELECT user_id, last_logon_date_time,current_logon_date_time 
					 FROM login_info WHERE user_id = {$user_id}");
					confirm_query($last_logon);
					$date_set  = date('D dS M, Y ');
					$time_set = date('g:i A');
					$current_logon_date = $date_set." ". $time_set;
					
				if(mysqli_num_rows($last_logon) > 0 )
				{
					$record = mysqli_fetch_assoc($last_logon);
					
					 $_SESSION['LAST_LOGON'] =  $record['last_logon_date_time'];
					 $_SESSION['CURRENT_LOGON'] =  $record['current_logon_date_time'];
					 /////update current logon date and time
					 $update_logon = mysqli_query($conn,"UPDATE  login_info SET current_logon_date_time = '$current_logon_date'  WHERE user_id =  {$user_id}");
					confirm_query($update_logon);
						
				}else{
					/////insert into login for the first time
					$first_time_logon = mysqli_query($conn,"INSERT INTO login_info (user_id,current_logon_date_time )  VALUES ('$user_id', '$current_logon_date')  ");
					confirm_query($first_time_logon);
					
					}
					
					
					
					
					
					
					switch ($rows['job_type'])
					{
							case 0:
							header("location:main/dashboard.php");
							break; 
							
							
							case 1;
							header("location:main/dashboard.php");
							break; 
							
							
							default:
							header("location:./index.php");
							
					}
					
				
			}
		}
}

$login = new login();
?>














