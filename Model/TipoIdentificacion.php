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
class TipoIdentificacion {
    //put your code here
    private $id;
    private $Nombre;
    private $Activo;
    const Tabla = "Tipo_Identificacion";

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

        
    public function getByFilter($filtro=array(), $activo=TRUE){
        $conn = new Conn();
        $conn->conectar();
        
        $filter = "WHERE ";
        
        if (count($filtro)>0){
            foreach ($filtro as $key => $value) {
                $filter .= "{$key} = '{$value}' AND ";
            }
        }
        
        $filter .= ($activo === TRUE)?"Activo = 1":"(Activo = 1 OR Activo = 0)";
        
//        $filter = substr($filter, 0, -4);
        
        $str = "SELECT * FROM ".$this::Tabla." {$filter}";
        $qry = mysql_query($str) or die(mysql_error());
        
        if (mysql_num_rows($qry)==0)
            return FALSE;
        
        $arrayPadre = array();
        while ($row = mysql_fetch_assoc($qry)){
            $myClass = new TipoIdentificacion();
            $myClass->setActivo($row['Activo']);
            $myClass->setId($row['id']);
            $myClass->setNombre($row['Nombre']);
            
            array_push($arrayPadre, $myClass);
        }
        
        $conn->cerrar();
        return $arrayPadre;
    }
}

?>
