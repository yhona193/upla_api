<?php
require_once "models/connection.php";

$routesArray = explode("/", $_SERVER['REQUEST_URI']);


$routesArray = array_filter($routesArray);

/*=============================================
Cuando no se hace ninguna petición a la API
=============================================*/

if(count($routesArray) == 0){

	$json = array(

		'status' => 404,
		'results' => 'Not Found'

	);

	echo json_encode($json, http_response_code($json["status"]));

	return;

}
/*=============================================
Cuando si se hace una petición a la API
=============================================*/

if(count($routesArray) == 1 && isset($_SERVER['REQUEST_METHOD'])){

	
	$table = explode("?", $routesArray[1])[0];

	/*=============================================
	Validar llave secreta
	=============================================*/

	// if(!isset(getallheaders()["Authorization"])){

	// 	$json = array(
		
	// 		'status' => 400,
	// 		"results" => "No esta autorizado para realizar esta solicitud."
	// 	);

	// 	echo json_encode($json, http_response_code($json["status"]));

	// 	return;

	// }
    /*=============================================
	Peticiones GET
	=============================================*/

	if($_SERVER['REQUEST_METHOD'] == "GET"){

        include 'services/get.php';
	}

	/*=============================================
	Peticiones POST
	=============================================*/

    if($_SERVER['REQUEST_METHOD'] == "POST"){

        include "services/post.php";

	}

	/*=============================================
	Peticiones PUT
	=============================================*/

	if($_SERVER['REQUEST_METHOD'] == "PUT"){

        include "services/put.php";

	}

	/*=============================================
	Peticiones DELETE
	=============================================*/

	if($_SERVER['REQUEST_METHOD'] == "DELETE"){

		include "services/delete.php";

	}
}
