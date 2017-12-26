<?php 
include_once "UserLoginManager.php";

$obj = new UserLoginManager();

$email = $_REQUEST['email'];
$user_token = $_REQUEST['user_token'];
$user_id = $_POST['user_id'];
$password = $_REQUEST['password'];
$confirmPassword = $_REQUEST['confirmPassword'];
$error_message = '';
$is_used = 0; 
$is_used_new = 1; 

//echo "Hello"; exit;

date_default_timezone_set("Asia/Kolkata");
$current_timestamp = (new \DateTime())->format('Y-m-d H:i:s'); 

if(($email !="") && ($user_token !="") && ($user_id !="") && ($password !="") && ($confirmPassword !=""))
{	
	if($password === $confirmPassword)
	{
		$result = $obj->validateToken($email, $user_token, $is_used);


		if(($result['token'] === $user_token))
		{
			
			if( ($current_timestamp <= $result['valid_till']) && ($result['is_used'] == 0) )
			{
				// Call Reset password.
				$changePasssword = $obj->resetPassword($email, $user_id, $password);
				
				if($changePasssword == true)
				{
					
					$updateIsUsedValue = $obj->updateIsUsed($is_used_new, $result['id']);
					//echo $updateIsUsedValue; exit;
					if($updateIsUsedValue == true)
					{
						
						$error_message = "Password changed successfully";
					}
					//else
					//{
						//echo "Unable to update is_used";
						//$error_message = "Unable to update is_used";
					//}
				}
				else
				{
					$error_message = "Sorry..!! We are unable to change the password at this time, please try again later";
				}
				
			}
			else
			{
				$error_message = "Sorry..!! Sorry the password reset link has expired.";
			}
		}
		else
		{
			$error_message = "Sorry..!! Invalid token";
		}
	}
	else
	{
		$error_message = "Sorry..!! Password and Confirm Password do not match";
	}
}
else
{
	$error_message = "Please fill all the mandatory fields";
}

echo $error_message;


?>