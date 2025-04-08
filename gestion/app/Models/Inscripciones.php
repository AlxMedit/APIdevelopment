<?php

namespace App\Models;
class Inscripciones extends DBAbstractModel
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

    public function get(){}

    public function delete($id = ''){
        $this->query = "DELETE FROM inscripciones WHERE id = :id";
        $this->parametros['id'] = $id;
        $this->get_results_from_query();
        if ($this->affected_rows > 0){
            $this->mensaje = "Inscripción eliminada";
            return true;
        } else {
            $this->mensaje = "Inscripción no encontrada";
            return false;
        }
    }
    public function edit(){}
    public function set($nombreSolicitante = '', $idUsuario = '', $correoUsuario = '', $data = ''){
        foreach ($data as $campo => $valor) {
            $$campo = $valor;
        }
        $this->query = "INSERT INTO inscripciones(nombre_solicitante, telefono, correo, actividad_id, fecha_inscripcion, estado, id_usuario) VALUES (:nombre_solicitante, :telefono, :correo, :actividad_id, NOW(), 'PENDIENTE', :id_usuario)";
        $this->parametros['nombre_solicitante'] = $nombreSolicitante;
        $this->parametros['telefono'] = $telefono;
        $this->parametros['id_usuario'] = $idUsuario;
        $this->parametros['correo'] = $correoUsuario;
        $this->parametros['actividad_id'] = $actividad_id;
        $this->get_results_from_query();
        return true;
    }

    public function getPlazasDisponibles($data = ''){
        foreach( $data as $campo => $valor) {
            $$campo = $valor;
        }
        $this->query = "SELECT plazas FROM actividades WHERE id = :actividad_id";
        $this->parametros['actividad_id'] = $actividad_id;
        $this->get_results_from_query();
        return $this->rows[0]['plazas'] ?? null;
    }
    public function getCantidadInscritos($data = ''){
        foreach( $data as $campo => $valor) {
            $$campo = $valor;
        }
        $this->query = "SELECT COUNT(*) as inscritos FROM inscripciones WHERE actividad_id = :actividad_id";
        $this->parametros['actividad_id'] = $actividad_id;
        $this->get_results_from_query();
        return $this->rows[0]['inscritos'] ?? null;
    }

    public function getIdUsuarioByInscripcion($id){
        $this->query = "SELECT correo FROM inscripciones WHERE id = :id";
        $this->parametros['id'] = $id;
        $this->get_results_from_query();
        return $this->rows[0]['correo'] ?? null;
    }

    

    
}
