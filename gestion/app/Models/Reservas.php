<?php

namespace App\Models;
class Reservas extends DBAbstractModel
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

    // Obtener todas las reservas del usuario
    public function getUserReserva($idUsuario = '')
    {
        $this->query = "SELECT * FROM reservas WHERE id_usuario = :id_usuario";
        $this->parametros['id_usuario'] = $idUsuario;
        $this->get_results_from_query();
        return $this->rows ?? null;
    }

    // Obtener una reserva específica
    public function get($idReserva = '')
    {
        if (!$idReserva) {
            $this->query = "SELECT * FROM reservas";
            $this->get_results_from_query();
            return $this->rows ?? null;
        } else {
            $this->query = "SELECT * FROM reservas WHERE id = :id_reserva";
            $this->parametros['id_reserva'] = $idReserva;
            $this->get_results_from_query();
            return $this->rows[0] ?? null; // Aseguramos que obtenemos solo un resultado
        }
    }

    public function delete($id = '')
    {
        $this->query = "DELETE FROM reservas WHERE id = :id";
        $this->parametros['id'] = $id;
        $this->get_results_from_query();
        if ($this->affected_rows > 0) {
            $this->mensaje = "Reserva eliminada";
            return true;
        } else {
            $this->mensaje = "Reserva no encontrada";
            return false;
        }
    }

    public function set($nombreSolicitante = '', $idUsuario = '', $correoUsuario = '', $data = '')
    {
        foreach ($data as $campo => $valor) {
            $$campo = $valor;
        }
        $this->query = "INSERT INTO reservas(nombre_solicitante, telefono, correo, instalacion_id, fecha_hora_inicio, fecha_hora_final, estado, id_usuario) 
                        VALUES (:nombre_solicitante, :telefono, :correo, :instalacion_id, :fecha_hora_inicio, :fecha_hora_final , 'PENDIENTE', :id_usuario)";
        $this->parametros['nombre_solicitante'] = $nombreSolicitante;
        $this->parametros['telefono'] = $telefono;
        $this->parametros['correo'] = $correoUsuario;
        $this->parametros['instalacion_id'] = $instalacion_id;
        $this->parametros['fecha_hora_inicio'] = $fecha_hora_inicio;
        $this->parametros['fecha_hora_final'] = $fecha_hora_final;
        $this->parametros['id_usuario'] = $idUsuario;
        $this->get_results_from_query();
        $this->mensaje = "Reserva creada";
        return $this->mensaje;  
    }

    public function edit(){}
}