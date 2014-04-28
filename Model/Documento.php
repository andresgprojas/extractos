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
class Documento {
    //put your code here
    private $id;
    private $Alias;
    private $Nombre;
    private $Activo;
    const Tabla = "Documento";

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getAlias() {
        return $this->Alias;
    }

    public function setAlias($Alias) {
        $this->Alias = $Alias;
    }

    public function getNombre() {
        return $this->Nombre;
    }

    public function setNombre($Nombre) {
        $this->Nombre = $Nombre;
    }

    public function getActivo() {
        return $this->Activo;
    }

    public function setActivo($Activo) {
        $this->Activo = $Activo;
    }

    
    public function setDocumento($conn){
        $str = "INSERT INTO " . $this::Tabla . " (Alias, Nombre)" .
                " VALUES ('{$this->getAlias()}', " .
                "'{$this->getNombre()}')";
        $qry = mysqli_query($conn->getLink(), $str) or die(mysqli_error($conn->getLink()));
        
        $return = ($qry == TRUE)?mysqli_insert_id($conn->getLink()):FALSE;
        
        return $return;
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
//        
        $filter = "WHERE ";
        
        if (count($filtro)>0){
            foreach ($filtro as $key => $value) {
                $filter .= "{$key} = '{$value}' AND ";
            }
        }
        
        $filter .= ($activo === TRUE)?"Activo = 1":"(Activo = 1 OR Activo = 0)";
        
        $str = "SELECT * FROM ".$this::Tabla." {$filter}";
        $qry = mysqli_query($conn->getLink(), $str) or die(mysqli_error());
        
        if (mysqli_num_rows($qry)==0)
            return FALSE;
        
        $arrayPadre = array();
        while ($row = mysqli_fetch_assoc($qry)){
            $myClass = new Documento();
            $myClass->setActivo($row['Activo']);
            $myClass->setAlias($row['Alias']);
            $myClass->setId($row['id']);
            $myClass->setNombre($row['Nombre']);
            
            array_push($arrayPadre, $myClass);
        }
        
//        $conn->cerrar();
        return $arrayPadre;
    }
}

?>
