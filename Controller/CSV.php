<?php

require_once '../cab.inc.php';
extract($_POST);

if(!$_POST){
        die('0');//no hacer nada
}

switch ($action) {
    case 'Subir al Servidor':
        
        $Con = new Conn();
        $Con->conectar();
        $sesion = $Con->getSesion();
        
        if ($sesion === FALSE)
            die('0');
        else if(!in_array("1", $_SESSION['Roles']))
            die('0');
        
        
        if (!$_FILES)
            die('Es obligatorio seleccionar un archivo');

        if ($_FILES['myFile']['type'] == 'text/csv' || $_FILES['myFile']['type'] == 'application/vnd.ms-excel'){
            $ok = TRUE;
            $fp = fopen ( $_FILES['myFile']['tmp_name'], "r" );
            
            mysqli_autocommit($Con->getLink(), FALSE);
            while (( $data = fgetcsv ( $fp , 2048, ";" )) !== false ) { // Mientras hay líneas que leer...
                $data = implode("", $data);
                $fila = explode(";", $data);
               if ($fila[0] === "1"){//datos del usuario
                    $nombre     = trim($fila[4]);//nombre
                    $direccion  = trim($fila[5]);//direccion
                    $ciudad     = trim($fila[6]);//[7];//ciudad
                    $pais       = trim($fila[7]);//[8];//pais
                    $CC         = ltrim($fila[8], '0');//[10];//cedula
                    $saldoAnt   = trim($fila[9]);//[11];//saldo Anterior

                    $Usuario = new Usuario();
                    $rta = $Usuario->getByFilter($Con, array('Nuip'=>$CC));
                    
                    if ($rta === FALSE){//si no existe el usuario
                        $Usuario->setNuip($CC);
                        $Usuario->setCiudad($ciudad);
                        $Usuario->setDireccion($direccion);
                        $Usuario->setPais($pais);
                        $Usuario->setPrimerNom($nombre);
                        if (!$Usuario->setUsuario($Con)){
//                            echo "a";
                            $ok=FALSE;
                            break;
                        }
                        
                        $Login = new Login();
                        $Login->setPassword(md5($CC));
                        $Login->setUsuarioNuip($CC);
                        if (!$Login->setLogin($Con)){
//                            echo "b";
                            $ok=FALSE;
                            break;
                        }
                        
                        $LoginPerfil = new LoginHasPerfil();
                        $LoginPerfil->setLoginUsuarioNuip($CC);
                        $LoginPerfil->setPerfilId(2);
                        if (!$LoginPerfil->setLoginPerfil($Con)){
//                            echo "c";
                            $ok=FALSE;
                            break;
                        }
                    }
                
                }
                elseif($fila[0] === "2"){
                    $fecha      = trim($fila[1]);//fecha
                    $documento  = trim($fila[2]);//documento
                    $numero     = trim($fila[3]);//numero
                    $transaccion= trim($fila[4]);//transaccion
                    $detalle    = trim($fila[5]. " ".$fila[6]);//. " ". $fila[7];//detalle
                    $debito     = trim($fila[7]);//[9];//debito
                    $credito    = trim($fila[8]);//[10];//credito
                    $saldo      = trim($fila[9]);//[11];//
                    
                    $Documento = new Documento();
                    $rtaDoc = $Documento->getByFilter($Con, array('Alias'=>$documento));
                    
                    if ($rtaDoc === FALSE){
                        $Documento->setAlias($documento);
                        $Documento->setNombre($documento);
                        $idDoc = $Documento->setDocumento($Con);
                    }
                    else{
                        $idDoc = $rtaDoc[0]->getId();//obtener documento id
                    }
                    
                    $rtaTrn = $Documento->getByFilter($Con, array('Alias'=>$transaccion));
                    if ($rtaTrn === FALSE){
                        $Documento->setAlias($transaccion);
                        $Documento->setNombre($transaccion);
                        $idTrn = $Documento->setDocumento($Con);
                    }
                    else{
                        $idTrn = $rtaTrn[0]->getId();//obtener documento id
                    }
                    
                    $Rubros = new Rubros();
                    $Rubros->setCredito($credito);
                    $Rubros->setDetalle($detalle);
                    $Rubros->setDebito($debito);
                    $Rubros->setDocumentoId($idDoc);
                    $Rubros->setFechaDocumento($fecha);
                    $Rubros->setNumero($numero);
                    $Rubros->setSaldo($saldo);
                    $Rubros->setTransaccionId($idTrn);
                    $Rubros->setUsuarioNuip($CC);

                    if (!$Rubros->setRubro($Con)){
                        $ok=FALSE;
                        break;
                    }
                    
                }


            } 
            
            if($ok===TRUE){
                mysqli_commit($Con->getLink());
                printf(utf8_encode("Archivo Cargado Satisfactoriamente"));
            }else{
                mysqli_rollback($Con->getLink());
                printf(utf8_encode("Hubo un error al cargar el archivo. Posiblemente ya fue cargado"));
            }
            
            fclose ( $fp );
            
            
        }else{
            printf('El archivo que intenta cargar no es un CSV');
        }
        
        
        
        $Con->cerrar();
        break;

    default:
        break;
}
die();
?>
