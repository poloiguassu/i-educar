<?php

require_once 'CoreExt/Enum.php';

class App_Model_TurnoEstudo extends CoreExt_Enum
{
    const INVALIDO          = 0;
    const MANHA             = 1;
    const TARDE             = 2;
    const NOITE             = 3;
    const CEBEJA            = 4;

    protected $_data = [
        self::INVALIDO      => 'Egresso',
        self::MANHA         => 'Manhã',
        self::TARDE         => 'Tarde',
        self::NOITE         => 'Noite',
        self::CEBEJA        => 'Cebeja'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
