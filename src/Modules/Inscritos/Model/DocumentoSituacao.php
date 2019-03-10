<?php

namespace iEducar\Modules\Inscritos\Model;

class DocumentoSituacao
{
    const NAO_ENTREGUE  = 0;
    const INVALIDO  = 1;
    const ENTREGUE  = 2;

    public static function getDescriptiveValues()
    {
        return [
            self::NAO_ENTREGUE => 'Não Entregue',
            self::INVALIDO => 'Inválido',
            self::ENTREGUE => 'Entregue'
        ];
    }

}
