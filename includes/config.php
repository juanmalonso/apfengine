<?php
return $getConfig = function($p_configPath){

    $result = FALSE;

    $dir = $p_configPath;
    
    if(is_dir($dir)){

        if($dh = opendir($dir)){

            $config = array();

            while (($file = readdir($dh)) !== false){

                if($file != "." && $file != ".." && $file != "config.php" && $file != "configOld.php" && $file != "loader.php"){

                    $fileNamePartes = explode(".", $file);

                    $jsonData       = file_get_contents($dir."$file");

                    $arrayData     = json_decode($jsonData, true);

                    if(is_array($arrayData)){

                        $config[$fileNamePartes[0]] = $arrayData;
                    }else{

                        //TODO: Error Config Incorrecto
                        $config = FALSE;
                        break;
                    }

                }
            }

            $result = new Phalcon\Config($config);

            closedir($dh);
        }else{

            //TODO: Error de Apertura de Directorios
        }
    }

    return $result;
};
?>
