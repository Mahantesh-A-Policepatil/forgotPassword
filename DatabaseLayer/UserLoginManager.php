<?php

include_once "DBManager.php";
require_once("../Config/mail_configuration.php");
date_default_timezone_set("Asia/Kolkata");
if(!class_exists('PHPMailer')) {
    require('phpmailer/class.phpmailer.php');
	require('phpmailer/class.smtp.php');
}
/*
*
* Author : Mahantesh - A - Policepatil.
* Class  : UserLoginManager Class
* This class forms a data access layer,
* This class is used for CURD operations (CREATE, UPDATE, READ, DELETE).
*
*/
class UserLoginManager {
	
	private $dbObj;
	
	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : Constructor.	
	*
	*/
	function __construct() {
	
		$this->dbObj=new DBManager();
	
	}

	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : checkIfEmailExists - This method checks if email exists or not,
	* Returns True of False.
	*
	*/
	public function checkIfEmailExists($user_email)
	{
		try{
			$sql = "SELECT id, user_name, email from user_accounts where email = ?";
			$stmt = $this->dbObj->dbconn->prepare($sql);
			$stmt->execute(array($user_email));
			$basicResult = $stmt -> fetch(PDO::FETCH_ASSOC);
			return $basicResult;
		}
		catch(PDOException $e){
			echo ($e->getMessage ());
		}
	}

	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : validateToken - This method checks if the token exists,
	* Note : The token will be valid for 1 hour only, after that the token will expire
	* Returns True of False.
	*
	*/
	public function checkIfTokenExpired($email, $user_token, $is_used) {
		try{

			//echo "Email ".$email." User Token ".$user_token; exit;
			$sql = "SELECT id, user_account_id, token, is_used, created_at + INTERVAL 1 HOUR AS `valid_till` 
			from reset_password where email = ? AND token = ? AND is_used = ?";
	
			$stmt = $this->dbObj->dbconn->prepare($sql);
			$stmt->execute(array($email, $user_token, $is_used));
			$basicResult = $stmt -> fetch(PDO::FETCH_ASSOC);
			
			return $basicResult;
			
		}
		catch(PDOException $e){
			echo ($e->getMessage ());
		}
	
	}

	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : checkIfTokenAvailable - This method checks if the token exists,
	* Note : The token will be valid for 1 hour only, after that the token will expire
	* 
	*
	*/
	public function checkIfTokenAvailable($email, $is_used) {
		try{
			
			$sql = "SELECT id, token, is_used, created_at + INTERVAL 1 HOUR AS `valid_till` 
			from reset_password where email = ? AND is_used = ?";
	
			$stmt = $this->dbObj->dbconn->prepare($sql);
			$stmt->execute(array($email, $is_used));
			$basicResult = $stmt -> fetch(PDO::FETCH_ASSOC);
			
			return $basicResult;
			
		}
		catch(PDOException $e){
			echo ($e->getMessage ());
		}
	
	}

	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : validateToken - This method checks if the token exists,
	* Note : The token will be valid for 1 hour only, after that the token will expire
	* 
	*
	*/
	public function validateToken($email, $user_token, $is_used) {
		try{
			
			//echo "user token = ".$user_token." Email = ".$email." is_used = ".$is_used; exit;
			$sql = "SELECT id, token, is_used, created_at + INTERVAL 1 HOUR AS `valid_till` 
			from reset_password where email = ? AND token = ? AND is_used = ?";
	
			$stmt = $this->dbObj->dbconn->prepare($sql);
			$stmt->execute(array($email, $user_token, $is_used));
			$basicResult = $stmt -> fetch(PDO::FETCH_ASSOC);
			
			return $basicResult;
			
		}
		catch(PDOException $e){
			echo ($e->getMessage ());
		}
	
	}

	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : generateToken - This method generates a token,
	* Returns True of False.
	*
	*/
	public function generateToken($length) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return md5($randomString);
	}
	
	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : storeToken - This method stores user_id, email, token into reset_password table.
	*
	*/
	
	public function storeToken($users_id, $email, $token) {
		try{
			$sql="INSERT INTO reset_password(user_account_id, email, token) values (?,?,?)";

			$stmt= $this->dbObj->dbconn->prepare($sql);
			$result = $stmt->execute(array($users_id, $email, $token ));
			return $result;
		}
		catch(PDOException $e){
			echo($e->getMessage());
		}
	
	}

	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : Updates the password i.e old passsword will be updated with new password.
	*
	*/
	public function resetPassword($email, $user_id, $newPassword) {
		try{

			$hasshedPasssword = md5($newPassword);
			$sql="UPDATE user_accounts SET password = ?, modified_by = ? WHERE email = ? AND id = ?";
			$stmt= $this->dbObj->dbconn->prepare($sql);
			$result = $stmt->execute(array( $hasshedPasssword, $user_id, $email, $user_id ));
			return $result;
		}
		catch(PDOException $e){
			echo($e->getMessage());
		}
	
	}

	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : updateIsUsed - This method updates is_used from 0 to 1 in reset_password table,
	* is_used = 0 manse token IS available for use,
	* is_used = 1 manse token IS NOT available for use ( token no longer valid ).
	*
	*/
	public function updateIsUsed($is_used_new, $id) {
		try{
			
			$sql="UPDATE reset_password SET is_used = ? WHERE id = ?";
			$stmt= $this->dbObj->dbconn->prepare($sql);
			$result = $stmt->execute(array($is_used_new, $id));
			return $result;
		}
		catch(PDOException $e){
			echo($e->getMessage());
		}
	
	}

	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : updateTokenTimeStamp - If the token is available then we should not generate a new token, instead we 
	* should send the same token and we should update the timestamp(i.e timestamp at which token was created) with 
	* current timestamp.
	* This method updates the time stamp
	*
	*/
	
	public function updateTokenTimeStamp($token_id, $current_timestamp) {
		try{
			
			$sql="UPDATE reset_password SET created_at = ? WHERE id = ?";
			$stmt= $this->dbObj->dbconn->prepare($sql);
			$result = $stmt->execute(array($current_timestamp, $token_id));
			return $result;
		}
		catch(PDOException $e){
			echo($e->getMessage());
		}
	
	}

	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : Sends the password reset link to user's email address.
	*
	*/
	public function sendResetLinkToUser($user_id, $user_name, $email, $token)
	{
		
		$mail = new PHPMailer();

		$emailBody = "<div>" . $user_name 
							 . ",<br><br><p>Click this link to recover your password<br><br><a href='" 
							 . PROJECT_HOME 
							 . "/projects/teraspin2/webapps/teraspin2/main/src/php/forgot_password/DatabaseLayer/resetPasswordManager.php?email=" 
							 . $email . "&token=". $token . "'>Click Here</a><br><br></p>Regards,<br> Admin.</div>";
		
		$mail->IsSMTP();
		$mail->SMTPDebug  = 0;
		$mail->SMTPAuth   = TRUE;
		$mail->SMTPSecure = "tls";
		$mail->Port       = PORT;  
		$mail->Username   = MAIL_USERNAME;
		$mail->Password   = MAIL_PASSWORD;
		$mail->Host       = MAIL_HOST;
		$mail->Mailer     = MAILER;

		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);

		$mail->SetFrom(SERDER_EMAIL, SENDER_NAME);
		$mail->AddReplyTo(SERDER_EMAIL, SENDER_NAME);
		$mail->ReturnPath=SERDER_EMAIL;	
		$mail->AddAddress($email);
		$mail->Subject = "Forgot Password Recovery";		
		$mail->MsgHTML($emailBody);
		$mail->IsHTML(true);
		$error_message = '';
		if(!$mail->Send()) {
			return false;
		} else {
			return true;
		}
		return $error_message;
	}
}

?>