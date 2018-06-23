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
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';
require_once 'FormulaMedia/Validate/Formula.php';

/**
 * EditController class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class EditController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'FormulaMedia_Model_FormulaDataMapper';
  protected $_titulo            = 'Cadastro de f�rmula de c�lculo de m�dia';
  protected $_processoAp        = 948;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = TRUE;

  protected $_formMap = array(
    'instituicao' => array(
      'label'  => 'Institui��o',
      'help'   => ''
    ),
    'nome' => array(
      'label'  => 'Nome',
      'help'   => ''
    ),
    'formulaMedia' => array(
      'label'  => 'F�rmula de m�dia final',
      'help'   => 'A f�rmula de c�lculo.<br />
                   Vari�veis dispon�veis:<br />
                   &middot; En - Etapa n (de 1 a 10)<br />
                   &middot; Et - Total de etapas<br />
                   &middot; Se - Soma das notas das etapas<br />
                   &middot; Rc - Nota da recupera��o<br />
                   S�mbolos dispon�veis:<br />
                   &middot; (), +, /, *, x<br />
                   A vari�vel "Rc" est� dispon�vel apenas<br />
                   quando Tipo de f�rmula for "Recupera��o".'
    ),
    'tipoFormula' => array(
      'label'  => 'Tipo de f�rmula',
      'help'   => ''
    ),
    'substituiMenorNotaRc' => array(
      'label'  => 'Substitui menor nota por recuperação ',
      'help'   => 'Substitui menor nota (En) por nota de recuperação (Rc) em ordem descrescente.<br/>
                   Somente substitui quando Rc é maior que En.
                   Ex: E1 = 2, E2 = 3, E3 = 2, Rc = 5.
                   Na fórmula será considerado: E1 = 2, E2 = 3, E3 = 5, Rc = 5.'
    )
  );

  function _preRender(){
    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');
    Portabilis_View_Helper_Application::loadJavascript($this, '/modules/FormulaMedia/Assets/Javascripts/FormulaMedia.js');

    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "$nomeMenu f&oacute;rmula de m&eacute;dia"             
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
      40, 50, TRUE, FALSE, FALSE, $this->_getHelp('nome'));

    // F�rmula de m�dia
    $this->campoTexto('formulaMedia', $this->_getLabel('formulaMedia'),
      $this->getEntity()->formulaMedia, 40, 200, TRUE, FALSE, FALSE,
      $this->_getHelp('formulaMedia'));

    // F�rmula de recupera��o
    /*$this->campoTexto('formulaRecuperacao', $this->_getLabel('formulaRecuperacao'),
      $this->getEntity()->formulaRecuperacao, 40, 50, TRUE, FALSE, FALSE,
      $this->_getHelp('formulaRecuperacao'));*/

    // Tipo de f�rmula
    $tipoFormula = FormulaMedia_Model_TipoFormula::getInstance();
    $this->campoRadio('tipoFormula', $this->_getLabel('tipoFormula'),
      $tipoFormula->getEnums(), $this->getEntity()->get('tipoFormula'));
  }


  /**
   * Implementa uma rotina de criação ou atualização de registro padrão para
   * uma instância de CoreExt_Entity que use um campo identidade.
   * @return bool
   * @todo Atualizar todas as Exception de CoreExt_Validate, para poder ter
   *   certeza que o erro ocorrido foi gerado de alguma camada diferente, como
   *   a de conexão com o banco de dados.
   */
  protected function _save()
  {
    $data = array();

    foreach ($_POST as $key => $val) {
      if (array_key_exists($key, $this->_formMap)) {
        $data[$key] = $val;
      }
    }

    //fixup for checkbox nota geral
    if(!isset($data['substituiMenorNotaRc'])){
      $data['substituiMenorNotaRc'] = '0';
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
}
