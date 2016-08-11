<?php
$mesabot_token = 'b5Jzo6hZlPM9gL0YLyhDphqSHWsNLm0SGO9m5g6V';

include 'Mesabot.php';
include 'Playsms_Mesabot.php';

try {
	$data = array(
		'destination' => '081234567890',
		'text' => 'test mesabot ' . mktime() 
	);
	
	$mesabot = new Playsms_Mesabot();
	$mesabot->setToken($mesabot_token);
	$mesabot->sms($data);
	
	//print_r($mesabot->response());
	$response = $mesabot->response();
	echo "status code: " . $response->status_code . PHP_EOL;
}
catch (Exception $e) {
	echo $e->getMessage();
}
