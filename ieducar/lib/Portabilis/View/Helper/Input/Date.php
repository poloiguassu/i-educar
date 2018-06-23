<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'Portabilis/View/Helper/Input/Core.php';
require_once 'Portabilis/Date/Utils.php';

/**
 * Portabilis_View_Helper_Input_Date class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Date extends Portabilis_View_Helper_Input_Core {

  public function date($attrName, $options = array()) {
    $defaultOptions = array('options' => array(), 'objectName' => '');

    $options             = $this->mergeOptions($options, $defaultOptions);
    $spacer              = ! empty($options['objectName']) && ! empty($attrName) ? '_' : '';

    $label = ! empty($attrName) ? $attrName : $options['objectName'];
    $label = str_replace('_id', '', $label);

    $defaultInputOptions = array('id'             => $options['objectName'] . $spacer . $attrName,
                                 'label'          => ucwords($label),
                                 'value'          => null,
                                 'required'       => true,
                                 'label_hint'     => '',
                                 'inline'         => false,
                                 'callback'       => false,
                                 'disabled'       => false,
                                 'size'           => 9, // opção suportada pelo elemento, mas não pelo helper ieducar
                                 'hint'       => 'dd/mm/aaaa',
                             );

    $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

    $isDbFormated = strrpos($inputOptions['value'], '-') > -1;

    if ($isDbFormated)
      $inputOptions['value'] = Portabilis_Date_Utils::pgSQLToBr($inputOptions['value']);

	Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, "$('#{$inputOptions['id']}').datepicker({
			dateFormat: 'dd/mm/yy',
			dayNames: ['Domingo','Segunda','Ter�a','Quarta','Quinta','Sexta','S�bado'],
			dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
			dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','S�b','Dom'],
			monthNames: ['Janeiro','Fevereiro','Mar�o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
			monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
			nextText: 'Pr�ximo',
			prevText: 'Anterior'
		})", true);

    call_user_func_array(array($this->viewInstance, 'campoData'), $inputOptions);
    $this->fixupPlaceholder($inputOptions);

    // implementado fixup via js, pois algumas op��es n�o est�o sendo verificadas pelo helper ieducar.
    $this->fixupOptions($inputOptions);
  }

  protected function fixupOptions($inputOptions) {
    $id           = $inputOptions['id'];

    $sizeFixup    = "\$input.attr('size', " . $inputOptions['size'] . ");";
    $disableFixup = $inputOptions['disabled'] ? "\$input.attr('disabled', 'disabled');" : '';

    $script = "
      var \$input = \$j('#" . $id . "');
      $sizeFixup
      $disableFixup
      \$input.change(function(){
        if (this.value == '') {
            return true;
        }

        var validateData = /^(((0[1-9]|[12][0-9]|3[01])([-.\/])(0[13578]|10|12)([-.\/])(\d{4}))|(([0][1-9]|[12][0-9]|30)([-.\/])(0[469]|11)([-.\/])(\d{4}))|((0[1-9]|1[0-9]|2[0-8])([-.\/])(02)([-.\/])(\d{4}))|((29)(\.|-|\/)(02)([-.\/])([02468][048]00))|((29)([-.\/])(02)([-.\/])([13579][26]00))|((29)([-.\/])(02)([-.\/])([0-9][0-9][0][48]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][2468][048]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][13579][26])))$/;

        if (!validateData.test(this.value)){
          messageUtils.error('Informe data válida.', this);
          this.value = '';
        }
      });
    ";

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $script, $afterReady = true);
  }
}
