<?php

require_once "models/post.model.php";
require_once "models/connection.php";
require_once "models/put.model.php";

class PutController{

    /*=============================================
	RESPUESTA PUT PARA EDITAR DATOS
	=============================================*/	

    static public function putData($table, $data,$id, $nameId){

        $response = PutModel::putData($table, $data,$id, $nameId);
        $return = new PutController();
        $return -> fncResponse($response, null);

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
						'method' => 'put'
					);

				}

	
			 }
	
			echo json_encode($json, http_response_code($json["status"]));
	
	
	
		}
}