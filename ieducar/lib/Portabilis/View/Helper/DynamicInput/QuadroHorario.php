<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';

/**
 * Portabilis_View_Helper_DynamicInput_QuadroHorario class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   Portabilis
 *
 * @since     Classe disponível desde a versão 1.1.0
 *
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_DynamicInput_QuadroHorario extends Portabilis_View_Helper_DynamicInput_CoreSelect
{

  // subscreve para não acrescentar '_id' no final
    protected function inputName()
    {
        return 'quadro_horario_horarios';
    }

    protected function inputOptions($options)
    {
        // não implementado load resources ainda, por enquanto busca somente com ajax.
        return $this->insertOption(null, 'Selecione uma aula', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Aula']];
    }

    public function quadroHorario($options = [])
    {
        parent::select($options);
    }
}
