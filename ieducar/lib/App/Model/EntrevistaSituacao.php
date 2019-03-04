<?php

require_once 'CoreExt/Enum.php';

class App_Model_EntrevistaSituacao extends CoreExt_Enum
{
    const EM_ANDAMENTO  = 1;
    const REPROVADO     = 2;
    const APROVADO      = 3;

    protected $_data = array(
        ''                  => 'Informe a situação desta entrevista',
        self::EM_ANDAMENTO  => 'Aguardando entrevista',
        self::REPROVADO     => 'Nenhum jovem selecionado',
        self::APROVADO      => 'Jovens Contratados'
    );

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
