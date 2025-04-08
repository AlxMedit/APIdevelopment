<?php

namespace App\Controller;
use App\Models\Usuarios;
use App\Models\Inscripciones;

require_once __DIR__ . "/../Functions/decodificarToken.php";

class InscripcionesController
{
    private $requestMethod;
    private $idInscripcion;
    private $inscripcionesModel;
    public function __construct($requestMethod, $idInscripcion = '')
    {
        $this->requestMethod = $requestMethod;
        $this->idInscripcion = $idInscripcion;
        $this->inscripcionesModel = Inscripciones::getInstancia();
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case "POST":
                $response = $this->nuevaReserva();
                break;
            case "DELETE":
                $response = $this->cancelarReserva($this->idInscripcion);
                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function nuevaReserva()
    {
        $input = (array) json_decode(file_get_contents('php://input'), true);
        $plazasDisponibles = $this->inscripcionesModel->getPlazasDisponibles($input);
        $cantidadInscritos = $this->inscripcionesModel->getCantidadInscritos($input);
        if ($plazasDisponibles > $cantidadInscritos) {
            $idUsuario = decodificarToken();
            $nombreSolicitante = Usuarios::getInstancia()->get($idUsuario)['nombre'];
            $correoUsuario = Usuarios::getInstancia()->get($idUsuario)['email'];
            $respuesta = $this->inscripcionesModel->set($nombreSolicitante, $idUsuario, $correoUsuario, $input);
            if (!$respuesta) {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(['mensaje' => 'Inscripción creada correctamente']);
            return $response;

        } else {
            $response['status_code_header'] = 'HTTP/1.1 404 NOT FOUND';
            $response['body'] = 'No hay plazas disponibles';
            return $response;
        }
    }

    private function cancelarReserva($idInscripcion)
    {
        $idUsuario = decodificarToken();
        $emailUsuario = $this->inscripcionesModel->getIdUsuarioByInscripcion($idInscripcion);
        if (!$emailUsuario) {
            return [
                'status_code_header' => 'HTTP/1.1 404 Not Found',
                'body' => json_encode(['mensaje' => 'Inscripción no encontrada'])
            ];
        }
        $idUsuarioAccion = Usuarios::getInstancia()->obtenerIdPorEmail($emailUsuario);
        
        if ($idUsuario == $idUsuarioAccion) {
            $response = $this->inscripcionesModel->delete($idInscripcion);
            if (!$response) {
                return [
                    'status_code_header' => 'HTTP/1.1 404 Not Found',
                    'body' => json_encode(['mensaje' => 'No se pudo cancelar la inscripción'])
                ];
            }
            return [
                'status_code_header' => 'HTTP/1.1 200 OK',
                'body' => json_encode(['mensaje' => 'Inscripción cancelada correctamente'])
            ];
        } else {
            return [
                'status_code_header' => 'HTTP/1.1 403 Forbidden',
                'body' => json_encode(['mensaje' => 'No tienes permisos para cancelar esta inscripción'])
            ];
        }
    }

    public function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode(['mensaje' => 'Inscripción no encontrada o no válida']);
        return $response;
    }

    public function noProcesas()
    {
        $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
        $response['body'] = json_encode(['mensaje' => 'No se ha proporcionado ningún valor para actualizar']);
        return $response;
    }
}