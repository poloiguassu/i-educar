<?php

require_once 'CoreExt/Enum.php';

class App_Model_EntrevistaResultado extends CoreExt_Enum
{
    const EM_ANDAMENTO          = 1;
    const ABANDONO              = 2;
    const REPROVADO             = 3;
    const APROVADO_ABANDONO     = 4;
    const APROVADO_ETAPA        = 5;
    const APROVADO_EXTRA        = 6;
    const APROVADO_ESTAGIO      = 7;
    const APROVADO_CONTRATADO   = 8;

    protected $_data = array(
        ''                          => 'Informe a situação desta entrevista',
        self::EM_ANDAMENTO          => 'Aguardando entrevista',
        self::ABANDONO              => 'Não compareceu',
        self::REPROVADO             => 'Não contratado',
        self::APROVADO_ABANDONO     => 'Aprovado mais abandonou',
        self::APROVADO_ETAPA        => 'Avançou próxima etapa',
        self::APROVADO_EXTRA        => 'Extra',
        self::APROVADO_ESTAGIO      => 'Estágio',
        self::APROVADO_CONTRATADO   => 'Aprovado'
    );

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
