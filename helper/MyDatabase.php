<?php

class MyDatabase
{
    private $conexion;

    public function __construct($host, $user, $pass, $db)
    {
        $this->conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass); //agrego el charset para provocar que la db guarde bien las tildes, la ñ y los emojis
    }

    public function queryOne(string $sql)
    {
        Log::info($sql);
        $resultado = $this->conexion->query($sql, PDO::FETCH_ASSOC);
        $fila = $resultado->fetch();
        return $fila === false ? null : $fila;
    }

    public function queryAll(string $sql)
    {
        Log::info($sql);
        $resultado = $this->conexion->query($sql, PDO::FETCH_ASSOC);
        return $resultado->fetchAll();
    }

    public function execute(string $sql, array $parameters = [])
    {
        Log::info($sql);
        $statement = $this->conexion->prepare($sql);

        if (!$statement->execute($parameters)) {
            return false;
        }

        return $statement->rowCount();
    }

    public function lastInsertId()
    {
        return $this->conexion->lastInsertId();
    }
}
