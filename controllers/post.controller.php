<?php

require_once "models/post.model.php";
require_once "models/connection.php";
require_once "models/get.model.php";

class PostController{

    /*=============================================
	RESPUESTA DEL CONTROLADOR
	=============================================*/

    static public function postData($table, $data){

        $response = PostModel::postData($table, $data);

        $return = new PostController();
        $return -> fncResponse($response, null);

    }

	/*=============================================
	Peticion POST para registrar usuario
	=============================================*/

	static public function postRegister($table, $data, $suffix){

		if(isset($data[$suffix."Password"]) && $data[$suffix."Password"] != null){

			$crypt = crypt($data[$suffix."Password"], '$2a$07$azybxcags23425sdg23sdfhsd$');

			$data[$suffix."Password"] = $crypt;

			$response = PostModel::postData($table, $data);
			$return = new PostController();
			$return -> fncResponse($response,null,$suffix);

		}
	}

	/*=============================================
	Peticion POST para login de usuario
	=============================================*/

	static public function postLogin($table, $data, $suffix){

		/*=============================================
		Validar que el usuario exista en BD
		=============================================*/

		//LOGIN CON CORREO

		$select = "*";
		$table = "Persona";

		$response = GetModel::getDataFilter($table, "*", $suffix."Email", $data[$suffix."Email"], null,null,null,null);

		// LOGIN CON ID USUARIO

		//$response = GetModel::getDataFilter($table, "*", "id_".$suffix, $data["id_".$suffix], null, null);


		if(!empty($response)){

				/*=============================================
				Encriptamos la contraseña
				=============================================*/

				$crypt = crypt($data[$suffix."Password"], '$2a$07$azybxcags23425sdg23sdfhsd$');

				if($response[0]->{$suffix."Password"} == $crypt){

					$return = new PostController();
					$return -> fncResponse($response[0], null);
				}else{
					$response = null;
					$return = new PostController();
					$return -> fncResponse($response, "Password errado");
				}
		}else{

			$response = null;
			$return = new PostController();
			$return -> fncResponse($response, "Correo errado");
		}
	}




    /*=============================================
	RESPUESTA DEL CONTROLADOR
	=============================================*/

		public function fncResponse($response, $error) {

			if(!empty($response)){

				$json = array(

					'status' => 200,
					'results' => $response
				);
			}
			 else{

			    if($error != null){

			    	$json = array(
			    		'status' => 400,
			    		"results" => $error
			    	);
			    }
                 else{
					$json = array(

						'status' => 404,
						'results' => 'Not Found',
						'method' => 'post'
					);

				}


			 }

			echo json_encode($json, http_response_code($json["status"]));



		}
}