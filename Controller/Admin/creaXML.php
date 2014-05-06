<?php

extract($_POST);
$xml = new DomDocument('1.0', 'UTF-8');
$root = $xml->createElement('credenciales');
$root = $xml->appendChild($root);

$cred = $xml->createElement('credencial');
$cred = $root->appendChild($cred);

$user = $xml->createElement('usuario', $user);
$user = $cred->appendChild($user);

$pass = $xml->createElement('password', $password);
$pass = $cred->appendChild($pass);

$host = $xml->createElement('host', $Host);
$host = $cred->appendChild($host);

$data = $xml->createElement('database', $data);
$data = $cred->appendChild($data);

$xml->formatOutput = true;



$strings_xml = $xml->saveXML();

$xml->save('../../Config/config.xml');



echo 'Se han creado satisfactoriamente las credenciales de acceso';
?>
