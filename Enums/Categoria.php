<?php

namespace CrudGabit\Enums;

enum Categoria: string
{
    case SALUD = "Salud";
    case FITNESS = "Fitness";
    case EDUCACION = "Educación";
    case ALIMENTACION = "Alimentación";
    case PRODUCTIVIDAD = "Productividad";
    case SALUD_MENTAL = "Salud Mental";
    case FINANZAS = "Finanzas";
    case HOBBIES = "Hobbies";

    public static function toArray(): array
    {
        return array_column(self::cases(), "value");
    }
}