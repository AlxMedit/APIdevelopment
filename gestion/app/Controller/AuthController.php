<?php
namespace App\Controller;
use App\Models\Usuarios;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . "/../Functions/decodificarToken.php";

class AuthController
{
    private $requestMethod;
    private $usuarioId;
    private $usuario;
    private $password;
    private $email;
    public function __construct($requestMethod, $usuarioId = '', $email = '')
    {
        $this->requestMethod = $requestMethod;
        $this->usuarioId = $usuarioId;
        $this->email = $email;
        $this->usuario = Usuarios::getInstancia();
    }


    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'POST':
                $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $uri = explode('/', $uri);
                if ($uri[2] == 'login') {
                    $response = $this->loginFromRequest();
                } else {
                    $response = $this->refreshToken();
                }
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    public function refreshToken()
    {
        $idUsuario = decodificarToken();

        $key = KEY; // definido en el archivo .env
        $issuer_claim = "http://http://gestion.local";
        $audience_claim = "http://http://gestion.local";
        $issuedat_claim = time();
        $notbefore_claim = time();
        $expire_claim = $issuedat_claim + 3600; // tiempo de expiración del token
        $payload = [
            'iss' => $issuer_claim,
            'aud' => $audience_claim,
            'iat' => $issuedat_claim,
            'nbf' => $notbefore_claim,
            'exp' => $expire_claim,
            'data' => [
                'id' => $idUsuario,
            ]
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        $res = json_encode(
            array(
                "message" => "Token refrescado que tenia calor",
                "jwt" => $jwt,
                "expira" => $expire_claim
            )
        );
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $res;
        return $response;
    }

    public function loginFromRequest()
    {
        //leemos el flujo de entrada
        $input = json_decode(file_get_contents('php://input'), true);
        // determinamos si el cuerpo de la solicitud es un JSON válido
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['mensaje' => 'El cuerpo de la solicitud no es un JSON valido', "error" => json_last_error_msg()]);
            exit;
        }
        if (!isset($input['email']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['mensaje' => 'Faltan datos obligatorios']);
            exit;
        }
        $email = $input['email'];
        $password = $input['password'];
        $dataUser = $this->usuario->login($email, $password);
        $idUsuario = $this->usuario->obtenerIdPorEmail($email);

        if ($dataUser) {
            $key = KEY; // definido en el archivo .env
            $issuer_claim = "http://http://gestion.local";
            $audience_claim = "http://http://gestion.local";
            $issuedat_claim = time();
            $notbefore_claim = time();
            $expire_claim = $issuedat_claim + 3600; // tiempo de expiración del token
            $payload = [
                'iss' => $issuer_claim,
                'aud' => $audience_claim,
                'iat' => $issuedat_claim,
                'nbf' => $notbefore_claim,
                'exp' => $expire_claim,
                'data' => [
                    'id' => $idUsuario,
                ]
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');

            $res = json_encode(
                array(
                    "message" => "Inicio de sesion exitoso",
                    "jwt" => $jwt,
                    "expira" => $expire_claim
                )
            );
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = $res;
        } else {
            $response['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
            $response['body'] = null;
        }
        return $response;

    }

    public function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode(['mensaje' => 'Ruta no válida']);
        return $response;
    }

}