<?php

namespace iEducar\Modules\Inscritos\Model;

class AvaliacaoEtapa
{
    const NAO_ADEQUADO  = 1;
    const PARCIALMENTE_ADEQUADO  = 2;
    const ADEQUADO  = 3;

    public static function getDescriptiveValues()
    {
        return [
            self::NAO_ADEQUADO => 'Não Adequado',
            self::PARCIALMENTE_ADEQUADO => 'Parcialmente Adequado',
            self::ADEQUADO => 'Adequado'
        ];
    }

    public static function getDescriptiveValue($value)
    {
        return $self::getDescriptiveValues()[$value];
    }

}
