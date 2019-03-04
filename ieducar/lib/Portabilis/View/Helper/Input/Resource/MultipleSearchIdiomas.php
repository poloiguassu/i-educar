<?php

require_once 'lib/Portabilis/View/Helper/Input/MultipleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_MultipleSearchIdiomas extends Portabilis_View_Helper_Input_MultipleSearch {

    protected function getOptions($resources)
    {
        if (empty($resources)) {
            $resources = new clsPmieducarVPSIdioma();
            $resources = $resources->lista();
            $resources = Portabilis_Array_Utils::setAsIdValue(
                $resources, 'cod_vps_idioma', 'nm_idioma'
            );
        }
        return $this->insertOption(null, '', $resources);
    }

    public function multipleSearchIdiomas($attrName, $options = array())
    {
        $defaultOptions = array(
            'objectName'    => 'idiomas',
            'apiController' => 'Idioma',
            'apiResource'   => 'idioma-search'
        );
        $options = $this->mergeOptions($options, $defaultOptions);
        $options['options']['resources']
            = $this->getOptions($options['options']['resources']);
        //var_dump($options['options']['options']);
        $this->placeholderJs($options);
        parent::multipleSearch($options['objectName'], $attrName, $options);
    }
    
    protected function placeholderJs($options)
    {
        $optionsVarName = "multipleSearch" . Portabilis_String_Utils::camelize($options['objectName']) . "Options";
        $js = "if (typeof $optionsVarName == 'undefined') { $optionsVarName = {} };
        $optionsVarName.placeholder = safeUtf8Decode('Selecione os idiomas');";
        Portabilis_View_Helper_Application::embedJavascript(
            $this->viewInstance, $js, $afterReady = true
        );
    }
}
