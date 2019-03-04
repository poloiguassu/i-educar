<?php

namespace iEducar\Modules\Inscritos\Model;

class SerieEstudo
{
    const EFETIVO = 1;
    const TEMPORARIO = 2;
    const TERCEIRIZADO = 3;
    const CLT = 4;

    const SERIE_EF9         = 5;
    const SERIE_EM1         = 6;
    const SERIE_EM2         = 7;
    const SERIE_EM3         = 8;
    const SERIE_EJA         = 9;
    const SERIE_CEBEJA      = 10;

    public static function getDescriptiveValues()
    {
        return [
            self::SERIE_EF9         => '9ª série',
            self::SERIE_EM1         => '1º ano Ensino Médio',
            self::SERIE_EM2         => '2º ano Ensino Médio',
            self::SERIE_EM3         => '3º ano Ensino Médio',
            self::SERIE_EJA         => 'EJA',
            self::SERIE_CEBEJA      => 'CEBEJA'
        ];
    }

}
