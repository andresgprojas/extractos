<?php

require_once '../cab.inc.php';
$Con = new Conn();
$Con->conectar();
$sesion = $Con->getSesion();

if ($sesion === FALSE)
    die('0');

$Usuario = new Usuario();
$Usuarios = $Usuario->getComplete($Con, $_GET['term']);

foreach ($Usuarios as $Usu) {
    $json[] = array('id' => $Usu->getNuip(), 'value' => trim($Usu->getPrimerNom() . " " . $Usu->getSegundoNom() . " " . utf8_encode($Usu->getPrimerApell()) . " " . $Usu->getSegundoApell()));
}

die(json_encode($json));
?>
