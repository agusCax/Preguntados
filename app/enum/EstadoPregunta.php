<?php 

namespace app\Enum;

enum EstadoPregunta: string
{
    case Activa = 'activa';
    case Pendiente = 'pendiente';
    case Reportada = 'reportada';
    case Inactiva = 'inactiva';
}