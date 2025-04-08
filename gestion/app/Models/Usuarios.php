<?php

namespace App\Models;

class Usuarios extends DBAbstractModel
{
    private static $instancia;
    protected $rowsAffected;

    public static function getInstancia()
    {
        if (!isset(self::$instancia)) {
            $miclase = __CLASS__;
            self::$instancia = new $miclase;
        }
        return self::$instancia;
    }

    /**
     * Summary of set
     * @param mixed $sh_data its the information
     * @return string verifying the creation
     */
    public function set($sh_data = array())
    {
        foreach ($sh_data as $campo => $valor) {
            $$campo = $valor;
        }
        $this->query = "INSERT INTO usuarios(nombre, email, password) VALUES (:nombre, :email, :password)";
        $this->parametros['nombre'] = $nombre;
        $this->parametros['email'] = $email;
        $this->parametros['password'] = $password;
        $this->get_results_from_query();
        $this->mensaje = "Usuario creado";
        return $this->mensaje;
    }

    public function get($id='')
    {
        $this->query = "SELECT * FROM usuarios WHERE id = :id";
        $this->parametros['id'] = $id;
        $this->get_results_from_query();
        return $this->rows[0] ?? null;
    }
    public function edit($id = '', $data = '')
    {
        // Inicializamos la consulta base
        $this->query = "UPDATE usuarios SET";
        $setValues = [];  // Para almacenar las columnas que se deben actualizar
    
        // Comprobamos si se pasa un nuevo nombre de usuario
        if (isset($data['nombre']) && !empty($data['nombre'])) {
            $setValues[] = "nombre = :nombre";
            $this->parametros['nombre'] = $data['nombre'];
        }
    
        // Comprobamos si se pasa un nuevo email
        if (isset($data['email']) && !empty($data['email'])) {
            $setValues[] = "email = :email";
            $this->parametros['email'] = $data['email'];
        }
    
        // Comprobamos si se pasa una nueva contraseña
        if (isset($data['password']) && !empty($data['password'])) {
            $setValues[] = "password = :password";
            $this->parametros['password'] = $data['password'];
        }
    
        // Si no hay ningún valor para actualizar, retornamos un mensaje de error
        if (empty($setValues)) {
            $this->mensaje = "No se ha proporcionado ningún valor para actualizar";
            return true;
        }
    
        // Unimos las partes de la consulta
        $this->query .= " " . implode(", ", $setValues);
        $this->query .= " WHERE id = :id";
        $this->parametros['id'] = $id;

        // Ejecutamos la consulta
        $this->get_results_from_query();

        // Comprobamos si se actualizó el usuario
        if ($this->affected_rows != 1) {
            $this->mensaje = "No se ha actualizado el usuario";
            return false;
        }
        $this->mensaje = "Usuario actualizado correctamente";
        return true;
    }
    
    public function delete($id = '')
    {
        $this->query = "DELETE FROM usuarios WHERE id = :id";
        $this->parametros['id'] = $id;
        $this->get_results_from_query();
    
        if ($this->affected_rows > 0) {
            $this->mensaje = "Usuario eliminado";
            return true;  // Cambiado de false a true
        } else {
            $this->mensaje = "Usuario no encontrado";
            return false;
        }
    }
    
    public function login($email, $password)
    {
        $this->query = "SELECT * FROM usuarios WHERE email = :email AND password = :password";
        $this->parametros['email'] = $email;
        $this->parametros['password'] = $password;
        $this->get_results_from_query();
        return $this->rows[0] ?? null;
    }

    public function loginToken($email)
    {
        $this->query = "SELECT * FROM usuarios WHERE email = :email";
        $this->parametros['email'] = $email;
        $this->get_results_from_query();
        return $this->rows[0] ?? null;
    }

    public function obtenerIdPorEmail($email){
        $this->query = "SELECT id FROM usuarios WHERE email = :email";
        $this->parametros['email'] = $email;
        $this->get_results_from_query();
        return $this->rows[0]['id'] ?? null;
    }

}