<?php

namespace app\enum;

enum ResultadoPartida: string
{
    case Ganada = 'ganada';
    case Perdida = 'perdida';
    case Rendida = 'rendida';
}