<?php

namespace iEducar\Modules\Inscritos\Model;

class TurnoEstudo
{
    const MANHA = 1;
    const TARDE = 2;
    const NOITE = 3;

    public static function getDescriptiveValues()
    {
        return [
            self::MANHA => 'Manhã',
            self::TARDE => 'Tarde',
            self::NOITE => 'Noite',
        ];
    }

}
