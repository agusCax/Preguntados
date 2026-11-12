<?php

namespace app\enum;

enum MotivoReporte: string
{
    case RespuestaIncorrecta = 'La respuesta correcta es otra';
    case MalRedactada = 'Pregunta mal redactada';
    case Inapropiada = 'Contenido inapropiado';
    case Duplicada = 'Pregunta duplicada';
    case Otro = 'Otro';
}