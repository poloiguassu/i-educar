<?php

require_once 'lib/Portabilis/View/Helper/Input/CoreSelect.php';

class Portabilis_View_Helper_Input_Resource_EscolaMunicipio extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($options['resources'])) {

            // HACK: Necessária criar método para passar munícipio da
            // escola em que está gerando o cadastro.
            $sql = "SELECT
                        idescola, nome
                    FROM
                        public.escola_municipio
                    WHERE
                        ref_idmun = '4031'";

            $resources = Portabilis_Utils_Database::fetchPreparedQuery($sql);

            $resources = Portabilis_Array_Utils::setAsIdValue(
                $resources,
                'idescola',
                'nome'
            );
        }

        return $this->insertOption(null, Portabilis_String_Utils::toLatin1('Selecione uma escola'), $resources);
    }

    public function escolaMunicipio($options = [])
    {
        parent::select($options);
    }
}
