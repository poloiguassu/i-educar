<?php
/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu�do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl�cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

/**
 * Portabilis_View_Helper_Input_SimpleSearchPessoa class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Resource_SimpleSearchPessoa extends Portabilis_View_Helper_Input_SimpleSearch {

  protected function resourceValue($id) {
    if ($id) {
      $sql     = "select nome from cadastro.pessoa where idpes = $1";
      $options = array('params' => $id, 'return_only' => 'first-field');
      $nome    = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

      return Portabilis_String_Utils::toLatin1($nome, array('transform' => true, 'escape' => false));
    }
  }

  public function simpleSearchPessoa($attrName, $options = array()) {
    $defaultOptions = array('objectName'    => 'pessoa',
                            'apiController' => 'Pessoa',
                            'apiResource'   => 'pessoa-search');

    $options        = $this->mergeOptions($options, $defaultOptions);

    parent::simpleSearch($options['objectName'], $attrName, $options);
  }

  protected function inputPlaceholder($inputOptions = null) {
    return 'Informe o nome, c�digo, CPF ou RG da pessoa';
  }
}
