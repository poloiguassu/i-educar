<?php

namespace iEducar\Modules\Inscritos\Model;

class AreaInteresse
{
    const EVENTOS = 1;
    const TEA = 2;
    const COMERCIO = 3;
    const HOSPEDAGEM = 4;

    public static function getDescriptiveValues()
    {
        return [
            self::EVENTOS       => 'Eventos',
            self::TEA           => 'Turismo e Atendimento',
            self::COMERCIO      => 'Comércio e Atendimento',
            self::HOSPEDAGEM    => 'Hospedagem'
        ];
    }

    public static function getDescriptiveValue($value)
    {
        return $self::getDescriptiveValues()[$value];
    }

}
