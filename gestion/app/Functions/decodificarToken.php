<?php
use FireBase\JWT\JWT;
use FireBase\JWT\Key;
function decodificarToken(){
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return false;
    }
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    $arr = explode(' ', $authHeader);
    $jwt = $arr[1];
    if (!$jwt){
        return false;
    }
    try {
        $tokenDecodificado = JWT::decode($jwt, new Key(KEY, 'HS256'));
        $idUsuario = $tokenDecodificado->data->id;
        return $idUsuario;
    } catch (\Exception $e) {
        return false;
    }
}