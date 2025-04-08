<?php

namespace App\Controller;
use App\Models\Reservas;
use App\Models\Usuarios;

require_once __DIR__ . "/../Functions/decodificarToken.php";

class ReservasController
{
    private $requestMethod;
    private $reservaId;
    private $reserva;
    
    public function __construct($requestMethod, $reservaId = '')
    {
        $this->requestMethod = $requestMethod;
        $this->reservaId = $reservaId;
        $this->reserva = Reservas::getInstancia();
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'POST':
                $response = $this->nuevaReserva();
                break;
            case 'GET':
                $response = $this->mostrarReservas();
                break;
            case 'DELETE':
                $response = $this->cancelarReserva($this->reservaId);
                break;
            default:
                $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                $response['body'] = ['mensaje' => 'Método no válido'];
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    public function nuevaReserva()
    {
        $input = (array) json_decode(file_get_contents('php://input'), true);
        $idUsuario = decodificarToken();
        $nombreSolicitante = Usuarios::getInstancia()->get($idUsuario)['nombre'];
        $correoUsuario = Usuarios::getInstancia()->get($idUsuario)['email'];
        $respuesta = $this->reserva->set($nombreSolicitante, $idUsuario, $correoUsuario, $input);
        if (!$respuesta) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['mensaje' => 'Reserva creada correctamente']);
        return $response;
    }

    public function mostrarReservas()
    {
        $idUsuario = decodificarToken(); // Obtener el id del usuario desde el token
        $respuesta = $this->reserva->getUserReserva($idUsuario); // Obtener reservas del usuario

        if (!$respuesta) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['mensaje' => 'Reservas encontradas', 'reservas' => $respuesta]);
        return $response;
    }

    public function cancelarReserva($reservaId)
    {
        // Verificamos si la reserva existe
        $respuesta = $this->reserva->get($reservaId);
        if (!$respuesta) {
            return $this->notFoundResponse();
        }

        $whoDid = $respuesta['id_usuario'];
        $usuarioPeticion = decodificarToken();
        if ($whoDid != $usuarioPeticion) {
            $response['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
            $response['body'] = json_encode(['mensaje' => 'No estás autorizado para cancelar la reserva']);
            return $response;
        }

        // Si el usuario es el propietario de la reserva, proceder a eliminarla
        $this->reserva->delete($reservaId);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['mensaje' => 'Reserva eliminada']);
        return $response;
    }

    public function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode(['mensaje' => 'Reserva no encontrada o no válida']);
        return $response;
    }
}

?>
