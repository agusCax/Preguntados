<?php

class MyDatabase
{
    private $conexion;

    public function __construct($host, $user, $pass, $db)
    {
        $this->conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass); //agrego el charset para provocar que la db guarde bien las tildes, la ñ y los emojis
    }

    public function queryOne(string $sql, array $parameters = [])
    {
        Log::info($sql);
        $statement = $this->conexion->prepare($sql);
        $statement->execute($parameters);
        $fila = $statement->fetch(PDO::FETCH_ASSOC);
        return $fila === false ? null : $fila;
    }

    public function queryAll(string $sql, array $parameters = [])
    {
        Log::info($sql);
        $statement = $this->conexion->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
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
