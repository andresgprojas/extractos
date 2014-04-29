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
        
        
        $pdf = new PDF_MC_Table('L');
        $pdf->Open();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetWidths(array(30, 25, 70, 62, 30, 30, 30));
        $pdf->Cell(0,10,'DATOS CLIENTE',1,1,'C');
        $pdf->SetFont('Arial', 'B', 12);
//        $pdf->SetDrawColor(0,0,100);//borde
//        $pdf->SetTextColor(0,0,100);//color letra
//        $pdf->SetFillColor(74,88,210);//fondo
        $pdf->Row(array('FECHA EXT', 'CÉDULA', 'NOMBRE', 'DIRECCIÓN', 'CIUDAD', 'PAIS', 'SALDO ANT'), TRUE);
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->Row(array($fechaFin, $rta[0]->getNuip(), "{$rta[0]->getPrimerNom()} {$rta[0]->getSegundoNom()} {$rta[0]->getPrimerApell()} {$rta[0]->getSegundoNom()}", $rta[0]->getDireccion(), $rta[0]->getCiudad(), $rta[0]->getPais(), '--'), TRUE);
        
        $pdf->Ln();
        $pdf->Ln();
        
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0,10,'MOVIMIENTOS DEL MES',1,1,'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetWidths(array(25, 30, 25, 35, 50, 40, 40, 32));
        
        $pdf->Row(array('FECHA', 'DOCUMENTO', 'NÚMERO', 'TRANSACCIÓN', 'DETALLE', 'VALOR DÉBITO', 'VALOR CRÉDITO', 'SALDO'),TRUE);

        $pdf->SetFont('Arial', '', 12);
        foreach ($datos as $fila) {
            $debito = number_format((float)substr($fila['Debito'], 0, -2).".00", 2);
            $credit = number_format((float)substr($fila['Credito'], 0, -2).".00", 2);
            $saldo  = number_format((float)substr($fila['Saldo'], 0, -3).".00", 2);
            $sym    = substr($fila['Saldo'], -1);
            
            $pdf->Row(array($fila['FechaDocumento'],$fila['Documento'],$fila['Numero'],$fila['Transaccion'],$fila['Detalle'],$debito,$credit,$saldo.$sym));
        }
        $pdf->Output();

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
