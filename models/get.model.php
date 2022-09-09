<?php

require_once "connection.php";


class GetModel{

    /*========================================
        PETICIONES GET SIN FILTRO
    ========================================*/

    static public function getData($table, $select, $orderBy, $orderMode,$startAt,$endAt){

         /*========================================
          VALIDAR EXISTENCIA DE LA TABLA
         ========================================*/

       $selectArray = explode(",",$select);

         if(empty(Connection::getColumnsData($table, $selectArray))){

             return null;
         }

        $schema = "Proyeccion";

        /*========================================
          GET - SELECCIONAR DATO ESPECIFICO
        ========================================*/    

        $sql = "SELECT $select FROM $schema.$table";


        /*=============================================
		GET - Ordenar datos sin limites
		=============================================*/

        if($orderBy !=  null && $orderMode != null && $startAt == null && $endAt == null){
           
            $sql = "SELECT $select FROM $schema.$table ORDER BY $orderBy $orderMode";


        }
		/*=============================================
		Ordenar y limitar datos
		=============================================*/

        if($orderBy !=  null && $orderMode != null  && $startAt != null && $endAt != null){
           
            $sql = "SELECT $select FROM $schema.$table ORDER BY $orderBy  OFFSET $startAt ROW FETCH NEXT $endAt ROWS ONLY ";

        }


        $stmt=Connection::connect()->prepare($sql);

        try {

            $stmt->execute();

        } catch (PDOException $Exception) {
            
            return null;
        }

        return $stmt->fetchAll(PDO::FETCH_CLASS);

    }

    /*========================================
     PETICIONES GET CON FILTRO
    ========================================*/

    static public function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode,$startAt,$endAt){
        
        /*========================================
          VALIDAR EXISTENCIA DE LA TABLA
         ========================================*/

         $schema = "Proyeccion";      
         $linkToArray = explode(",",$linkTo);
         $selectArray = explode(",",$select);

        foreach ($linkToArray as $key=>$value) {
            array_push($selectArray, $value);
        }

        $selectArray = array_unique($selectArray);

        if(empty(Connection::getColumnsData($table, $selectArray))){

            return null;
        }

         $linkToArray = explode(",",$linkTo);

         $equalToArray = explode("_",$equalTo);

         $linkToText = "";

         if(count($linkToArray)>1){
 
             foreach ($linkToArray as $key=>$value) {
 
                 if ($key > 0) {
                     
                     $linkToText .= "AND ".$value." = :".$value." ";
                     
                 }
             }
         }

        /*=============================================
		Sin ordenar y sin limitar datos
		=============================================*/
       
        $sql = "SELECT $select FROM $schema.$table WHERE $linkToArray[0]=:$linkToArray[0] $linkToText";

		/*=============================================
		Ordenar datos sin limites
		=============================================*/

        if($orderBy !=  null && $orderMode != null && $startAt == null && $endAt == null){
           
        $sql = "SELECT $select FROM $schema.$table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode";

        }

        /*=============================================
		Ordenar y limitar datos
		=============================================*/

		if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){

		$sql = "SELECT $select FROM $schema.$table WHERE $linkToArray[0]=:$linkToArray[0]$linkToText ORDER BY $orderBy $orderMode OFFSET $startAt ROW FETCH NEXT $endAt ROWS ONLY ";


		}


        $stmt = Connection::connect()->prepare($sql);

        foreach ($linkToArray as $key=>$value) {

            $stmt->bindParam(":".$value,$equalToArray[$key],PDO::PARAM_STR);

            
        }

       
        try {

            $stmt -> execute();

        } catch (PDOException $Exception) {
            
            return null;
        }

        return $stmt->fetchAll(PDO::FETCH_CLASS);

    }

    /*==================================================
      PETICIONES GET SIN FILTRO entre tablas relacioneas
    ==================================================*/

    static public function getRelData($rel, $type, $nameFk, $select, $orderBy, $orderMode, $startAt, $endAt){

        /*========================================
        ORGANIZAMOS LAS RELACIONES
        ========================================*/
        $schema = "Proyeccion";    
        
        $selectArray = explode(",", $select);
        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);
        $nameArray = explode(",", $nameFk);
        $innerJoinText = "";
            
        if(count($relArray)>1){
            

            foreach ($relArray as $key=>$value) {                
               

             /*========================================
             VALIDAR EXISTENCIA DE LA TABLA
             ========================================*/

                 if(empty(Connection::getColumnsData($value, ["*"]))){
                    
                     return null;
                 }
                   if($key > 0){

                        if($value>=1){

 			    $innerJoinText .= " INNER JOIN ".$schema.".".$value. " ON ".$relArray[0].".".$nameArray[$key-1] ."=".$value.".".$typeArray[$key]."";
                        }
                   }                   
            }
            /*=============================
            SIN ORDENAR Y LIMITAR DATOS
            =============================*/
            
            $sql = "SELECT $select FROM $schema.$relArray[0] $innerJoinText";
            //echo '<pre>'; print_r($sql); echo '</pre>';

            

            /*===================================
            ORDENAR DATOS SIN LIMITES
            ===================================*/

            if($orderBy != null && $orderMode != null && $startAt == null && $endAt == null){

				$sql = "SELECT $select FROM $schema.$relArray[0] $innerJoinText ORDER BY $orderBy $orderMode";
                echo '<pre>'; print_r($sql); echo '</pre>';
                return;
			}

            /*=============================================
			Ordenar y limitar datos
			=============================================*/

            if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){
                        
                $sql = "SELECT $select FROM  $schema.$relArray[0] $innerJoinText ORDER BY $orderBy $orderMode OFFSET $startAt ROW FETCH NEXT $endAt ROWS ONLY ";
                echo '<pre>'; print_r($sql ); echo '</pre>';
                return;
            }

            $stmt = Connection::connect()->prepare($sql);

            try {

                $stmt -> execute();

            } catch (PDOException $Exception) {
                
                return null;
            }

            return $stmt -> fetchAll(PDO::FETCH_CLASS);

        }else{
            return null;
        }

    }
    
    /*==================================================
     PETICIONES GET con FILTRO entre tablas relacioneas
    ==================================================*/

    static public function getRelDataFilter($rel, $type, $nameFk, $select, $linkTo, $equalTo, $orderBy, $orderMode,$startAt,$endAt){
        
        /*========================================
          VALIDAR EXISTENCIA DE LA COLUMNA
         ========================================*/
         $schema = "Proyeccion";     
         $linkToArray = explode(",",$linkTo);
         $selectArray = explode(",",$select);
         $nameArray = explode(",", $nameFk);
         
        foreach ($linkToArray as $key=>$value) {
            array_push($selectArray, $value);
        }

        $selectArray = array_unique($selectArray);

        /*==========================================
          ORGANIZAMOS LOS FILTROS
        ===========================================*/
                
        $linkToArray = explode(",",$linkTo);

        $equalToArray = explode("_",$equalTo);

        $linkToText = "";

            if(count($linkToArray)>1){

                foreach ($linkToArray as $key=>$value) {



                    if ($key > 0) {
                            
                        $linkToText .= "AND ".$value." = :".$value." ";
                            
                    }
                }
            }

                /**=================================================
                 * ORGANIZAMOS LAS RELACIONES
                 * ==================================================
                 */

                $relArray = explode(",", $rel);
                $typeArray = explode(",", $type);

                $innerJoinText = "";
        
                if(count($relArray)>1){

                    foreach ($relArray as $key=>$value) {

                        /*========================================
                        VALIDAR EXISTENCIA DE LA TABLA
                        ========================================*/
                    
                        if(empty(Connection::getColumnsData($value, ["*"]))){

                            return null;
                        }

                        if($key > 0){

                                if($value>=1){
                                    //$innerJoinText .= " INNER JOIN ".$value." ON ".$relArray[0].".id_".$typeArray[0] ." =".$value.".id_".$typeArray[0]."_".$typeArray[$key]. " ";

                                    $innerJoinText .= " INNER JOIN ".$schema.".".$value. " ON ".$relArray[0].".".$nameArray[$key-1] ."=".$value.".".$typeArray[$key]."";
                                }
                        }
                    }
                
                    /**=============================
                     * SIN ORDENAR Y LIMITAR DATOS
                     * =============================
                     */

                    $sql = "SELECT $select FROM $schema.$relArray[0] $innerJoinText WHERE $relArray[0].$linkToArray[0] = :$linkToArray[0] $linkToText";



                    /*=============================================
                    Ordenar datos sin limites
                    =============================================*/

                    if($orderBy != null && $orderMode != null && $startAt == null && $endAt == null){

                        $sql = "SELECT $select FROM $schema.$relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode";


                    }
                    /*=============================================
                    Ordenar y limitar datos
                    =============================================*/

                    if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){
                        
                        $sql = "SELECT $select FROM $schema.$relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode OFFSET $startAt ROW FETCH NEXT $endAt ROWS ONLY ";


                    }


                    $stmt = Connection::connect()->prepare($sql);

                    foreach ($linkToArray as $key=>$value) {

                        $stmt -> bindParam(":".$value, $equalToArray[$key], PDO::PARAM_STR);
                    
    
                    }

                    try {

                        $stmt -> execute();
        
                    } catch (PDOException $Exception) {
                        
                        return null;
                    }

                    return $stmt -> fetchAll(PDO::FETCH_CLASS);

                }else{
                    return null;
                }


    }
    
    /*==============================================
    PETICIONES GET PARA EL BUSCADOR SIN RELACIONES
    ==============================================*/

    static public function getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt){

        /*========================================
        VALIDAR EXISTENCIA DE LA TABLA
        ========================================*/
        $linkToArray = explode(",",$linkTo);
        $selectArray = explode(",",$select);
        $schema = 'Proyeccion';
        foreach ($linkToArray as $key=>$value) {
            array_push($selectArray,$value);
        }

        $selectArray = array_unique($selectArray);

        if(empty(Connection::getColumnsData($table, $selectArray))){

            return null;
        }
            
        $searchArray = explode("_",$search);
        $linkToText = "";    

            if(count($linkToArray)>1){
    
                foreach ($linkToArray as $key=>$value) {
    
                    if ($key > 0) {
                        
                        $linkToText .= "AND ".$value." = :".$value." ";
                        
                    }
                }
            }
                /*=============================================
                Sin ordenar y sin limitar datos
                =============================================*/

                $sql = "SELECT $select FROM $schema.$table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText";

                /*=============================================
                Ordenar datos sin limites
                =============================================*/
                
                if($orderBy != null && $orderMode != null && $startAt == null && $endAt == null){

                    $sql = "SELECT $select FROM $schema.$table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode";

                }
                /*=============================================
                Ordenar y limitar datos
                =============================================*/

                if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){

                    $sql = "SELECT $select FROM $schema.$table WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode OFFSET $startAt ROW FETCH NEXT $endAt ROWS ONLY ";

                }


                $stmt = Connection::connect()->prepare($sql);

                foreach ($linkToArray as $key=>$value) {

                    if($key > 0){
                    
                        $stmt -> bindParam(":".$value, $searchArray[$key], PDO::PARAM_STR);

                    } 
        
                    
                }

                $stmt -> execute();

                return $stmt -> fetchAll(PDO::FETCH_CLASS);

        }

    
    /*==================================================
    PETICIONES GET con FILTRO entre tablas relacioneas
    ==================================================*/

    static public function getRelDataSearch($rel, $type, $nameFk, $select, $linkTo, $search, $orderBy, $orderMode, $startAt, $endAt){

        $schema = 'Proyeccion';
        $linkToArray = explode(",",$linkTo);
        $selectArray = explode(",",$select);
        $nameArray = explode(",", $nameFk);
        foreach ($linkToArray as $key=>$value) {
            array_push($selectArray, $value);
        }

        $selectArray = array_unique($selectArray);

        /*===========================================
         ORGANIZAMOS LOS FILTROS
        ============================================*/
        
        $searchArray = explode("_",$search);

        $linkToText = "";

        if(count($linkToArray)>1){

            foreach ($linkToArray as $key=>$value) {


                if ($key > 0) {
                    
                    $linkToText .= "AND ".$value." = :".$value." ";
                    
                }
            }
        }

        /**=================================================
         * ORGANIZAMOS LAS RELACIONES
         * ==================================================
         */

        $relArray = explode(",", $rel);
        $typeArray = explode(",", $type);

        $innerJoinText = "";


        if(count($relArray)>1){

            foreach ($relArray as $key=>$value) {

                /*===========================================
                validar la existencia de la tabla
                ============================================*/

                if(empty(Connection::getColumnsData($value, ["*"]))){
    
                    return null;
                }

                if($key > 0){

                        if($value>=1){
                        //     $innerJoinText .= " INNER JOIN ".$value." ON ".$relArray[0].".id_".$typeArray[0] ." =
                        // ".$value.".id_".$typeArray[0]."_".$typeArray[$key]. " ";
                        $innerJoinText .= " INNER JOIN ".$schema.".".$value. " ON ".$relArray[0].".".$nameArray[$key-1] ."=".$value.".".$typeArray[$key]."";
                        }
                }
            }

			/*=============================================
			Sin ordenar y sin limitar datos
			=============================================*/
            
            $sql = "SELECT $select FROM $schema.$relArray[0] $innerJoinText WHERE $relArray[0].$linkToArray[0] LIKE '%$searchArray[0]%' $linkToText";


            
			/*=============================================
			Ordenar datos sin limites
			=============================================*/

			if($orderBy != null && $orderMode != null && $startAt == null && $endAt == null){

				$sql = "SELECT $select FROM $schema.$relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode";


			}
            /*=============================================
			Ordenar y limitar datos
			=============================================*/

			if($orderBy != null && $orderMode != null && $startAt != null && $endAt != null){

				$sql = "SELECT $select FROM $schema.$relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchArray[0]%' $linkToText ORDER BY $orderBy $orderMode OFFSET $startAt ROW FETCH NEXT $endAt ROWS ONLY ";


			}


			$stmt = Connection::connect()->prepare($sql);

			foreach ($linkToArray as $key => $value) {

				if($key > 0){
				
					$stmt -> bindParam(":".$value, $searchArray[$key], PDO::PARAM_STR);

				}

			}

			try{

				$stmt -> execute();

			}catch(PDOException $Exception){

				return null;
			
			}

			return $stmt -> fetchAll(PDO::FETCH_CLASS);

		}else{

			return null;
		}
		
	}


}