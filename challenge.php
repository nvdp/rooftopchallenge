<?php
$con = = new mysqli("localhost","my_user","my_password","my_db");
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

// we use  two table 
// first have record of all doctor with its available time

// 2nd have  record of time of apointment  with doctor


// Ist Api- I want to see which coaches I can schedule with.
if(!empty($_GET['allDoctor']))
{
	$sql = 'select name from doctor group by name';
	$result = mysqli_query($con, $sql)
	if(!empty($result)){
		echo json_encode($result);
		exit;
	}
		
}

// 2nd Api- I want to see what 30-minute time slots are available to schedule with a particular coach.

if(!empty($_GET['name']) && !empty($_GET['dow']))
{
	$sql = "select available_at,available_until from doctor where name='".$_GET['name']."' && Day of Week = '".$_GET['dow']."'";
	$result = mysqli_query($con, $sql)
	if(!empty($result)){
		$rowdata = mysqli_fetch_assoc($result)
		$available_at = strtotime($rowdata['available_at']);
		$available_until = strtotime($rowdata['available_until']);

		for ($i=$available_at;$i<=$available_until;$i = $i + 30*60)
		{
			$time = date('H:i',$i);		

			$sql_sel = "select booking_at from appointment where name='".$_GET['name']."' AND Day of Week = '".$_GET['dow']."' AND time = '".$time."'";
			$result = mysqli_query($con, $sql_sel);
			$count = mysqli_num_rows($result);
			if($count == 0){
				$availableArr[]['appointmentTime'] = $time;
			}

		}
		echo json_encode($availableArr);
		exit;
		
	}
		
}


// 3rd Api-  I want to book an appointment with a coach at one of their available times.
if(!empty($_GET['name']) && !empty($_GET['dow']) && !empty($_GET['appointment_time']))
{

	$sql_sel = "select booking_at from appointment where name='".$_GET['name']."' && Day of Week = '".$_GET['dow']."' and time = '".$_GET['appointment_time']."'";
	$result = mysqli_query($con, $sql_sel);
	$count = mysqli_num_rows($result);
	$finalArr = array();
	if($count == 0){
		
		sql_ins = "insert into appointment(`time`,`name`,`Day of Week`)Values('".$_GET['name']."','".$_GET['appointment_time']."','".$_GET['dow']."')";
		$result = mysqli_query($con, $sql_ins);
		$finalArr['message'] ='your appointment has booked';
		$finalArr['success'] ='1';
	}else{
		$finalArr['message'] ='Time is not availble for you.please choose another time';
		$finalArr['error'] ='1';
	}
	echo json_encode($finalArr);
	exit;
}

?>