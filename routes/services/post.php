<?php

require_once "models/connection.php";
require_once "controllers/post.controller.php";

if(isset($_POST)){

	/*=============================================
	Separar propiedades en un arreglo
	=============================================*/
	
	$columns = array();

	foreach (array_keys($_POST) as $key => $value) {
		
		array_push($columns, $value);
			
	}
	/*=============================================
	Validar tablas y columnas
	=============================================*/	

	if(empty(Connection::getColumnsData($table, $columns))){

		$json = array(
			'status' => 400,
			'results' => "Error: los campos no coinciden con la base de datos"
		);

		echo json_encode($json, http_response_code($json["status"]));

		return;
	}

	
	$response = new PostController();

	/*=============================================
	Peticion POST para registrar usuario
	=============================================*/	

	if(isset($_GET["register"]) && $_GET["register"] == true){

		$suffix = $_GET["suffix"] ?? "pers";

		$response -> postRegister($table,$_POST,$suffix);

	/*=============================================
	Peticion POST para login de usuario
	=============================================*/	

	}else if(isset($_GET["login"]) && $_GET["login"] == true){

		$table = "Sigedo".".".$table;

		$suffix = $_GET["suffix"] ?? "pers";

		$response -> postLogin($table,$_POST,$suffix);
		
		return;

	/*=============================================
	Solicitamos respuesta del controlador
	=============================================*/	

	}else{

		$response -> postData($table,$_POST);
	}

}