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
class Usuario {

    //put your code here
//    private $TipoIdentificacionId;
    private $Nuip;
    private $PrimerNom;
    private $SegundoNom;
    private $PrimerApell;
    private $SegundoApell;
    private $Direccion;
    private $Ciudad;
    private $Pais;

    const Tabla = "Usuario";

//    public function getTipoIdentificacionId() {
//        return $this->TipoIdentificacionId;
//    }
//    public function setTipoIdentificacionId($TipoIdentificacionId) {
//        $this->TipoIdentificacionId = $TipoIdentificacionId;
//    }
    
    public function getDireccion() {
        return $this->Direccion;
    }

    public function setDireccion($Direccion) {
        $this->Direccion = $Direccion;
    }
    
    public function getNuip() {
        return $this->Nuip;
    }

    public function setNuip($Nuip) {
        $this->Nuip = $Nuip;
    }

    public function getPrimerNom() {
        return $this->PrimerNom;
    }

    public function setPrimerNom($PrimerNom) {
        $this->PrimerNom = $PrimerNom;
    }

    public function getSegundoNom() {
        return $this->SegundoNom;
    }

    public function setSegundoNom($SegundoNom) {
        $this->SegundoNom = $SegundoNom;
    }

    public function getPrimerApell() {
        return $this->PrimerApell;
    }

    public function setPrimerApell($PrimerApell) {
        $this->PrimerApell = $PrimerApell;
    }

    public function getSegundoApell() {
        return $this->SegundoApell;
    }

    public function setSegundoApell($SegundoApell) {
        $this->SegundoApell = $SegundoApell;
    }

    public function getCiudad() {
        return $this->Ciudad;
    }

    public function setCiudad($Ciudad) {
        $this->Ciudad = $Ciudad;
    }

    public function getPais() {
        return $this->Pais;
    }

    public function setPais($Pais) {
        $this->Pais = $Pais;
    }

    
    public function setUsuario($conn) {
//        $conn = new Conn();
//        $conn->conectar();

        $str = "INSERT INTO " . $this::Tabla . " (Nuip, PrimerNom, SegundoNom, PrimerApell, SegundoApell, Ciudad, Pais, Direccion)" .
                " VALUES ('{$this->getNuip()}', " .
                "'{$this->getPrimerNom()}', " .
                "'{$this->getSegundoNom()}', " .
                "'{$this->getPrimerApell()}', " .
                "'{$this->getSegundoApell()}', " .
                "'{$this->getCiudad()}', " .
                "'{$this->getPais()}', " .
                "'{$this->getDireccion()}')";
        $qry = mysqli_query($conn->getLink(), $str) or die(mysqli_error($conn->getLink()));

//        $conn->cerrar();

        return $qry;
    }

    /**
     * 
     * @param type $filtro
     * @return boolean|array
     */
    public function getByFilter($conn, $filtro = array()) {

        $filter = "";
        if (count($filtro) > 0) {
            $filter = "WHERE ";
            foreach ($filtro as $key => $value) {
                $filter .= "{$key} = '{$value}' AND ";
            }
            $filter = substr($filter, 0, -4);
        }

        $str = "SELECT * FROM " . $this::Tabla . " {$filter}";
        $qry = mysqli_query($conn->getLink(), $str) or die(mysql_error());

        if (mysqli_num_rows($qry) == 0)
            return FALSE;

        $arrayPadre = array();
        while ($row = mysqli_fetch_assoc($qry)) {
            $myClass = new Usuario();
            $myClass->setCiudad($row['Ciudad']);
            $myClass->setNuip($row['Nuip']);
            $myClass->setPais($row['Pais']);
            $myClass->setPrimerApell($row['PrimerApell']);
            $myClass->setDireccion($row['Direccion']);
            $myClass->setPrimerNom($row['PrimerNom']);
            $myClass->setSegundoApell($row['SegundoApell']);
            $myClass->setSegundoNom($row['SegundoNom']);
//            $myClass->setTipoIdentificacionId($row['TipoIdentificacion_id']);

            array_push($arrayPadre, $myClass);
        }

//        $conn->cerrar();
        return $arrayPadre;
    }
    
    public function getComplete($conn, $term) {
        $Login = new Login();
        
        $str = "SELECT ".$this::Tabla.".* FROM " . $this::Tabla . " "
                . "INNER JOIN ".$Login::Tabla." ON Usuario_Nuip = Nuip "
                . "WHERE Activo = 1 AND (PrimerNom LIKE '%{$term}%' OR SegundoNom LIKE '%{$term}%' OR PrimerApell LIKE '%{$term}%' OR SegundoApell LIKE '%{$term}%')";
        
        $qry = mysqli_query($conn->getLink(), $str) or die(mysql_error());

        if (mysqli_num_rows($qry) == 0)
            return FALSE;

        $arrayPadre = array();
        while ($row = mysqli_fetch_assoc($qry)) {
            $myClass = new Usuario();
            $myClass->setCiudad($row['Ciudad']);
            $myClass->setNuip($row['Nuip']);
            $myClass->setPais($row['Pais']);
            $myClass->setPrimerApell($row['PrimerApell']);
            $myClass->setDireccion($row['Direccion']);
            $myClass->setPrimerNom($row['PrimerNom']);
            $myClass->setSegundoApell($row['SegundoApell']);
            $myClass->setSegundoNom($row['SegundoNom']);

            array_push($arrayPadre, $myClass);
        }

        return $arrayPadre;
    }

}

?>
