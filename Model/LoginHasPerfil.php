<?php
require_once '../cab.inc.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Perfil
 *
 * @author andresprojas
 */
class LoginHasPerfil {
    //put your code here
    private $LoginUsuarioNuip;
    private $PerfilId;
    const Tabla = "Login_has_Perfil";
    
    public function getLoginUsuarioNuip() {
        return $this->LoginUsuarioNuip;
    }

    public function setLoginUsuarioNuip($LoginUsuarioNuip) {
        $this->LoginUsuarioNuip = $LoginUsuarioNuip;
    }

    public function getPerfilId() {
        return $this->PerfilId;
    }

    public function setPerfilId($PerfilId) {
        $this->PerfilId = $PerfilId;
    }
    
    public function setLoginPerfil($conn){
        $str = "INSERT INTO ". $this::Tabla." (Login_Usuario_Nuip, Perfil_id) VALUES ('{$this->getLoginUsuarioNuip()}', '{$this->getPerfilId()}')";
        $qry = mysqli_query($conn->getLink(), $str);
        return $qry;
    }

    /**
     * 
     * @param type $filtro
     * @return boolean|array
     */
    public function getByFilter($conn, $filtro=array()){

        $filter = "";
        if (count($filtro)>0){
            $filter = "WHERE ";
            foreach ($filtro as $key => $value) {
                $filter .= "{$key} = '{$value}' AND ";
            }
        }
        
        $filter = substr($filter, 0, -4);
        
        $str = "SELECT * FROM ".$this::Tabla." {$filter}";
        $qry = mysqli_query($conn->getLink(),$str) or die(mysqli_error($conn->getLink()));
        
        if (mysqli_num_rows($qry)==0)
            return FALSE;
        
        $arrayPadre = array();
        while ($row = mysqli_fetch_assoc($qry)){
            $myClass = new LoginHasPerfil();
            $myClass->setLoginUsuarioNuip($row['Login_Usuario_Nuip']);
            $myClass->setPerfilId($row['Perfil_id']);
            
            array_push($arrayPadre, $myClass);
        }
        
        return $arrayPadre;
    }
}

?>
