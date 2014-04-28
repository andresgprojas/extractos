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

    const _HOST = "localhost";
    const _USUARIO = "girograp_andres";
    const _PASSWORD = "Rubr05*2014";
    const _DATABASE = "girograp_rubros";
    private $link;
    
    
    
    public function conectar(){
        $link = @mysqli_connect($this::_HOST, $this::_USUARIO, $this::_PASSWORD, $this::_DATABASE) or die("Error al conectarse con el servidor");
        $this->setLink($link);
//        $this->setLink(@mysqli_select_db($this::_DATABASE) or die('Error al conectarse con la base de datos'));
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