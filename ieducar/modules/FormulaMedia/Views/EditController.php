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
    )
  );

  function _preRender(){
    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

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
      $this->getEntity()->formulaMedia, 40, 50, TRUE, FALSE, FALSE,
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
}