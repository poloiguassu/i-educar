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

require_once 'Core/Controller/Page/EditController.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'ComponenteCurricular/Model/TipoBase.php';

/**
 * EditController class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class EditController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'ComponenteCurricular_Model_ComponenteDataMapper';
  protected $_titulo            = 'Cadastro de componente curricular';
  protected $_processoAp        = 946;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = FALSE;

  protected $_formMap = array(
    'instituicao' => array(
      'label'  => 'Institui��o',
      'help'   => '',
    ),
    'nome' => array(
      'label'  => 'Nome',
      'help'   => 'Nome por extenso do componente.',
    ),
    'abreviatura' => array(
      'label'  => 'Nome abreviado',
      'help'   => 'Nome abreviado do componente.',
      'entity' => 'abreviatura'
    ),
    'tipo_base' => array(
      'label'  => 'Base curricular',
      'help'   => '',
      'entity' => 'tipo_base'
    ),
    'area_conhecimento' => array(
      'label'  => '�rea conhecimento',
      'help'   => '',
      'entity' => 'area_conhecimento'
    ),
  );

  protected function _preRender()
  {
    parent::_preRender();    

    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "$nomeMenu componente curricular"             
    ));
    $this->enviaLocalizacao($localizacao->montar());    
  }  

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('id', $this->getEntity()->id);

    // Institui��o
    $instituicoes = App_Model_IedFinder::getInstituicoes();
    $this->campoLista('instituicao', $this->_getLabel('instituicao'),
      $instituicoes, $this->getEntity()->instituicao);

    // Nome
    $this->campoTexto('nome', $this->_getLabel('nome'), $this->getEntity()->nome,
      50, 200, TRUE, FALSE, FALSE, $this->_getHelp('nome'));

    // Abreviatura
    $this->campoTexto('abreviatura', $this->_getLabel('abreviatura'),
      $this->getEntity()->abreviatura, 50, 25, TRUE, FALSE,
      FALSE, $this->_getHelp('abreviatura'));

    // Tipo Base
    $tipoBase = ComponenteCurricular_Model_TipoBase::getInstance();
    $this->campoRadio('tipo_base', $this->_getLabel('tipo_base'),
      $tipoBase->getEnums(), $this->getEntity()->get('tipo_base'));

    // �rea de conhecimento
    $areas = $this->getDataMapper()->findAreaConhecimento();
    $areas = CoreExt_Entity::entityFilterAttr($areas, 'id', 'nome');
    $this->campoLista('area_conhecimento', $this->_getLabel('area_conhecimento'),
      $areas, $this->getEntity()->get('area_conhecimento'));
  }
}
