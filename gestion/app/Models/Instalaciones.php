<?php

namespace App\Models;

class Instalaciones extends DBAbstractModel
{
    private static $instancia;

    public static function getInstancia()
    {
        if (!isset(self::$instancia)) {
            $miclase = __CLASS__;
            self::$instancia = new $miclase;
        }
        return self::$instancia;
    }

    public function get($idCentroCivico = '', $nombre = '')
    {
        if ($idCentroCivico) {
            $this->query = "SELECT * FROM instalaciones WHERE centro_id = :idCentro";
            $this->parametros['idCentro'] = $idCentroCivico;
        } else {
            $this->query = "SELECT * FROM instalaciones WHERE 1";
        }

        if (!empty($nombre)) {
            $this->query .= " AND nombre LIKE :nombre";
            $this->parametros['nombre'] = "%$nombre%";
        }

        $this->get_results_from_query();
        return $this->rows ?? null;
    }

    public function delete() {}
    public function edit() {}
    public function set() {}
}
