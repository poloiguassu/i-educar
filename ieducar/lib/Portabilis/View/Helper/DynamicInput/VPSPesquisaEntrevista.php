<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/Core.php';

class Portabilis_View_Helper_DynamicInput_VPSPesquisaEntrevista extends Portabilis_View_Helper_DynamicInput_Core
{
    protected function getEntrevistaId($id = null)
    {
        if (! $id && $this->viewInstance->ref_cod_vps_entrevista) {
            $id = $this->viewInstance->ref_cod_vps_entrevista;
        }

        return $id;
    }

    protected function getEntrevista($id)
    {
        if (!$id) {
            $id = $this->getEntrevistaId($id);
        }

        $entrevista = empty($id) ? null : App_Model_IedFinder::getVPSEntrevista($id->ref_cod_curso, $id->ano, $id);

        return $entrevista;
    }

    public function vpsPesquisaEntrevista($options = [])
    {
        $defaultOptions = ['id' => null, 'options' => [], 'hiddenInputOptions' => []];
        $options        = $this->mergeOptions($options, $defaultOptions);

        $inputHint  = '<img border=\'0\' onclick=\'pesquisaObra();\' id=\'lupa_pesquisa_obra\' name=\'lupa_pesquisa_obra\' src=\'imagens/lupa.png\' />';

        $entrevista       = $this->getEntrevista($options['id']);
        $tituloEntrevista = $entrevista ? $entrevista['titulo'] : '';

        $defaultInputOptions = ['id'        => 'titulo_entrevista',
                                    'label'      => 'Entrevista',
                                    'value'      => $tituloEntrevista,
                                    'size'       => '30',
                                    'max_length' => '255',
                                    'required'   => true,
                                    'expressao'  => false,
                                    'inline'     => false,
                                    'label_hint' => '',
                                    'input_hint' => $inputHint,
                                    'callback'   => '',
                                    'event'      => 'onKeyUp',
                                    'disabled'   => true];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);
        call_user_func_array([$this->viewInstance, 'campoTexto'], $inputOptions);

        // hidden input
        $defaultHiddenInputOptions = ['id' => 'ref_cod_vps_entrevista',
                    'value' => $this->getEntrevistaId($options['id'])];

        $hiddenInputOptions = $this->mergeOptions($options['hiddenInputOptions'], $defaultHiddenInputOptions);
        call_user_func_array([$this->viewInstance, 'campoOculto'], $hiddenInputOptions);

        $this->viewInstance->campoOculto('cod_curso', '');

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, '
			var resetObra = function() {
				$("#ref_cod_vps_entrevista").val("");
				$("#titulo_entrevista").val("");
			}
			$("#ref_cod_curso").change(resetObra);', true);

        Portabilis_View_Helper_Application::embedJavascript(
            $this->viewInstance,
            '
			function pesquisaObra() {
				var additionalFields = getElementFor("biblioteca");
				var exceptFields     = getElementFor("titulo_entrevista");

				if (validatesPresenseOfValueInRequiredFields(additionalFields, exceptFields)) {
					var bibliotecaId = getElementFor("biblioteca").val();

					pesquisa_valores_popless("educar_pesquisa_obra_lst.php?campo1=ref_cod_vps_entrevista&campo2=titulo_entrevista&campo3="+bibliotecaId)
				}
			}'
        );
    }
}
