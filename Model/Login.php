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
class Login {
    //put your code here
    private $Usuario_Nuip;
    private $Password;
    private $Habilitado;
    private $Activo;
    private $UltimoLogin;
    const Tabla = "Login";
    
    public function getUsuarioNuip() {
        return $this->Usuario_Nuip;
    }

    public function setUsuarioNuip($UsuarioNuip) {
        $this->Usuario_Nuip = $UsuarioNuip;
    }

    public function getPassword() {
        return $this->Password;
    }

    public function setPassword($Password) {
        $this->Password = $Password;
    }

    public function getHabilitado() {
        return $this->Habilitado;
    }

    public function setHabilitado($Habilitado) {
        $this->Habilitado = $Habilitado;
    }

    public function getActivo() {
        return $this->Activo;
    }

    public function setActivo($Activo) {
        $this->Activo = $Activo;
    }

    public function getUltimoLogin() {
        return $this->UltimoLogin;
    }

    public function setUltimoLogin($UltimoLogin) {
        $this->UltimoLogin = $UltimoLogin;
    }

    
    public function setLogin($conn){
//        $conn = new Conn();
//        $conn->conectar();
        
        $str = "INSERT INTO " . $this::Tabla . " (Usuario_Nuip, Password)" .
                " VALUES ('{$this->getUsuarioNuip()}', " .
                "'{$this->getPassword()}')";
        $qry = mysqli_query($conn->getLink(), $str) or die (mysqli_error($conn->getLink()));
        
//        $conn->cerrar();
        
        return $qry;
        
    }
    
    public function updateByFilter($conn, $filtro=array()){
        
        if (count($filtro)>0){
            $filter = "WHERE ";
            foreach ($filtro as $key => $value) {
                $filter .= "{$key} = '{$value}' AND ";
            }
            $filter = substr($filter, 0, -4);
        }
        
        $set = "SET ";
        foreach ($this as $index => $value) {
            if ($value || $value === "0")
                $set .= "{$index} = '{$value}', ";
            
        }
        $set = substr($set, 0, -2);
        
        $str = "UPDATE ".$this::Tabla." {$set} {$filter}";
        $qry = mysqli_query($conn->getLink(), $str);
        if (mysqli_affected_rows($conn->getLink())>0){
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * 
     * @param type $filtro
     * @param type $activo
     * @return boolean|array
     */
    public function getByFilter($conn, $filtro=array(), $activo=TRUE){
//        $conn = new Conn();
//        $conn->conectar();
        
        $filter = "WHERE ";
        
        if (count($filtro)>0){
            foreach ($filtro as $key => $value) {
                $filter .= "{$key} = '{$value}' AND ";
            }
        }
        
        $filter .= ($activo === TRUE)?"Activo = 1":"(Activo = 1 OR Activo = 0)";
        
//        $filter = substr($filter, 0, -4);
        
        $str = "SELECT * FROM ".$this::Tabla." {$filter}";
        $qry = mysqli_query($conn->getLink(), $str) or die(mysql_error());
        
        if (mysqli_num_rows($qry)==0)
            return FALSE;
        
        $arrayPadre = array();
        while ($row = mysqli_fetch_assoc($qry)){
            $myClass = new Login();
            $myClass->setActivo($row['Activo']);
            $myClass->setHabilitado($row['Habilitado']);
            $myClass->setPassword($row['Password']);
            $myClass->setUltimoLogin($row['UltimoLogin']);
            $myClass->setUsuarioNuip($row['Usuario_Nuip']);
            
            array_push($arrayPadre, $myClass);
        }
        
//        $conn->cerrar();
        return $arrayPadre;
    }
}

?>
