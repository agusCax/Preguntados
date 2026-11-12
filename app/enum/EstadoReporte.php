<?php 

namespace app\enum;

enum EstadoReporte: string
{
    case Pendiente = 'pendiente';
    case Aceptado = 'aceptado';
    case Rechazado = 'rechazado';
}