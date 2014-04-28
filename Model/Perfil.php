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
class Perfil {
    //put your code here
    private $id;
    private $Nombre;
    private $Activo;
    const Tabla = "Perfil";


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
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

    /**
     * 
     * @param type $filtro
     * @param type $activo
     * @return boolean|array
     */
    public function getByFilter($conn, $filtro=array(), $activo=TRUE){
        $filter = "WHERE ";
        
        if (count($filtro)>0){
            foreach ($filtro as $key => $value) {
                $filter .= "{$key} = '{$value}' AND ";
            }
        }
        
        $filter .= ($activo === TRUE)?"Activo = 1":"(Activo = 1 OR Activo = 0)";
        
        $str = "SELECT * FROM ".$this::Tabla." {$filter}";
        $qry = mysqli_query($conn->getLink(),$str) or die(mysqli_error($conn->getLink()));
        
        if (mysqli_num_rows($qry)==0)
            return FALSE;
        
        $arrayPadre = array();
        while ($row = mysqli_fetch_assoc($qry)){
            $myClass = new Perfil();
            $myClass->setActivo($row['Activo']);
            $myClass->setId($row['id']);
            $myClass->setNombre($row['Nombre']);
            
            array_push($arrayPadre, $myClass);
        }
        
        return $arrayPadre;
    }
}

?>
