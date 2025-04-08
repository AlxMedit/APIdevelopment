<?php

namespace App\Controller;
use App\Models\Usuarios;

require_once __DIR__ . "/../Functions/decodificarToken.php";

class UsuariosController
{
    private $requestMethod;
    private $usuarioId;
    private $usuario;
    public function __construct($requestMethod, $usuarioId)
    {
        $this->requestMethod = $requestMethod;
        $this->usuarioId = $usuarioId;
        $this->usuario = Usuarios::getInstancia();
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'POST':
                $response = $this->registerFormRequest();
                break;
            case 'GET':
                $response = $this->getUsuario();
                break;
            case 'PUT':
                $response = $this->editarUsuario();
                break;
            case 'DELETE':
                $response = $this->deleteUsuario();
                break;

        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    public function registerFormRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), true);
        $existe = $this->usuario->obtenerIdPorEmail($input['email']);

        if ($existe) {
            $response['status_code_header'] = 'HTTP/1.1 409 Conflict';
            $response['body'] = json_encode(['error' => 'El correo ya está registrado']);
            return $response;
        }
        $respuesta = $this->usuario->set($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode($respuesta);
        return $response;
    }
    public function getUsuario()
    {
        $idUsuario = decodificarToken();
        $usuario = $this->usuario->get($idUsuario);
        if (!$usuario) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($usuario);
        return $response;
    }

    public function editarUsuario()
    {
        $idUsuario = decodificarToken();
        $usuario = $this->usuario->get($idUsuario);
        if (!$usuario) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), true);
        if (empty($input['nombre']) && empty($input['email']) && empty($input['password'])) {
            return $this->noProcesas();
        }

        $respuesta = $this->usuario->edit($idUsuario, $input);
        if (!$respuesta) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['mensaje' => 'El usuario ha sido actualizado correctamente']);
        return $response;
    }

    public function deleteUsuario()
    {
        $idUsuario = decodificarToken();
        $respuesta = $this->usuario->delete($idUsuario);
        if (!$respuesta) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['mensaje' => 'Usuario eliminado']);
        return $response;
    }

    public function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode(['mensaje' => 'Usuario no encontrado o no válido']);
        return $response;
    }

    public function noProcesas()
    {
        $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
        $response['body'] = json_encode(['mesnsaje' => 'No se ha proporcionado ningún valor para actualizar']);
        return $response;
    }

}