<?php

	class Chores {	
		private $db_conn;
		
		function __construct() {
			
			// make the connection
			$this->db_conn = mysql_connect('internal-db.s41232.gridserver.com', 'db41232_di_usr', 'dope911t') or die("Could not connect to database.");
			
			// select the proper db
			mysql_select_db('db41232_diddit') or die("Could not select database.");
		}
	
	
		function __destruct() {
		
			if ($this->db_conn) {
				mysql_close($this->db_conn);
				$this->db_conn = null;
			}
		}
		
		
		/**
		 * Helper method to get a string description for an HTTP status code
		 * http://www.gen-x-design.com/archives/create-a-rest-api-with-php/ 
		 * @returns status
		 */
		function getStatusCodeMessage($status) {
			
			$codes = Array(
				100 => 'Continue',
				101 => 'Switching Protocols',
				200 => 'OK',
				201 => 'Created',
				202 => 'Accepted',
				203 => 'Non-Authoritative Information',
				204 => 'No Content',
				205 => 'Reset Content',
				206 => 'Partial Content',
				300 => 'Multiple Choices',
				301 => 'Moved Permanently',
				302 => 'Found',
				303 => 'See Other',
				304 => 'Not Modified',
				305 => 'Use Proxy',
				306 => '(Unused)',
				307 => 'Temporary Redirect',
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Timeout',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request-URI Too Long',
				415 => 'Unsupported Media Type',
				416 => 'Requested Range Not Satisfiable',
				417 => 'Expectation Failed',
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Timeout',
				505 => 'HTTP Version Not Supported');

			return (isset($codes[$status])) ? $codes[$status] : '';
		}
		
		
		/**
		 * Helper method to send a HTTP response code/message
		 * @returns body
		 */
		function sendResponse($status=200, $body='', $content_type='text/html') {
			
			$status_header = "HTTP/1.1 ". $status ." ". $this->getStatusCodeMessage($status);
			header($status_header);
			header("Content-type: ". $content_type);
			echo $body;
		}
	
		
		
		function activeByUserID($user_id) {

			$query = 'SELECT `tblChores`.`id`, `tblChores`.`title`, `tblChores`.`info`, `tblChores`.`ico_path`, `tblChores`.`img_path`, `tblChores`.`expires`, `tblRewardTypes`.`points`, `tblRewardTypes`.`cost` FROM `tblChores` INNER JOIN `tblRewardTypes` ON `tblChores`.`reward_id` = `tblRewardTypes`.`id` WHERE `tblChores`.`user_id` ='. $user_id .' AND `tblChores`.`status_id` =2 ORDER BY `tblChores`.`added`;';
			$res = mysql_query($query);
			
			// Return data, as JSON
			$result = array();
				
			// error performing query
			if (mysql_num_rows($res) > 0) {
				
				while ($row = mysql_fetch_array($res, MYSQL_BOTH)) {
					array_push($result, array(
						"id" => $row[0], 
						"title" => $row[1], 
						"info" => $row[2], 
						"icoPath" => $row[3], 
						"imgPath" => $row[4],
						"expires" => $row[5], 
						"points" => $row[6], 
						"cost" => $row[7]
					));
				}
			}
			
			$this->sendResponse(200, json_encode($result));
			return (true);  
		}
		
		
		
		function finishedByUserID($user_id) {

			$query = 'SELECT * FROM `tblChores` INNER JOIN `tblUsersChores` ON `tblChores`.`id` = `tblUsersChores`.`chore_id` WHERE `tblUsersChores`.`user_id` = "'. $user_id .'" AND `tblUsersChores`.`status_id` = "4" ORDER BY `tblUsersChores`.`added`;';
			$res = mysql_query($query);
			
			// Return data, as JSON
			$result = array(); 
				
			// error performing query
			if (mysql_num_rows($res) > 0) {
			    while ($row = mysql_fetch_array($res, MYSQL_BOTH)) {
					array_push($result, array(
						"id" => $row['id'], 
						"title" => $row['title'], 
						"info" => $row['info'], 
						"icoPath" => $row['ico_path'], 
						"imgPath" => $row['img_path'], 
						"finished" => "Y"
					));
				} 
			}
			
			$this->sendResponse(200, json_encode($result));
			return (true);   
		}
		
		function updStatusByUserID($user_id, $chore_id, $status_id) {
			
			$query = 'UPDATE `tblUsersChores` SET `status_id` ='. $status_id .' WHERE `user_id` = "'. $user_id .'" AND `chore_id` = "'. $chore_id .'";';
			$result = mysql_query($query);
			
			return (true);
		}
		
		
		function addNew($user_id, $chore_title, $chore_info, $cost, $expires) {
			
			$query = 'SELECT `id`, `points` FROM `tblRewardTypes` WHERE `cost` = "'. $cost .'";';
			$row = mysql_fetch_row(mysql_query($query));

			// has entry
			if ($row) {
                $reward_id = $row[0];
				$points = $row[1];
			
			} else {
				$reward_id = 0;
				$points = 0;
			}
			
			$query = 'INSERT INTO `tblChores` (';
			$query .= '`id`, `user_id`, `reward_id`, `title`, `info`, `ico_path`, `img_path`, `status_id`, `expires`, `added`, `modified`) ';
			$query .= 'VALUES (NULL, "'. $user_id .'", "'. $reward_id .'", "'. $chore_title .'", "'. $chore_info .'", "", "", "2", "'. $expires .'", NOW(), CURRENT_TIMESTAMP);';
			$result = mysql_query($query); 
			$chore_id = mysql_insert_id();
			
			
			$this->sendResponse(200, json_encode(array(
				"id" => $chore_id, 
				"title" => $chore_title, 
				"info" => $chore_info, 
				"icoPath" => "", 
				"imgPath" => "",
				"expires" => $expires, 
				"points" => $points, 
				"cost" => $cost
			)));
			
			return (true);
		}
	}
	
	$chores = new Chores;
	
	//$user_id= "2";
	//$chores->availByUserID($user_id);
	
	if (isset($_POST["action"])) {
		switch ($_POST["action"]) {
			case "1":
				if (isset($_POST["userID"]))
					 $chores_json = $chores->activeByUserID($_POST["userID"]);
				break;
				
			case "2":
				if (isset($_POST["userID"]))
					 $chores_json = $chores->finishedByUserID($_POST["userID"]);
				break;
				
			case "3":
				if (isset($_POST["userID"]) && isset($_POST['choreID']))
					 $chores_json = $chores->updStatusByUserID($_POST["userID"], $_POST['choreID'], 1);
				break;
				
			case "4":
				if (isset($_POST["userID"]) && isset($_POST['choreID']))
					 $chores_json = $chores->updStatusByUserID($_POST["userID"], $_POST['choreID'], 2);
				break;
				
			case "5":
				if (isset($_POST["userID"]) && isset($_POST['choreID']))
					 $chores_json = $chores->updStatusByUserID($_POST["userID"], $_POST['choreID'], 3);
				break;
				
			case "6":
				if (isset($_POST["userID"]) && isset($_POST['choreID']))
					 $chores_json = $chores->updStatusByUserID($_POST["userID"], $_POST['choreID'], 4);
				break;
				
		   case "7":
				if (isset($_POST["userID"]) && isset($_POST['choreTitle']) && isset($_POST['choreInfo']) && isset($_POST['cost']) && isset($_POST['expires']))
					 $chores_json = $chores->addNew($_POST['userID'], $_POST['choreTitle'], $_POST['choreInfo'], $_POST['cost'], $_POST['expires']);
				break;
		}
	}   
	
	//if (isset($_POST["fbid"])) {
	//    $fb_id = $_POST["fbid"];
	//	$userInfo_arr = $jobs->jobSearch($fb_id);
	//}
?>