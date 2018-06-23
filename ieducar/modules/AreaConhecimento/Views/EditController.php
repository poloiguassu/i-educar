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
 * @package     AreaConhecimento
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'AreaConhecimento/Model/AreaDataMapper.php';
require_once '../intranet/include/clsBanco.inc.php';

/**
 * EditController class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     AreaConhecimento
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class EditController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'AreaConhecimento_Model_AreaDataMapper';
  protected $_titulo            = 'Cadastro de �rea de conhecimento';
  protected $_processoAp        = 945;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = TRUE;

  protected $_formMap = array(
    'instituicao' => array(
      'label' => 'Institui��o',
      'help'  => ''
    ),
    'nome' => array(
      'label'  => 'Nome',
      'help'   => 'O nome da �rea de conhecimento. Exemplo: "<em>Ci�ncias da natureza</em>".',
      'entity' => 'nome'
    ),
    'secao' => array(
      'label'  => 'Se��o',
      'help'   => 'A se��o que abrange a �rea de conhecimento. Exemplo: "<em>L�gico Matem�tico</em>".',
      'entity' => 'secao'
    ),
    'ordenamento_ac' => array(
      'label'  => 'Ordem de apresentação',
      'help'   => 'Ordem respeitada no lançamento de notas/faltas.',
      'entity' => 'ordenamento_ac'
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
         ""        => "$nomeMenu &aacute;rea de conhecimento"
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
      60, 200, TRUE, FALSE, FALSE, $this->_getHelp('nome'));

    // Se��o
    $this->campoTexto('secao', $this->_getLabel('secao'), $this->getEntity()->secao,
      50, 50, FALSE, FALSE, FALSE, $this->_getHelp('secao'));

    // Ordenamento
    $this->campoTexto('ordenamento_ac', $this->_getLabel('ordenamento_ac'), $this->getEntity()->ordenamento_ac==99999 ? null : $this->getEntity()->ordenamento_ac,
      10, 50, FALSE, FALSE, FALSE, $this->_getHelp('ordenamento_ac'));
  }

  protected function _save(){
    $data = array();

    foreach ($_POST as $key => $val) {
      if (array_key_exists($key, $this->_formMap)) {

        if($key == "ordenamento_ac"){

          if((trim($val) == "") || (is_null($val))) {
            $data[$key] = 99999;
            continue;
          }
        }

        $data[$key] = $val;
      }
    }


    // Verifica pela existência do field identity
    if (isset($this->getRequest()->id) && 0 < $this->getRequest()->id) {
      $entity = $this->setEntity($this->getDataMapper()->find($this->getRequest()->id));
    }

    if (isset($entity)) {
      $this->getEntity()->setOptions($data);
    }
    else {
      $this->setEntity($this->getDataMapper()->createNewEntityInstance($data));
    }

    try {
      $this->getDataMapper()->save($this->getEntity());
      return TRUE;
    }
    catch (Exception $e) {
      // TODO: ver @todo do docblock
      $this->mensagem = 'Erro no preenchimento do formulário. ';
      return FALSE;
    }
  }

  function Excluir()
  {
    if (isset($this->getRequest()->id)) {

      $sql = "SELECT id FROM modules.componente_curricular WHERE area_conhecimento_id = ". $this->getRequest()->id;
      $db = new clsBanco();
      $db->Consulta($sql);
      if($db->numLinhas()){
        $this->mensagem = 'Não é possível excluir esta área de conhecimento, pois a mesma possui vinculo com componentes curriculares.';
        return FALSE;
      }

      if ($this->getDataMapper()->delete($this->getRequest()->id)) {
        if (is_array($this->getOption('delete_success_params'))) {
          $params = http_build_query($this->getOption('delete_success_params'));
        }

        $this->redirect(
          $this->getDispatcher()->getControllerName() . '/' .
          $this->getOption('delete_success') .
          (isset($params) ? '?' . $params : '')
        );
      }
    }
    return FALSE;
  }

}
