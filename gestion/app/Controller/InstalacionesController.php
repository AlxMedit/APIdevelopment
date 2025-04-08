<?php

namespace App\Controller;

use App\Models\Instalaciones;

class InstalacionesController
{
    private $requestMethod;
    private $idCentroCivico;
    private $instalacionesModel;

    public function __construct($requestMethod, $idCentroCivico = '')
    {
        $this->requestMethod = $requestMethod;
        $this->idCentroCivico = $idCentroCivico;
        $this->instalacionesModel = Instalaciones::getInstancia();
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case "GET":
                $input = (array) json_decode(file_get_contents('php://input'), true);
                $nombre = $input['nombre'] ?? '';

                if ($this->idCentroCivico) {
                    $response = $this->obtenerPorCentro($this->idCentroCivico, $nombre);
                } else {
                    $response = $this->obtenerTodas($nombre);
                }
                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function decodificar_json(string $json, bool $asociativo = false)
    {
        return json_decode($json, $asociativo);
    }

    public function obtenerPorCentro($id, $nombre = '')
    {
        $instalaciones = $this->instalacionesModel->get($id, $nombre);
        if ($instalaciones) {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($instalaciones);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 404 NOT FOUND';
            $response['body'] = 'No se encontraron instalaciones para este centro.';
        }
        return $response;
    }

    public function obtenerTodas($nombre = '')
    {
        $instalaciones = $this->instalacionesModel->get('', $nombre);
        if ($instalaciones) {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($instalaciones);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 404 NOT FOUND';
            $response['body'] = 'No se encontraron instalaciones.';
        }
        return $response;
    }
}
