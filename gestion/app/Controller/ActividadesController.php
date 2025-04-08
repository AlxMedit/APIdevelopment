<?php
namespace App\Controller;
use App\Models\Actividades;

class ActividadesController
{
    private $requestMethod;
    private $idCentroCivico;
    private $actividadesModel;

    public function __construct($requestMethod, $idCentroCivico = '')
    {
        $this->requestMethod = $requestMethod;
        $this->idCentroCivico = $idCentroCivico;
        $this->actividadesModel = Actividades::getInstancia();
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
                    $response = $this->obtenerTodas();
                }
                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    public function obtenerPorCentro($id, $nombre='')
    {
        $actividades = $this->actividadesModel->get($id, $nombre);
        if ($actividades) {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($actividades);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 404 NOT FOUND';
            $response['body'] = 'No se encontraron actividades para este centro.';
        }
        return $response;
    }

    public function obtenerTodas()
    {
        $actividades = $this->actividadesModel->get();
        if ($actividades) {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($actividades);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 404 NOT FOUND';
            $response['body'] = 'No se encontraron actividades.';
        }
        return $response;
    }
}