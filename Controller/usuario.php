<?php

require_once '../cab.inc.php';

extract($_POST);
if(!$_POST){
        die('0');//no hacer nada
}
$action = (isset($action)) ? $action : null;
switch ($action) {

    case "loadName"://cargar el nombre del usuario loguedo
        $Con = new Conn();
        $Con->conectar();
        $sesion = $Con->getSesion();
        
        if (in_array("1", $_SESSION['Roles']))
            $role = "SU";
        else
            $role = "NO";
        
        if ($sesion === FALSE)
            die('0');

        $Usuario = new Usuario();
        $dato = $Usuario->getByFilter($Con, array('Nuip' => $sesion));
        echo "{$role}::".ucfirst(utf8_encode($dato[0]->getPrimerNom())) . " " . ucfirst(utf8_encode($dato[0]->getPrimerApell()));

        break;

    case 'endSesion'://Salir del modulo
        session_start();
        session_destroy();

        die("1");
        break;
    
    case 'Modificar Clave'://Editar Contraseña
        $Con = new Conn();
        $Con->conectar();
        $sesion = $Con->getSesion();
        
        if ($sesion === FALSE)
            die('0');//salir
        
        $reg = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,15}$/";
        //hacer la misma validacion del JS
        if ($newPassword && $newPassword2 && $newPassword){
            if ($newPassword !== $newPassword2){
                die(utf8_encode("La nueva contraseña y la confirmación no coinciden"));
            }
            if(preg_match($reg, $newPassword)){
                $Login = new Login();
                $Login->setPassword(md5($newPassword));
                $rta = $Login->updateByFilter($Con, array('Usuario_Nuip'=>$sesion, 'Password'=>  md5($oldPassword)));

                if ($rta == TRUE){
                    printf(utf8_encode('Modificación realizada con éxito'));
                }
                else{
                    printf(utf8_encode('La clave anterior no es la correcta (Recuerde que es OBLIGATORIO cambiar la contraseña)'));
                }
            }
            else{
                printf(utf8_encode("La contraseña no cumple con las condiciones (Mayúsculas, minúscilas y número)"));
            }
            
        }
        else{
            printf("Hay campos que no pueden estar vacios");
        }
        
        
        $Con->cerrar();
        
        break;
        
    case 'loadPerfiles':
        $Con = new Conn();
        $Con->conectar();
        
        $Perfil = new Perfil();
        $Roles = $Perfil->getByFilter($Con);
        $html = "";
        foreach ($Roles as $Rol) {
            $html .= "<option value='{$Rol->getId()}'>{$Rol->getNombre()}</option>";
        }
        printf('%s', $html);
        
        $Con->cerrar();
        break;
    
    case 'Crear'://Crear Usuario
        
        $Con = new Conn();
        $Con->conectar();
        $sesion = $Con->getSesion();
        
        if ($sesion === FALSE)
            die('0');
        else if(!in_array("1", $_SESSION['Roles']))
            die('0');
        
        if(!isset($perfiles))
            die("Debe seleccionar un perfil");
        
        if ($nuip && $pNombre && $pApellido && count($perfiles)>0){
            mysqli_autocommit($Con->getLink(), FALSE);

            $ok = TRUE;

            $Usuario = new Usuario();
            $Usuario->setNuip($nuip);
            $Usuario->setPrimerApell(utf8_decode($pApellido));
            $Usuario->setPrimerNom(utf8_decode($pNombre));
            $Usuario->setSegundoApell(utf8_decode($sApellido));
            $Usuario->setSegundoNom(utf8_decode($sNombre));
            $Usuario->setUsuario($Con) ? NULL:$ok=FALSE;

            $Login = new Login();
            $Login->setPassword(md5($nuip));
            $Login->setUsuarioNuip($nuip);
            $Login->setLogin($Con) ? NULL:$ok=FALSE;
            
            foreach ($perfiles as $perfil) {
                $LoginPerfil = new LoginHasPerfil();
                $LoginPerfil->setLoginUsuarioNuip($nuip);
                $LoginPerfil->setPerfilId($perfil);
                $LoginPerfil->setLoginPerfil($Con) ? NULL:$ok=FALSE;
                if($ok === FALSE){break;}
                
            }
            if($ok===TRUE){
                mysqli_commit($Con->getLink());
                printf(utf8_encode("Usuario creado Satisfactoriamente, recuerde que el password de acceso es el número de identificación"));
            }else{
                mysqli_rollback($Con->getLink());
                printf(utf8_encode("Hubo un error en la creación del usuario"));
            }

        }else{
            printf("Recuerde que hay campos obligatorios");
        }
        
        $Con->cerrar();
        
        
        break;
        
    case 'Generar'://Generar Extracto
        $Con = new Conn();
        $Con->conectar();
        $sesion = $Con->getSesion();
        
        if ($sesion === FALSE)
            die('0');
        
        $fechaIni = substr($date, 0, -3);//."-01";
        $fechaFin = substr($date, 0, -3);//."-31";
        
        $Usuario = new Usuario();
        $rta = $Usuario->getByFilter($Con, array('Nuip'=>$sesion));
        
        $Rubros = new Rubros();
        $datos = $Rubros->getByIdUserDates($Con, $sesion, $fechaIni."-01", $fechaFin."-31");
        
        if($datos === FALSE)
            die("No se han cargado datos para la fecha seleccionada");
        
        
        $pdf = new PDF_MC_Table();
        $pdf->Open();
        $pdf->AddPage();
        $pdf->SetMargins(20, 20, 20, 20);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(163, 18, 1);//Texto Rojo Lorenca
        $pdf->Cell(0, 10);
        $pdf->Ln();
        $pdf->Cell(0,10,'EXTRACTOS',0,0,'R');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Image('../View/css/logo.png', 20, 10, 80, 30, 'PNG');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetWidths(array(16, 16, 46, 36, 20, 16, 20));
        $pdf->SetTextColor(0, 0, 0);//Texto Negro
        $pdf->Cell(0,10,'DATOS CLIENTE',1,1,'C');
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Row(array('FECHA EXT', 'CÉDULA', 'NOMBRE', 'DIRECCIÓN', 'CIUDAD', 'PAIS', 'SALDO ANT'), TRUE);
        
        $pdf->SetFont('Arial', '', 7);
        $pdf->Row(array($fechaFin, $rta[0]->getNuip(), "{$rta[0]->getPrimerNom()} {$rta[0]->getSegundoNom()} {$rta[0]->getPrimerApell()} {$rta[0]->getSegundoNom()}", $rta[0]->getDireccion(), $rta[0]->getCiudad(), $rta[0]->getPais(), '--'), TRUE);
        
        $pdf->Ln();
        $pdf->Ln();
        
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0,10,'MOVIMIENTOS DEL MES',1,1,'C');
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetWidths(array(15, 20, 14, 21, 36, 21, 23, 20));
        
        $pdf->Row(array('FECHA', 'DOCUMENTO', 'NÚMERO', 'TRANSACCIÓN', 'DETALLE', 'VALOR DÉBITO', 'VALOR CRÉDITO', 'SALDO'),TRUE);

        $pdf->SetFont('Arial', '', 7);
        foreach ($datos as $fila) {
            $debito = number_format((float)substr($fila['Debito'], 0, -2).".00", 2);
            $credit = number_format((float)substr($fila['Credito'], 0, -2).".00", 2);
            $saldo  = number_format((float)substr($fila['Saldo'], 0, -3).".00", 2);
            $sym    = substr($fila['Saldo'], -1);
            
            $pdf->Row(array($fila['FechaDocumento'],$fila['Documento'],$fila['Numero'],$fila['Transaccion'],$fila['Detalle'],$debito,$credit,$saldo.$sym));
        }
        $pdf->Output();

        break;
        
    case 'Restaurar'://Editar Contraseña
        $Con = new Conn();
        $Con->conectar();
        $sesion = $Con->getSesion();
        
        if ($sesion === FALSE)
            die('0');//salir
        
        $nuip = trim($cc);
        
        $Login = new Login();
        $Login->setPassword(md5($nuip));
        $rta = $Login->updateByFilter($Con, array('Usuario_Nuip'=>$nuip));

        
        if ($rta == TRUE){
            printf(utf8_encode('Modificación realizada con éxito'));
        }
        else{
            printf(utf8_encode('No se ha realizado la restauración. Puede que se haya hecho una previamente'));
        }

        break;
        
    default://Verificar datos de inicio de sesión
        $Con = new Conn();
        $Con->conectar();
        
        $nuip = trim($nuip);
        $psw = trim($psw);
        $Login = new Login();
        $rta = $Login->getByFilter($Con, array('Usuario_Nuip' => $nuip, 'Password' => md5($psw)));
        if ($rta === FALSE) {
            die(utf8_encode('Clave o Contraseña Incorrecta'));
        }

        session_start();
        $_SESSION['usuario']    = $nuip;
        $Perfil = new LoginHasPerfil;
        $roles = $Perfil->getByFilter($Con,array('Login_Usuario_Nuip'=>$nuip));

        $_SESSION['Roles'] = array();
        foreach ($roles as $role) {
            array_push($_SESSION['Roles'], $role->getPerfilId());
        }

        $Con->cerrar();
        
        die('TRUE');

        break;
}
die();
?>
