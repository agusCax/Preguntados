<?php 

namespace app\enum;

enum Rol: int
{
    case Jugador = 1;
    case Editor = 2;
    case Administrador = 3;
}