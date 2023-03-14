<?php
session_start();
error_reporting(E_ALL);

// Функция получения данных конигурации из файла JSON
function getConfig($pathToFile){
	
	$pathToFile = str_replace('\\','\\\\',$pathToFile);
	$confJSON = file_get_contents($pathToFile);
	$confArray = json_decode($confJSON, true);
	//print_r($confArray);
	return $confArray;
	
}

function logout(){

	$statlogged = statLogged();
	if($statlogged['statlogged'] == 1){
		//$_SESSION = array();
		//session_destroy();
		$_SESSION['user_name'] = "";
		$_SESSION['user_login'] = "";
		$_SESSION['user_id'] = "";	
		$_SESSION['auth_basic64'] = "";	
		$_SESSION['access'] = "";	
		$_SESSION['user_prof'] = "";
		$_SESSION['user_division'] = "";		
	}
	$statlogged = statLogged();
	if($statlogged['statlogged'] == 2){
		$usersArr = getUsers($authUser="");
		$resArr = [
		"statlogged" => 2,
		"data" => $usersArr
		];	
		return $resArr;
	}else{
		return $statlogged;
	}
	//$resArr = ["res_logout" => (session_status() !== PHP_SESSION_ACTIVE ? 1 : 0)];
}

// Функция проверки статуса авторизации пользователя.
// Если переменные сессии существуют и ЕСТЬ значение ID пользователя, то возвращается статус 1 и данные пользователя.
// Если переменные сессии существуют и НЕТ значение ID пользователя, то возвращается статус 2.
// Иначе возвращается статус 0.
function statLogged(){
	
	if(isset($_SESSION['user_name']) && isset($_SESSION['user_login']) && isset($_SESSION['user_id']) && $_SESSION['user_id']!=""){
		$resArr = [
				"statlogged" => 1,
				"data" => [
					"user_name" => $_SESSION['user_name'],
					"user_login" => $_SESSION['user_login'],
					"user_id" => $_SESSION['user_id'],
					"user_photo" => $_SESSION['user_photo'],
					"access" => $_SESSION['access'],
					"user_prof" => $_SESSION['user_prof'],
					"user_division" => $_SESSION['user_division'],
					],
			];
	}else if(isset($_SESSION['user_name']) && isset($_SESSION['user_login']) && isset($_SESSION['user_id']) && $_SESSION['user_id']==""){
		$resArr = ["statlogged" => 2 ];
	}else {
		$resArr = ["statlogged" => 0 ];
	}
	return $resArr;
	
}

// $userInfo - данные авторизованного пользователя в ОС
function setAuthOS($userInfo){
	
	//echo "Регистрируем пользователя ". $userInfo['name'] ."!!!";
	//print_r($userInfo);
	
	$_SESSION['user_name'] = $userInfo['name'];
	$_SESSION['user_login'] = $userInfo['login'];
	$_SESSION['user_id'] = $userInfo['id_user'];
	$_SESSION['access'] = $userInfo['access'];
	$_SESSION['user_photo'] = $userInfo['photo'];
	//$_SESSION['auth_basic64'] = "";
	$authBasic64 = base64_encode($userInfo['name_auth'].":");
	$_SESSION['auth_basic64'] = $authBasic64;
	$_SESSION['user_prof'] = $userInfo['prof'];
	$_SESSION['user_division'] = $userInfo['division'];
	
	if(isset($_SESSION['user_name']) && isset($_SESSION['user_login']) && isset($_SESSION['user_id'])){
		$resArr = [
		"user_name" => $_SESSION['user_name'],
		"user_login" => $_SESSION['user_login'],
		"user_id" => $_SESSION['user_id'],
		"user_photo" => $_SESSION['user_photo'],
		"access" => $userInfo['access'],
		"user_prof" => $userInfo['prof'],
		"user_division" => $userInfo['division'],
		];	
		return $resArr;		
	}
	else{
		$resArr = ["error" => "Ошибка создания переменных сессии!"];
		return $resArr;
	}

}

function setAuthBasic($userInfo,$authBasic64){
	
	//echo "Регистрируем пользователя ". $userInfo['name'] ."!!!";
	
	$_SESSION['user_name'] = $userInfo['payload'][0]['name'];
	$_SESSION['user_photo'] = $userInfo['payload'][0]['photo'];
	$_SESSION['user_login'] = "BasicAuthorization";
	$_SESSION['user_id'] = $userInfo['payload'][0]['ID'];
	$_SESSION['auth_basic64'] = $authBasic64;
	$_SESSION['access'] = $userInfo['payload'][0]['access'];
	$_SESSION['user_prof'] = $userInfo['payload'][0]['prof'];
	$_SESSION['user_division'] = $userInfo['payload'][0]['division'];
	
	if(isset($_SESSION['user_name']) && isset($_SESSION['user_login']) && isset($_SESSION['user_id'])){
		$resArr = [
		"user_name" => $_SESSION['user_name'],
		"user_login" => $_SESSION['user_login'],
		"user_id" => $_SESSION['user_id'],
		"user_photo" => $_SESSION['user_photo'],
		"access" => $userInfo['payload'][0]['access'],
		"user_prof" => $userInfo['payload'][0]['prof'],
		"user_division" => $userInfo['payload'][0]['division'],
		];	
		return $resArr;		
	}
	else{
		$resArr = ["error" => "Ошибка создания переменных сессии!"];
		return $resArr;
	}

}

function getAlerts(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array('id' => $_GET['user_id']);

	$ch = curl_init($api_url_service.'/alerts?'. http_build_query($param));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function hideAlerts(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array('id' => $_GET['id']);

	$ch = curl_init($api_url_service.'/alerts?'. http_build_query($param));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");  
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function getAppealsOut(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];	
	//$api_url_common = $configArray['api_url_common'];
	$api_url_service = $configArray['api_url_service'];
	$name_query = "appears_out";
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array(	'page' => $_GET['page'],
					'per_page' => $_GET['per_page'],
					'status_value' => $_GET['status_value'],
					'search' => $_GET['search'],
					'register_date_inc' => $_GET['sort_reg_date'],
					'closing_date_inc' => $_GET['sort_close_date'],
					'name_inc' => $_GET['sort_name'],
					'status_inc' => $_GET['sort_status'],
					'number_inc' => $_GET['sort_num'],
					'stage_inc' => $_GET['sort_stage']);
	
	$ch = curl_init($api_url_service."/".$name_query."?". http_build_query($param));

	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);
	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function getAppealsIn(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];	
	//$api_url_common = $configArray['api_url_common'];
	$api_url_service = $configArray['api_url_service'];
	
	$name_query = "appears_in";
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array(	'page' => $_GET['page'],
					'per_page' => $_GET['per_page'],
					'status_value' => $_GET['status_value'],
					'search' => $_GET['search'],
					'register_date_inc' => $_GET['sort_reg_date'],
					'closing_date_inc' => $_GET['sort_close_date'],
					'name_inc' => $_GET['sort_name'],
					'status_inc' => $_GET['sort_status'],
					'number_inc' => $_GET['sort_num'],
					'stage_inc' => $_GET['sort_stage']);
	
	$ch = curl_init($api_url_service."/".$name_query."?". http_build_query($param));

	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	//echo $resJSON;
	curl_close($ch);
	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function getAppealItem(){
		
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];	
	$api_url_common = $configArray['api_url_common'];
	$name_query = "req";
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$ch = curl_init($api_url_common."/".$name_query."/".$_GET['id']."?process=".$_GET['process']);

	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);
	$resArray = json_decode($resJSON, true);
	
	return $resArray;
	
}

function createAppeal(){
	
	$attachments = [];
	foreach ($_FILES as $file) { $attachments[] = [ "attachment_name" => $file['name'], "attachment_data" => base64_encode(file_get_contents($file['tmp_name'])) ];}
	
	$fieldsArray = [	[ "name" => "Инициатор", "value" => $_POST['user_id'], "link_object" => "user" ],
						[ "name" => "Услуга", "value" => $_POST['service_id'], "link_object" => "service" ], 
						[ "name" => "Наименование", "value" => $_POST['theme'] ], 
						[ "name" => "Описание", "value" => $_POST['description'] ] ];
						
	$dataArray = [ "fields" => $fieldsArray , "attachments" => $attachments ];

	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];	
	$api_url_common = $configArray['api_url_common'];
	$name_query = "req";
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}

	$data_string = json_encode($dataArray);                                                                                   
	$ch = curl_init($api_url_common."/".$name_query);                                                                    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));                                                                                                                  
	$resJSON = curl_exec($ch);
	curl_close($ch);
	
	$resArray = json_decode($resJSON, true);
	return $resArray;
	
}


function returnAppeal(){
	
	$fieldsArray = [	[ "name" => "id_app", "value" => $_POST['id_app'] ], 
						[ "name" => "cause_repeat", "value" => $_POST['cause_repeat'] ] ];
						
	$dataArray = [ "fields" => $fieldsArray ];
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];	
	$api_url_service = $configArray['api_url_service'];
	$name_query = "appeals";
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
    
	$data_string = json_encode($dataArray); 	
	//$ch = curl_init($api_url_service."/".$name_query."?id_app=".$_GET['id_app']."&cause_repeat=".$_GET['cause_repeat']);  
	$ch = curl_init($api_url_service."/".$name_query); 	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);	
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));                                                                                                                  
	$resJSON = curl_exec($ch);
	curl_close($ch);
	
	$resArray = json_decode($resJSON, true);
	return $resArray;
	
}

function delAppeal(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array('id' => $_GET['id']);

	$ch = curl_init($api_url_service.'/appeals?'. http_build_query($param));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");  
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function addMessageAppeal(){
	
	$attachments = [];
	foreach ($_FILES as $file) { $attachments[] = [ "name" => $file['name'], "attachment" => base64_encode(file_get_contents($file['tmp_name'])) ];}
						
	$dataArray = [ "content" => $_POST['content'] , "process" => $_POST['process'] , "fields" => $attachments ];

	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];	
	$api_url_common = $configArray['api_url_common'];
	$name_query = "message";
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}

	$data_string = json_encode($dataArray);                                                                                   
	$ch = curl_init($api_url_common."/".$name_query."/".$_POST['id_appeal']);                                                                    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));                                                                                                                  
	$resJSON = curl_exec($ch);
	curl_close($ch);
	
	$resArray = json_decode($resJSON, true);
	return $resArray;
	
}

function setGrade(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];	
	$api_url_common = $configArray['api_url_common'];
	$name_query = "mark";
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$fieldsArray = [	[ "name" => "ПроцессУправления", "value" => "Запрос" ],
						[ "name" => "ОценкаПроцесса", "value" => $_GET['grade'] ], 
						[ "name" => "Комментарий", "value" => $_GET['comment'] ] ];
						
	$dataArray = [ "fields" => $fieldsArray ];
	
	$data_string = json_encode($dataArray); 
	$ch = curl_init($api_url_common."/".$name_query."/". $_GET['id_app']);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);	
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));	
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);
	$resArray = json_decode($resJSON, true);
	
	return $resArray;

}

function getServices(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];	
	$api_url_common = $configArray['api_url_common'];
	$name_query = "service";
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array();
	if($_GET['group_id']!="")$param['group_id'] = $_GET['group_id'];
	if($_GET['search']!="")$param['search'] = $_GET['search'];
	
	$ch = curl_init($api_url_common."/".$name_query."?". http_build_query($param));

	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);
	$resArray = json_decode($resJSON, true);
	
	return $resArray;

}

function getDescription(){
		
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];	
	$api_url_common = $configArray['api_url_common'];
	$name_query = "description";
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	
	///description/${e}?link_object=service
	//echo $api_url_common."/".$name_query."/".$_GET['id']."?".$_GET['link_object'];
	$ch = curl_init($api_url_common."/".$name_query."/".$_GET['id']."?link_object=".$_GET['link_object']);

	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);
	$resArray = json_decode($resJSON, true);
	
	return $resArray;
	
}

function getUsers($authUser=""){
	
	//echo "111";
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$auth_service = $configArray['auth_service'];
	$api_url_service = $configArray['api_url_service'];
	
	//echo $api_key."<br>";
	//echo $auth_service."<br>";
	//echo $authUser;
	//echo strrchr($authUser, "\\");
	//if($authUser=="") $subAuthUser = ""; else substr(strrchr($authUser,92),1);
	$subAuthUser = ($authUser=="" ? "" : substr(strrchr($authUser,"\\"),1));
	//echo $subAuthUser;
	
	$param = array('authUser' => $subAuthUser);

	$ch = curl_init($api_url_service.'/users?'. http_build_query($param));

	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($auth_service));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	//echo "<hr>";
	//print_r($resArray);
	//echo "<hr>";
	//echo $resJSON;
	//echo "<hr>";
	//print_r($_SERVER);
	//echo "<hr>";
	
	return $resArray;
}

function showInTreeview(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array('id' => $_GET['id']);

	$ch = curl_init($api_url_service.'/show_in_treeview?'. http_build_query($param));

	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	//echo "<hr>";
	//print_r($resArray);
	//echo "<hr>";
	//echo $resJSON;
	//echo "<hr>";
	//print_r($_SERVER);
	//echo "<hr>";
	
	return $resArray;
}

function getAddressBook(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array('guidNode' => $_GET['guidNode']);

	$ch = curl_init($api_url_service.'/addressbook?'. http_build_query($param));

	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	//echo "<hr>";
	//print_r($resArray);
	//echo "<hr>";
	//echo $resJSON;
	//echo "<hr>";
	//print_r($_SERVER);
	//echo "<hr>";
	
	return $resArray;
}

function setLike(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	$name_query = "likes";
	$dataArray = [ "id_post" => $_POST['id_post'] ];
	
	$data_string = json_encode($dataArray); 
	$ch = curl_init($api_url_service."/".$name_query);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function addComment(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	$name_query = "comments";
	$dataArray = [ "id_post" => $_POST['id_post'], "text" => $_POST['text'] ];
	
	$data_string = json_encode($dataArray); 
	$ch = curl_init($api_url_service."/".$name_query);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function getComments(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array('id_post' => $_GET['id_post']);
	$ch = curl_init($api_url_service.'/comments?'. http_build_query($param));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function getDateLastPost(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$ch = curl_init($api_url_service.'/date_last_post');
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function getPosts(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array('item_start' => $_GET['item_start'], 'item_end' => $_GET['item_end'], 'period_post_filter' => $_GET['period_post_filter']);
	$ch = curl_init($api_url_service.'/posts?'. http_build_query($param));
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	//echo "<hr>";
	//print_r($resArray);
	//echo "<hr>";
	//echo $resJSON;
	//echo "<hr>";
	//print_r($_SERVER);
	//echo "<hr>";
	
	return $resArray;
}

function addPost(){
	
	$attachments = [];
	$images = [];
	//foreach ($_FILES as $file) { $attachments[] = [ "attachment_name" => $file['name'], "attachment_data" => base64_encode(file_get_contents($file['tmp_name'])) ];}
	for ($i = 0; $i <= count($_FILES); $i++) {
		$key = $i."_attach";
		if(array_key_exists($key,$_FILES)){
			$attachments[] = [ "attachment_name" => $_FILES[$key]['name'], "attachment_data" => base64_encode(file_get_contents($_FILES[$key]['tmp_name'])) ];
		}
		$key = $i."_image";
		if(array_key_exists($key,$_FILES)){
			$images[] = [ "image_name" => $_FILES[$key]['name'], "image_data" => base64_encode(file_get_contents($_FILES[$key]['tmp_name'])) ];
		}
	}
	
	$fieldsArray = [	[ "name" => "Заголовок", "value" => $_POST['title_post'] ],
						[ "name" => "Текст", "value" => $_POST['text_post'] ], 
						[ "name" => "Дата", "value" => $_POST['date_post'] ], 
						[ "name" => "Время", "value" => $_POST['time_post'] ],
						[ "name" => "Видимость", "value" => $_POST['visible_post'] ] ];
						
	$dataArray = [ "fields" => $fieldsArray , "attachments" => $attachments, "images" => $images ];

	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];	
	$api_url_service = $configArray['api_url_service'];
	$name_query = "posts";
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}

	$data_string = json_encode($dataArray);                                                                                   
	$ch = curl_init($api_url_service."/".$name_query);                                                                    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));                                                                                                                  
	$resJSON = curl_exec($ch);
	curl_close($ch);
	
	$resArray = json_decode($resJSON, true);
	return $resArray;
}

function editPost(){
	
	$attachments = [];
	$images = [];
	//foreach ($_FILES as $file) { $attachments[] = [ "attachment_name" => $file['name'], "attachment_data" => base64_encode(file_get_contents($file['tmp_name'])) ];}
	for ($i = 0; $i <= count($_FILES); $i++) {
		$key = $i."_attach";
		if(array_key_exists($key,$_FILES)){
			$attachments[] = [ "attachment_name" => $_FILES[$key]['name'], "attachment_data" => base64_encode(file_get_contents($_FILES[$key]['tmp_name'])) ];
		}
		$key = $i."_image";
		if(array_key_exists($key,$_FILES)){
			$images[] = [ "image_name" => $_FILES[$key]['name'], "image_data" => base64_encode(file_get_contents($_FILES[$key]['tmp_name'])) ];
		}
	}
	
	$fieldsArray = [	[ "name" => "id", "value" => $_POST['id_post'] ],
						[ "name" => "Заголовок", "value" => $_POST['title_post'] ],
						[ "name" => "Текст", "value" => $_POST['text_post'] ], 
						[ "name" => "Дата", "value" => $_POST['date_post'] ], 
						[ "name" => "Время", "value" => $_POST['time_post'] ],
						[ "name" => "Видимость", "value" => $_POST['visible_post'] ],
						[ "name" => "ФайлыДляУдаления", "value" => $_POST['delFiles'] ], 
						[ "name" => "КартинкиДляУдаления", "value" => $_POST['delImages'] ] ];
						
	$dataArray = [ "fields" => $fieldsArray , "attachments" => $attachments, "images" => $images ];

	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];	
	$api_url_service = $configArray['api_url_service'];
	$name_query = "posts";
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}

	$data_string = json_encode($dataArray);                                                                                   
	$ch = curl_init($api_url_service."/".$name_query);                                                                    
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));                                                                                                                  
	$resJSON = curl_exec($ch);
	curl_close($ch);
	
	$resArray = json_decode($resJSON, true);
	return $resArray;
}

function delPost(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array('id' => $_GET['id']);

	$ch = curl_init($api_url_service.'/posts?'. http_build_query($param));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");  
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function delСomment(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array('id_comment' => $_GET['id_comment']);

	$ch = curl_init($api_url_service.'/comments?'. http_build_query($param));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");  
	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function searchContacts(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array('text_search' => $_GET['text_search']);

	$ch = curl_init($api_url_service.'/search_contacts?'. http_build_query($param));

	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	//echo "<hr>";
	//print_r($resArray);
	//echo "<hr>";
	//echo $resJSON;
	//echo "<hr>";
	//print_r($_SERVER);
	//echo "<hr>";
	
	return $resArray;
}

function searchServices(){
	
	$configArray = getConfig('config.json');
	$api_key = $configArray['api_key'];
	$api_url_service = $configArray['api_url_service'];
	
	if(isset($_SESSION['auth_basic64']) && $_SESSION['auth_basic64']!=""){
		$authorization = $_SESSION['auth_basic64'];
	}else{
		$authorization = $configArray['auth_service'];
	}
	
	$param = array('text_search' => $_GET['text_search']);

	$ch = curl_init($api_url_service.'/search_services?'. http_build_query($param));

	curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"x-authorization: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);

	$resArray = json_decode($resJSON, true);
	
	return $resArray;
}

function sendMailDir(){
/**/
	include('Mail.php');
	
	$user_id = $_POST['user_id'];
	$user_name = $_POST['user_name'];
	$user_prof = $_POST['user_prof'];
	$user_division = $_POST['user_division'];
	$checkbox_anonym = $_POST['checkbox_anonym'];
	
	$from = $_POST['from'];
	$to = $_POST['to'];
	$to_full_copy = $_POST['to_full_copy'];
	$subject = "Сообщение с портала 'Личный кабинет работника' на тему: ".$_POST['theme_mail_dir'];
	$body_main = "Текст сообщения:\r\n";
	$body_main .= "'".$_POST['description_mail_dir']."'\r\n";
	
	$body_user = "\r\nДанные работника:\r\n";
	$body_user .= "ID: ".$user_id."\r\n";
	$body_user .= "ФИО: ".$user_name."\r\n";
	$body_user .= "Должность/профессия: ".$user_prof."\r\n";
	$body_user .= "Подразделение: ".$user_division."\r\n";
	
	if($checkbox_anonym=="false"){
		$body = $body_main.$body_user;
	}else{
		$body = $body_main."\r\nСообщение отправлено анонимно.\r\n";
	}
	
	$host = "mail.kmz.ru";
	$username = "portal@kmz.ru";
	$password = "#Qwerty123";
	
	$headers = array ('From' => $from,
	'To' => $to,
	'Subject' => $subject);
	
	$headers_full_copy = array ('From' => $from,
	'To' => $to_full_copy,
	'Subject' => $subject);
	
	$smtp = Mail::factory('smtp',array ('host' => $host,'auth' => true,'socket_options' => array('ssl' => array('verify_peer_name' => false, 'allow_self_signed' => true)),'username' => $username,'password' => $password,'port' => '25'));
	
	$mail = $smtp->send($to, $headers, $body);
	
	if (PEAR::isError($mail)){
		$statsend = "error";
		$errormess = $mail->getMessage();
	}else{
		$statsend = "success";
		$errormess = "";
	}
	
	// Отправляем копию сообщения с данными пользователя
	$mail_full_copy = $smtp->send($to_full_copy, $headers_full_copy, $body_main.$body_user);
	
	if (PEAR::isError($mail_full_copy)){
		$statsend_full_copy = "error";
		$errormess_full_copy = $mail->getMessage();
	}else{	
		$statsend_full_copy = "success";
		$errormess_full_copy = "";
	}
	
	$res = [ "statsend" => $statsend, "errormess" => $errormess, "statsend_full_copy" => $statsend_full_copy, "errormess_full_copy" => $errormess_full_copy ];
	return $res;

}


function sendMailCorr(){
/**/
	include('Mail.php');
	
	$user_id = $_POST['user_id'];
	$user_name = $_POST['user_name'];
	$user_prof = $_POST['user_prof'];
	$user_division = $_POST['user_division'];
	
	$from = $_POST['from'];
	$to = $_POST['to'];
	$to_full_copy = $_POST['to_full_copy'];
	$subject = "Сообщение о коррупции с портала 'Личный кабинет работника'";
	$body_main = "Текст сообщения:\r\n";
	$body_main .= "'".$_POST['description_mail']."'\r\n";
	
	$body_user = "\r\nДанные работника:\r\n";
	$body_user .= "ID: ".$user_id."\r\n";
	$body_user .= "ФИО: ".$user_name."\r\n";
	$body_user .= "Должность/профессия: ".$user_prof."\r\n";
	$body_user .= "Подразделение: ".$user_division."\r\n";
	
	$body = $body_main.$body_user;

	$host = "mail.kmz.ru";
	$username = "portal@kmz.ru";
	$password = "#Qwerty123";
	
	$headers = array ('From' => $from,
	'To' => $to,
	'Subject' => $subject);
	
	$headers_full_copy = array ('From' => $from,
	'To' => $to_full_copy,
	'Subject' => $subject);
	
	$smtp = Mail::factory('smtp',array ('host' => $host,'auth' => true,'socket_options' => array('ssl' => array('verify_peer_name' => false, 'allow_self_signed' => true)),'username' => $username,'password' => $password,'port' => '25'));
	
	$mail = $smtp->send($to, $headers, $body);
	
	if (PEAR::isError($mail)){
		$statsend = "error";
		$errormess = $mail->getMessage();
	}else{
		$statsend = "success";
		$errormess = "";
	}
	
	// Отправляем копию сообщения с данными пользователя
	$mail_full_copy = $smtp->send($to_full_copy, $headers_full_copy, $body_main.$body_user);
	
	if (PEAR::isError($mail_full_copy)){
		$statsend_full_copy = "error";
		$errormess_full_copy = $mail->getMessage();
	}else{	
		$statsend_full_copy = "success";
		$errormess_full_copy = "";
	}
	
	$res = [ "statsend" => $statsend, "errormess" => $errormess, "statsend_full_copy" => $statsend_full_copy, "errormess_full_copy" => $errormess_full_copy ];
	return $res;

}


function getRates(){
	// Формируем дату
	$year = substr($_GET['date'],0,4);
	$month = substr($_GET['date'],5,2);
	$day = substr($_GET['date'],8,2);
	$date = $day."/".$month."/".$year;
    //$date = date("d/m/Y");
    // Формируем ссылку
    $link = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=".$date;
    // Загружаем HTML-страницу
    $fd = @fopen($link, "r");
    $text="";
    if (!$fd) echo "Сервер ЦБ не отвечает";
    else
    {
      // Чтение содержимого файла в переменную $text
      while (!feof ($fd)) $text .= fgets($fd, 4096);
      // Закрыть открытый файловый дескриптор
      fclose ($fd);
    }
	$text = iconv('windows-1251', 'utf-8', $text);
    return $text;
}

function getWeather(){

	$api_key = '56b30cb255.3443075';
	$ch = curl_init('https://api.gismeteo.net/v2/weather/forecast/aggregate/4368/?lang=en&days=3');

	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"X-Gismeteo-Token: {$api_key}",
				"Accept: application/json",
				"Connection: keep-alive",
				"Accept-Language: ru,en;q=0.9",
	));

	$resJSON = curl_exec($ch);
	curl_close($ch);
	$resArray = json_decode($resJSON, true);
	
	return $resArray;

}


function curl_get_contents($page_url, $base_url, $pause_time, $retry) {
	/*
	$page_url - адрес страницы-источника
	$base_url - адрес страницы для поля REFERER
	$pause_time - пауза между попытками парсинга
	$retry - 0 - не повторять запрос, 1 - повторить запрос при неудаче
	*/
	$error_page = array();
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0");   
	curl_setopt($ch, CURLOPT_COOKIEJAR, str_replace("\\", "/", getcwd()).'/gearbest.txt'); 
	curl_setopt($ch, CURLOPT_COOKIEFILE, str_replace("\\", "/", getcwd()).'/gearbest.txt'); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Автоматом идём по редиректам
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Не проверять SSL сертификат
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // Не проверять Host SSL сертификата
	curl_setopt($ch, CURLOPT_URL, $page_url); // Куда отправляем
	curl_setopt($ch, CURLOPT_REFERER, $base_url); // Откуда пришли
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Возвращаем, но не выводим на экран результат
    $response['html'] = curl_exec($ch);
	$info = curl_getinfo($ch);
	if($info['http_code'] != 200 && $info['http_code'] != 404) {
		$error_page[] = array(1, $page_url, $info['http_code']);
		if($retry) {
			sleep($pause_time);
			$response['html'] = curl_exec($ch);
			$info = curl_getinfo($ch);
			if($info['http_code'] != 200 && $info['http_code'] != 404)
				$error_page[] = array(2, $page_url, $info['http_code']);
		}
	}
	$response['code'] = $info['http_code'];
	$response['errors'] = $error_page;
	curl_close($ch);
	return $response;

}

function getWeatherParser(){

	return curl_get_contents('https://www.gismeteo.ru/weather-kurgan-4569/10-days/', 'https://yandex.ru/', 10, 0);

/*
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://www.gismeteo.ru/weather-kurgan-4569/10-days/');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$urlContent = curl_exec($ch);
	curl_close($ch);
	return $urlContent;
*/

}


//$usersInfo = getUsers('KMZ\kz46721');
//print_r($usersInfo);
//echo "<hr>";
//$usersInfo = getUsers($_SERVER['AUTH_USER']);
//print_r($usersInfo);

//	Функция авторизации из формы регистрации.
//	Входной параметр: 'authorization'. 
function login_in_form(){
	
	$statlogged = statLogged();
	if($statlogged['statlogged']==0 || $statlogged['statlogged']==2){
		if(isset($_GET['authorization'])){
			
			$configArray = getConfig('config.json');
			$api_key = $configArray['api_key'];	
			$api_url_common = $configArray['api_url_common'];
			
			//$authorization = substr(strrchr($_GET['authorization']," "),1); //Отсекаем префикс 'Basic' по разделителю пробел

			$ch = curl_init($api_url_common."/current_user");

			//curl_setopt($ch, CURLOPT_USERPWD, base64_decode($authorization));
			curl_setopt($ch, CURLOPT_USERPWD, $_GET['authorization']);
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, '1C Enterprise 8.3');
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						"x-authorization: {$api_key}",
						"Accept: application/json",
						"Connection: keep-alive",
						"Accept-Language: ru,en;q=0.9",
			));

			$resJSON = curl_exec($ch);
			curl_close($ch);

//echo "|".$resJSON."|";
			if($resJSON!=""){

				$resArray = json_decode($resJSON, true);
				$authBasic64 = base64_encode($_GET['authorization']);
	//print_r ($resArray);
				$user = setAuthBasic($resArray,$authBasic64);
				$statlogged = statLogged();
				
				if($resArray['error_flag']==0 && $statlogged['statlogged']==1) $res = ["statlogged" => $statlogged['statlogged'], "data" => $user];
				else $res = ["statlogged" => "error"];		
			}else{
				$res = ["statlogged" => "error"];
			}	
			return $res;
		}
	}
	else{
		return $statlogged;
	}
}

//	Функция начала авторизации.
//	Если пользователь уже авторизован на сайте, то возвращаются данные авторизации.
//	Если пользователь авторизован в ОС, то создается сессия и в сессии записываются данные аутенификации ОС и возвращаются данные пользователя.
//	Иначе возвращается список доступных пользователей для формы регистрации.
function login_start(){
	//echo $_SERVER['AUTH_USER'];
	$statlogged = statLogged();
	if($statlogged['statlogged']==0){
		//Если пользователь зарегистрированный в ОС установлен
		if(isset($_SERVER['AUTH_USER']) && $_SERVER['AUTH_USER']!=""){

			$usersInfo = getUsers($_SERVER['AUTH_USER']);

			//print_r ($usersInfo);

			//Установлена аутентификация операционной системы
			if(count($usersInfo)>0 && $usersInfo[0]['authOS']) $res = setAuthOS($usersInfo[0]);
			else $res = getUsers();
		}
		else{
			$res = getUsers();
		}
		
		$statlogged = statLogged();
		$resArr = [
		"statlogged" => $statlogged['statlogged'],
		"data" => $res
		];
		return $resArr;
		
	}else if($statlogged['statlogged']==2){
		$usersArr = getUsers();
		$resArr = [
		"statlogged" => 2,
		"data" => $usersArr
		];
		return $resArr;
		
	}else{
		return $statlogged;
	}
	
}


//$configArray = getConfig('config.json');
//$api_key = $configArray['api_key'];
//echo $api_key;

if(isset($_GET['action']) && $_GET['action'] == "login_start") /*print_r(login_start());*/{header("Content-Type:application/json"); echo json_encode(login_start());}
if(isset($_GET['action']) && $_GET['action'] == "login_in_form") /*print_r(login_in_form());*/{header("Content-Type:application/json"); echo json_encode(login_in_form());}
if(isset($_GET['action']) && $_GET['action'] == "logout") /*print_r(logout());*/{header("Content-Type:application/json"); echo json_encode(logout());}
if(isset($_GET['action']) && $_GET['action'] == "statlogged") /*print_r(statLogged());*/{header("Content-Type:application/json"); echo json_encode(statLogged());}
if(isset($_GET['action']) && $_GET['action'] == "get_appeals_out") /*print_r(getAppealsOut());*/{header("Content-Type:application/json"); echo json_encode(getAppealsOut());}
if(isset($_GET['action']) && $_GET['action'] == "get_appeals_in") /*print_r(getAppealsIn());*/{header("Content-Type:application/json"); echo json_encode(getAppealsIn());}
if(isset($_GET['action']) && $_GET['action'] == "get_services") /*print_r(getServices());*/{header("Content-Type:application/json"); echo json_encode(getServices());}
if(isset($_GET['action']) && $_GET['action'] == "get_description") /*print_r(getDescription());*/{header("Content-Type:application/json"); echo json_encode(getDescription());}
if(isset($_GET['action']) && $_GET['action'] == "create_appeal") /*print_r(createAppeal());*/{header("Content-Type:application/json"); echo json_encode(createAppeal());}
if(isset($_GET['action']) && $_GET['action'] == "get_appeal_item") /*print_r(getAppealItem());*/{header("Content-Type:application/json"); echo json_encode(getAppealItem());}
if(isset($_GET['action']) && $_GET['action'] == "add_message_appeal") /*print_r(addMessageAppeal());*/{header("Content-Type:application/json"); echo json_encode(addMessageAppeal());}
if(isset($_GET['action']) && $_GET['action'] == "get_address_book") /*print_r(getAddressBook());*/{header("Content-Type:application/json"); echo json_encode(getAddressBook());}
if(isset($_GET['action']) && $_GET['action'] == "search_contacts") /*print_r(searchContacts());*/{header("Content-Type:application/json"); echo json_encode(searchContacts());}
if(isset($_GET['action']) && $_GET['action'] == "search_services") /*print_r(searchServices());*/{header("Content-Type:application/json"); echo json_encode(searchServices());}
if(isset($_GET['action']) && $_GET['action'] == "show_in_treeview") /*print_r(showInTreeview());*/{header("Content-Type:application/json"); echo json_encode(showInTreeview());}
if(isset($_GET['action']) && $_GET['action'] == "get_posts") /*print_r(getPosts());*/{header("Content-Type:application/json"); echo json_encode(getPosts());}
if(isset($_GET['action']) && $_GET['action'] == "add_post") /*print_r(addPost());*/{header("Content-Type:application/json"); echo json_encode(addPost());}
if(isset($_GET['action']) && $_GET['action'] == "del_post") /*print_r(delPost());*/{header("Content-Type:application/json"); echo json_encode(delPost());}
if(isset($_GET['action']) && $_GET['action'] == "edit_post") /*print_r(editPost());*/{header("Content-Type:application/json"); echo json_encode(editPost());}
if(isset($_GET['action']) && $_GET['action'] == "add_comment") /*print_r(addComment());*/{header("Content-Type:application/json"); echo json_encode(addComment());}
if(isset($_GET['action']) && $_GET['action'] == "get_comments") /*print_r(getComments());*/{header("Content-Type:application/json"); echo json_encode(getComments());}
if(isset($_GET['action']) && $_GET['action'] == "set_like") /*print_r(setLike());*/{header("Content-Type:application/json"); echo json_encode(setLike());}
if(isset($_GET['action']) && $_GET['action'] == "get_date_last_post") /*print_r(getDateLastPost());*/{header("Content-Type:application/json"); echo json_encode(getDateLastPost());}
if(isset($_GET['action']) && $_GET['action'] == "del_comment") /*print_r(delСomment());*/{header("Content-Type:application/json"); echo json_encode(delСomment());}
if(isset($_GET['action']) && $_GET['action'] == "send_mail_dir") /*print_r(sendMailDir());*/{header("Content-Type:application/json"); echo json_encode(sendMailDir());}
if(isset($_GET['action']) && $_GET['action'] == "send_mail_corr") /*print_r(sendMailCorr());*/{header("Content-Type:application/json"); echo json_encode(sendMailCorr());}
if(isset($_GET['action']) && $_GET['action'] == "get_rates") /*print_r(getRates());*/{header("Content-Type:text/xml");echo getRates();}
if(isset($_GET['action']) && $_GET['action'] == "get_weather") /*print_r(getWeatherParser());*/{header("Content-Type:application/json"); echo json_encode(getWeatherParser());}
if(isset($_GET['action']) && $_GET['action'] == "set_grade") /*print_r(setGrade());*/{header("Content-Type:application/json"); echo json_encode(setGrade());}
if(isset($_GET['action']) && $_GET['action'] == "return_appeal") /*print_r(returnAppeal());*/{header("Content-Type:application/json"); echo json_encode(returnAppeal());}
if(isset($_GET['action']) && $_GET['action'] == "del_appeal") /*print_r(delAppeal());*/{header("Content-Type:application/json"); echo json_encode(delAppeal());}
if(isset($_GET['action']) && $_GET['action'] == "get_alerts") /*print_r(getAlerts());*/{header("Content-Type:application/json"); echo json_encode(getAlerts());}
if(isset($_GET['action']) && $_GET['action'] == "hide_alerts") /*print_r(hideAlerts());*/{header("Content-Type:application/json"); echo json_encode(hideAlerts());}

if(isset($_GET['action']) && $_GET['action'] == "getserver") /*print_r(statLogged());*/print_r($_SERVER);

if(isset($_GET['action']) && isset($_GET['user']) && $_GET['action'] == "getusers") getUsers($_GET['user']);



?>