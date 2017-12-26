<?php 
include_once "UserLoginManager.php";

$obj = new UserLoginManager();
$user_email = $_POST['email'];

$result = $obj->checkIfEmailExists($user_email);

$user_id = $result['id'];
$user_name = $result['user_name'];
$email = $result['email'];

$error_message = '';
$sendMail = '';
$token = '';
$is_used = 0;
date_default_timezone_set("Asia/Kolkata");
$current_timestamp = (new \DateTime())->format('Y-m-d H:i:s'); 

if(isset($result['email']))
{
	$sendSameToken = $obj->checkIfTokenAvailable($email, $is_used);
	if(isset($sendSameToken['token']) && ($current_timestamp <= $sendSameToken['valid_till']) )
	{
		
		$updateTimeStamp = $obj->updateTokenTimeStamp($sendSameToken['id'], $current_timestamp);
		$sendResteLink = $obj->sendResetLinkToUser($user_id, $user_name, $email, $sendSameToken['token']);
		
		if($sendResteLink == true)
		{
			$error_message = "Password reset link has been sent to your email, please click the link to reset your password";
		}
		else
		{
			$error_message = "Sorry..!! We are unable to send password reset link to your registered mail adress, please try again after some time..!!";
		}
		

	}
	else
	{
	
		//Generate Token
		$token = $obj->generateToken(20);
		
		//send mail - Send password reset link to user's email address.
		$sendMail = $obj->sendResetLinkToUser($user_id, $user_name, $email, $token);
		
		//store token into to database
		if($sendMail == true)
		{
			$error_message = "Password reset link has been sent to your email, please click the link to reset your password";
			$storeToken = $obj->storeToken($user_id, $email, $token);
		}
		else
		{
			$error_message = "Sorry..!! We are unable to send password reset link to your registered mail adress, please try again after some time..!!";
		}
	}
}
else
{
	/* 
	* We are not suppose to tell the user that the email entered by him is invalid, 
	* If we inform that the email entered by him is invalid then he may try to hack.
	* So we just need to show a message as follows. 
	*  "Password reset link has been sent to your registered email address"
	*/
	$error_message = "Password reset link has been sent to your registered email address";
}

echo $error_message;


?>