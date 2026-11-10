<?php 

namespace app\Enum;

enum Rol: int
{
    case Jugador = 1;
    case Editor = 2;
    case Administrador = 3;
}