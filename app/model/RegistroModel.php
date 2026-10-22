<?php

namespace app\model;

class RegistroModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function registrar(array $datos)
    {
        $passwordHash = md5($datos['password']);

        $sql = "INSERT INTO usuarios (
                    nombre_usuario,
                    password,
                    nombre_1,
                    nombre_2,
                    apellido_1,
                    apellido_2,
                    anio_nacimiento,
                    sexo,
                    pais,
                    ciudad,
                    latitud,
                    longitud,
                    email,
                    token_validacion,
                    foto_perfil
                ) VALUES (
                    :nombre_usuario,
                    :password,
                    :nombre_1,
                    :nombre_2,
                    :apellido_1,
                    :apellido_2,
                    :anio_nacimiento,
                    :sexo,
                    :pais,
                    :ciudad,
                    :latitud,
                    :longitud,
                    :email,
                    :token_validacion,
                    :foto_perfil
                )";

        $parametros = [
            ':nombre_usuario' => $datos['nombre_usuario'],
            ':password' => $passwordHash,
            ':nombre_1' => $datos['nombre_1'],
            ':nombre_2' => $datos['nombre_2'],
            ':apellido_1' => $datos['apellido_1'],
            ':apellido_2' => $datos['apellido_2'],
            ':anio_nacimiento' => $datos['anio_nacimiento'],
            ':sexo' => $datos['sexo'],
            ':pais' => $datos['pais'],
            ':ciudad' => $datos['ciudad'],
            ':latitud' => $datos['latitud'],
            ':longitud' => $datos['longitud'],
            ':email' => $datos['email'],
            ':token_validacion' => $datos['token_validacion'],
            ':foto_perfil' => $datos['foto_perfil']
        ];

        return $this->database->execute($sql, $parametros);
    }

    public function activarUsuario(string $token){
        $sql = "UPDATE usuarios 
                SET estado = 1 
                WHERE token_validacion = :token";
        return $this->database->execute($sql, [':token' => $token]);
    }
}
