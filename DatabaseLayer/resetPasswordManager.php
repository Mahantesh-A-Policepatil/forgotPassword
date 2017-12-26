
<?php 
include_once "UserLoginManager.php";

$obj = new UserLoginManager();

$is_used = 0;
$email = $_REQUEST['email'];
$user_token = $_REQUEST['token'];
$result = $obj->checkIfTokenExpired($email, $user_token, $is_used);

date_default_timezone_set("Asia/Kolkata");
$current_timestamp = (new \DateTime())->format('Y-m-d H:i:s'); 

if(isset($result['token']) && ($result['is_used'] == 0))
{

	if( ($current_timestamp <= $result['valid_till']) && ($result['token'] === $user_token) )
	{
		header('Location: ../Views/reset-password.html?email='. $email .'&token='. $user_token .'&user_id='.$result['user_account_id']);
	}
	else
	{
		echo "This password reset link has expired or invalid";
	}
}
else
{
	echo "This password reset link has expired";
	
}

?>