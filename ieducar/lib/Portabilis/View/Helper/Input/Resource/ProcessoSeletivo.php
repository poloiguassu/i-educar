<?php

require_once 'lib/Portabilis/View/Helper/Input/CoreSelect.php';

class Portabilis_View_Helper_Input_Resource_ProcessoSeletivo extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($options['resources'])) {
            $resources = new clsPmieducarProcessoSeletivo();
            $resources = $resources->lista();
            $resources = Portabilis_Array_Utils::setAsIdValue(
                $resources,
                'cod_selecao_processo',
                'ref_ano'
            );
        }

        return $this->insertOption(null, Portabilis_String_Utils::toLatin1('Processo Seletivo'), $resources);
    }

    public function processoSeletivo($options = [])
    {
        parent::select($options);
    }
}
