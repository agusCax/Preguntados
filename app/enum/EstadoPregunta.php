<?php 

namespace app\enum;

enum EstadoPregunta: string
{
    case Activa = 'activa';
    case Pendiente = 'pendiente';
    case Reportada = 'reportada';
    case Inactiva = 'inactiva';
}