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
    
    case 'Modificar Clave'://Editar Contrase�a
        $Con = new Conn();
        $Con->conectar();
        $sesion = $Con->getSesion();
        
        if ($sesion === FALSE)
            die('0');//salir
        
        $reg = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,15}$/";
        //hacer la misma validacion del JS
        if ($newPassword && $newPassword2 && $newPassword){
            if ($newPassword !== $newPassword2){
                die(utf8_encode("La nueva contrase�a y la confirmaci�n no coinciden"));
            }
            if(preg_match($reg, $newPassword)){
                $Login = new Login();
                $Login->setPassword(md5($newPassword));
                $rta = $Login->updateByFilter($Con, array('Usuario_Nuip'=>$sesion, 'Password'=>  md5($oldPassword)));

                if ($rta == TRUE){
                    printf(utf8_encode('Modificaci�n realizada con �xito'));
                }
                else{
                    printf(utf8_encode('La clave anterior no es la correcta (Recuerde que es OBLIGATORIO cambiar la contrase�a)'));
                }
            }
            else{
                printf(utf8_encode("La contrase�a no cumple con las condiciones (May�sculas, min�scilas y n�mero)"));
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
                printf(utf8_encode("Usuario creado Satisfactoriamente, recuerde que el password de acceso es el n�mero de identificaci�n"));
            }else{
                mysqli_rollback($Con->getLink());
                printf(utf8_encode("Hubo un error en la creaci�n del usuario"));
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
        
        $html = "<table border = '1'>
            <tr>
                <th>Extracto mes</th><th>C�dula</th><th>Nombre</th><th>Direcci�n</th><th>Ciudad</th><th>Pa�s</th><th>Saldo Anterior</th>
            </tr>
            <tr>
                <td>{$fechaFin}</td>
                <td>{$rta[0]->getNuip()}</td>
                <td>{$rta[0]->getPrimerNom()} {$rta[0]->getSegundoNom()} {$rta[0]->getPrimerApell()} {$rta[0]->getSegundoNom()}</td>
                <td>{$rta[0]->getDireccion()}</td>
                <td>{$rta[0]->getCiudad()}</td>
                <td>{$rta[0]->getPais()}</td>
                <td>--</td>
            </tr>
            </table>
            ";
        $html .= "<br><br><table border='1' style='width:100%'>";
        $html .= "<tr>
                    <th>Fecha Documento</th>
                    <th>Documento</th>
                    <th>N�mero</th>
                    <th>Transacci�n</th>
                    <th>Detalle</th>
                    <th>Valor D�bito</th>
                    <th>Valor Cr�dito</th>
                    <th>Saldo</th>
                </tr>";
        foreach ($datos as $fila) {

            $debito = number_format((float)substr($fila['Debito'], 0, -2).".00", 2);
            $credit = number_format((float)substr($fila['Credito'], 0, -2).".00", 2);
            $saldo  = number_format((float)substr($fila['Saldo'], 0, -3).".00", 2);
            $sym    = substr($fila['Saldo'], -1);
            
            $html .= 
                "<tr>
                    <td>{$fila['FechaDocumento']}</td>
                    <td>{$fila['Documento']}</td>
                    <td>{$fila['Numero']}</td>
                    <td>{$fila['Transaccion']}</td>
                    <td>{$fila['Detalle']}</td>
                    <td>{$debito}</td>
                    <td>{$credit}</td>
                    <td>{$saldo}{$sym}</td>
                </tr>";
            
        }
        $html .= "<table>";
        
        printf('%s', utf8_encode($html));
        
        break;
        
    default://Verificar datos de inicio de sesi�n
        $Con = new Conn();
        $Con->conectar();
        
        $nuip = trim($nuip);
        $psw = trim($psw);
        $Login = new Login();
        $rta = $Login->getByFilter($Con, array('Usuario_Nuip' => $nuip, 'Password' => md5($psw)));
        if ($rta === FALSE) {
            die(utf8_encode('Clave o Contrase�a Incorrecta'));
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
