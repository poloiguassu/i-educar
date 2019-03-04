<?php

require_once 'CoreExt/Enum.php';

class App_Model_SerieEstudo extends CoreExt_Enum
{
    const INVALIDO                  = 0;
    const SERIE_EF5                 = 1;
    const SERIE_EF6                 = 2;
    const SERIE_EF7                 = 3;
    const SERIE_EF8                 = 4;
    const SERIE_EF9                 = 5;
    const SERIE_EM1                 = 6;
    const SERIE_EM2                 = 7;
    const SERIE_EM3                 = 8;
    const SERIE_EGRESSO             = 9;
    const SERIE_EJA                 = 10;
    const SERIE_CEBEJA              = 11;

    protected $_data = [
        self::INVALIDO              => 'Série',
        self::SERIE_EF5             => '5ª série',
        self::SERIE_EF6             => '6ª série',
        self::SERIE_EF7             => '7ª série',
        self::SERIE_EF8             => '8ª série',
        self::SERIE_EF9             => '9ª série',
        self::SERIE_EM1             => '1º ano Ensino M�dio',
        self::SERIE_EM2             => '2º ano Ensino M�dio',
        self::SERIE_EM3             => '3º ano Ensino M�dio',
        self::SERIE_EGRESSO         => 'Egresso',
        self::SERIE_EJA             => 'EJA',
        self::SERIE_CEBEJA          => 'CEBEJA'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
