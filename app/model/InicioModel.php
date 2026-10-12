<?php

namespace app\model;
class InicioModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getInicio()
    {
        $sql_usuarios = "SELECT usuarios.* FROM usuarios";

        $usuarios = $this->database->queryAll($sql_usuarios);

        foreach ($usuarios as &$u) {
            $u['usuarios'] = $u['usuario'];
        }

        return $usuarios;

    }
}


/*
public function getReservas()
{
    $sql_reservas = "SELECT reservas.*, lugares.sector, lugares.precio
                  FROM reservas
                  INNER JOIN lugares ON reservas.lugar_id = lugares.id
                  ORDER BY reservas.fecha_reserva DESC";

    $reservas = $this->database->queryAll($sql_reservas);

    foreach ($reservas as &$r) {
        $r['nombre_completo'] = $r['nombre'] . ' ' . $r['apellido'];
        $r['precio'] = number_format($r['precio'], 2, ',', '.');
        $r['fecha_reserva'] = date('d/m/Y H:i', strtotime($r['fecha_reserva']));
    }
    unset($r);

    return $reservas;
}

*/