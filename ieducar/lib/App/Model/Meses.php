<?php

require_once 'CoreExt/Enum.php';

class App_Model_MESES extends CoreExt_Enum
{
    const INVALIDO          = 0;
    const JANEIRO           = 1;
    const FEVEREIRO         = 2;
    const MARCO             = 3;
    const ABRIL             = 4;
    const MAIO              = 5;
    const JUNHO             = 6;
    const JULHO             = 7;
    const AGOSTO            = 8;
    const SETEMBRO          = 9;
    const OUTUBRO           = 10;
    const NOVEMBRO          = 11;
    const DEZEMBRO          = 12;

    protected $_data = [
        self::INVALIDO      => 'Selecione um mês',
        self::JANEIRO       => 'Janeiro',
        self::FEVEREIRO     => 'Fevereiro',
        self::MARCO         => 'Março',
        self::ABRIL         => 'Abril',
        self::MAIO          => 'Maio',
        self::JUNHO         => 'Junho',
        self::JULHO         => 'Julho',
        self::AGOSTO        => 'Agosto',
        self::SETEMBRO      => 'Setembro',
        self::OUTUBRO       => 'Outubro',
        self::NOVEMBRO      => 'Novembro',
        self::DEZEMBRO      => 'Dezembro'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
