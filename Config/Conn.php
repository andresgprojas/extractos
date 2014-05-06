<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Conn
 *
 * @author andresprojas
 */
class Conn {

    private $_HOST;
    private $_USUARIO;
    private $_PASSWORD;
    private $_DATABASE;
    private $link;
    
    public function __construct() {
        $Credenciales = simplexml_load_file('config.xml');
        $c = (array) $Credenciales->credencial;
        $this->_HOST    = $c['host'];
        $this->_USUARIO = $c['usuaro'];
        $this->_PASSWORD= $c['password'];
        $this->_DATABASE= $c['database'];
    }

    public function conectar(){
        $link = @mysqli_connect($this->_HOST, $this->_USUARIO, $this->_PASSWORD, $this->_DATABASE) or die(mysql_error());
        $this->setLink($link);
    }
    
    public function cerrar(){
        mysqli_close($this->getLink());
    }
    
    
    public function getLink() {
        return $this->link;
    }

    public function setLink($link) {
        $this->link = $link;
    }
    
    public function getSesion() {
        @session_start();
        if (count($_SESSION)>0)
            return $_SESSION['usuario'];
        else
            return FALSE;
    }
}

?>