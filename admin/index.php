<?php
	require_once("../php/classPanel.php");
	require_once("../php/classPanelPageGenerator.php");
	
	session_start();
	
	$url_array = parse_url($_SERVER['REQUEST_URI']);
	$path = $url_array["path"];
	
	$Panel = new classPanel();
	
	$_ERROR = 0;
	
	if(!empty($_POST)){
		if (isset($_POST["settings"])) {
			$Panel->void_UpdateSettings($_POST);
		}
		if (isset($_POST["auth"])) {
			if ($Panel->b_checkAdmin($_POST["email"], $_POST["password"])) {
				$_SESSION["admin_login"] = $_POST["email"];
				$_SESSION["admin_password"] = $_POST["password"];
			}
			else {
				header("Location: /admin/auth");
			}
		}
		if (isset($_POST["addSpamer"])) {
			if (!$Panel->b_CheckSpammerAcc($_POST["log"])) {
				$Panel->void_AddSpammer($_POST["log"], $_POST["pass"]);
			}
			else {
				$_ERROR = 1;
			}
		}
	}
	
	if (!empty($_GET)){
		if (array_key_exists("do", $_GET)) {
			if (($_GET["do"] == "SetBalance") && isset($_GET["id"]) && isset($_GET["balance"])) {
				$Panel->void_SetBalance($_GET["id"], $_GET["balance"]);
			}
			if (($_GET["do"] == "SetPassword") && isset($_GET["id"]) && isset($_GET["pass"])) {
				$Panel->void_SetPassword($_GET["id"], $_GET["pass"]);
			}
			if (($_GET["do"] == "SetKT") && isset($_GET["id"]) && isset($_GET["kt"])) {
				$Panel->void_SetKT($_GET["id"], $_GET["kt"]);
			}
		}
	}
	
	if ((!isset($_SESSION["admin_login"]) && !isset($_SESSION["admin_password"])) && (($path !== "/admin/auth") && ($path !== "/admin/auth/"))){
		header("Location: /admin/auth");
	}
	else if (!$Panel->b_checkAdmin($_SESSION["admin_login"], $_SESSION["admin_password"]) && (($path !== "/admin/auth") && ($path !== "/admin/auth/"))) {
		header("Location: /admin/auth");
	}
	
	$PanelPageGen = new PageGenerator("template");
	
	switch ($path){
		case "/admin/auth": 
		case "/admin/auth/": 
			if (isset($_SESSION["admin_login"]) && isset($_SESSION["admin_password"])){
				header("Location: /admin");
			}
			else echo $PanelPageGen->GenerateAuth();
		break;
		
		case "/admin/logout": 
		case "/admin/logout/": 
			if (isset($_SESSION["admin_login"]) && isset($_SESSION["admin_password"])){
				unset($_SESSION["admin_login"]);
				unset($_SESSION["admin_password"]);
			}
			header("Location: /admin/auth");
		break;
		
		case "/admin": 
		case "/admin/": 
			echo $PanelPageGen->GenerateIndex();
		break;
		
		case "/admin/spammers": 
		case "/admin/spammers/": 
			echo $PanelPageGen->GenerateSpammers($_ERROR);
		break;
		
		case "/admin/settings":
		case "/admin/settings/": 
			echo $PanelPageGen->GenerateSettings();
		break;
		
		case "/admin/del_all": 
		case "/admin/del_all/": 
			$Panel->void_TruncAccs();
			header("Location: /admin");
		break;
		
		case "/admin/delete": 
		case "/admin/delete/": 
			$Panel->void_dropAccount($_GET["id"]);
			header("Location: /admin");
		break;
		
		case "/admin/resetVisits": 
		case "/admin/resetVisits/": 
			$Panel->void_resetVisits();
			header("Location: /admin");
		break;
		
		case "/admin/toggleValid": 
		case "/admin/toggleValid/": 
			$Panel->void_ToggleValid($_GET["id"]);
			header("Location: /admin");
		break;
		
		case "/admin/delSpamer": 
		case "/admin/delSpamer/": 
			$Panel->void_dropSpammer($_GET["id"]);
			header("Location: /admin/spammers");
		break;
		
		case "/admin/newaccs": 
		case "/admin/newaccs/": 
			if ($Panel->b_newAccs()) echo "1";
			$Panel->void_setNotificated();
		break;
	}
?>