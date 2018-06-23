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
 * Este programa � distribu��do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl��cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/ViewController.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'include/pmieducar/geral.inc.php';

/**
 * ViewController class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class ViewController extends Core_Controller_Page_ViewController
{
  protected $_dataMapper = 'ComponenteCurricular_Model_ComponenteDataMapper';
  protected $_titulo     = 'Detalhes de �rea de conhecimento';
  protected $_processoAp = 946;
  protected $_tableMap   = array(
    'Nome' => 'nome',
    'Abreviatura' => 'abreviatura',
    'Base curricular' => 'tipo_base',
    '�rea conhecimento' => 'area_conhecimento'
  );

  /**
   * Construtor.
   */
  public function __construct()
  {
    @session_start();
    $pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();
    $obj_permissao = new clsPermissoes();
    if($obj_permissao->permissao_cadastra(946, $pessoa_logada, 7))
      $this->addBotao('Configurar anos escolares', 'ano?cid=' . $_GET['id']);
  }

  protected function _preRender(){

    parent::_preRender();

    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

    $localizacao = new LocalizacaoSistema();

    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Detalhe do componente curricular"             
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }

  public function setUrlCancelar(CoreExt_Entity $entry)
  {
    $this->url_cancelar = 'intranet/educar_componente_curricular_lst.php';
  }
}
