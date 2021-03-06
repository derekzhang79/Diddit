<?php

	class InAppPurchases {	
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
	
		
		
		function allTypes() {

			$query = 'SELECT * FROM `tblIAPTypes` WHERE `active` = "Y";';
			$res = mysql_query($query);
			
			// Return data, as JSON
			$result = array();
				
			// error performing query
			if (mysql_num_rows($res) > 0) {
				
				while ($row = mysql_fetch_array($res, MYSQL_BOTH)) {
					array_push($result, array(
						"id" => $row['id'], 
						"name" => $row['name'], 
						"info" => $row['info'], 
						"itunes_id" => $row['itunes_id'], 
						"points" => $row['points'],
						"price" => $row['cost'], 
						"ico_url" => $row['ico_url']
					));
				}
			}
			
			$this->sendResponse(200, json_encode($result));
			return (true);  
		}
		
		function addReceipt($user_id, $subs_id, $trans_id, $trans_data, $trans_date) {
			
			$query = 'INSERT INTO `tblReceipts` (';
			$query .= '`id`, `trans_id`, `trans_date`, `trans_data`, `added`) ';
			$query .= 'VALUES (NULL, "'. $trans_id .'", "'. $trans_date .'", "'. $trans_data .'", CURRENT_TIMESTAMP);';
			$result = mysql_query($query);
			$receipt_id = mysql_insert_id();
			
			foreach (explode("|", $subs_id) as $sub_id) {
				$query = 'INSERT INTO `tblUsersReceipts` (';
				$query .= '`receipt_id`, `user_id`, `sub_id`) ';
				$query .= 'VALUES (NULL, "'. $receipt_id .'", "'. $user_id .'", "'. $sub_id .'");';
				$result = mysql_query($query);
			}
			
			$this->sendResponse(200, json_encode(array(
				"id" => $receipt_id
			)));  
		}
	}
	
	$inAppPurchases = new InAppPurchases;
	
	if (isset($_POST["action"])) {
		switch ($_POST["action"]) {
			case "1":
				$json = $inAppPurchases->allTypes();
				break;
			
			case "2":
			 	if (isset($_POST['userID']) && isset($_POST['subIDs']) && isset($_POST['transID']) && isset($_POST['data']) && isset($_POST['transDate']))
					$json = $inAppPurchases->addReceipt($_POST['userID'], $_POST['subIDs'], $_POST['transID'], $_POST['data'], $_POST['transDate']);
				break;
		}
	}   
?>