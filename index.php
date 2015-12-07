<?php 
session_start();

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'Store.php');

$action   = "";
$store    = new Store();
$response = array();

if(!empty($_GET['action'])) {
	$action = strip_tags($_GET['action']);
}

switch($action) {
	case "login" :
		$data = $store->login();
		break;
	case "logout" :
		$data = $store->logout();
		break;
	case "add" :
		$data = $store->add();
		break;
	case "delete" :
		$data = $store->delete();
		break;
	case "edit" :
		$data = $store->edit();
		break;
	case "search" :
		$data = $store->search();
		break;	
	default :
		$data = array(
			'success' => false,
			'data'	  => array(),
			'error'   => array(
				'warning' => 'Invalid action'
				)
			);
		break;			
}

echo json_encode($data);

?>