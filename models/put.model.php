<?php

require_once "connection.php";
require_once "get.model.php";

class PutModel{

    /*=============================================
	PETICION PUT PARA editar DATOS DE FORMA DINAMICA 
	=============================================*/	

    static public function putData($table, $data, $id, $nameId){
        
        /*=============================================
	    validar id
	    =============================================*/	

        $response = GetModel:: getDataFilter($table, $nameId, $nameId, $id, null, null, null, null);

        if(empty($response)){

           return null;
        }

        /*=============================================
	    Actualizamos registros
	    =============================================*/	
        $Schema = 'Proyeccion';
        $set = "";

        foreach ($data as $key => $value) {
            
            $set .= $key." = :".$key.",";
            
        }
        $set =   substr($set, 0, -1);

        $sql = "UPDATE $Schema.$table SET $set WHERE $nameId = :$nameId";

        $link = Connection::connect();
        $stmt = $link->prepare($sql);

        foreach ($data as $key => $value) {
          
            $stmt -> bindParam(":".$key, $data[$key], PDO::PARAM_STR);

        }

        $stmt -> bindParam(":".$nameId, $id, PDO::PARAM_STR);

        if($stmt -> execute()){

            $response = array(

                "comment" => "Editado con exito"
            );
            
            return $response;

        }else{

            return $link->errorInfo();
        }

    }
}