<?php

namespace App\Models;

class CentrosCivicos extends DBAbstractModel
{
    private static $instancia;
    private $id;
    private $idCentroCivico;

    public static function getInstancia()
    {
        if (!isset(self::$instancia)) {
            $miclase = __CLASS__;
            self::$instancia = new $miclase;
        }
        return self::$instancia;
    }

    public function get($id='')
    {
        $this->query = "SELECT * FROM centros_civicos WHERE id = :id";
        $this->parametros['id'] = $id;
        $this->get_results_from_query();
        if (count($this->rows) > 0){
            $this->rows[0]['instalaciones'] = Instalaciones::getInstancia()->get($id);
            return $this->rows[0];
        }
        return null;
    }
    

    public function getAll()
    {
        $this->query = "SELECT * FROM centros_civicos";
        $this->get_results_from_query();
        $newCentro = [];
        foreach ($this->rows as $centro){
            $centro['instalaciones'] = Instalaciones::getInstancia()->get($centro['id']);
            $newCentro[] = $centro;
        }
        $this->rows = $newCentro;
        return $this->rows ?? null;
    }
    

    public function set()
    {
    }
    public function edit()
    {
    }
    public function delete()
    {
    }

}
;