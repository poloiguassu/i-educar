<?php

require_once 'CoreExt/Enum.php';

class App_Model_VivenciaProfissionalSituacao extends CoreExt_Enum
{
    const EVADIDO               = 1;
    const DESISTENT             = 2;
    const DESLIGADO             = 3;
    const APTO                  = 4;
    const EM_CUMPRIMENTO        = 5;
    const CONCLUIDO             = 6;
    const INSERIDO              = 7;

    protected $_data = [
        ''                          => 'Informe a situação desta entrevista',
        self::EVADIDO               => 'Evadido',
        self::DESISTENTE            => 'Desistente',
        self::DESLIGADO             => 'Desligado',
        self::APTO                  => 'Apto a VPS',
        self::EM_CUMPRIMENTO        => 'Em cumprimento',
        self::CONCLUIDO             => 'Concluído (Avaliado)',
        self::INSERIDO              => 'Inserido'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
