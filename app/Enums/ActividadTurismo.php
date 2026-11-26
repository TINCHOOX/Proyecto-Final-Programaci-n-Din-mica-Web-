<?php

namespace App\Enums;

enum ActividadTurismo: string
{
    case Trekking = 'trekking';
    case Running = 'running';
    case Paseo = 'paseo';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Trekking => 'Trekking',
            self::Running => 'Running',
            self::Paseo => 'Paseo y parques',
        };
    }

    public function etiquetasIndicadores(): array
    {
        return match ($this) {
            self::Trekking => ['desnivel' => 'Desnivel', 'dificultad' => 'Dificultad', 'distancia' => 'Distancia'],
            self::Running => ['distancia' => 'Distancia', 'dificultad' => 'Dificultad', 'equipo' => 'Equipo necesario'],
            self::Paseo => ['bicisenda' => 'Bicisenda', 'parrilla' => 'Parrilla', 'observacion' => 'Observación caminando'],
        };
    }
}
