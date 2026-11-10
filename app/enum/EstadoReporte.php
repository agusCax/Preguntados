<?php 

namespace app\Enum;

enum EstadoReporte: string
{
    case Pendiente = 'pendiente';
    case Aceptado = 'aceptado';
    case Rechazado = 'rechazado';
}