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
class Rubros {
    //put your code here
    private $id;
    private $DocumentoId;
    private $Numero;
    private $FechaDocumento;
    private $Detalle;
    private $Debito;
    private $Credito;
    private $Saldo;
    private $UsuarioNuip;
    private $TransaccionId;
    const Tabla = "Rubros";

    public function getDebito() {
        return $this->Debito;
    }

    public function setDebito($Debito) {
        $this->Debito = $Debito;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getDocumentoId() {
        return $this->DocumentoId;
    }

    public function setDocumentoId($DocumentoId) {
        $this->DocumentoId = $DocumentoId;
    }

    public function getNumero() {
        return $this->Numero;
    }

    public function setNumero($Numero) {
        $this->Numero = $Numero;
    }

    public function getFechaDocumento() {
        return $this->FechaDocumento;
    }

    public function setFechaDocumento($FechaDocumento) {
        $this->FechaDocumento = $FechaDocumento;
    }

    public function getDetalle() {
        return $this->Detalle;
    }

    public function setDetalle($Detalle) {
        $this->Detalle = $Detalle;
    }

    public function getCredito() {
        return $this->Credito;
    }

    public function setCredito($Credito) {
        $this->Credito = $Credito;
    }

    public function getSaldo() {
        return $this->Saldo;
    }

    public function setSaldo($Saldo) {
        $this->Saldo = $Saldo;
    }

    public function getUsuarioNuip() {
        return $this->UsuarioNuip;
    }

    public function setUsuarioNuip($UsuarioNuip) {
        $this->UsuarioNuip = $UsuarioNuip;
    }

    public function getTransaccionId() {
        return $this->TransaccionId;
    }

    public function setTransaccionId($TransaccionId) {
        $this->TransaccionId = $TransaccionId;
    }
    
    public function setRubro($conn){
        $str = "INSERT INTO " . $this::Tabla . " (Documento_id, Numero, FechaDocumento, Detalle, Debito, Credito, Saldo, Usuario_Nuip, Transaccion_id)" .
                " VALUES ('{$this->getDocumentoId()}', " .
                "'{$this->getNumero()}', " .
                "'{$this->getFechaDocumento()}', " .
                "'{$this->getDetalle()}', " .
                "'{$this->getDebito()}', " .
                "'{$this->getCredito()}', " .
                "'{$this->getSaldo()}', " .
                "'{$this->getUsuarioNuip()}', " .
                "'{$this->getTransaccionId()}')";
        $qry = mysqli_query($conn->getLink(), $str) or die(mysqli_error($conn->getLink()));
        
        return $qry;
    }
    
    public function getByIdUserDates($conn, $idUsuario, $ini, $fin) {
        $str = "SELECT r.FechaDocumento, d.Nombre AS Documento, r.Numero, d2.Nombre AS Transaccion, r.Detalle, r.Debito, r.Credito, r.Saldo FROM Rubros r ".
                "INNER JOIN Usuario u ON r.Usuario_Nuip = u.Nuip ".
                "INNER JOIN Documento d ON r.Documento_id = d.id ".
                "INNER JOIN Documento d2 ON r.Transaccion_id = d2.id ".
                "WHERE u.Nuip = '{$idUsuario}' AND FechaDocumento BETWEEN '{$ini}' AND '{$fin}'";
        $qry = mysqli_query($conn->getLink(), $str) or die(mysqli_error($conn->getLink()));
        
        if (mysqli_num_rows($qry)==0)
            return FALSE;
        
        $arrayPadre = array();
        while ($row = mysqli_fetch_assoc($qry)){
            array_push($arrayPadre, $row);
        }
        return $arrayPadre;
    }

    /**
     * 
     * @param type $filtro
     * @return boolean|array
     */
    public function getByFilter($filtro=array()){
        $conn = new Conn();
        $conn->conectar();
        
        $filter = "";
        
        if (count($filtro)>0){
            $filter = "WHERE ";
            foreach ($filtro as $key => $value) {
                $filter .= "{$key} = '{$value}' AND ";
            }
          $filter = substr($filter, 0, -4);
        }
        
        
        $str = "SELECT * FROM ".$this::Tabla." {$filter}";
        $qry = mysql_query($str) or die(mysql_error());
        
        if (mysql_num_rows($qry)==0)
            return FALSE;
        
        $arrayPadre = array();
        while ($row = mysql_fetch_assoc($qry)){
            $myClass = new Rubros();
            $myClass->setCredito($row['Credito']);
            $myClass->setDetalle($row['Detalle']);
            $myClass->setDocumentoId($row['Documento_id']);
            $myClass->setFechaDocumento($row['FechaDocumento']);
            $myClass->setId($row['id']);
            $myClass->setNumero($row['Numero']);
            $myClass->setSaldo($row['Saldo']);
            $myClass->setTransaccionId($row['Transaccion_id']);
            $myClass->setUsuarioNuip($row['Usuario_Nuip']);
            
            array_push($arrayPadre, $myClass);
        }
        
        $conn->cerrar();
        return $arrayPadre;
    }
}

?>
