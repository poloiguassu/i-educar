<?php

require_once 'lib/Portabilis/View/Helper/Input/CoreSelect.php';

class Portabilis_View_Helper_Input_Resource_SelecaoDataEtapa extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($options['resources'])) {
            $resources = new clsPmieducarProcessoSeletivoDataEtapa();
            $resources = $resources->lista();

            foreach ($resources as $key => $resource) {
                $data = Portabilis_Date_Utils::pgSQLToBr($resource['data_etapa']);
                $resources[$key]['nome'] = "{$data} às {$resource['horario']}";
            }

            $resources = Portabilis_Array_Utils::setAsIdValue(
                $resources,
                'cod_etapa_data',
                'nome'
            );
        }

        return $this->insertOption(null, Portabilis_String_Utils::toLatin1('Datas da Etapa'), $resources);
    }

    public function selecaoDataEtapa($options = [])
    {
        parent::select($options);
    }
}
