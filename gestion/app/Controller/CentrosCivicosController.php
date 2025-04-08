<?php
namespace App\Controller;
use App\Models\CentrosCivicos;

class CentrosCivicosController
{
    private $requestMethod;
    private $idCentroCivico;
    private $centroCivico;

    public function __construct($requestMethod, $idCentroCivico)
    {
        $this->requestMethod = $requestMethod;
        $this->idCentroCivico = $idCentroCivico;
        $this->centroCivico = CentrosCivicos::getInstancia();
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case "GET":
                if ($this->idCentroCivico) {
                    $response = $this->obtenerPorId($this->idCentroCivico);
                } else {
                    $response = $this->obtenerTodos();
                }
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    public function obtenerPorId($idCentroCivico)
    {
        $centro = $this->centroCivico->get($idCentroCivico);
        if ($centro !== null) {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($centro);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 404 NOT FOUND';
            $response['body'] = 'No se encontró el centro civico';
        }
        return $response;
    }
    public function obtenerTodos()
    {
        $centros = $this->centroCivico->getAll();
        if ($centros !== null) {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($centros);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 404 NOT FOUND';
            $response['body'] = 'No se encontraron centros civicos';
        }
        return $response;
    }
}