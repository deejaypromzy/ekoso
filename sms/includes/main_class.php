<?php ob_start();//error_reporting(E_ALL ^ E_NOTICE); 
error_reporting(error_reporting() & (-1 ^ E_DEPRECATED)); 
include ("functions.php");
include ("top_ridge_db_connection.php");
/*
if a new class is added, check the ff

*/
//strtoupper

class top_ridge
{

	public $address = "P.O. BOX AS 259, ASAMANKESE";

	public $location = "EKOSO";
	public $telephone_details = "-";


	public $title = "eSchool";
	public $footer = "&copy Copyright | Ekoso Presby JHS A & B  | That They All May Be One";
	public $main_title = "Ekoso Presby JHS A & B ";
	public $slogan = "That They All May Be One";
	public $conn;


	public $staff_id;
	public $staff_username;
	public $staff_job_type;
	public $last_logon_date_time;
	public $current_logon_date_time;
	public $staff_login_id;
	private $total_count;
	public $current_term_id;
	public $current_mock_id;
	public $academicYear;
	public $academicTerm;
	public $date_of_vacation;
	public $date_of_resumption;
	public $staff_name;
	public $CURRENT_TERM;
	public $number_of_weeks;
	public $last_staff_id;
	public $last_student_id;
	public $staffid;
	public $immedaite_past_term_id;
	public $immedaite_past_academic_term;
	public $query;
	public $term_start_date;
	public $current_term_start_date;
	public $current_term_end_date;

	function __construct($connection)
	{


		if (!isset($_SESSION)) {
			session_start();
		}
		if (!isset($_SESSION['USERNAME'])) {
			header("location:../index.php");
		}
		//$this->staff_id = $_SESSION['STAFF_ID'];
		$this->staff_username = $_SESSION['USERNAME'];
		$this->staff_login_id = $_SESSION['USER_LOGIN_ID'];
		$this->staff_job_type = $_SESSION['JOB_TYPE'];
		$this->staffid = $_SESSION['STAFFID'];
		$this->conn = $connection;
		if (isset($_SESSION['LAST_LOGON'])) {

			$this->last_logon_date_time = $_SESSION['LAST_LOGON'];
		}
		$this->staff_name = $_SESSION['STAFF_NAME'];
		//$this->current_logon_date_time = $_SESSION['CURRENT_LOGON'];

		if (isset($_SESSION['CURRENT_LOGON'])) {

			$this->last_logon_date_time = $_SESSION['CURRENT_LOGON'];
		}


		date_default_timezone_set("Africa/Accra");
		date_default_timezone_set('GMT');


		///////Current term info
		$get_current_term_id = mysqli_query($this->conn, "SELECT term_id, start_date, vacation_date FROM term_settings ORDER BY term_id DESC LIMIT 1");
		confirm_query($get_current_term_id);
		$result = mysqli_fetch_assoc($get_current_term_id);
		$this->current_term_start_date = $result['start_date'];
		$this->current_term_end_date = $result['vacation_date'];

		$this->current_term_id = $result['term_id'];
		if (mysqli_num_rows($get_current_term_id) != 0) {
			$this->term_details($this->current_term_id);
		}
		$this->immedaite_past_academic_calender();

		///current mock
		$current_mock_id = mysqli_query($this->conn, "SELECT mock_id FROM std_mock_settings ORDER BY mock_id DESC LIMIT 1");
		confirm_query($current_mock_id);
		$result = mysqli_fetch_assoc($current_mock_id);
		$this->current_mock_id = $result['mock_id'];


		$get_previous_resumption = mysqli_query($this->conn, "SELECT resumption_date FROM term_settings ORDER BY term_id DESC LIMIT 2");
		confirm_query($get_previous_resumption);
		$result = mysqli_fetch_assoc($get_previous_resumption);  ///fetch data we dont need
		$fetch_data_we_need = mysqli_fetch_assoc($get_previous_resumption);  ///fetch data we need
		$this->term_start_date = $fetch_data_we_need['resumption_date'];


		/*-----------------------------START OPENING BALANCES--------------------------------*/
		///get fee owing
		$term_id = $this->current_term_id;
		$get_fee_owing = mysqli_query($this->conn, "SELECT  sum(balance) AS fee_owing FROM ca_student_validated_balances ");
		confirm_query($get_fee_owing);

		$data_set = mysqli_fetch_assoc($get_fee_owing);
		$sum_fee_owing = $data_set['fee_owing'];

		$update_opening_bal = mysqli_query($this->conn, "update ca_opening_balance set opening_balance='$sum_fee_owing' , closing_balance='$sum_fee_owing' ,dr_cr='D', term_id='$term_id'  where name='fees owing' ");
		confirm_query($update_opening_bal);


///BANKS

		$get_all_banks = mysqli_query($this->conn, "SELECT * FROM bank_accounts  WHERE  delete_status='A'  ORDER BY   id ASC  ");
		confirm_query($get_all_banks);
		$total_bank_balance = 0;
		$actual_bank_balance = 0;
		while ($a = mysqli_fetch_assoc($get_all_banks)) {
			$bankid = $a['id'];

			$account_balance = $a['account_balance'];

			$term_start_date = $this->current_term_start_date;
			$opening_bal_details = $this->get_account_opening_bal($bankid, 'cash at bank');

			$dr_cr_indicator = $opening_bal_details['dr_cr'];

			if ($dr_cr_indicator == 'C') {
				$bank_op = '-' . $this->get_format_number(abs($opening_bal_details['opening_balance']));

			} else {

				$bank_op = $this->get_format_number(abs($opening_bal_details['opening_balance']));
			}

			$total_bank_balance += $bank_op;


			/*GET SCHOOL FEES PAYMENT*/
			$get_all_term_payment = mysqli_query($this->conn, "SELECT * FROM  student_fee_payment WHERE term_id='$term_id'  and delete_status='A' 
				and payment_type='B' and bank_id='$bankid'  order by id   asc ");
			confirm_query($get_all_term_payment);

			$total = 0;
			while ($a = mysqli_fetch_assoc($get_all_term_payment)) {

				$amount_paid = $this->get_format_number($a['amount_paid']);

				$dr_cr = $a['txn_type'];


				if ($dr_cr == 'C') {
					$amount_paid = $this->get_format_number(-1 * $amount_paid);

				} else {
					$amount_paid = $this->get_format_number($amount_paid);

				}
				$total_bank_balance += $amount_paid;

			}

			///get other bank deposit transactions
			$other_deposits = mysqli_query($this->conn, "SELECT description, payer , amount, date, txn_type  from ca_other_bank_deposits
			   where bank_id=$bankid and delete_status='A' and term_id='$term_id'   order by id asc   ");
			confirm_query($other_deposits);

			while ($rows = mysqli_fetch_assoc($other_deposits)) {

				$description = $rows['description'] . ' - ' . $rows['payer'];

				$amount = $rows['amount'];
				$date = $rows['date'];
				$txn_type = $rows['txn_type'];


				if ($txn_type == 'C') {
					//$amount  = '-'.$amount ;
					$amount = $this->get_format_number(-1 * $amount);

				} else {
					$amount = $this->get_format_number($amount);

				}

				$total_bank_balance += $amount;


			}
			$update_bank_closing_bal = mysqli_query($this->conn, "update ca_opening_balance set closing_balance='$total_bank_balance' , 
			    dr_cr='D' , term_id='$term_id'  where name='cash at bank' and op_id=$bankid			");
			confirm_query($update_bank_closing_bal);
			$total_bank_balance = 0;

		}


//CASH AT HAND


		$total_balance = 0;
		$open_bal = $this->get_opening_bal(0, 'cash at hand');
		$dr_cr = $open_bal['dr_cr'];
		$cash_at_hand_opeing_bal = abs($open_bal['opening_balance']);
		if ($dr_cr == 'C') {
			$cash_at_hand_opeing_bal = -1 * $cash_at_hand_opeing_bal;
		}

		$cash_at_hand_opeing_bal = $this->get_format_number($cash_at_hand_opeing_bal);
		$total_balance += $cash_at_hand_opeing_bal;


		$get_all_term_payment = mysqli_query($this->conn, "SELECT * FROM  student_fee_payment WHERE term_id ='$term_id'  and delete_status='A' 
and payment_type='C'  and delete_status='A' order by id  asc ");
		confirm_query($get_all_term_payment);

		$no = 2;

		while ($a = mysqli_fetch_assoc($get_all_term_payment)) {
			$txn_type = $a['txn_type'];

			$amount_paid = $this->get_format_number($a['amount_paid']);


			$total_balance += $amount_paid;


		}


		///get other cash at hand  transactions eg. from bank transfer
		$other_cash_at_hand_txn = mysqli_query($this->conn, "SELECT description, payer , amount, date, txn_type 
			   from ca_cash_at_hand_txn
			   where delete_status='A' and term_id='$term_id'   order by id asc   ");
		confirm_query($other_cash_at_hand_txn);

		while ($rows = mysqli_fetch_assoc($other_cash_at_hand_txn)) {

			$description = $rows['description'] . ' - ' . $rows['payer'];

			$amount = $this->get_format_number($rows['amount']);

			$txn_type = $rows['txn_type'];


			if ($txn_type == 'C') {
				$amount = $this->get_format_number(-1 * $amount);

			}

			$total_balance += $amount;


		}

		$update_bank_closing_bal = mysqli_query($this->conn, "update ca_opening_balance set closing_balance='$total_balance' , 
			    dr_cr='D' , term_id='$term_id'  where name='cash at hand' 			");
		confirm_query($update_bank_closing_bal);


//PETTY CASH
		$total_balance = 0;
		$open_bal = $this->get_opening_bal(0, 'petty cash');
		$dr_cr = $open_bal['dr_cr'];
		$cash_at_hand_opeing_bal = abs($open_bal['opening_balance']);
		if ($dr_cr == 'C') {
			$cash_at_hand_opeing_bal = -1 * $cash_at_hand_opeing_bal;
		}

		$cash_at_hand_opeing_bal = $this->get_format_number($cash_at_hand_opeing_bal);
		$total_balance += $cash_at_hand_opeing_bal;

		$get_all_term_payment = mysqli_query($this->conn, "SELECT * FROM  ca_petty_cash_txn WHERE term_id='$term_id'  and delete_status='A' 
 order by id  asc ");
		confirm_query($get_all_term_payment);


		while ($a = mysqli_fetch_assoc($get_all_term_payment)) {

			$txn_type = $a['txn_type'];

			$amount_paid = $a['amount'];

			if ($txn_type == 'C') {
				$amount_paid = $this->get_format_number(-1 * $amount_paid);


			}
			$total_balance += $amount_paid;

		}

		$update_petty_cash_closing_bal = mysqli_query($this->conn, "update ca_opening_balance set closing_balance='$total_balance' , 
			    dr_cr='D', term_id='$term_id'  where name='petty cash' 			");
		confirm_query($update_petty_cash_closing_bal);


//ASSETS
		$get_assets = mysqli_query($this->conn, "SELECT id, asset_name FROM ca_assets  WHERE delete_status='A'  ");
		confirm_query($get_assets);


		while ($row_fetch = mysqli_fetch_assoc($get_assets)) {
			$total_closing_balance = 0;
			$id = $row_fetch['id'];
			$asset_name = $row_fetch['asset_name'];

			$open_bal = $this->get_opening_bal($id, 'asset');
			$dr_cr = $open_bal['dr_cr'];
			$asset_opening_balance = abs($open_bal['opening_balance']);
			if ($dr_cr == 'C') {
				$asset_opening_balance = -1 * $asset_opening_balance;
			}

			$total_closing_balance += $asset_opening_balance;

			//get other assets transactions
			$get_asset_txn = mysqli_query($this->conn, "SELECT * FROM  ca_asset_txn where asset_id =$id   and term_id='$term_id' order by id asc");
			confirm_query($get_asset_txn);
			while ($row_results = mysqli_fetch_assoc($get_asset_txn)) {
				$amount = $row_results['amount'];
				if ($row_results['txn_type'] == 'C') {
					$amount = $this->get_format_number(-1 * $amount);

				}

				$total_closing_balance += $amount;
			}

			$update_asset_closing_bal = mysqli_query($this->conn, "update ca_opening_balance set closing_balance='$total_closing_balance' , 
			    dr_cr='D', term_id='$term_id'  where name='asset' 	 and op_id=$id		");
			confirm_query($update_asset_closing_bal);


		}


		///LIABILITIES

		$get_liabities = mysqli_query($this->conn, "SELECT id FROM ca_liability  WHERE delete_status='A'  ");
		confirm_query($get_liabities);


		while ($row_fetch = mysqli_fetch_assoc($get_liabities)) {
			$total_liability_closing_balance = 0;
			$id = $row_fetch['id'];


			$open_bal = $this->get_opening_bal($id, 'liability');
			$dr_cr = $open_bal['dr_cr'];
			$liability_opening_balance = abs($open_bal['opening_balance']);
			if ($dr_cr == 'D') {
				$liability_opening_balance = -1 * $liability_opening_balance;
			}

			$total_liability_closing_balance += $liability_opening_balance;

			//get other liability transactions
			$get_liability_txn = mysqli_query($this->conn, "SELECT * FROM  ca_liability_txn where liability_id =$id   and term_id='$term_id' order by id asc");
			confirm_query($get_liability_txn);
			while ($row_results = mysqli_fetch_assoc($get_liability_txn)) {
				$amount = $row_results['amount'];
				if ($row_results['txn_type'] == 'D') {
					$amount = $this->get_format_number(-1 * $amount);

				}

				$total_liability_closing_balance += $amount;
			}

			$update_liabilty_closing_bal = mysqli_query($this->conn, "update ca_opening_balance set closing_balance='$total_liability_closing_balance' , 
			    dr_cr='C', term_id='$term_id'  where name='liability' 	 and op_id=$id		");
			confirm_query($update_liabilty_closing_bal);


		}


		///EQUITY

		$get_equities = mysqli_query($this->conn, "SELECT id, equity_name FROM ca_equity   WHERE delete_status='A'  ");
		confirm_query($get_equities);


		while ($row_fetch = mysqli_fetch_assoc($get_equities)) {
			$total_equity_closing_balance = 0;
			$id = $row_fetch['id'];
			$equity_name = $row_fetch['equity_name'];

			$open_bal = $this->get_opening_bal($id, 'equity');
			$dr_cr = $open_bal['dr_cr'];
			$equity_opening_balance = abs($open_bal['opening_balance']);
			if ($dr_cr == 'D') {
				$equity_opening_balance = -1 * $equity_opening_balance;
			}

			$total_equity_closing_balance += $equity_opening_balance;


			if (strtolower($equity_name) == 'surplus') {


				$sum_of_income = 0;
				$get_sum_income_txn = mysqli_query($this->conn, "SELECT * FROM ca_income_txn  
							WHERE  term_id='$term_id'      and delete_status='A'  ");
				confirm_query($get_sum_income_txn);
				$total = 0;
				while ($a = mysqli_fetch_assoc($get_sum_income_txn)) {
					$income_amount = $a['amount'];
					$txn_type = $a['txn_type'];

					if ($txn_type == 'C') {
						$sum_of_income += $income_amount;
					} else if ($txn_type == 'D') {
						$sum_of_income -= $income_amount;
					}

				}


				$get_expense_txn = mysqli_query($this->conn, "SELECT *   FROM ca_expense_txn  
							WHERE    term_id='$term_id' and  delete_status='A'  ");
				confirm_query($get_expense_txn);


				$sum_of_expense = 0;
				while ($a = mysqli_fetch_assoc($get_expense_txn)) {
					$expense_amount = $a['amount'];
					$txn_type = $a['txn_type'];

					if ($txn_type == 'D') {
						$sum_of_expense += $expense_amount;
					} else if ($txn_type == 'C') {
						$sum_of_expense -= $expense_amount;
					}

				}
				$total_fees_period = $this->get_school_fees_with_period($this->current_term_start_date, $this->current_term_end_date, $term_id);
				$surplus = $this->get_format_number(($sum_of_income + $total_fees_period) - $sum_of_expense);
				$total_balance += $surplus;
				$total_equity_closing_balance += $surplus;

			}


			//get other equity  transactions

			$get_equity_txn = mysqli_query($this->conn, "SELECT * FROM  ca_equity_txn where equity_id =$id   and term_id='$term_id' order by id asc");
			confirm_query($get_equity_txn);
			while ($row_results = mysqli_fetch_assoc($get_equity_txn)) {
				$amount = $row_results['amount'];
				if ($row_results['txn_type'] == 'D') {
					$amount = $this->get_format_number(-1 * $amount);

				}

				$total_equity_closing_balance += $amount;


			}

			$update_equity_closing_bal = mysqli_query($this->conn, "update ca_opening_balance set closing_balance='$total_equity_closing_balance' , 
			    dr_cr='D', term_id='$term_id'  where name='equity' 	 and op_id=$id		");
			confirm_query($update_equity_closing_bal);


		}


		/*-----------------------------END OPENING BALANCES--------------------------------*/


		$today_date = date('Y-m-d');
		$hour = date('g') + 1;
		$system_time = $hour . ':' . date('i');

		$msg_birthday = mysqli_query($this->conn, "SELECT * FROM msg_birthday_schedules");
		confirm_query($msg_birthday);
		$row = mysqli_fetch_assoc($msg_birthday);

		$message_send_time = $row['time'];
		//$message = "Hello Blessing" . $row['message'];


		/*

				if($system_time == $message_send_time)
				{
						$get_all_group_phone_numbers = mysqli_query($this->conn,"SELECT telephone_no, dob, fname FROM members
						 ");
						confirm_query($get_all_group_phone_numbers);


						while($rows_set = mysqli_fetch_assoc($get_all_group_phone_numbers))
						{

							$phone_number =  $rows_set['telephone_no'];
							$fname =  strtoupper($rows_set['fname']);
							$message = "Hello ". $fname . ' '. $row['message'];
							$url="http://bulk.mnotify.net/smsapi?key=$key&to=$phone_number&msg=$message&sender_id=$sender_id";
						   $result=file_get_contents($url); //call url and store result;

						  /* switch($result){
							case "1000":
							echo "Message sent";
							break;
							case "1002":
							echo "Message not sent";
							break;
							case "1003":
							echo "You don't have enough balance";
							break;
							case "1004":
							echo "Invalid API Key";
							break;
							case "1005":
							echo "Phone number not valid";
							break;
							case "1006":
							echo "Invalid Sender ID";
							break;
							case "1008":
							echo "Empty message";
							break;
						}
						//}
					//echo mysqli_num_rows($get_all_group_phone_numbers );
				    }

				*/

	}


	function send_email($title, $from, $to, $message_body)
	{


		//require the swiftmailer library
		require_once('swiftmailer/lib/swift_required.php');

//create the actual message to be sent with or without html
		//$mail_message.='<h1>Welcome</h1>Your registration Code is ';

		// Create the Transport engine or path for sending
		//using gmail here.
		$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
			->setUsername('ansah0015@gmail.com')
			->setPassword('clems$kkk000');
//create the mailer
		$mailer = Swift_Mailer::newInstance($transport);
		// Create the message
		$message = Swift_Message::newInstance();

		// Give the message a subject
		$message->setSubject($title)
//Set the From address with an associative array email_address=> name of sender
			->setFrom(array($from => 'Prudent Start Company Ltd.'))
			// Set the To addresses with an associative array
			->setTo(array($to => 'Prudent Star'))
			// Give it a body
			->setBody($message_body, 'text/html')
			// And optionally an alternative body in case the mail server cannot process the html in the message
			->addPart(strip_tags($message_body), 'text/plain');

		$mailer->send($message);

		if ($mailer) {
			echo $this->print_message(1, 'Mail sent successfully.');
		} else {
			echo $this->print_message(0, 'Mail failed. Please try again later.');
		}
	}


	function jhs_3_teacher()
	{
		$staff_id = $this->staffid;

		$jhs_3_class_id = $this->jhs_3_class_id();

		$jhs_3_teacher = mysqli_query($this->conn, "SELECT staff_id FROM class_teachers WHERE staff_id = {$staff_id} AND class_id = '$jhs_3_class_id' LIMIT 1");
		confirm_query($jhs_3_teacher);

		if (mysqli_num_rows($jhs_3_teacher) > 0) {
			return true;
		} else {
			return false;
		}
	}

	function get_term_of_mock($mock_id)
	{
		$QUERY = mysqli_query($this->conn, "SELECT term_id FROM std_mock_settings WHERE mock_id = {$mock_id}");
		confirm_query($QUERY);
		$row = mysqli_fetch_assoc($QUERY);
		return $row['term_id'];
	}

	function get_total_expenses_for_period_by_expenses_id($expenses_id, $start_date, $end_date)
	{
		$query = mysqli_query($this->conn, "SELECT sum(amount) as total FROM expenses where (date_paid BETWEEN '$start_date'  AND  '$end_date')  and delete_status='A' and expense_id=$expenses_id    ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['total'];
	}


	function get_income_name($income_id)
	{
		$query = mysqli_query($this->conn, "SELECT income_name  FROM ca_income  
	WHERE id='$income_id'  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['income_name'];
	}

	function get_asset_name($asset_id)
	{
		$query = mysqli_query($this->conn, "SELECT asset_name  FROM ca_assets  
	WHERE id='$asset_id'  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['asset_name'];
	}

	function get_liability_name($line_id)
	{
		$query = mysqli_query($this->conn, "SELECT liability_name  FROM ca_liability  
	WHERE id='$line_id'  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['liability_name'];
	}

	function get_equity_name($line_id)
	{
		$query = mysqli_query($this->conn, "SELECT equity_name  FROM ca_equity  
	WHERE id='$line_id'  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['equity_name'];
	}


	function get_petty_cash_balance()
	{
		$term_id = $this->current_term_id;
		$total_balance = 0;
		$open_bal = $this->get_opening_bal(0, 'petty cash');
		$dr_cr = $open_bal['dr_cr'];
		$cash_at_hand_opeing_bal = abs($open_bal['opening_balance']);
		if ($dr_cr == 'C') {
			$cash_at_hand_opeing_bal = -1 * $cash_at_hand_opeing_bal;
		}

		$cash_at_hand_opeing_bal = $this->get_format_number($cash_at_hand_opeing_bal);
		$total_balance += $cash_at_hand_opeing_bal;


		$get_all_term_payment = mysqli_query($this->conn, "SELECT * FROM  ca_petty_cash_txn WHERE term_id='$term_id'  and delete_status='A' 
 order by id  asc ");
		confirm_query($get_all_term_payment);


		while ($a = mysqli_fetch_assoc($get_all_term_payment)) {
			$txn_type = $a['txn_type'];

			$amount_paid = $a['amount'];


			if ($txn_type == 'C') {
				$amount_paid = $this->get_format_number(-1 * $amount_paid);
			}

			$total_balance += $amount_paid;


		}

		return $this->get_format_number($total_balance);


		/* $get_bal_re = mysqli_query($this->conn,"select sum(amount) as total_receipt from ca_petty_cash_txn
   where txn_type='D'  and delete_status='A' and term_id='$term_id' ");
   confirm_query($get_bal_re);

   $a = mysqli_fetch_assoc($get_bal_re);
   $total_receipt = $a['total_receipt'];


    $get_bal_expenses= mysqli_query($this->conn,"select sum(amount) as total_expenses from ca_petty_cash_txn
   where txn_type='C'  and delete_status='A'  ");
   confirm_query($get_bal_expenses);

   $c = mysqli_fetch_assoc($get_bal_expenses);
   $total_expenses = $c['total_expenses'];

   return $this->get_format_number($total_receipt - $total_expenses);*/


	}

	function get_total_expense_txn($expense_id)
	{

		$term_id = $this->current_term_id;
		$query = mysqli_query($this->conn, "SELECT count(*) as total_tnx  FROM ca_expense_txn  
	WHERE expense_id='$expense_id'   and  term_id= $term_id   and  delete_status='A'  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['total_tnx'];
	}

	function get_account_opening_bal($id, $name)
	{

		$query = mysqli_query($this->conn, "SELECT * from ca_opening_balance  
	WHERE op_id='$id'   and  name= '$name'  ");
		confirm_query($query);
		return mysqli_fetch_assoc($query);

	}

	function get_grade_details($grade_id)
	{
		$query = mysqli_query($this->conn, "SELECT * from py_grades   where id='$grade_id'");
		confirm_query($query);

		return mysqli_fetch_assoc($query);


	}


	function get_employee_allowances($grade_id, $gross_monthly_salary)
	{
		$get_all_allowances = mysqli_query($this->conn, "SELECT * from  py_allowance where delete_status='A' and grade_id='$grade_id' order by 
			  name asc ");
		confirm_query($get_all_allowances);

		$total_allowance = 0;
		while ($rs_allowances = mysqli_fetch_assoc($get_all_allowances)) {

			$rate = $rs_allowances['rate'];
			$applied_as_percentage = $rs_allowances['applied_as_percentage'];

			if ($applied_as_percentage == 'Y') {
				$rate_on_gross = $this->get_format_number(($rate / 100) * $gross_monthly_salary);

			} else {
				$rate_on_gross = $this->get_format_number($rate);
			}

			$total_allowance += $rate_on_gross;


		}

		return $this->get_format_number($total_allowance);

	}


	function get_payroll_deductions($gross_monthly_salary)
	{
		$get_all_deductions = mysqli_query($this->conn, "SELECT * FROM  py_deductions WHERE delete_status='A'
				   ORDER BY id ASC ");
		confirm_query($get_all_deductions);

		$total_employee_contribution = 0;
		$total_employer_contribution = 0;
		while ($rs_deductions = mysqli_fetch_assoc($get_all_deductions)) {

			$employer_contribution = $rs_deductions['employer_contribution'];
			$employee_contribution = $rs_deductions['employee_contribution'];
			$applied_as_percentage = $rs_deductions['applied_as_percentage'];

			if ($applied_as_percentage == 'Y') {
				$employee_cont = $this->get_format_number(($employee_contribution / 100) * $gross_monthly_salary);
				$employer_cont = $this->get_format_number(($employer_contribution / 100) * $gross_monthly_salary);
			} else {
				$employee_cont = $this->get_format_number($employee_contribution);
				$employer_cont = $this->get_format_number($employer_contribution);
			}

			$total_employee_contribution += $employee_cont;
			$total_employer_contribution += $employer_cont;
		}

		return array($this->get_format_number($total_employee_contribution),
			$this->get_format_number($total_employer_contribution));

	}


	function get_total_txn($id, $table_name, $primary_id)
	{

		$term_id = $this->current_term_id;
		$query = mysqli_query($this->conn, "SELECT count(*) as total_tnx  FROM {$table_name}  
	WHERE {$primary_id}  ='$id'   and  term_id= $term_id   and  delete_status='A'  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['total_tnx'];
	}


	function get_sum_of_expense($expense_id, $start_date, $end_date, $term_id)
	{

		//$term_id = $this->current_term_id;
		$query = mysqli_query($this->conn, "SELECT *   FROM ca_expense_txn  
	WHERE expense_id='$expense_id'   and  date >='$start_date'  and  date <='$end_date'   and  term_id='$term_id' and  delete_status='A'  ");
		confirm_query($query);
		//$a =  mysqli_fetch_assoc($query);

		$total = 0;
		while ($a = mysqli_fetch_assoc($query)) {
			$amount = $a['amount'];
			$txn_type = $a['txn_type'];

			if ($txn_type == 'D') {
				$total += $amount;
			} else if ($txn_type == 'C') {
				$total -= $amount;
			}

		}
		return $this->get_format_number($total);


		//return $this->get_format_number( $a['total_tnx']);
	}

	function fees_owing()
	{
		$term_id = $this->current_term_id;
		$get_fees = mysqli_query($this->conn, "SELECT class_id, class_name FROM classes WHERE class_status=1 ORDER BY class_name ASC ");
		confirm_query($get_fees);

		$total = 0;
		$total_fee_owing = 0;
		while ($a = mysqli_fetch_assoc($get_fees)) {
			$class_id = $a['class_id'];
			$class_name = $a['class_name'];
			$total_class_fees = $this->get_class_total_fees($class_id);

			$total += $total_class_fees;


			//$total_paid  = mysqli_query($this->conn,"SELECT sum()");
			$get_sum_paid = mysqli_query($this->conn, "SELECT SUM(amount_paid) AS TOTAL_PAID from student_fee_payment 
			  WHERE  term_id = $term_id  and class_id=$class_id");
			confirm_query($get_sum_paid);
			$result = mysqli_fetch_assoc($get_sum_paid);

			$TOTAL_PAID = $this->get_format_number($result['TOTAL_PAID']);

			$bal = $this->get_format_number($total_class_fees - $TOTAL_PAID);
			$total_fee_owing += $bal;

		}

		return $this->get_format_number($total_fee_owing);
	}

	function total_incone_txn($income_id)
	{

		$term_id = $this->current_term_id;
		$query = mysqli_query($this->conn, "SELECT count(*) as total_tnx  FROM ca_income_txn  
	WHERE income_id='$income_id'   and term_id='$term_id'  and   delete_status='A'  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['total_tnx'];
	}

	function get_sum_of_income($income_id, $start_date, $end_date, $term_id)
	{

		//$term_id = $this->current_term_id;
		$query = mysqli_query($this->conn, "SELECT * FROM ca_income_txn  
	WHERE income_id='$income_id'   and  date >='$start_date'  and  date <='$end_date'   and term_id='$term_id' and   delete_status='A'  ");
		confirm_query($query);
		$total = 0;
		while ($a = mysqli_fetch_assoc($query)) {
			$amount = $a['amount'];
			$txn_type = $a['txn_type'];

			if ($txn_type == 'C') {
				$total += $amount;
			} else if ($txn_type == 'D') {
				$total -= $amount;
			}

		}
		return $this->get_format_number($total);
	}

	function get_expense_name($expense_id)
	{
		$query = mysqli_query($this->conn, "SELECT expense_name  FROM ca_expenses  
	WHERE id='$expense_id'  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['expense_name'];
	}

	function get_tax_code($tax_code_id)
	{
		$query = mysqli_query($this->conn, "SELECT *  FROM ca_tax  
	WHERE id='$tax_code_id'  ");
		confirm_query($query);
		return mysqli_fetch_assoc($query);

	}


	function get_total_classes()
	{
		$query = mysqli_query($this->conn, "SELECT count(*) AS total_class FROM classes WHERE class_status=1  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['total_class'];
	}

	function get_total_student_per_class($class_id)
	{
		$query = mysqli_query($this->conn, "SELECT count(*) as total_student_in_class from students where class_id=$class_id  and student_status=1  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['total_student_in_class'];
	}


	function get_count_cash_on_hand()
	{

		$term_id = $this->current_term_id;

		$query = mysqli_query($this->conn, "SELECT count(*) as total_cash_on_hand from 
   student_fee_payment where term_id='$term_id' and delete_status='A'  and payment_type='C'  ");
		confirm_query($query);
		$rows = mysqli_fetch_assoc($query);
		$a = $rows['total_cash_on_hand'];

		/*
	$other_deposits_count   = mysqli_query($this->conn,"SELECT count(*) as total_other_deposits_count
	from   ca_cash_at_hand_txn  where
	delete_status='A' and term_id='$term_id'    ");
	 confirm_query($other_deposits_count);
	$row =  mysqli_fetch_assoc($other_deposits_count);
	$b = $row['total_other_deposits_count'];	    */

		return $a;

	}


	function get_arrears_per_class($class_id)
	{
		$current_term_id = $this->current_term_id;

		///get  student validated balances table
		$get_total_arrears = mysqli_query($this->conn, "select sum(balance) as total_arrears from ca_student_validated_balances 
		where  term_id != $current_term_id   and class_id=$class_id ");
		confirm_query($get_total_arrears);
		$b = mysqli_fetch_assoc($get_total_arrears);
		return $this->get_format_number($b['total_arrears']);

	}


	function get_arrears($student_id)
	{
		$current_term_id = $this->current_term_id;

		///get  student validated balances table
		$get_total_arrears = mysqli_query($this->conn, "select sum(balance) as total_arrears from ca_student_validated_balances 
		where  term_id != $current_term_id    and student_id=$student_id ");
		confirm_query($get_total_arrears);
		$b = mysqli_fetch_assoc($get_total_arrears);
		return $this->get_format_number($b['total_arrears']);


		//get all previous class for the student
		// $get_previous_class_id = mysqli_query($this->conn,"SELECT class_id");
		/* $get_previous_class_id = mysqli_query($this->conn,"SELECT  class_id, term_id
	from student_term_total where term_id != $current_termID  and student_id=$student_id ");
  confirm_query($get_previous_class_id);

  $TOTAL_ARREARS_DUES  = 0;
  while($b = mysqli_fetch_assoc($get_previous_class_id))
  {
	$previous_class_id  = $b['class_id'];
	$previous_term_id  = $b['term_id'];

		  //get fees for this term-----SPECIAL BILLING
		  $get_term_fees_special_condition = mysqli_query($this->conn,"SELECT SUM(amount_due) AS TERM_FEE_AMOUNT
		  from billing_special_condition where term_id = $previous_term_id   and class_id=$previous_class_id ");
		  confirm_query($get_term_fees_special_condition);
		  $rows = mysqli_fetch_assoc($get_term_fees_special_condition);

	  if(mysqli_num_rows($get_term_fees_special_condition) > 0)
	  {
	      $TOTAL_ARREARS_DUES  += $rows['TERM_FEE_AMOUNT'];
	  }else{
		 //get fees for this term-----GENERAL BILLING
		  $get_term_fees = mysqli_query($this->conn,"SELECT SUM(amount_due) AS TERM_FEE_AMOUNT
		  from billing_general where term_id = $previous_term_id   and class_id=$previous_class_id  ");
		  confirm_query($get_term_fees);
		  $a = mysqli_fetch_assoc($get_term_fees);
		  $TOTAL_ARREARS_DUES  += $a['TERM_FEE_AMOUNT'];
	  }

  }


  ///NOW GET SUM OF ALL FEES PAID
  $get_sum_paid = mysqli_query($this->conn,"SELECT SUM(amount_paid) AS TOTAL_ARREARS_PAID from student_fee_payment
  WHERE student_id = $student_id  and   term_id != $current_termID");
   confirm_query($get_sum_paid);
   $result = mysqli_fetch_assoc($get_sum_paid);
   $TOTAL_ARREARS_PAID = $result['TOTAL_ARREARS_PAID'];

   $STUDENT_ARREARS = $TOTAL_ARREARS_DUES -  $TOTAL_ARREARS_PAID;

   return $this->get_format_number($STUDENT_ARREARS);*/
	}


	function get_class_arrears($class_id)
	{

		$current_termID = $this->current_term_id;

		$TOTAL_ARREARS_DUES = 0;
		//get fees for this term-----SPECIAL BILLING
		$get_term_fees_special_condition = mysqli_query($this->conn, "SELECT SUM(amount_due) AS TERM_FEE_AMOUNT  
		  from billing_special_condition where class_id=$class_id  and  term_id != $current_termID ");
		confirm_query($get_term_fees_special_condition);
		$rows = mysqli_fetch_assoc($get_term_fees_special_condition);

		if (mysqli_num_rows($get_term_fees_special_condition) > 0) {
			$TOTAL_ARREARS_DUES += $rows['TERM_FEE_AMOUNT'];
		} else {
			//get fees for this term-----GENERAL BILLING
			$get_term_fees = mysqli_query($this->conn, "SELECT SUM(amount_due) AS TERM_FEE_AMOUNT  
		  from billing_general where class_id=$previous_class_id  and  term_id != $current_termID ");
			confirm_query($get_term_fees);
			$a = mysqli_fetch_assoc($get_term_fees);
			$TOTAL_ARREARS_DUES += $a['TERM_FEE_AMOUNT'];
		}


		///NOW GET SUM OF ALL FEES PAID
		$get_sum_paid = mysqli_query($this->conn, "SELECT SUM(amount_paid) AS TOTAL_ARREARS_PAID from student_fee_payment 
  WHERE class_id=$class_id  and   term_id != $current_termID");
		confirm_query($get_sum_paid);
		$result = mysqli_fetch_assoc($get_sum_paid);
		$TOTAL_ARREARS_PAID = $result['TOTAL_ARREARS_PAID'];

		$STUDENT_ARREARS = $TOTAL_ARREARS_DUES - $TOTAL_ARREARS_PAID;

		return $this->get_format_number($STUDENT_ARREARS);
	}


	function get_bank_details($bank_id)
	{


		$all_banks = mysqli_query($this->conn, "SELECT * FROM bank_accounts where id='$bank_id'  ");
		confirm_query($all_banks);
		$a = mysqli_fetch_assoc($all_banks);

		//$bankid = $a['id'];
		$account_name = $a['name'];
		$account_number = $a['account_number'];
		//$account_balance = $a['account_balance'] + $acc_opening_bal;
		$account_balance = $this->get_account_balance($bank_id);
		$financial_institution = $a['financial_institution'];
		$currency = $a['currency'];
		return $bank_account_details = $account_name . ' |    ' . $account_number . ' |   ' . $financial_institution . '  |  ' . $currency . ' ' . $account_balance;


	}
	function get_format_number($number)
	{
		return number_format((float)$number, 2, '.', '');
	}

	function print_message($flag, $message)
	{


		if ($flag == 0) {
			echo "<div class='col-md-12 text-center alert alert-danger alert-dismissable'>
<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>$message</b></div>";
		} else if ($flag == 1) {
			echo "<div class='col-md-12 text-center alert alert-success alert-dismissable'>
<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>$message</b></div>";		}
		else if ($flag == 2) {
			echo "<div class='col-md-12 text-center  alert alert-warning alert-dismissable'>
<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>$message</b></div>";		}

	}



	function get_account_balance($bankid)
	{

		$term_id = $this->current_term_id;
		$total_bank_balance = 0;
		$opening_bal_details = $this->get_account_opening_bal($bankid, 'cash at bank');

		$dr_cr_indicator = $opening_bal_details['dr_cr'];

		if ($dr_cr_indicator == 'C') {
			$bank_op = '-' . $this->get_format_number(abs($opening_bal_details['opening_balance']));
		} else {

			$bank_op = $this->get_format_number(abs($opening_bal_details['opening_balance']));
		}

		$total_bank_balance += $bank_op;
		//	$actual_bank_balance +=$bank_op;
		$get_all_term_payment = mysqli_query($this->conn, "SELECT * FROM  student_fee_payment WHERE term_id='$term_id'  and delete_status='A' 
				and payment_type='B' and bank_id='$bankid'  order by id   asc ");
		confirm_query($get_all_term_payment);

		while ($a = mysqli_fetch_assoc($get_all_term_payment)) {

			$amount_paid = $this->get_format_number($a['amount_paid']);

			$dr_cr = $a['txn_type'];


			if ($dr_cr == 'C') {
				$amount_paid = $this->get_format_number(-1 * $amount_paid);
				// echo "<td style='color:red;'>  $amount_paid  </td> ";
			} else {
				$amount_paid = $this->get_format_number($amount_paid);
				//echo "<td>  $amount_paid  </td> ";
			}
			$total_bank_balance += $amount_paid;


		}

		///get other bank deposit transactions
		$other_deposits = mysqli_query($this->conn, "SELECT description, payer , amount, date, txn_type  from ca_other_bank_deposits
			   where bank_id=$bankid and delete_status='A' and term_id='$term_id'   order by id asc   ");
		confirm_query($other_deposits);

		while ($rows = mysqli_fetch_assoc($other_deposits)) {
			$amount = $rows['amount'];

			$txn_type = $rows['txn_type'];


			if ($txn_type == 'C') {
				//$amount  = '-'.$amount ;
				$amount = $this->get_format_number(-1 * $amount);

			} else {
				$amount = $this->get_format_number($amount);

			}

			$total_bank_balance += $amount;


		}

		/*   $update_temp_closing_balance = mysqli_query($this->conn,"update ca_opening_balance  set
			   temp_closing_balance=$total_bank_balance where op_id=$bankid and name='cash at bank'   ");
			   confirm_query($update_temp_closing_balance); */
		return $this->get_format_number($total_bank_balance);

		/*$acc_opening_bal=0;
	$acc_opening_bal_details =abs($this->get_opening_bal($bank_id,'cash at bank'));

$dr_cr_indicator = $acc_opening_bal_details['dr_cr'];
if($dr_cr_indicator =='C')
{
  //$acc_opening_bal = '-'.$this->get_format_number( $acc_opening_bal_details['opening_balance']);
  $acc_opening_bal =  -1 * $this->get_format_number( $acc_opening_bal_details['opening_balance']);
} else{

$acc_opening_bal =  $this->get_format_number( $acc_opening_bal_details['opening_balance']);
}



$all_banks = mysqli_query($this->conn,"SELECT * FROM bank_accounts where id='$bank_id'  ");
confirm_query($all_banks);
	while ($a = mysqli_fetch_assoc($all_banks))
	{

		return $this->get_format_number($a['account_balance'] + $acc_opening_bal);

	}*/
	}

	function get_account_info($bank_id)
	{
		$account_info = mysqli_query($this->conn, "SELECT * FROM bank_accounts where id='$bank_id'  ");
		confirm_query($account_info);
		$a = mysqli_fetch_assoc($account_info);

		return $a['name'] . ' | ' . $a['account_number'] . ' - ' . $a['financial_institution'];
	}

	function get_opening_bal($id, $name)
	{
		$op_bal = mysqli_query($this->conn, "SELECT * FROM ca_opening_balance where op_id='$id'   and name='$name'   ");
		confirm_query($op_bal);
		return mysqli_fetch_assoc($op_bal);

	}

	function get_total_bank_balance_period($date_period, $bankid)
	{


		$term_id = $this->current_term_id;
		$total_bank_balance = 0;
		$opening_bal_details = $this->get_account_opening_bal($bankid, 'cash at bank');

		$dr_cr_indicator = $opening_bal_details['dr_cr'];

		if ($dr_cr_indicator == 'C') {
			$bank_op = '-' . $this->get_format_number(abs($opening_bal_details['opening_balance']));
		} else {

			$bank_op = $this->get_format_number(abs($opening_bal_details['opening_balance']));
		}

		$total_bank_balance += $bank_op;
		//	$actual_bank_balance +=$bank_op;
		$get_all_term_payment = mysqli_query($this->conn, "SELECT * FROM  student_fee_payment WHERE term_id='$term_id'  and delete_status='A' 
				and payment_type='B' and bank_id='$bankid' and date_paid <='$date_period'   order by id   asc ");
		confirm_query($get_all_term_payment);

		while ($a = mysqli_fetch_assoc($get_all_term_payment)) {

			$amount_paid = $this->get_format_number($a['amount_paid']);

			$dr_cr = $a['txn_type'];


			if ($dr_cr == 'C') {
				$amount_paid = $this->get_format_number(-1 * $amount_paid);
				// echo "<td style='color:red;'>  $amount_paid  </td> ";
			} else {
				$amount_paid = $this->get_format_number($amount_paid);
				//echo "<td>  $amount_paid  </td> ";
			}
			$total_bank_balance += $amount_paid;


		}

		///get other bank deposit transactions
		$other_deposits = mysqli_query($this->conn, "SELECT description, payer , amount, date, txn_type  from ca_other_bank_deposits
			   where bank_id=$bankid and delete_status='A' and term_id='$term_id'  and date <='$date_period' order by id asc   ");
		confirm_query($other_deposits);

		while ($rows = mysqli_fetch_assoc($other_deposits)) {
			$amount = $rows['amount'];

			$txn_type = $rows['txn_type'];


			if ($txn_type == 'C') {
				//$amount  = '-'.$amount ;
				$amount = $this->get_format_number(-1 * $amount);

			} else {
				$amount = $this->get_format_number($amount);

			}

			$total_bank_balance += $amount;


		}
		/*
			   $update_temp_closing_balance = mysqli_query($this->conn,"update ca_opening_balance  set
			   temp_closing_balance=$total_bank_balance where op_id=$bankid and name='cash at bank'   ");
			   confirm_query($update_temp_closing_balance); */
		return $this->get_format_number($total_bank_balance);


		/*  $term_id = $this->current_term_id;
   $query = mysqli_query($this->conn,"SELECT amount,txn_type  from
   ca_other_bank_deposits where  delete_status='A'  and date <='$date_period' and  term_id='$term_id'   ");
	confirm_query($query);
	$total = 0;
	while ($a =  mysqli_fetch_assoc($query))
	{

			$amount  =  $a['amount'];
			$txn_type  =  $a['txn_type'];

			if($txn_type=='D')
			{
			  $total -= $amount;
			}else{
			      $total  +=  $amount;

			}
	}

	return $this->get_format_number($total);
	 */
	}


	function get_total_bank_balance_period_range($start_date, $end_date)
	{

		$query = mysqli_query($this->conn, "SELECT amount,txn_type  from 
   ca_other_bank_deposits where  delete_status='A'  and date >='$start_date'    and  date <='$end_date'     ");
		confirm_query($query);
		$total = 0;
		while ($a = mysqli_fetch_assoc($query)) {

			$amount = $a['amount'];
			$txn_type = $a['txn_type'];

			if ($txn_type == 'D') {
				$total -= $amount;
			} else {
				$total += $amount;

			}
		}

		return $this->get_format_number($total);

	}


	function get_balance_sheet($table_name, $primary_key, $date, $primary_key_value)
	{

		$term_id = $this->current_term_id;

		if ($primary_key == 'asset_id') {

			$total_asset = 0;
			$open_bal = $this->get_opening_bal($primary_key_value, 'asset');
			$dr_cr = $open_bal['dr_cr'];
			$cash_at_hand_opeing_bal = abs($open_bal['opening_balance']);
			if ($dr_cr == 'C') {
				$cash_at_hand_opeing_bal = -1 * $cash_at_hand_opeing_bal;
			}

			$total_asset += $cash_at_hand_opeing_bal;


			$query = mysqli_query($this->conn, "SELECT amount,txn_type  from 
		   {$table_name} where  delete_status='A'  and date <='$date'   and  {$primary_key} = {$primary_key_value}   and term_id='$term_id'  ");
			confirm_query($query);
			//$total = 0;
			while ($a = mysqli_fetch_assoc($query)) {

				$amount = $a['amount'];
				$txn_type = $a['txn_type'];

				if ($txn_type == 'C') {

					$amount = (-1 * $amount);
				}

				$total_asset += $amount;
			}

			return $this->get_format_number($total_asset);

		}


		if ($primary_key == 'liability_id') {

			$total_liability = 0;
			$open_bal = $this->get_opening_bal($primary_key_value, 'liability');
			$dr_cr = $open_bal['dr_cr'];
			$op_liability = abs($open_bal['opening_balance']);
			if ($dr_cr == 'D') {
				$op_liability = -1 * $op_liability;
			}

			$total_liability += $op_liability;

			$query = mysqli_query($this->conn, "SELECT amount,txn_type  from 
		   {$table_name} where  delete_status='A'  and date <='$date'   and  {$primary_key} = {$primary_key_value}   and term_id='$term_id'  ");
			confirm_query($query);
			//$total = 0;
			while ($a = mysqli_fetch_assoc($query)) {

				$amount = $a['amount'];
				$txn_type = $a['txn_type'];

				if ($txn_type == 'D') {
					$amount = (-1 * $amount);
				}

				$total_liability += $amount;
			}

			return $total_liability;
		}


		if ($primary_key == 'equity_id') {

			$total_equity = 0;
			$open_bal = $this->get_opening_bal($primary_key_value, 'equity');
			$dr_cr = $open_bal['dr_cr'];
			$surplus_opening_bal = abs($open_bal['opening_balance']);
			if ($dr_cr == 'D') {
				$surplus_opening_bal = -1 * $surplus_opening_bal;
			}

			$total_equity += $surplus_opening_bal;


			$query = mysqli_query($this->conn, "SELECT amount,txn_type  from 
		   {$table_name} where  delete_status='A'  and date <='$date'   and  {$primary_key} = {$primary_key_value}   and term_id='$term_id'  ");
			confirm_query($query);
			//$total = 0;
			while ($a = mysqli_fetch_assoc($query)) {

				$equity_amount = $a['amount'];
				$txn_type = $a['txn_type'];

				if ($txn_type == 'D') {

					$equity_amount = (-1 * $equity_amount);
				}

				$total_equity += $equity_amount;
			}

			return $this->get_format_number($total_equity);

		}


	}


	function get_asset_transaction($table_name, $start_date, $end_date, $account_type, $account_id)
	{
		$query = mysqli_query($this->conn, "SELECT amount,txn_type  from 
   {$table_name} where  delete_status='A'  and  id = {$account_id} 
   and (date >='$start_date'  and date <='$end_date' )  ");
		confirm_query($query);
		$total = 0;

		if ($account_type == 'asset') {

			/*......Works for only asset.......*/
			while ($a = mysqli_fetch_assoc($query)) {

				$amount = $a['amount'];
				$txn_type = $a['txn_type'];

				if ($txn_type == 'D') {
					$total += $amount;
				} else {
					$total -= $amount;

				}
			}

		} else {
			/* Works for only liability  and equity  */
			while ($a = mysqli_fetch_assoc($query)) {

				$amount = $a['amount'];
				$txn_type = $a['txn_type'];

				if ($txn_type == 'D') {
					$total -= $amount;
				} else {
					$total += $amount;

				}
			}
		}

		return $this->get_format_number($total);

	}


	function get_cash_at_hand_period($date_period)
	{


		$term_id = $this->current_term_id;
		$total_cash_at_hand_balance = 0;
		$opening_bal_details = $this->get_account_opening_bal(0, 'cash at hand');

		$dr_cr_indicator = $opening_bal_details['dr_cr'];

		if ($dr_cr_indicator == 'C') {
			$bank_op = '-' . $this->get_format_number(abs($opening_bal_details['opening_balance']));
		} else {

			$bank_op = $this->get_format_number(abs($opening_bal_details['opening_balance']));
		}

		$total_cash_at_hand_balance += $bank_op;
		//	$actual_bank_balance +=$bank_op;
		$get_all_term_payment = mysqli_query($this->conn, "SELECT * FROM  student_fee_payment WHERE term_id='$term_id'  and delete_status='A' 
				and payment_type='C' and  date_paid <='$date_period'   order by id   asc ");
		confirm_query($get_all_term_payment);

		while ($a = mysqli_fetch_assoc($get_all_term_payment)) {

			$amount_paid = $this->get_format_number($a['amount_paid']);

			$dr_cr = $a['txn_type'];


			if ($dr_cr == 'C') {
				$amount_paid = $this->get_format_number(-1 * $amount_paid);
				// echo "<td style='color:red;'>  $amount_paid  </td> ";
			} else {
				$amount_paid = $this->get_format_number($amount_paid);
				//echo "<td>  $amount_paid  </td> ";
			}
			$total_cash_at_hand_balance += $amount_paid;


		}

		$query = mysqli_query($this->conn, "SELECT amount,txn_type  from 
   ca_cash_at_hand_txn where  delete_status='A'  and date <='$date_period'   and term_id='$term_id'    ");
		confirm_query($query);

		while ($a = mysqli_fetch_assoc($query)) {

			$amount = $a['amount'];
			$txn_type = $a['txn_type'];

			if ($txn_type == 'C') {
				$total_cash_at_hand_balance -= $amount;
			} else {
				$total_cash_at_hand_balance += $amount;

			}
		}
		return $this->get_format_number($total_cash_at_hand_balance);
	}


	function get_school_fees_with_period($start_date, $end_date, $term_id)
	{
		/* $query = mysqli_query($this->conn,"SELECT sum(amount_paid) as total_fees from
   student_fee_payment where  delete_status='A'  and date_paid >='$start_date'
   and  date_paid <='$end_date'   and term_id='$term_id' and source_account='SCHOOL FEES'  ");
	confirm_query($query);

	$a =  mysqli_fetch_assoc($query);*/

		$query = mysqli_query($this->conn, "SELECT class_id  FROM classes  WHERE class_status=1 ORDER BY class_id ASC  ");
		confirm_query($query);
		$total = 0;
		while ($a = mysqli_fetch_assoc($query)) {
			$class_id = $a['class_id'];

			$get_total_fees_for_term_class = $this->get_fees_period($class_id, $term_id, $start_date, $end_date);
			$total += $get_total_fees_for_term_class;
			/* 	    $get_students = mysqli_query($this->conn,"select count(*) as total_students  from students where start_date>='$start_date'
		and start_date<='$end_date'  and class_id=$class_id");
		confirm_query($query); */

		}
		return $this->get_format_number($total);
	}


	function get_fees_period($class_id, $term_id, $start_date, $end_date)
	{
		$total_general_students = $this->get_total_general_students($class_id, $start_date, $end_date);
		$total_fees_general_students = $this->get_class_fees_for_term_general_billing($class_id, $term_id) * $total_general_students;


		$total_special_students_fees = $this->get_total_special_billing_for_term($class_id, $term_id, $start_date, $end_date);

		return $total_fees_general_students + $total_special_students_fees;

	}


	function get_total_general_students($class_id, $start_date, $end_date)
	{
		$get_students = mysqli_query($this->conn, "select count(*) as total_general_students  
	from students where class_id=$class_id   and billing_type='G' 
	and student_status=1  and  start_date<='$end_date'   ");
		confirm_query($get_students);
		$result = mysqli_fetch_assoc($get_students);

		return $result['total_general_students'];
		//echo $result['total_general_students'];

	}


	function get_total_special_students($class_id)
	{
		$get_students = mysqli_query($this->conn, "select count(*) as total_general_students  
	from students where class_id=$class_id   and billing_type='S' 
	and student_status=1   ");
		confirm_query($get_students);
		$result = mysqli_fetch_assoc($get_students);

		return $result['total_general_students'];

	}


	function get_class_fees_for_term_general_billing($class_id, $term_id)
	{
		$get_the_income_for_this_class = mysqli_query($this->conn, "SELECT sum(amount_due) as amount_due
	 FROM billing_general WHERE  term_id='$term_id' AND class_id=$class_id");
		confirm_query($get_the_income_for_this_class);
		$result = mysqli_fetch_assoc($get_the_income_for_this_class);

		return $result['amount_due'];

	}


	function get_total_special_billing_for_term($class_id, $term_id, $start_date, $end_date)
	{
		$get_the_income_for_this_class = mysqli_query($this->conn, "SELECT sum(amount_due) as amount_due
	 FROM billing_special_condition WHERE  term_id='$term_id' AND class_id=$class_id  and student_start_date <='$end_date'  ");
		confirm_query($get_the_income_for_this_class);
		$result = mysqli_fetch_assoc($get_the_income_for_this_class);

		return $result['amount_due'];

	}

	function get_charge_name($id)
	{
		$get_charge_name = mysqli_query($this->conn, "select name from ca_charges  where id = '$id' ");
		confirm_query($get_charge_name);
		$result = mysqli_fetch_assoc($get_charge_name);

		return $result['name'];
	}

	function validate_term_start_date($start_date, $end_date)
	{


		$term_id = $this->current_term_id;
		$query = mysqli_query($this->conn, "SELECT * FROM term_settings  where term_id='$term_id'
   and start_date>='$start_date' and   vacation_date <='$end_date'  ");
		confirm_query($query);

		/*
	$query = mysqli_query($this->conn,"SELECT * FROM term_settings  where term_id='$term_id'
   and start_date>='$start_date' and  start_date  !>  '$end_date'  and vacation_date <='$end_date'   and vacation_date !< '$start_date' ");
	confirm_query($query); */


		echo mysqli_num_rows($query);
		if (mysqli_num_rows($query) == 0) {
			return 1;
		}


	}


	function get_cash_at_hand_period_range($start_date, $end_date)
	{

		$query = mysqli_query($this->conn, "SELECT amount,txn_type  from 
   ca_cash_at_hand_txn where  delete_status='A'  and date >='$start_date'   and date <='$end_date'   ");
		confirm_query($query);
		$total = 0;
		while ($a = mysqli_fetch_assoc($query)) {

			$amount = $a['amount'];
			$txn_type = $a['txn_type'];

			if ($txn_type == 'D') {
				$total -= $amount;
			} else {
				$total += $amount;

			}
		}
		return $this->get_format_number($total);
	}


	function get_count_cash_at_bank()
	{

		$term_id = $this->current_term_id;

		$query = mysqli_query($this->conn, "SELECT count(*) as students_deposit_count from 
   student_fee_payment where term_id='$term_id' and delete_status='A'  and payment_type='B'  ");
		confirm_query($query);
		$rows = mysqli_fetch_assoc($query);
		$a = $rows['students_deposit_count'];

		$other_deposits_count = mysqli_query($this->conn, "SELECT count(*) as total_other_deposits_count from   ca_other_bank_deposits  where 
	delete_status='A' and term_id='$term_id'    ");
		confirm_query($other_deposits_count);
		$row = mysqli_fetch_assoc($other_deposits_count);
		$b = $row['total_other_deposits_count'];

		return $a + $b;
	}


	function get_total_cash_at_hand()
	{
		$term_id = $this->current_term_id;
		/*$cash_at_hand_balance   = mysqli_query($this->conn,"SELECT balance from ca_cash_at_hand where term_id='$term_id'    ");
	 confirm_query($cash_at_hand_balance);
	$row =  mysqli_fetch_assoc($cash_at_hand_balance);
	return $row['balance'];	*/


		$total_balance = 0;
		$open_bal = $this->get_opening_bal(0, 'cash at hand');
		$dr_cr = $open_bal['dr_cr'];
		$cash_at_hand_opeing_bal = abs($open_bal['opening_balance']);
		if ($dr_cr == 'C') {
			$cash_at_hand_opeing_bal = -1 * $cash_at_hand_opeing_bal;
		}

		$cash_at_hand_opeing_bal = $this->get_format_number($cash_at_hand_opeing_bal);
		$total_balance += $cash_at_hand_opeing_bal;

		$get_all_term_payment = mysqli_query($this->conn, "SELECT * FROM  student_fee_payment WHERE term_id='$term_id'  and delete_status='A' 
and payment_type='C'  and delete_status='A' order by id  asc ");
		confirm_query($get_all_term_payment);


		while ($a = mysqli_fetch_assoc($get_all_term_payment)) {
			$txn_type = $a['txn_type'];
			$amount_paid = $this->get_format_number($a['amount_paid']);

			$total_balance += $amount_paid;

		}

		///get other cash at hand  transactions eg. from bank transfer
		/*   $other_cash_at_hand_txn  = mysqli_query($this->conn,"SELECT description, payer , amount, date, txn_type
			   from ca_cash_at_hand_txn
			   where delete_status='A' and term_id='$term_id'   order by id asc   ");
			   confirm_query($other_cash_at_hand_txn);

			   while($rows = mysqli_fetch_assoc($other_cash_at_hand_txn))
			   {
			        $amount =$this->get_format_number( $rows['amount']);
					 $date = $rows['date'];
					 $txn_type = $rows['txn_type'];


					if($txn_type=='C')
					 {
					   $amount = $this->get_format_number( -1*$amount);

					 }

					$total_balance +=$amount;


			   }
			   */
		return $this->get_format_number($total_balance);

	}


	function get_petty_cash_txn()
	{
		$term_id = $this->current_term_id;
		$ptty = mysqli_query($this->conn, "SELECT count(*) as total_tnx from ca_petty_cash_txn where term_id='$term_id'  
 and delete_status='A'   ");
		confirm_query($ptty);
		$row = mysqli_fetch_assoc($ptty);
		return $row['total_tnx'];
	}


	function get_school_opening_balance()
	{

		$ptty = mysqli_query($this->conn, "SELECT opening_balance, dr_cr FROM ca_opening_balance WHERE name='account_opening_balance'  ");
		confirm_query($ptty);
		$row = mysqli_fetch_assoc($ptty);
		return array($row['dr_cr'], $row['opening_balance']);

	}


	function get_class_total_fees($class_id)
	{
		$term_id = $this->current_term_id;
		$get_all_students = mysqli_query($this->conn, "SELECT id, concat(first_name, ' ' , last_name) as student_name
		 FROM students WHERE student_status='1' and class_id=$class_id  ORDER BY first_name  ASC");
		confirm_query($get_all_students);

		$total_income_count = mysqli_num_rows($get_all_students);

		$no = 1;


		$total_bill_amount = 0;
		$total_amount_paid = 0;
		$total_balance = 0;
		while ($get_results = mysqli_fetch_assoc($get_all_students)) {
			$student_name = strtoupper($get_results['student_name']);
			$student_id = $get_results['id'];

			//CHECK IF STUDENT HAS SPECIAL CONDITION
			$special_condtion_bill = $this->get_total_special_condition_bill_for_student($term_id, $student_id);
			$bill_amount = $this->get_total_class_bill($term_id, $class_id);

			if ($special_condtion_bill > 0) {
				$bill_amount = $this->get_format_number($special_condtion_bill);
			}


			$amount_paid = $this->get_total_bill_amount_paid($term_id, $student_id, $class_id);
			$bal = $this->get_format_number($bill_amount - $amount_paid);

			/*echo "<tr class='hover_background'>

				   <td>$no.  </td>
						<td> $student_name   </td>
						<td> $bill_amount    </td>
						<td> $amount_paid     </td>
						<td> $bal      </td>
				</tr>";*/


			$total_bill_amount += $bill_amount;
			$total_amount_paid += $amount_paid;
			$total_balance += $bal;


			$no++;
		}


		return $this->get_format_number($total_bill_amount);

	}


	function get_class_total_fees_2($class_id, $term_id, $total_class_students)
	{

		$get_general_billing = mysqli_query($this->conn, "select sum(amount_due) as amount_due from billing_general where term_id='$term_id'  and class_id='$class_id' ");
		confirm_query($get_general_billing);
		$a = mysqli_fetch_assoc($get_general_billing);
		$amount_due_general = $a['amount_due'];


		$get_special_billing = mysqli_query($this->conn, "select sum(amount_due) as amount_due from billing_special_condition where term_id='$term_id'  and class_id='$class_id' ");
		confirm_query($get_special_billing);
		$b = mysqli_fetch_assoc($get_special_billing);
		$amount_due_special = $b['amount_due'];


///get the total number of special students
		$get_special_billing_total = mysqli_query($this->conn, "select distinct (student_id) as student_id
  from billing_special_condition where term_id='$term_id'  and class_id='$class_id' ");
		confirm_query($get_special_billing_total);
		$total_special_students = mysqli_num_rows($get_special_billing_total);


		$total_general_students = $total_class_students - $total_special_students;

		$total_amount_general = $total_general_students * $amount_due_general;

		$total_bill_due = $total_amount_general + $amount_due_special;

		return $this->get_format_number($total_bill_due);

		/*
//$term_id = $this->current_term_id ;
$get_all_students = mysqli_query($this->conn,"SELECT id, concat(first_name, ' ' , last_name) as student_name
		 FROM students WHERE student_status='1' and class_id=$class_id  ORDER BY first_name  ASC");
		 confirm_query($get_all_students);

		 $total_income_count = mysqli_num_rows($get_all_students);

		 $no = 1;


		$total_bill_amount = 0;
		$total_amount_paid = 0;
		$total_balance = 0;
		 while($get_results= mysqli_fetch_assoc($get_all_students))
		 {
		       $student_name =strtoupper( $get_results['student_name']);
			    $student_id =$get_results['id'];

				//CHECK IF STUDENT HAS SPECIAL CONDITION
				$special_condtion_bill = $this->get_total_special_condition_bill_for_student( $term_id,$student_id );
				 $bill_amount = $this->get_total_class_bill($term_id, $class_id );

				if($special_condtion_bill > 0)
				{
				  $bill_amount  = $this->get_format_number($special_condtion_bill) ;
				}


				$amount_paid = $this->get_total_bill_amount_paid($term_id,$student_id, $class_id);
				$bal = $this->get_format_number( $bill_amount  -  $amount_paid );




				$total_bill_amount +=$bill_amount ;
				$total_amount_paid +=  $amount_paid ;
				$total_balance +=  $bal ;




				$no++;
		 }


		 return $this->get_format_number($total_bill_amount );*/

	}


	function get_total_income_for_period_by_income_id($income_id, $start_date, $end_date)
	{
		$query = mysqli_query($this->conn, "SELECT sum(amount) as total FROM income where (date_paid BETWEEN '$start_date'  AND  '$end_date')  and delete_status='A' and income_id=$income_id    ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['total'];
	}

	function get_total_expenses_for_today_by_expenses_id($expense_id, $date)
	{
		$query = mysqli_query($this->conn, "SELECT sum(amount) as total FROM expenses where date_paid='$date'  and delete_status='A' and expense_id =$expense_id    ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['total'];
	}

	function get_total_income_for_today_by_income_id($income_id, $date)
	{
		$query = mysqli_query($this->conn, "SELECT sum(amount) as total FROM income where date_paid='$date'  and delete_status='A' and income_id=$income_id    ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['total'];
	}


	function student_picture($student_id)
	{
		$query = mysqli_query($this->conn, "SELECT picture FROM students
		WHERE id  = {$student_id}");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		$picture = $a['picture'];
		echo "<img src='$picture' style='width:50px;height:50px;'>";

	}

	function academic_term($term_id)
	{
		$query = mysqli_query($this->conn, "SELECT term_id, academic_year, academic_term FROM term_settings
				WHERE term_id = {$term_id}");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_assoc($query);

			return $row['academic_term'] . ' TERM, ' . $row['academic_year'];


		}
	}


	function transcript_header($student_id)
	{
		$query = mysqli_query($this->conn, "SELECT CONCAT(first_name,' ',last_name) AS student_name,gender, date_of_birth,picture,
				class_name FROM students, classes  WHERE students.class_id = classes.class_id AND id = {$student_id} ;");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		$student_name = $row['student_name'];
		$date_of_birth = $row['date_of_birth'];
		$gender = $row['gender'];
		$picture = $row['picture'];
		$class_name = $row['class_name'];

		echo "<table border=0 width='100%' style='margin-bottom:2%'>
				
						<tr class='bold'>	<td colspan=4 style='text-align:center; font-weight:bold; font-size:15px'>STUDENT TRANSCRIPT</td>	</tr>
						<tr class='bold'>
						<td class='capitalize'>STUDENT NAME: $student_name</td>
						<td rowspan=3  width='10%'><img alt='Student Picture Here'  src='$picture' style='height:100px; width:150px;' /></td>
						</tr>
					
						<tr class='bold'><td class='capitalize'>GENDER: $gender</td></tr>	
						<tr class='bold'><td class='capitalize'>DATE OF BIRTH: $date_of_birth
						<br/>CLASS: $class_name
						</td></tr>	
						
							
						
				</table>";
	}

	function get_current_academic_term()
	{
		$query = mysqli_query($this->conn, "SELECT * FROM term_settings WHERE delete_status='A'  ORDER BY term_id DESC LIMIT 1");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);

		return $a['academic_term'] . ' TERM - ' . $a['academic_year'];
	}


	function term($term_id)
	{
		$query = mysqli_query($this->conn, "SELECT academic_term FROM term_settings
				WHERE term_id = {$term_id}");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_assoc($query);

			return $row['academic_term'];


		}
	}


	function class_id($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT class_id FROM  student_term_total WHERE student_id = {$student_id}
				AND term_id = {$term_id}");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_assoc($query);

			return $row['class_id'];

		}
	}


	function parent_name($parent_id)
	{
		$query = mysqli_query($this->conn, "SELECT CONCAT(first_name,' ', last_name) AS PARENT_NAME FROM  parents
				 WHERE parent_id = {$parent_id}");

		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_assoc($query);

			return $row['PARENT_NAME'];

		}

	}


	function academic_year($term_id)
	{
		$query = mysqli_query($this->conn, "SELECT academic_year FROM term_settings
				WHERE term_id = {$term_id}");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_assoc($query);

			return $row['academic_year'];


		}
	}


	function announcement()
	{
		$query = mysqli_query($this->conn, "SELECT announcement FROM announcement");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_assoc($query);
			return $row['announcement'];
		}
	}

	function conducts()
	{
		$query = mysqli_query($this->conn, "SELECT conduct_id, conduct FROM conducts WHERE
				 conduct_status = 1 ORDER BY conduct ASC");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			while ($row = mysqli_fetch_assoc($query)) {
				$conduct_id = $row['conduct_id'];
				$conduct = $row['conduct'];
				echo "<option value=$conduct_id >$conduct  </option>";
			}

		}
	}

	function reload_opener_page()
	{
		echo "<script type='text/javascript'>opener.location.reload();</script>";
	}

	function reload_opener_page_and_close()
	{
		echo "<script type='text/javascript'>opener.location.reload();window.close();</script>";

	}

	function table_students_by_class_id_and_gender($class_id, $gender)
	{
		$query = mysqli_query($this->conn, "SELECT COUNT(*) FROM students WHERE gender='$gender' AND student_status = 1 AND class_id = {$class_id}");
		confirm_query($query);
		$a = mysqli_fetch_array($query);
		return $a[0];
	}

	function get_student_id($student_name)
	{
		$query = mysqli_query($this->conn, "SELECT id FROM students WHERE concat(first_name, ' ', last_name) = '$student_name'  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['id'];
	}


	function get_staff_id($staff_name)
	{
		$query = mysqli_query($this->conn, "SELECT id FROM staff WHERE concat(first_name, ' ', last_name) = '$staff_name'  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['id'];
	}


	function get_staff_details($staff_id)
	{
		$query = mysqli_query($this->conn, "SELECT * FROM staff WHERE id=$staff_id ");
		confirm_query($query);
		return mysqli_fetch_assoc($query);

	}


	function get_class_name($class_id)
	{
		$query = mysqli_query($this->conn, "SELECT class_name FROM classes WHERE class_id = $class_id ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['class_name'];
	}


	function get_bill_amount_by_class($class_id, $term_id, $income_id)
	{
		$query = mysqli_query($this->conn, "
SELECT * FROM `billing_general` WHERE  `class_id`='$class_id'  and `income_id`='$income_id'  and `term_id`='$term_id'
  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['amount_due'];
	}


	function get_yoc($student_id)
	{
		$query = mysqli_query($this->conn, "SELECT yoc FROM yoc WHERE student_id = $student_id");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['yoc'];
	}


	function get_student_details($student_id)
	{
		$query = mysqli_query($this->conn, "SELECT * FROM students  WHERE id='$student_id'");
		confirm_query($query);

		return mysqli_fetch_assoc($query);
	}

	function get_term_details($term_id)
	{
		$query = mysqli_query($this->conn, "SELECT concat(academic_term, ' TERM, ',academic_year)  as term_detail
		FROM term_settings WHERE term_id = '$term_id'");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['term_detail'];
	}


	function get_bill_amount_by_student_id($class_id, $term_id, $income_id, $student_id)
	{
		$query = mysqli_query($this->conn, "
SELECT amount_due FROM `billing_special_condition` WHERE  `class_id`='$class_id' 
 and `income_id`='$income_id'  and `term_id`='$term_id'  and student_id='$student_id'
  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['amount_due'];
	}


	function Altitudes()
	{
		$query = mysqli_query($this->conn, "SELECT altitude_id, altitude FROM altitudes WHERE
				 altitude_status = 1 ORDER BY altitude ASC");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			while ($row = mysqli_fetch_assoc($query)) {
				$altitude_id = $row['altitude_id'];
				$altitude = $row['altitude'];
				echo "<option value=$altitude_id >$altitude  </option>";
			}

		}
	}


	function Remarks()
	{
		$query = mysqli_query($this->conn, "SELECT remark_id, remarks FROM remarks WHERE
				 remark_status = 1 ORDER BY remarks ASC");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			while ($row = mysqli_fetch_assoc($query)) {
				$remark_id = $row['remark_id'];
				$remarks = $row['remarks'];
				echo "<option value=$remark_id >$remarks  </option>";
			}

		}
	}


	function get_student_promoted_class($student_id, $term_id)
	{
		$get_promoted_class = mysqli_query($this->conn, "select promoted_class_id  from student_term_total
				where student_id='$student_id'  and term_id='$term_id'");
		confirm_query($get_promoted_class);

		$query_results = mysqli_fetch_assoc($get_promoted_class);
		$promoted_to_class_id = $query_results['promoted_class_id'];
		$promted_class = $this->get_class_name($promoted_to_class_id);

		return $array_result = array($promoted_to_class_id, $promted_class);

	}


	function attendence()
	{
		for ($count = 1; $count <= 150; $count++) {
			echo "<option value=$count >$count</option>";
		}
	}

	function promotion($class_id)
	{
		$query = mysqli_query($this->conn, "SELECT class_id, class_name FROM classes WHERE class_status = 1 ORDER BY class_id ASC");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			while ($row = mysqli_fetch_assoc($query)) {
				$class_id = $row['class_id'];
				$class_name = $row['class_name'];
				echo "<option value=$class_id >$class_name  </option>";
			}

		} else {

			echo "<option value=0 >Old Student </option>";
		}
	}


	function promotion2($class_id)
	{
		$query = mysqli_query($this->conn, "SELECT class_id, class_name FROM classes WHERE
				 class_id > {$class_id} AND class_status = 1 ORDER BY class_id ASC LIMIT 1");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		///echo $class_name = $row['class_name'];
		$class_name = $row['class_name'];

		RETURN $class_name;

	}

	function get_total_payment_per_date($date, $charge_id)
	{
		$query = mysqli_query($this->conn, "select sum(amount_paid) as total  from student_fee_payment
						where date_paid='$date'  and income_id='$charge_id'  and delete_status='A'   ");
		confirm_query($query);
		$row = mysqli_fetch_assoc($query);
		return $row['total'];
	}


	function primary_grade_interpretation()
	{
		echo "<lable style='font-size:12px;'>Results Interpretation</lable>";
		echo "<table border=0 width='100%' style='border-top:1px solid #0C6; margin-bottom:0;'>
									
									
									
									<tr class='hover_background'>
										<td>90 - 100:A - Excellent</td>
										<td>65 - 69:C1 - High Average</td>
										<td>50 - 54:D2 - Low</td>
									</tr>
									
									
									<tr class='hover_background'>
										<td>80 - 89:B1 - Very Good</td>
										<td>60 - 64:C2 - Average</td>
										<td>40 - 49:E1 - Lower</td>
									</tr>
									
									
									<tr class='hover_background'>
										<td>70 - 79:B2 - Good</td>
										<td>55 - 59:D1 - Low Average</td>
										<td>10 - 39:E2 - Lowest</td>
									</tr>
									
									</table>";
	}

	function jhs_grade_interpretation()
	{
		echo "<table border=0 width='100%' style='border-top:0 ; ' >";

		echo "<tr><td height='40px' style='vertical-align:bottom'>_________________________________  </td>
												<td style='vertical-align:bottom; text-align:right'>____________________________  </td>  </tr>";

		echo "<tr> <td  style='padding-left:4%'> (TEACHER'S SIGNATURE) </td>  <td style='text-align:right'> (HEAD TEACHER'S SIGNATURE)  </td>  </tr>
									</table>";

		echo "</div>";
	}


	function mock_student_attendence($student_id, $mock_id, $flag)
	{
		$query = mysqli_query($this->conn, "SELECT student_attendence FROM  std_mock_students_remarks WHERE student_id = {$student_id}
				AND mock_id = {$mock_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		$student_attendence = $row['student_attendence'];

		if ($flag == 0) {
			echo "<option value=$student_attendence >$student_attendence  </option>";
		} else {
			echo $student_attendence;

		}


	}


	function mock_total_attendence($student_id, $mock_id, $flag)
	{
		$query = mysqli_query($this->conn, "SELECT total_attendence FROM std_mock_students_remarks WHERE student_id = {$student_id}
				AND mock_id = {$mock_id} ");
		confirm_query($query);
		//if(mysqli_num_rows($query) > 0 )
		//{
		$row = mysqli_fetch_assoc($query);
		$total_attendence = $row['total_attendence'];
		if ($flag == 0) {
			echo "<option value=$total_attendence >$total_attendence  </option>";
		} else {
			echo $total_attendence;

		}

		//}
	}


	function mock_student_conduct($student_id, $mock_id, $flag)
	{
		$query = mysqli_query($this->conn, "SELECT conducts.conduct_id,conducts.conduct
				  FROM std_mock_students_remarks,conducts WHERE conducts.conduct_id =std_mock_students_remarks.conduct_id
				  AND std_mock_students_remarks.student_id={$student_id}   AND std_mock_students_remarks.mock_id = {$mock_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		$conduct_id = $row['conduct_id'];
		$conduct = $row['conduct'];

		if ($flag == 0) {
			if (mysqli_num_rows($query) > 0) {
				echo "<option value=$conduct_id >$conduct  </option>";

			} else {
				echo "<option value=0 >---Select---  </option>";
			}
		} else {
			echo strtoupper($conduct);
		}
	}


	function mock_student_altitude($student_id, $mock_id, $flag)
	{
		$query = mysqli_query($this->conn, "SELECT altitudes.altitude_id,altitudes.altitude
				  FROM std_mock_students_remarks,altitudes WHERE altitudes.altitude_id = std_mock_students_remarks.altitude_id
				  AND std_mock_students_remarks.student_id={$student_id}   AND std_mock_students_remarks.mock_id = {$mock_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		$altitude_id = $row['altitude_id'];
		$altitude = $row['altitude'];


		if ($flag == 0) {
			if (mysqli_num_rows($query) > 0) {

				echo "<option value=$altitude_id >$altitude  </option>";
			} else {
				echo "<option value=0 >---Select---  </option>";
			}

		} else {

			//echo $altitude;
			echo strtoupper($altitude);
		}
	}


	function mock_student_interest($student_id, $mock_id, $flag)
	{
		$query = mysqli_query($this->conn, "SELECT interest FROM std_mock_students_remarks WHERE student_id = {$student_id}
				AND mock_id = {$mock_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		return $interest = strtoupper($row['interest']);

	}


	function mock_class_teacher_remarks($student_id, $mock_id, $flag)
	{
		$query = mysqli_query($this->conn, "SELECT remarks.remark_id,remarks.remarks
				  FROM std_mock_students_remarks,remarks WHERE remarks.remark_id = std_mock_students_remarks.remark_id
				  AND std_mock_students_remarks.student_id={$student_id}   AND std_mock_students_remarks.mock_id = {$mock_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		$remark_id = $row['remark_id'];
		$remarks = $row['remarks'];


		if ($flag == 0) {
			if (mysqli_num_rows($query) > 0) {

				echo "<option value=$remark_id >$remarks  </option>";
			} else {
				echo "<option value=0 >---Select---  </option>";
			}
		} else {

			echo strtoupper($remarks);
		}
	}


	function student_attendence($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT student_attendence FROM student_remarks WHERE student_id = {$student_id}
				AND term_id = {$term_id} ");
		confirm_query($query);
		//if(mysqli_num_rows($query) > 0 )
		//{
		$row = mysqli_fetch_assoc($query);
		$student_attendence = $row['student_attendence'];
		echo "<option value=$student_attendence >$student_attendence  </option>";

		//}
	}


	function student_attendence2($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT student_attendence FROM student_remarks WHERE student_id = {$student_id}
				AND term_id = {$term_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		echo $student_attendence = $row['student_attendence'];

	}

	/*
			function mock_student_attendence2($student_id,$mock_id)
			{
				$query = mysqli_query($this->conn,"SELECT student_attendence FROM std_mock_students_remarks WHERE student_id = {$student_id}
				AND mock_id = {$mock_id} ");
				confirm_query($query);

					$row = mysqli_fetch_assoc($query);
					echo	$student_attendence = $row['student_attendence'];

			}
			 */


	function total_attendence($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT total_attendence FROM student_remarks WHERE student_id = {$student_id}
				AND term_id = {$term_id} ");
		confirm_query($query);
		//if(mysqli_num_rows($query) > 0 )
		//{
		$row = mysqli_fetch_assoc($query);
		$total_attendence = $row['total_attendence'];
		echo "<option value=$total_attendence >$total_attendence  </option>";

		//}
	}


	function total_attendence2($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT total_attendence FROM student_remarks WHERE student_id = {$student_id}
				AND term_id = {$term_id} ");
		confirm_query($query);
		//if(mysqli_num_rows($query) > 0 )
		//{
		$row = mysqli_fetch_assoc($query);
		echo $total_attendence = $row['total_attendence'];


		//}
	}


	function student_conduct($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT conducts.conduct_id,conducts.conduct
				  FROM student_remarks,conducts WHERE conducts.conduct_id =student_remarks.conduct_id
				  AND student_remarks.student_id={$student_id}   AND student_remarks.term_id = {$term_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		$conduct_id = $row['conduct_id'];
		$conduct = $row['conduct'];

		if (mysqli_num_rows($query) > 0) {
			echo "<option value=$conduct_id >$conduct  </option>";

		} else {
			echo "<option value=0 >---Select---  </option>";
		}
	}


	function student_conduct2($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT conducts.conduct_id,conducts.conduct
				  FROM student_remarks,conducts WHERE conducts.conduct_id =student_remarks.conduct_id
				  AND student_remarks.student_id={$student_id}   AND student_remarks.term_id = {$term_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		$conduct_id = $row['conduct_id'];
		echo $conduct = $row['conduct'];

	}

	function student_altitude2($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT altitudes.altitude_id,altitudes.altitude
				  FROM student_remarks,altitudes WHERE altitudes.altitude_id = student_remarks.altitude_id
				  AND student_remarks.student_id={$student_id}   AND student_remarks.term_id = {$term_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		$altitude_id = $row['altitude_id'];
		echo $altitude = $row['altitude'];

	}


	function student_altitude($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT altitudes.altitude_id,altitudes.altitude
				  FROM student_remarks,altitudes WHERE altitudes.altitude_id = student_remarks.altitude_id
				  AND student_remarks.student_id={$student_id}   AND student_remarks.term_id = {$term_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		$altitude_id = $row['altitude_id'];
		$altitude = $row['altitude'];


		if (mysqli_num_rows($query) > 0) {

			echo "<option value=$altitude_id >$altitude  </option>";
		} else {
			echo "<option value=0 >---Select---  </option>";
		}
	}


	function student_interest($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT interest FROM student_remarks WHERE student_id = {$student_id}
				AND term_id = {$term_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		return $interest = $row['interest'];

	}

	function class_teacher_remarks2($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT remarks.remark_id,remarks.remarks
				  FROM student_remarks,remarks WHERE remarks.remark_id = student_remarks.remark_id
				  AND student_remarks.student_id={$student_id}   AND student_remarks.term_id = {$term_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		$remark_id = $row['remark_id'];
		echo $remarks = $row['remarks'];

	}

	function class_teacher_remarks($student_id, $term_id)
	{
		$query = mysqli_query($this->conn, "SELECT remarks.remark_id,remarks.remarks
				  FROM student_remarks,remarks WHERE remarks.remark_id = student_remarks.remark_id
				  AND student_remarks.student_id={$student_id}   AND student_remarks.term_id = {$term_id} ");
		confirm_query($query);

		$row = mysqli_fetch_assoc($query);
		$remark_id = $row['remark_id'];
		$remarks = $row['remarks'];


		if (mysqli_num_rows($query) > 0) {

			echo "<option value=$remark_id >$remarks  </option>";
		} else {
			echo "<option value=0 >---Select---  </option>";
		}
	}


	function immedaite_past_academic_calender()
	{
		$previous_term_id = $this->current_term_id - 1;
		$query = mysqli_query($this->conn, "SELECT term_id, academic_year, academic_term FROM term_settings
				WHERE term_id = {$previous_term_id}");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_assoc($query);
			$this->immedaite_past_term_id = $row['term_id'];
			$this->immedaite_past_academic_term = $row['academic_term'] . ' TERM, ' . $row['academic_year'];
		}
	}


	function all_academic_calender()
	{
		$current_term_id = $this->current_term_id;
		$query = mysqli_query($this->conn, "SELECT term_id, academic_year, academic_term FROM term_settings
				ORDER BY term_id DESC");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			while ($row = mysqli_fetch_assoc($query)) {
				$acdemic_term = $row['academic_term'] . ' TERM, ' . $row['academic_year'];
				$term_id = $row['term_id'];
				echo "<option value='$term_id' >$acdemic_term</option>";
			}

		}
	}


	function all_mock_calender()
	{
		//$current_term_id = $this->current_term_id;
		$query = mysqli_query($this->conn, "SELECT term_settings.term_id, academic_year, academic_term, mock_id FROM term_settings, std_mock_settings
				WHERE term_settings.term_id = std_mock_settings.term_id ORDER BY mock_id DESC");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			while ($row = mysqli_fetch_assoc($query)) {
				$acdemic_term = $row['academic_term'] . ' TERM, ' . $row['academic_year'];
				$term_id = $row['term_id'];
				echo "<option value='$term_id' >$acdemic_term</option>";
			}

		}
	}

	function subjects($limit, $offset_value)
	{
		$query = mysqli_query($this->conn, "SELECT class_id, class_name FROM classes WHERE class_status = 1 ORDER BY class_id ASC LIMIT {$limit} OFFSET {$offset_value}");
		confirm_query($query);
		if (mysqli_num_rows($query) > 0) {
			while ($rows = mysqli_fetch_assoc($query)) {
				$class_id = $rows['class_id'];
				$class_name = $rows['class_name'];
				echo "<input type='checkbox' name='class_id[]' value=$class_id />$class_name<br/>";
			}
		}
	}

	function class_name($class_id)
	{
		$class = mysqli_query($this->conn, "SELECT class_name FROM classes WHERE class_id = {$class_id}");
		confirm_query($class);
		$row = mysqli_fetch_assoc($class);
		return $row['class_name'];
	}


	function class_name2($class_id,$top_ridge_db_connection)
	{
		$class = mysqli_query($this->conn, "SELECT class_id FROM classes WHERE class_id = {$class_id}");
		confirm_query2($class,$top_ridge_db_connection);
		$row = mysqli_fetch_assoc($class);
		return $row['class_id'];
	}
	function stage_name($class_id)
	{
		$class = mysqli_query($this->conn, "SELECT class_name FROM classes WHERE class_id = {$class_id}");
		confirm_query($class);
		$row = mysqli_fetch_assoc($class);
		return $row['class_name'];
	}
	
	function stage($class_id)
	{
		$class = mysqli_query($this->conn, "SELECT stage FROM classes WHERE class_id = {$class_id}");
		confirm_query($class);
		$row = mysqli_fetch_assoc($class);
		return $row['stage'];
	}


	function subject_name($subject_id)
	{
		$subject_name = mysqli_query($this->conn, "SELECT subject_name FROM subjects WHERE subject_id = {$subject_id}");
		confirm_query($subject_name);
		$row = mysqli_fetch_assoc($subject_name);
		return $row['subject_name'];
	}


	function get_subject_short_name($subject_id)
	{
		$subject_name = mysqli_query($this->conn, "SELECT short_name FROM subjects WHERE subject_id = {$subject_id}");
		confirm_query($subject_name);
		$row = mysqli_fetch_assoc($subject_name);
		return $row['short_name'];
	}

	/*
			function MOCK_NUMBER($mock_id)
			{
				$MOCK_NUMBER= mysqli_query($this->conn,"SELECT mock_number FROM std_mock_settings WHERE mock_id = {$mock_id}");
				confirm_query($MOCK_NUMBER);
				$row = mysqli_fetch_assoc($MOCK_NUMBER);
				return $row['mock_number'];
			}
			 */

	function term_details($term_id)
	{

		///SELECT CURRENT TERM DETAILS
		$term_details = mysqli_query($this->conn, "SELECT * FROM term_settings WHERE term_id = '$term_id'");
		confirm_query($term_details);
		if (mysqli_num_rows($term_details) != 0) {
			$row_set = mysqli_fetch_assoc($term_details);
			$this->academicYear = $row_set['academic_year'];
			$this->academicTerm = $row_set['academic_term'];
			$this->date_of_vacation = $row_set['vacation_date'];
			$this->date_of_resumption = $row_set['resumption_date'];
			$this->number_of_weeks = $row_set['number_of_weeks'];

		}
		return $this->CURRENT_TERM = $this->academicTerm . ' TERM, ' . $this->academicYear;
	}


	function academic_class()
	{

		if ($this->staff_job_type == 1) {
			//select * classes
			$query = mysqli_query($this->conn, "SELECT class_id, class_name FROM classes WHERE
					class_status = 1 ORDER BY class_id ASC");
		} else {
			///select only classes registered with staff
			$staff_id = $this->staffid;

			$query = mysqli_query($this->conn, "SELECT distinct classes.class_id, classes.class_name FROM classes, class_teachers WHERE
					classes.class_id = class_teachers.class_id AND class_teachers.staff_id = {$staff_id} ");

		}

		if (mysqli_num_rows($query)) {
			while ($row = mysqli_fetch_assoc($query)) {
				$class_id = $row['class_id'];
				$class_name = $row['class_name'];

				echo "<option value=$class_id>$class_name</option>";

			}
		}
	}


	/////enable entry of score for nursey 1 and 2

	function all_academic_class()
	{

		if ($this->staff_job_type == 1) {
			//select * classes
			$query = mysqli_query($this->conn, "SELECT class_id, class_name FROM classes WHERE
					class_status = 1 ORDER BY class_id ASC");
		} else {
			///select only classes registered with staff
			$staff_id = $this->staffid;

			$query = mysqli_query($this->conn, "SELECT distinct classes.class_id, classes.class_name FROM classes, class_teachers WHERE
					classes.class_id = class_teachers.class_id AND class_teachers.staff_id = {$staff_id}  
					");

		}

		if (mysqli_num_rows($query)) {
			while ($row = mysqli_fetch_assoc($query)) {
				$class_id = $row['class_id'];
				$class_name = $row['class_name'];

				echo "<option value=$class_id>$class_name</option>";

			}
		}
	}
function all_academic_stage()
	{

		if ($this->staff_job_type == 1) {
			//select * classes
			$query = mysqli_query($this->conn, "SELECT class_id, class_name, stage FROM classes WHERE
					class_status = 1 ORDER BY class_id ASC");
		} else {
			///select only classes registered with staff
			$staff_id = $this->staffid;

			$query = mysqli_query($this->conn, "SELECT distinct classes.class_id, classes.class_name , classes.stage FROM classes, class_teachers WHERE
					classes.class_id = class_teachers.class_id AND class_teachers.staff_id = {$staff_id}  
					");

		}

		if (mysqli_num_rows($query)) {
			while ($row = mysqli_fetch_assoc($query)) {
				$class_id = $row['class_id'];
				$class_name = $row['class_name'];
				$stage = $row['stage'];

				echo "<option value=$class_id>$stage</option>";

			}
		}
	}


	function table_count($title, $table_name, $status)
	{
		$query = "SELECT count(*) FROM {$table_name} WHERE {$status} =1";
		$this->print_total($this->perform_query($query), $title);

	}

	function get_sms_left()
	{
		$get_sms_left = mysqli_query($this->conn, "SELECT sms_left FROM sms_counter ");
		confirm_query($get_sms_left);

		$a = mysqli_fetch_assoc($get_sms_left);
		return $a['sms_left'];

	}


	function reduce_sms_count()
	{
		$reduce_sms_count = mysqli_query($this->conn, "UPDATE sms_counter  SET  sms_left =sms_left - 1 ");
		confirm_query($reduce_sms_count);


	}


	function send_sms($phone, $msg)
	{
		$sender_id = 'WestEnd';
		$key = "a6e0a18bb84a73cdce75";


		if ($this->get_sms_left() != 0) {
			$url = "http://bulk.mnotify.net/smsapi?key=$key&to=$phone&msg=$msg&sender_id=$sender_id";
			echo $result = file_get_contents($url);
			if ($result == '1000') {
				$status = 'SENT';
			} else {
				$status = 'NOT SENT';
			}

			//populate sms_log table
			$result_of_sms = $this->sms_result($result);
			$update_sms_log = mysqli_query($this->conn, "insert into sms_log(sms_number,message,status, status_reason) 
				values ('$phone','$msg','$status','$result_of_sms' )");
			confirm_query($update_sms_log);

			if ($status == 'SENT') {
				///reduce sms count
				$reduce_sms_count = mysqli_query($this->conn, "UPDATE sms_counter SET sms_left =sms_left-1 ");
				confirm_query($reduce_sms_count);
			}
			return $result_of_sms;
		} else {

			//populate sms_log table
			$update_sms_log = mysqli_query($this->conn, "insert into sms_log(sms_number,message,status, status_reason) 
			values ('$phone','$msg','NOT SENT','Insufficient SMS Balance' )");
			confirm_query($update_sms_log);
			return "SMS failed. You do not have enough SMS balance";
		}

	}

	function sms_result($value)
	{
		switch ($value) {

			case "1000":
				$result = "Message sent";
				break;

			case "1002":
				$result = "Message not sent";
				break;

			case "1003":
				$result = "You don't have enough balance";
				break;

			case "1004":
				$result = "Invalid API Key";
				break;

			case "1005":
				$result = "Phone number not valid";
				break;

			case "1006":
				$result = "Invalid Sender ID";
				break;

			case "1008":
				$result = "Empty message";
				break;
		}

		return $result;
	}


	function table_records($table_name, $status)
	{
		$query = "SELECT count(*) FROM {$table_name} WHERE {$status} =1";
		echo "<b class='total ui-corner-all'>" . $this->total_count = $this->perform_query($query) . "</b>";


	}


	function total_old_students()
	{
		$query = "SELECT count(*) FROM students WHERE student_status =2";
		$this->print_total($this->perform_query($query), $title = 'OLD STUDENTS');


	}


	function table_records_by_gender($table_name, $status, $gender)
	{
		$query = "SELECT count(*) FROM {$table_name} WHERE {$status} =1 AND gender='$gender' ";
		echo "<b class='to'>" . $this->total_count = $this->perform_query($query) . "</b>";
		return $this->total_count = $this->perform_query($query);
	}


	function count_student_by_class_and_gender($class_id, $gender)
	{
		$query = "SELECT count(*) FROM students WHERE student_status =1 AND gender='$gender' 
				 AND class_id = {$class_id}";

		return $this->total_count = $this->perform_query($query);
	}


	function total_classes()
	{
		$query = "SELECT count(*) FROM classes";
		$this->print_total($this->perform_query($query), "Classes");

	}

	function total_class_subject($class_id)
	{
		$query = "SELECT count(*) FROM class_subjects, subjects WHERE class_subjects.class_id ='$class_id' AND subject_status = 1 AND subjects.subject_id = class_subjects.subject_id  AND class_subjects.status = 1  ";
		$this->print_total($this->perform_query($query), "Subjects");


	}

	function get_class_total_count($class_id)
	{
		$query = mysqli_query($this->conn,"SELECT count(*) AS TOTAL_COUNT 
			FROM students WHERE class_id={$class_id} AND student_status = 1  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['TOTAL_COUNT'];
	}


	function total_class_students($class_id)
	{
		$query = "SELECT count(*) FROM students WHERE class_id={$class_id} AND student_status = 1";
		$this->print_total($this->perform_query($query), "Students");

	}

	function total_class_students_by_term_id($class_id, $term_id)
	{
		$query = "SELECT count(*) FROM student_term_total WHERE class_id={$class_id} AND term_id = {$term_id}";
		$this->print_total($this->perform_query($query), "Students");

	}	function total_stage_students_by_term_id($stage, $term_id)
	{
		$query = "SELECT count(*) FROM students WHERE
 student_stage = {$stage} ";
		$this->print_total($this->perform_query($query), "Students");

	}


	function total_subject_offered_per_term($term_id, $class_id)
	{
		$total_subjects = mysqli_query($this->conn,"select distinct subject_id from continous_assessment
		where class_id='$class_id' AND  term_id='$term_id' ");
		confirm_query($total_subjects);
		return mysqli_num_rows($total_subjects);
	}

	function get_total_students_for_this_mock($mock_id)
	{

		$total = mysqli_query($this->conn, "SELECT count(*) as total_students FROM std_mock_total_results WHERE mock_id = {$mock_id}");
		confirm_query($total);
		$row = mysqli_fetch_assoc($total);
		return $row['total_students'];

	}

	function MOCK_NUMBER($mock_id)
	{
		$MOCK_NUMBER = mysqli_query($this->conn, "SELECT mock_number FROM std_mock_settings WHERE mock_id = {$mock_id}");
		confirm_query($MOCK_NUMBER);
		$row = mysqli_fetch_assoc($MOCK_NUMBER);
		return $row['mock_number'];
	}


	function get_mock_number($mock_id)
	{
		$a = mysqli_query($this->conn,"SELECT mock_number from std_mock_settings  where mock_id='$mock_id'");
		confirm_query($a);
		$b = mysqli_fetch_assoc($a);
		return $b['mock_number'];
	}

	function student_name($student_id)
	{
		$query = mysqli_query($this->conn, "SELECT CONCAT(first_name, ' ',last_name) AS student_name
		FROM students WHERE id = {$student_id}  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['student_name'];
	}


	/* $top_ridge->process_results('exams_results',$subject_id,$class_id,$term_id,$student_id,$reopening_exams,$mgt_exams,$exams_score,$array); */


	function best_six($id,$term_id){
		$total_best_six =0;

		$score = mysqli_query($this->conn, "SELECT grade, subjects.subject_id  FROM continous_assessment ,subjects WHERE 
								continous_assessment.subject_id=subjects.subject_id AND
								student_id = {$id} AND term_id = {$term_id} 
								AND subject_type=1 ");
		confirm_query($score);


		while ($a = mysqli_fetch_assoc($score)) {
			$four_core_subjects = round($a['grade']);
			$total_best_six += $four_core_subjects;

		}

		$other_score = mysqli_query($this->conn, "SELECT grade, subjects.subject_id  FROM continous_assessment ,subjects WHERE 
								continous_assessment.subject_id=subjects.subject_id AND
								student_id = {$id} AND term_id = {$term_id} 
								AND subject_type=0
								 order by   grade  ASC   LIMIT 2 ");
		confirm_query($other_score);


		while ($result_data = mysqli_fetch_assoc($other_score)) {

			$best_two_subjects = $result_data['grade'];
			$total_best_six += $best_two_subjects;

		}
	return $total_best_six;
	}


	function process_results($flag, $subject_id, $class_id, $term_id, $student_id, $home_work_one, $home_work_two,
							 $home_work_three, $home_work_four,$err)
	{		
		$valid = "no";
		$total_subjects_offered = $this->total_subject_offered_per_term($term_id, $class_id);
		$total_records = count($student_id);


		for ($count = 0; $count < $total_records; $count++) {

			$id = $student_id[$count];

			$h1 = $home_work_one[$count];////reopening_exams
			$h2 = $home_work_two[$count];////mgt_exams
			$h3 = $home_work_three[$count];////exams_score
			$h4 = $home_work_four[$count];
		//	$total = round($h1 + $h2 + $h3 + $h4);

				$best_six = $this->best_six($id,$term_id);
			////////CONTINOUS ASSESSMENT
			if ($flag == 'home_work_results') {



					$continous_assessment = mysqli_query($this->conn, "UPDATE continous_assessment SET home_work_one='$h1', 
				home_work_two='$h2', home_work_three='$h3', home_work_four='$h4',total_assessment=(home_work_one + 
				home_work_two+ home_work_three+ home_work_four + class_work_one +
				class_work_two + class_work_three + class_work_four + class_test_one + class_test_two + class_test_three),
				thirty_percent_score = round(0.833 * total_assessment),
				seventy_percent_score= round(0.5 *  exams_score) ,
				total_subject_score = (thirty_percent_score + seventy_percent_score),
				best_six=$best_six
				where student_id='$id' and term_id='$term_id' and subject_id='$subject_id' AND class_id = '$class_id' ");
					confirm_query($continous_assessment);






			} else if ($flag == 'class_work_results') {
					$continous_assessment = mysqli_query($this->conn, "UPDATE continous_assessment SET class_work_one='$h1', 
				class_work_two='$h2', class_work_three='$h3', class_work_four='$h4',
				total_assessment=(home_work_one + 
					home_work_two+ home_work_three+ home_work_four + class_work_one +
					class_work_two + class_work_three + class_work_four + class_test_one + class_test_two + class_test_three),
					thirty_percent_score = round(0.833 * total_assessment),
					seventy_percent_score= round(0.5 *  exams_score) ,
					total_subject_score = (thirty_percent_score + seventy_percent_score),
					best_six=$best_six
					where student_id='$id' and term_id='$term_id' and subject_id='$subject_id' AND class_id = '$class_id' ");
					confirm_query($continous_assessment);
			

			} else if ($flag == 'class_test_results') {
					$continous_assessment = mysqli_query($this->conn, "UPDATE continous_assessment SET class_test_one='$h1', 
						class_test_two ='$h2', class_test_three='$h3', total_assessment=(home_work_one + 
						home_work_two+ home_work_three+ home_work_four + class_work_one +
						class_work_two + class_work_three + class_work_four + class_test_one + class_test_two + class_test_three),
						thirty_percent_score = round(0.833 * total_assessment), 
						seventy_percent_score=round(0.5*  exams_score) ,
						total_subject_score = (thirty_percent_score + seventy_percent_score),
						best_six=$best_six
						where student_id='$id' and term_id='$term_id' and subject_id='$subject_id' AND class_id = '$class_id' ");
					confirm_query($continous_assessment);


				
			} else if ($flag == 'exams_results') {
					$continous_assessment = mysqli_query($this->conn, "UPDATE continous_assessment SET 
					    exams_score='$h3',
						total_assessment=(home_work_one + 
						home_work_two+ home_work_three+ home_work_four + class_work_one +
						class_work_two + class_work_three + class_work_four + class_test_one + class_test_two + class_test_three),
						thirty_percent_score = round(0.833 * total_assessment),
				seventy_percent_score= round(0.5 * exams_score),
						total_subject_score = (thirty_percent_score + seventy_percent_score),
						best_six=$best_six
						where student_id='$id' and term_id='$term_id' and subject_id='$subject_id' AND class_id = '$class_id' ");
					confirm_query($continous_assessment);
						}
		}/////ENDS FOR LOOP


		//////////////////////////////////////////////////////////////////////
		//if($class_id > 0 )
		//{
		$student_details = mysqli_query($this->conn, "SELECT total_subject_score FROM continous_assessment WHERE subject_id= {$subject_id} AND term_id = {$term_id} AND class_id = {$class_id} ORDER BY total_subject_score DESC  ");
		confirm_query($student_details);
		$size = mysqli_num_rows($student_details);
		$array1 = array();

		while ($row = mysqli_fetch_assoc($student_details)) {

			$total_subject_score = $row['total_subject_score'];

			/*  $student_id = $row['student_id'];
			///$student_name = $top_ridge->student_name($student_id);


			$home_work_one = $row['home_work_one'];	$student_id = $row['student_id'];	$class_work_one = $row['class_work_one'];
			$home_work_two = $row['home_work_two'];	$student_id = $row['student_id'];	$class_work_two = $row['class_work_two'];
			$home_work_three = $row['home_work_three'];	$student_id = $row['student_id']; $class_work_three = $row['class_work_three'];
			$home_work_four = $row['home_work_four'];	$student_id = $row['student_id'];  $class_work_four = $row['class_work_four'];

			$class_test_one = $row['class_test_one'];
			$class_test_two = $row['class_test_two'];
			$class_test_three = $row['class_test_three'];

			$total_assessment = round($home_work_one + $home_work_two + $home_work_three + $home_work_four + $class_test_one + $class_test_two + $class_test_three +
								$class_work_one + $class_work_two + $class_work_three + $class_work_four);



			$thirty_percent_score = round(0.3 * $total_assessment);
			$exams_score = round($row['exams_score']);
			$seventy_percent_score = round(0.7 * $exams_score);

			 $total_subject_score =$thirty_percent_score + $seventy_percent_score;
			 $update_score = mysqli_query($this->conn,"UPDATE continous_assessment SET total_subject_score = $total_subject_score,
						thirty_percent_score = $thirty_percent_score, seventy_percent_score = $seventy_percent_score
							WHERE student_id = {$student_id} AND  subject_id= {$subject_id}
							AND term_id = {$term_id} AND class_id = {$class_id}");
			confirm_query($update_score); */


			array_push($array1, $total_subject_score);

		}


		$array2 = array();
		$array2[0] = 1;

		for ($index = 1; $index <= $size; $index++) {
			if ($array1[$index] == $array1[$index - 1]) {

				$array2[$index] = $array2[$index - 1];
			} else {
				$array2[$index] = ($index + 1);

			}

		}


		$student_detailss = mysqli_query($this->conn, "SELECT total_subject_score,student_id FROM continous_assessment WHERE subject_id= {$subject_id}
		AND term_id = {$term_id} AND class_id = {$class_id} ORDER BY total_subject_score DESC  ");
		confirm_query($student_detailss);


		///$count = 1;
		$counter = 0;

		while ($row = mysqli_fetch_assoc($student_detailss)) {
			$student_id = $row['student_id'];
			$total_subject_score = $row['total_subject_score'];
			///$student_name = $top_ridge->student_name($student_id);


			/* $home_work_one = $row['home_work_one'];	$student_id = $row['student_id'];	$class_work_one = $row['class_work_one'];
				$home_work_two = $row['home_work_two'];	$student_id = $row['student_id'];	$class_work_two = $row['class_work_two'];
				$home_work_three = $row['home_work_three'];	$student_id = $row['student_id']; $class_work_three = $row['class_work_three'];
				$home_work_four = $row['home_work_four'];	$student_id = $row['student_id'];  $class_work_four = $row['class_work_four'];

				$class_test_one = $row['class_test_one'];
				$class_test_two = $row['class_test_two'];
				$class_test_three = $row['class_test_three'];

				$total_assessment = round($home_work_one + $home_work_two + $home_work_three + $home_work_four + $class_test_one + $class_test_two + $class_test_three +
									$class_work_one + $class_work_two + $class_work_three + $class_work_four);



				$thirty_percent_score = round(0.3 * $total_assessment);
				$exams_score = round($row['exams_score']);
				$seventy_percent_score = round(0.7 * $exams_score);

				 $total_subject_score =round($thirty_percent_score + $seventy_percent_score);
				  */
			$position = $array2[$counter];
			$code = $this->position_code($position);
			$position = $position . $code;


			//if($class_id > 9 )
			//{

			$grading = $this->subject_grade($total_subject_score);
			$grade = $grading[1];
			$remarks = $grading[0];
			/*}else{
					$grading = $this->primary_subject_grade($total_subject_score);
					$grade = $grading[1];
					$remarks = $grading[0];

				}*/
	$update_scores = mysqli_query($this->conn, "UPDATE continous_assessment SET remarks = '$remarks', position = '$position',
							grade = '$grade'
							WHERE student_id = {$student_id} AND  subject_id= {$subject_id}
							AND term_id = {$term_id} AND class_id = {$class_id}");
	confirm_query($update_scores);

			

			//$count++;
			$counter++;

		}
		///////////////////////////////////////////////////////////////////


		/////OVERALL CLASS POSITION


		$all_students_in_class = mysqli_query($this->conn, "SELECT DISTINCT student_id FROM continous_assessment 
								WHERE class_id = {$class_id}  AND term_id = {$term_id} ");
		confirm_query($all_students_in_class);


		$total_subjects = mysqli_query($this->conn, "select distinct `subject_id` from  continous_assessment 
										WHERE `class_id`= {$class_id} AND `term_id`={$term_id}");
		$total_subjects_for_term = mysqli_num_rows($total_subjects);


		WHILE ($QUERY_RESULT = mysqli_fetch_assoc($all_students_in_class)) {
			$id = $QUERY_RESULT['student_id'];
			$subject_scores = mysqli_query($this->conn, "SELECT best_six, SUM(total_subject_score) AS TOTAL_SCORE FROM continous_assessment
				WHERE class_id = {$class_id}  AND term_id = {$term_id}  AND student_id = {$id}  ");
			confirm_query($subject_scores);


			$a = mysqli_fetch_assoc($subject_scores);
			$OVERALL_SCORE = round($a['TOTAL_SCORE']);
			$AVERAGE_SCORE = round(($OVERALL_SCORE / $total_subjects_for_term), 2);
			$stu_best_six = $a['best_six'];


			$update_term_total = mysqli_query($this->conn, "UPDATE  student_term_total SET term_total_score = $OVERALL_SCORE, 
												term_average_score = $AVERAGE_SCORE,
												best_six = $stu_best_six 
												WHERE class_id={$class_id} AND term_id = {$term_id} AND student_id = {$id}	");
			confirm_query($update_term_total);

		}

		$score_sheet = array();


		$score_sheet_data = mysqli_query($this->conn, "SELECT  term_total_score FROM  student_term_total 
										WHERE class_id = {$class_id} AND term_id = {$term_id}  
										ORDER BY term_total_score DESC");
		confirm_query($score_sheet_data);
		$class_size = mysqli_num_rows($score_sheet_data);


		WHILE ($RESULTS = mysqli_fetch_assoc($score_sheet_data)) {
			$OVERALL_SCORE = $RESULTS['term_total_score'];
			array_push($score_sheet, $OVERALL_SCORE);

		}


		$overall_class_postion = array();
		$overall_class_postion[0] = 1;


		for ($index = 1; $index < $class_size; $index++) {
			if ($score_sheet[$index] == $score_sheet[$index - 1]) {

				$overall_class_postion[$index] = $overall_class_postion[$index - 1];
			} else {
				$overall_class_postion[$index] = ($index + 1);

			}

		}

		$student_overall_position = mysqli_query($this->conn, "SELECT student_id FROM student_term_total
									WHERE term_id = {$term_id} AND class_id = {$class_id}
									ORDER BY term_total_score DESC  ");
		confirm_query($student_overall_position);

		$initial_count = 0;
		while ($row = mysqli_fetch_assoc($student_overall_position)) {
			$student_id = $row['student_id'];
			$OVERALL_POSITION = $overall_class_postion[$initial_count];
			$CODE = $this->position_code($OVERALL_POSITION);
			$OVERALL_POSITION = $OVERALL_POSITION . $CODE;

			$update_overall_position = mysqli_query($this->conn, "UPDATE student_term_total SET  class_position = '$OVERALL_POSITION'
									WHERE class_id={$class_id}  AND term_id = {$term_id} AND student_id = {$student_id} ");
			confirm_query($update_overall_position);
			$initial_count++;
		}

		/////end of OVERALL CLASS POSITION


//}

		/*else{
					$pos = mysqli_query($this->conn,"SELECT students.id,
						CONCAT(students.first_name,' ',last_name) AS STUDENT_NAME,	marks_obtained,grade,position,remarks
						FROM  lower_primary_assessment,students WHERE students.id =  lower_primary_assessment.student_id AND
						 lower_primary_assessment.term_id={$term_id} AND  lower_primary_assessment.subject_id={$subject_id}
						AND  lower_primary_assessment.class_id = {$class_id} ORDER BY marks_obtained DESC
						");
						confirm_query($pos);
						$size = mysqli_num_rows($pos);
						$array1 = array();


						if($size > 0)
						{
								$count = 1;
								while($rows = mysqli_fetch_assoc($pos))
								{
									 $student_id = $rows['id'];
									$marks_obtained = round($rows['marks_obtained']);
									array_push($array1,$marks_obtained);

									$update_score = mysqli_query($this->conn,"UPDATE lower_primary_assessment SET marks_obtained = $marks_obtained
									WHERE student_id = {$student_id} AND  subject_id= {$subject_id}
									AND term_id = {$term_id} AND class_id = {$class_id}");
									confirm_query($update_score);
								}


						}




						$array2 = array();
						$array2[0] = 1;



						for($index=1; $index <= $size; $index++)
						{
								if($array1[$index]  == $array1[$index - 1])
								{

									 $array2[$index] =  $array2[$index - 1];
								}else{
									 $array2[$index] = ($index + 1);

								}

						}

						$student_results = mysqli_query($this->conn,"SELECT students.id,
						CONCAT(students.first_name,' ',last_name) AS STUDENT_NAME,	marks_obtained,grade,position,remarks
						FROM  lower_primary_assessment,students WHERE students.id =  lower_primary_assessment.student_id AND
						 lower_primary_assessment.term_id={$term_id} AND  lower_primary_assessment.subject_id={$subject_id}
						AND  lower_primary_assessment.class_id = {$class_id} ORDER BY marks_obtained DESC
						");
						confirm_query($student_results);
						$counter = 0;


						if(mysqli_num_rows($student_results) > 0)
						{

								 //$student_id = $row['student_id'];
								while($rows = mysqli_fetch_assoc($student_results))
								{

								   $student_id = $rows['id'];
									$student_name = $rows['STUDENT_NAME'];
									$marks_obtained = round($rows['marks_obtained']);


									$grading = $this->primary_subject_grade($marks_obtained);
									$grade = $grading[1];
									$remarks = $grading[0];

									 $position = $array2[$counter];
									 $code = $this->position_code($position);
									 $position =  $position.$code;



									  $update_scores = mysqli_query($this->conn,"UPDATE lower_primary_assessment SET remarks = '$remarks', position = '$position',
									grade = '$grade'  WHERE student_id = {$student_id} AND  subject_id= {$subject_id}
									AND term_id = {$term_id} AND class_id = {$class_id}");
									confirm_query($update_scores);


									$counter++;

								}

						}












	/////OVERALL CLASS POSITION


		$all_students_in_class = mysqli_query($this->conn,"SELECT DISTINCT student_id FROM lower_primary_assessment
								WHERE class_id = {$class_id}  AND term_id = {$term_id} ");
		confirm_query($all_students_in_class);


		$total_subjects = mysqli_query($this->conn,"select distinct `subject_id` from  lower_primary_assessment
										WHERE `class_id`= {$class_id} AND `term_id`={$term_id}");
		$total_subjects_for_term = mysqli_num_rows($total_subjects);



		WHILE($QUERY_RESULT = mysqli_fetch_assoc($all_students_in_class))
		{
				 $id = $QUERY_RESULT['student_id'];
				$subject_scores = mysqli_query($this->conn,"SELECT SUM(marks_obtained) AS TOTAL_SCORE FROM lower_primary_assessment
				WHERE class_id = {$class_id}  AND term_id = {$term_id}  AND student_id = {$id}  ");
				confirm_query($subject_scores);


				$a = mysqli_fetch_assoc($subject_scores);
				 $OVERALL_SCORE = round($a['TOTAL_SCORE']);
				$AVERAGE_SCORE = round(($OVERALL_SCORE / $total_subjects_for_term),2);



				$update_term_total = mysqli_query($this->conn,"UPDATE  student_term_total SET term_total_score = $OVERALL_SCORE,
												term_average_score = $AVERAGE_SCORE
												WHERE class_id={$class_id} AND term_id = {$term_id} AND student_id = {$id}	");
				confirm_query($update_term_total);

		}

		$score_sheet = array();



		$score_sheet_data = mysqli_query($this->conn,"SELECT  term_total_score FROM  student_term_total
										WHERE class_id = {$class_id} AND term_id = {$term_id}
										ORDER BY term_total_score DESC");
		confirm_query($score_sheet_data);
		$class_size = mysqli_num_rows($score_sheet_data);


		WHILE($RESULTS = mysqli_fetch_assoc($score_sheet_data))
		{
				$OVERALL_SCORE = $RESULTS['term_total_score'];
				array_push($score_sheet,$OVERALL_SCORE);

		}


		$overall_class_postion = array();
		$overall_class_postion[0] = 1;


		for($index=1; $index < $class_size; $index++)
		{
				if($score_sheet[$index]  == $score_sheet[$index - 1])
				{

					 $overall_class_postion[$index] =  $overall_class_postion[$index - 1];
				}else{
					 $overall_class_postion[$index] = ($index + 1);

				}

		}

		$student_overall_position = mysqli_query($this->conn,"SELECT student_id FROM student_term_total
									WHERE term_id = {$term_id} AND class_id = {$class_id}
									ORDER BY term_total_score DESC  ");
		confirm_query($student_overall_position);

		$initial_count = 0;
		while($row = mysqli_fetch_assoc($student_overall_position))
		{
			 $student_id = $row['student_id'];
			 $OVERALL_POSITION = $overall_class_postion[$initial_count];
			 $CODE = $this->position_code($OVERALL_POSITION);
			 $OVERALL_POSITION =  $OVERALL_POSITION.$CODE;

			 $update_overall_position = mysqli_query($this->conn,"UPDATE student_term_total SET  class_position = '$OVERALL_POSITION'
									WHERE class_id={$class_id}  AND term_id = {$term_id} AND student_id = {$student_id} ");
			 confirm_query($update_overall_position);
			$initial_count++;
		}

		/////end of OVERALL CLASS POSITION
}	*/
$valid='true';
		if ($update_scores) {
			if ($flag == 'class_work_results') {
				header("location:academics.php?class_work_results=$valid&&class_id=$class_id&&subject_id=$subject_id&&term_id=$term_id&&err=$err");
			} else if ($flag == 'class_test_results') {
				header("location:academics.php?class_test_results=$valid&&class_id=$class_id&&subject_id=$subject_id&&term_id=$term_id&&err=$err");
			} else if ($flag == 'exams_results') {
				header("location:academics.php?exams_results=$valid&&class_id=$class_id&&subject_id=$subject_id&&term_id=$term_id&&err=$err");
			} else if ($flag == 'lower_primary') {
				header("location:academics.php?exams_results=1&&class_id=$class_id&&subject_id=$subject_id&&term_id='$term_id'");
			} else if ($flag == 'home_work_results') {
				header("location:academics.php?home_work_results=$valid&&class_id=$class_id&&subject_id=$subject_id&&term_id=$term_id&&err=$err");
			}
		}

	}///////closes main function


	function position_code($position)
	{

		if ($position == 1 || $position == 21 || $position == 31 || $position == 41
			|| $position == 51 || $position == 61 || $position == 71 || $position == 81
			|| $position == 91 || $position == 101 || $position == 121 || $position == 131
			|| $position == 141 || $position == 151
		) {
			$code = "ST";
		} else if ($position == 2 || $position == 22 || $position == 32 || $position == 42
			|| $position == 52 || $position == 62 || $position == 72 || $position == 82
			|| $position == 92 || $position == 202 || $position == 222 || $position == 232
			|| $position == 242 || $position == 252
		) {
			$code = "ND";
		} else if ($position == 3 || $position == 23 || $position == 33 || $position == 43
			|| $position == 53 || $position == 63 || $position == 73 || $position == 83
			|| $position == 93 || $position == 303 || $position == 323 || $position == 333
			|| $position == 343 || $position == 351
		) {
			$code = "RD";
		} else {
			$code = "TH";
		}

		return $code;
	}


	function subject_grade($total_subject_score)
	{

		$grading = array();
		if (($total_subject_score >= 90) && ($total_subject_score <= 100)) {
			$grade = '1';
			$remarks = "Excellent";
		} elseif (($total_subject_score >= 80) && ($total_subject_score <= 89)) {
			$remarks = "Very Good";
			$grade = '2';
		} elseif (($total_subject_score >= 70) && ($total_subject_score <= 79)) {
			$remarks = "Good";
			$grade = '3';
		} elseif (($total_subject_score >= 60) && ($total_subject_score <= 69)) {
			$remarks = "Credit";
			$grade = '4';
		} elseif (($total_subject_score >= 50) && ($total_subject_score <= 59)) {
			$remarks = "Above Average";
			$grade = '5';
		} elseif (($total_subject_score >= 50) && ($total_subject_score <= 59)) {
			$remarks = "Average";
			$grade = '6';
		}elseif (($total_subject_score >= 50) && ($total_subject_score <= 59)) {
			$remarks = "Below Average";
			$grade = '7';
		}elseif (($total_subject_score >= 40) && ($total_subject_score <= 49)) {
			$remarks = "Fail";
			$grade = '8';
		} elseif ($total_subject_score <= 39) {
			$remarks = "Fail";
			$grade = '9';
		}


		$grading[0] = $remarks;
		$grading[1] = $grade;

		return $grading;
	}


	function mock_grading_system($total_subject_score)
	{

		$grading = array();
		if (($total_subject_score >= 85) && ($total_subject_score <= 100)) {
			$grade = '1';
			$remarks = "Highest";
		} elseif (($total_subject_score >= 75) && ($total_subject_score <= 84)) {
			$remarks = "Higher";
			$grade = '2';
		} elseif (($total_subject_score >= 60) && ($total_subject_score <= 74)) {
			$remarks = "High";
			$grade = '3';
		} elseif (($total_subject_score >= 55) && ($total_subject_score <= 59)) {
			$remarks = "High average";
			$grade = '4';
		} elseif (($total_subject_score >= 50) && ($total_subject_score <= 54)) {
			$remarks = "Average";
			$grade = '5';
		} elseif (($total_subject_score >= 45) && ($total_subject_score <= 49)) {
			$remarks = "Low Average";
			$grade = '6';
		} elseif (($total_subject_score >= 40) && ($total_subject_score <= 44)) {
			$remarks = "Low";
			$grade = "7";
		} elseif (($total_subject_score >= 30) && ($total_subject_score <= 39)) {
			$remarks = "Lower";
			$grade = "8";
		} elseif ($total_subject_score < 30) {
			$remarks = "Lowest";
			$grade = "9";
		}elseif ($total_subject_score < 0 || $total_subject_score > 100) {
			$remarks = "N/A";
			$grade = "0";
		}

		$grading[0] = $remarks;
		$grading[1] = $grade;

		return $grading;
	}


	function primary_subject_grade($total_subject_score)
	{

		$grading = array();
		if (($total_subject_score >= 90) && ($total_subject_score <= 100)) {
			$grade = 'A+';
			$remarks = "O. Performance";
		} elseif (($total_subject_score >= 85) && ($total_subject_score <= 89)) {
			$remarks = "Excellent";
			$grade = 'A';
		} elseif (($total_subject_score >= 75) && ($total_subject_score <= 84)) {
			$remarks = "V. Good";
			$grade = 'B';
		} elseif (($total_subject_score >= 65) && ($total_subject_score <= 74)) {
			$remarks = "Good";
			$grade = 'C';
		} elseif (($total_subject_score >= 50) && ($total_subject_score <= 64)) {
			$remarks = "Pass";
			$grade = 'D';
		} elseif ($total_subject_score < 50) {
			$remarks = "Below Average";
			$grade = 'E';
		}


		$grading[0] = $remarks;
		$grading[1] = $grade;

		return $grading;
	}

	/* function primary_subject_grade($total_subject_score)
	{
		$grading = array();
		if (($total_subject_score >=90 ) && ($total_subject_score <=100))
			{
				$grade = "A";
				$remarks = "Excellent";
			}elseif (($total_subject_score >=80 ) && ($total_subject_score <=89))
			{
				$remarks = "Very Good";
				$grade = "B1";
			}elseif (($total_subject_score >=70) && ($total_subject_score <=79))
			{
				$remarks = "Good";
				$grade = "B2";
			}elseif (($total_subject_score >=65) && ($total_subject_score <=69))
			{
				$remarks = "High average";
				$grade = "C1";
			}elseif (($total_subject_score >=60) && ($total_subject_score <=64))
			{
				$remarks = "Average";
				$grade = "C2";
			}elseif (($total_subject_score >=55) && ($total_subject_score <=59))
			{
				$remarks = "Low Average";
				$grade = "D1";
			}elseif (($total_subject_score >=50) && ($total_subject_score <=54))
			{
				$remarks = "Low";
				$grade = "D2";
			}elseif (($total_subject_score >=40) && ($total_subject_score <=49))
			{
				$remarks = "Lower";
				$grade = "E1";
			}
			elseif ($total_subject_score < 40)
			{
				$remarks = "Lowest";
				$grade = "E2";
			}

			$grading[0] = $remarks ;
			$grading[1] = $grade ;

			return $grading;
	}

	 */

	function print_total($total_count, $title)
	{
		echo " <b class='record_count'>Total</b> ";
		echo $title;
		echo ": ";
		echo $total_count;

	}


	function total_teacher_subjects($staff_id)
	{
		$query = "SELECT count(*) FROM class_teachers WHERE staff_id = {$staff_id}";
		return $this->perform_query($query);

	}


	function perform_query($sql)
	{
		$this->query = mysqli_query($this->conn,$sql);
		confirm_query($this->query);
		$this->query = mysqli_fetch_array($this->query);
		return $this->query = $this->query[0];
	}


	function logout()
	{

		$id = $this->staff_login_id;
		$LOGIN_UPDATE = mysqli_query($this->conn, "UPDATE login_info SET 
						last_logon_date_time = current_logon_date_time,  current_logon_date_time =' ' where user_id='$id' ");
		confirm_query($LOGIN_UPDATE);

		if (isset($_SESSION['PARENT_ID'])) {
			$p_id = $_SESSION['PARENT_ID'];
			$LOGIN_UPDATE = mysqli_query($this->conn, "UPDATE login_info SET 
						last_logon_date_time = current_logon_date_time,  current_logon_date_time =' ' where parent_id='$p_id' ");
			confirm_query($LOGIN_UPDATE);
		}

		$_SESSION = array();

		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time() - 42000, '/');
		}

		session_destroy();

		header("location:../index.php?logout=1");
	}


	function update_record_status($table_name, $status_name, $primary_key_field, $primary_key_value, $record_name, $title)
	{
		$query = mysqli_query($this->conn, "UPDATE   {$table_name} SET {$status_name} = 0 WHERE {$primary_key_field} = 
			{$primary_key_value} ");
		confirm_query($query);
		if ($query) {
			$message = "<lable class='del'> The " . $title . ':  ' . $record_name . " was deleted successfully.";
			return $this->print_message(1, $message);
		}
		return $this->print_message(1, "Opps");

	}


	function last_inserted_staff_record()
	{
		$query = mysqli_query($this->conn, "SELECT id FROM staff ORDER BY id DESC LIMIT 1");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $this->last_staff_id = $a['id'];
	}


	function last_inserted_student_id()
	{
		$query = mysqli_query($this->conn, "SELECT id FROM students ORDER BY id DESC LIMIT 1");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $this->last_student_id = $a['id'];
	}

	function jhs_3_class_id()
	{
		///JHS 3 class_id
		$JHS_3_class_id = mysqli_query($this->conn, "SELECT class_id  FROM classes  ORDER BY class_id  DESC LIMIT 1");
		confirm_query($JHS_3_class_id);
		$a = mysqli_fetch_assoc($JHS_3_class_id);
		//return $class_id = $a['class_id'];
		return 13;

	}


	function mock_setting($class_id)
	{
		$current_term = $this->CURRENT_TERM;
		//$current_term_id = $this->current_term_id;
		$term_id = $this->current_term_id;

		$get_class_last_mock_number = mysqli_query($this->conn, "SELECT mock_number FROM  std_mock_settings
											WHERE term_id = '$term_id' and class_id='$class_id' 
											ORDER BY mock_id DESC LIMIT 1");
		confirm_query($get_class_last_mock_number);

		if (mysqli_num_rows($get_class_last_mock_number) == 0) {
			$mock_number = 1;
		} else {

			$rows_set = mysqli_fetch_assoc($get_class_last_mock_number);
			$mock_number = $rows_set['mock_number'] + 1;
		}


		$class = strtoupper($this->get_class_name($class_id));


		///// std_mock_settings table
		$std_mock_settings = mysqli_query($this->conn, "INSERT INTO  std_mock_settings (term_id,mock_number,class_id) 
		VALUES ('$term_id', '$mock_number', '$class_id')  ");
		confirm_query($std_mock_settings);


		///get the mock_id
		$MOCK_ID_QUERY = mysqli_query($this->conn, "SELECT mock_id FROM std_mock_settings ORDER BY mock_id DESC LIMIT 1 ");
		confirm_query($MOCK_ID_QUERY);
		$ANS = mysqli_fetch_assoc($MOCK_ID_QUERY);
		$MOCK_ID = $ANS['mock_id'];

		//$this->jhs_3_class_id();


		/* ///JHS 3 class_id
		$JHS_3_class_id = mysqli_query($this->conn,"SELECT class_id  FROM classes  ORDER BY class_id  DESC LIMIT 1");
		confirm_query($JHS_3_class_id);
		$a =   mysqli_fetch_assoc($JHS_3_class_id);
		$class_id = $a['class_id']; */


		//$class_id  = $this->jhs_3_class_id();

		/////get_class_students
		$get_class_students = mysqli_query($this->conn, "SELECT id FROM students WHERE class_id = '$class_id'  AND student_status =1  ");
		confirm_query($get_class_students);

		WHILE ($rows = mysqli_fetch_assoc($get_class_students)) {
			$student_id = $rows['id'];

			//std_mock_total_results TABLE
			$std_mock_total_results = mysqli_query($this->conn, "INSERT INTO std_mock_total_results ( mock_id, student_id,class_id ) 
				VALUES ('$MOCK_ID', '$student_id', '$class_id')  ");
			confirm_query($std_mock_total_results);


			//std_mock_best_six_results  TABLE	for best six results
			$std_mock_best_six_results = mysqli_query($this->conn, "INSERT INTO std_mock_best_six_results ( mock_id, student_id,class_id) 
				VALUES ($MOCK_ID, $student_id, $class_id)  ");
			confirm_query($std_mock_best_six_results);

			//std_mock_students_remarks TABLE
			$std_mock_students_remarks = mysqli_query($this->conn, "INSERT INTO std_mock_students_remarks ( mock_id, student_id,class_id ) 
				VALUES ('$MOCK_ID', '$student_id', '$class_id')  ");
			confirm_query($std_mock_students_remarks);


			$JHS_3_SUBJECTS = mysqli_query($this->conn, "SELECT subject_id FROM class_subjects WHERE status = 1
			AND class_id = '$class_id'   ");
			confirm_query($JHS_3_SUBJECTS);

			WHILE ($row = mysqli_fetch_assoc($JHS_3_SUBJECTS)) {
				$subject_id = $row['subject_id'];

				///std_mock_results  table
				$std_mock_results = mysqli_query($this->conn, "INSERT INTO std_mock_results ( mock_id, student_id,subject_id,class_id ) 
				VALUES ('$MOCK_ID', '$student_id', '$subject_id', '$class_id')  ");
				confirm_query($std_mock_results);
			}
		}

		if ($std_mock_results) {
			header("location:settings.php?mock_results_settings=true&&class=$class&&current_term=$current_term&&mock_number=$mock_number");
			/*echo "<script type='text/javascript'>
				alert('$class MOCK NUMBER $mock_number IS SUCCESSFULLY SET  FOR $current_term ACADEMIC YEAR.');
			</script>";*/
		}

	}


	function get_bill_amount($income_id, $term_id, $class_id)
	{
		$query = mysqli_query($this->conn, "SELECT amount_due from billing_general WHERE income_id=$income_id  AND  term_id='$term_id'  AND class_id=$class_id   ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $this->get_format_number($a['amount_due']);
	}


	function get_total_class_bill($term_id, $class_id)
	{
		$query = mysqli_query($this->conn, "SELECT SUM(amount_due) AS TOTAL
 from billing_general WHERE  term_id='$term_id'  AND class_id=$class_id   ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $this->get_format_number($a['TOTAL']);
	}

	function get_bill_amount_paid($income_id, $term_id, $student_id)
	{

		$query = mysqli_query($this->conn, "SELECT SUM(amount) AS TOTAL_AMOUNT_PAID from income
 WHERE income_id=$income_id  AND  term_id='$term_id'   AND  student_id=$student_id  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $this->get_format_number($a['TOTAL_AMOUNT_PAID']);
	}

	function get_total_bill_amount_paid($term_id, $student_id, $class_id)
	{

		$query = mysqli_query($this->conn, "SELECT SUM(amount_paid) AS TOTAL_AMOUNT_PAID from   student_fee_payment
 WHERE  term_id='$term_id'   AND  student_id=$student_id AND class_id=$class_id and delete_status='A' ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $this->get_format_number($a['TOTAL_AMOUNT_PAID']);
	}

	function get_student_outstanding_balance($term_id, $class_id, $student_id)
	{
		$check_special_condition = mysqli_query($this->conn, "SELECT sum(amount_due) as amount_due
				 FROM billing_special_condition WHERE  term_id='$term_id'
				 AND class_id=$class_id and student_id=$student_id");
		confirm_query($check_special_condition);

		$a = mysqli_fetch_assoc($check_special_condition);

		if ($a['amount_due'] > 0) {


			$total_fee_amount = $this->get_format_number($a['amount_due']);
		} else {

			$get_the_income_for_this_class = mysqli_query($this->conn, "SELECT sum(amount_due) as amount_due
				 FROM billing_general WHERE  term_id='$term_id' AND class_id=$class_id");
			confirm_query($get_the_income_for_this_class);

			$results = mysqli_fetch_assoc($get_the_income_for_this_class);
			$total_fee_amount = $this->get_format_number($results['amount_due']);
		}


		////GET TOTAL FEES PAID
		$get_total_student_paid = mysqli_query($this->conn, "SELECT sum(amount_paid) as amount_due
				 FROM student_fee_payment WHERE  term_id='$term_id'
				 AND class_id=$class_id and student_id=$student_id");
		confirm_query($get_total_student_paid);

		$resul = mysqli_fetch_assoc($get_total_student_paid);

		$amount_paid = $this->get_format_number($resul['amount_due']);


		return $balance = $this->get_format_number($total_fee_amount - $amount_paid);

	}

	function get_student_parent_phone_number($parent_id)
	{
		$query = mysqli_query($this->conn, "SELECT phone_number FROM parents where parent_id=$parent_id");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $a['phone_number'];

	}

	function get_total_bill_amount_paid1($term_id, $student_id)
	{

		$query = mysqli_query($this->conn, "SELECT SUM(amount) AS TOTAL_AMOUNT_PAID from income
 WHERE  term_id='$term_id'   AND  student_id=$student_id  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $this->get_format_number($a['TOTAL_AMOUNT_PAID']);
	}


	function get_current_term_details()
	{
		$query = mysqli_query($this->conn, "SELECT * FROM term_settings
   ORDER BY term_id DESC LIMIT 1");
		confirm_query($query);
		return $a = mysqli_fetch_assoc($query);
	}

	function check_special_condition_bill($income_id, $term_id, $student_id)
	{
		$query = mysqli_query($this->conn, "SELECT amount_due from billing_special_condition
 WHERE income_id=$income_id  AND  term_id='$term_id'   AND  student_id=$student_id  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $this->get_format_number($a['amount_due']);
	}


	function get_total_special_condition_bill_for_student($term_id, $student_id)
	{
		$query = mysqli_query($this->conn, "SELECT SUM(amount_due) AS TOTAL from billing_special_condition
 WHERE   term_id='$term_id'   AND  student_id=$student_id  ");
		confirm_query($query);
		$a = mysqli_fetch_assoc($query);
		return $this->get_format_number($a['TOTAL']);
	}


	function current_term_registration($class_id, $start_date)
	{
		$student_id = $this->last_inserted_student_id();
		$current_term_id = $this->current_term_id;
		//$class_id = $class_id;


		/*added here to cater for mock*/

		$get_class_last_mock_number = mysqli_query($this->conn, "SELECT mock_id FROM  std_mock_settings
											WHERE term_id = '$current_term_id' and class_id='$class_id' 
											ORDER BY mock_id DESC LIMIT 1");
		confirm_query($get_class_last_mock_number);

		if (mysqli_num_rows($get_class_last_mock_number) > 0) {
			$rows_set = mysqli_fetch_assoc($get_class_last_mock_number);
			$MOCK_ID = $rows_set['mock_id'];

			//std_mock_total_results TABLE
			$std_mock_total_results = mysqli_query($this->conn, "INSERT INTO std_mock_total_results ( mock_id, student_id,class_id ) 
				VALUES ('$MOCK_ID', '$student_id', '$class_id')  ");
			confirm_query($std_mock_total_results);


			//std_mock_best_six_results  TABLE	for best six results
			$std_mock_best_six_results = mysqli_query($this->conn, "INSERT INTO std_mock_best_six_results ( mock_id, student_id,class_id) 
				VALUES ($MOCK_ID, $student_id, $class_id)  ");
			confirm_query($std_mock_best_six_results);

			//std_mock_students_remarks TABLE
			$std_mock_students_remarks = mysqli_query($this->conn, "INSERT INTO std_mock_students_remarks ( mock_id, student_id,class_id ) 
				VALUES ('$MOCK_ID', '$student_id', '$class_id')  ");
			confirm_query($std_mock_students_remarks);


			$get_class_subjects = mysqli_query($this->conn, "SELECT subject_id FROM class_subjects WHERE status = 1
					AND class_id = '$class_id'   ");
			confirm_query($get_class_subjects);

			WHILE ($row = mysqli_fetch_assoc($get_class_subjects)) {
				$subject_id = $row['subject_id'];

				///std_mock_results  table
				$std_mock_results = mysqli_query($this->conn, "INSERT INTO std_mock_results ( mock_id, student_id,subject_id,class_id ) 
						VALUES ('$MOCK_ID', '$student_id', '$subject_id', '$class_id')  ");
				confirm_query($std_mock_results);
			}
		}


		/////student_term_total table
		$term_total_table = mysqli_query($this->conn, "INSERT INTO student_term_total (class_id, student_id, term_id ) 
		VALUES ($class_id, $student_id, $current_term_id)  ");
		confirm_query($term_total_table);


		//get class fees if isset
		$get_fees = mysqli_query($this->conn, "select sum(amount_due) as term_fees from billing_general where class_id='$class_id' and term_id='$current_term_id'");
		confirm_query($get_fees);
		$rows = mysqli_fetch_assoc($get_fees);
		$term_fees = $rows['term_fees'];

		//if(mysqli_fetch_assoc)

		////populate student validated balances
		$ca_student_validated_balances = mysqli_query($this->conn, "INSERT INTO ca_student_validated_balances
				   (student_id, term_id, class_id, student_start_date, term_fees) VALUES ($student_id, $current_term_id, $class_id, '$start_date', '$term_fees')  ");
		confirm_query($ca_student_validated_balances);

		/////student_remarks table
		$student_remarks = mysqli_query($this->conn, "INSERT INTO student_remarks ( student_id, term_id ) 
		VALUES ( $student_id, $current_term_id)  ");
		confirm_query($student_remarks);


		$continous_assessment = mysqli_query($this->conn, "SELECT subject_id FROM class_subjects WHERE status = 1
		 AND class_id = {$class_id}");
		confirm_query($continous_assessment);
		if (mysqli_num_rows($continous_assessment) > 0) {
			while ($row = mysqli_fetch_assoc($continous_assessment)) {
				$subject_id = $row['subject_id'];

				/* 	if($class_id == 1 || $class_id== 2)
				{
					$query = mysqli_query($this->conn,"INSERT INTO  lower_primary_assessment (class_id, term_id, student_id,  subject_id)
					VALUES ($class_id, $current_term_id,  $student_id, $subject_id )");
					confirm_query($query);
				}else{ */
				$query = mysqli_query($this->conn, "INSERT INTO  continous_assessment (class_id,subject_id, student_id,  term_id)
					VALUES ($class_id, $subject_id,  $student_id, $current_term_id )");
				confirm_query($query);
				//}
			}


		}
	}

////////registers students for the current term
	function term_registration($start_date)
	{
		//SET BILLING TYPE AS GENERAL (G) FOR ALL STUDENTS
		$update_student_billing_status = mysqli_query($this->conn, "UPDATE students SET billing_type='G' ");
		confirm_query($update_student_billing_status);

		//////update term status table
		$term_status = mysqli_query($this->conn, "SELECT term_id FROM term_settings ORDER BY term_id DESC LIMIT 1");
		confirm_query($term_status);
		$rows = mysqli_fetch_assoc($term_status);
		$new_term_id = $rows['term_id'];

		///create cash at hand for this term
		$cash_at_hand_table = mysqli_query($this->conn, "INSERT INTO ca_cash_at_hand (balance, term_id) values (0, '$new_term_id') ");
		confirm_query($cash_at_hand_table);


		///INSERT CLOSING BALANCES INTO HISTORY TABLE
		$get_closing_bals = mysqli_query($this->conn, "SELECT * FROM ca_opening_balance");
		confirm_query($get_closing_bals);

		while ($row_closing = mysqli_fetch_assoc($get_closing_bals)) {
			$op_id = $row_closing['op_id'];
			$name = $row_closing['name'];
			$dr_cr = $row_closing['dr_cr'];
			$opening_balance = $row_closing['opening_balance'];
			$closing_balance = $row_closing['closing_balance'];
			$term_id = $row_closing['term_id'];

			$insert_record = mysqli_query($this->conn, "insert into ca_opening_balance_history 
			   (op_id, name, dr_cr, opening_balance, closing_balance, term_id)  values ($op_id, '$name',  '$dr_cr', '$opening_balance', 
			   '$closing_balance', '$term_id')  ");
			confirm_query($insert_record);
		}


		//SET ALL ACCOUNT CLOSING BAL AS OPENING BALANCE FOR NEW TERM
		$update_closing_balances = mysqli_query($this->conn, "UPDATE ca_opening_balance SET opening_balance =closing_balance, closing_balance=0 ");
		confirm_query($update_closing_balances);


		/////register students	in the student_term_total table
		$student_ids = mysqli_query($this->conn, "SELECT id,class_id,start_date FROM students WHERE student_status = 1 ");
		confirm_query($student_ids);
		if (mysqli_num_rows($student_ids) > 0) {
			while ($a = mysqli_fetch_assoc($student_ids)) {
				$student_id = $a['id'];
				$class_id = $a['class_id'];
				$student_start_date = $a['start_date'];

				////populate student validated balances
				$ca_student_validated_balances = mysqli_query($this->conn, "INSERT INTO ca_student_validated_balances
				   (student_id, term_id, class_id, student_start_date) VALUES ($student_id, $new_term_id, $class_id, '$student_start_date')  ");
				confirm_query($ca_student_validated_balances);


				$term_total_table = mysqli_query($this->conn, "INSERT INTO student_term_total (class_id, student_id, term_id, start_date ) 
					VALUES ($class_id, $student_id, $new_term_id, '$start_date')  ");
				confirm_query($term_total_table);

				/////student_remarks table
				$student_remarks = mysqli_query($this->conn, "INSERT INTO student_remarks ( student_id, term_id ) 
					VALUES ( $student_id, $new_term_id)  ");
				confirm_query($student_remarks);


				$continous_assessment = mysqli_query($this->conn, "SELECT subject_id FROM class_subjects WHERE status = 1
					AND class_id = {$class_id}");
				confirm_query($continous_assessment);
				if (mysqli_num_rows($continous_assessment) > 0) {
					while ($row = mysqli_fetch_assoc($continous_assessment)) {
						$subject_id = $row['subject_id'];

						/* if($class_id == 1 || $class_id== 2)
                        {
                            $query = mysqli_query($this->conn,"INSERT INTO  lower_primary_assessment (class_id, term_id, 
                            student_id,  subject_id)
                            VALUES ($class_id, $new_term_id,  $student_id, $subject_id )");
                            confirm_query($query);
                        }else{ */

						$query = mysqli_query($this->conn, "INSERT INTO  continous_assessment (class_id, subject_id, student_id,  term_id)
								VALUES ($class_id, $subject_id,  $student_id, $new_term_id )");
						confirm_query($query);
						//}
					}


				}
			}


			if ($query) {
				header("location:new_calender.php?term_reg=success");
			}

		}////closes if


		function update_record_name($table_name, $update_field_name, $new_name, $primary_key_field, $record_id, $message)
		{
			$query = mysqli_query($this->conn, "UPDATE   {$table_name} SET {$update_field_name} = '{$new_name}'  WHERE {$primary_key_field} = {$record_id} ");
			confirm_query($query);
			if ($query) {

				return $this->print_message(1, $message);
			}
			return $this->print_message(1, "Oops");
		}


		function get_format_number($number)
		{
			return number_format((float)$number, 2, '.', '');
		}

		function print_message($flag, $message)
		{
			if ($flag == 0) {
				echo "<div class='error_message'>$message</div>";
			} else if ($flag == 1) {
				echo "<div class='sucess_message'>$message</div>";
			} else if ($flag == 2) {
				echo "<div class='warning_message'>$message</div>";
			}

		}


		function get_student_validated_balance($student_id, $term_id, $class_id)
		{
			$get_student_validated_balance = mysqli_query($this->conn, "select * from ca_student_validated_balances
		  where student_id=$student_id   and term_id=$term_id  and class_id=$class_id  ");
			confirm_query($get_student_validated_balance);

			return mysqli_fetch_assoc($get_student_validated_balance);

		}


	}	///////////update subjects when a student class is changed
	function update_subject_registration($class_id,$student_id,$former_class_id)
	{



		////delete from continous assessment table
		$current_term_id = $this->current_term_id;
		// if($former_class_id > 2 )
		// {
		/* $del_assessment = mysqli_query($this->conn,"DELETE FROM continous_assessment WHERE student_id = {$student_id}
        AND term_id = {$current_term_id}  AND class_id = {$former_class_id}"); */

		$del_assessment = mysqli_query($this->conn,"DELETE FROM continous_assessment WHERE student_id = {$student_id}
			AND term_id = {$current_term_id} ");

		confirm_query($del_assessment);
		/* 	}else{
                $del_assessment = mysqli_query($this->conn,"DELETE FROM lower_primary_assessment WHERE student_id = {$student_id}
                AND term_id = {$current_term_id}  AND class_id = {$former_class_id} ");
                confirm_query($del_assessment);
    
            } */


		////delete from student_term_total table
		/* $del_student_term_total = mysqli_query($this->conn,"DELETE FROM student_term_total WHERE student_id = {$student_id}
		AND term_id = {$current_term_id}  AND class_id = {$former_class_id}"); */

		$del_student_term_total = mysqli_query($this->conn,"DELETE FROM student_term_total WHERE student_id = {$student_id}
		AND term_id = {$current_term_id} ");
		confirm_query($del_student_term_total);

		////delete from  student_remarks table
		/* $del_student_remarks = mysqli_query($this->conn,"DELETE FROM  student_remarks WHERE student_id = {$student_id}
		AND term_id = {$current_term_id}  ") */;
		$del_student_remarks = mysqli_query($this->conn,"DELETE FROM  student_remarks WHERE student_id = {$student_id}
		AND term_id = {$current_term_id}  ");
		confirm_query($del_student_remarks);





		/////student_term_total table
		$term_total_table = mysqli_query($this->conn,"INSERT INTO student_term_total (class_id, student_id, term_id ) 
				VALUES ($class_id, $student_id, $current_term_id)  ");
		confirm_query($term_total_table);



		/////student_remarks table
		$student_remarks = mysqli_query($this->conn,"INSERT INTO student_remarks ( student_id, term_id ) 
				VALUES ( $student_id, $current_term_id)  ");
		confirm_query($student_remarks);



		$continous_assessment = mysqli_query($this->conn,"SELECT subject_id FROM class_subjects WHERE status = 1
				 AND class_id = {$class_id}");
		confirm_query($continous_assessment);
		if(mysqli_num_rows($continous_assessment) > 0 )
		{
			while($row = mysqli_fetch_assoc($continous_assessment))
			{
				$subject_id = $row['subject_id'];

				/* if($class_id == 1 || $class_id== 2)
                {
                    $query = mysqli_query($this->conn,"INSERT INTO  lower_primary_assessment (class_id, term_id, student_id, 
                     subject_id)
                    VALUES ($class_id, $current_term_id,  $student_id, $subject_id )");
                    confirm_query($query);
                }else{ */
				$query = mysqli_query($this->conn,"INSERT INTO  continous_assessment (class_id,subject_id, student_id,  term_id)
							VALUES ($class_id, $subject_id,  $student_id, $current_term_id )");
				confirm_query($query);
				//}
			}


		}

		if($query)
		{
			header("location:student_edit.php?reg=1&&former_class_id=$former_class_id&&student_id=$student_id&&update=true");
		}
	}


}
	

	
	
	
$top_ridge = new top_ridge($top_ridge_db_connection);
global $top_ridge;

?>