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
require_once 'ComponenteCurricular/Model/Componente.php';
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';

/**
 * AnoController class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class AnoController extends Core_Controller_Page_EditController
{
  protected $_dataMapper = 'ComponenteCurricular_Model_AnoEscolarDataMapper';
  protected $_titulo     = 'Configura��o de ano escolar';
  protected $_processoAp = 946;
  protected $_formMap    = array();

  /**
   * Array de inst�ncias ComponenteCurricular_Model_AnoEscolar.
   * @var array
   */
  protected $_entries = array();

  /**
   * Setter.
   * @param array $entries
   * @return Core_Controller_Page Prov� interface flu�da
   */
  public function setEntries(array $entries = array())
  {
    foreach ($entries as $entry) {
      $this->_entries[$entry->anoEscolar] = $entry;
    }
    return $this;
  }

  /**
   * Getter.
   * @return array
   */
  public function getEntries()
  {
    return $this->_entries;
  }

  /**
   * Getter.
   * @param int $id
   * @return ComponenteCurricular_Model_AnoEscolar
   */
  public function getEntry($id)
  {
    return $this->_entries[$id];
  }

  /**
   * Verifica se uma inst�ncia ComponenteCurricular_Model_AnoEscolar identificada
   * por $id existe.
   * @param int $id
   * @return bool
   */
  public function hasEntry($id)
  {
    if (isset($this->_entries[$id])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Retorna um array associativo de s�ries com c�digo de curso como chave.
   * @return array
   */
  protected function _getSeriesAgrupadasPorCurso()
  {
    $series = App_Model_IedFinder::getSeries($this->getEntity()->instituicao);
    $cursos = array();

    foreach ($series as $id => $nome) {
      $serie    = App_Model_IedFinder::getSerie($id);
      $codCurso = $serie['ref_cod_curso'];

      $cursos[$codCurso][$id] = $nome;
    }

    return $cursos;
  }

  /**
   * Retorna o nome de um curso.
   * @param int $id
   * @return string
   */
  protected function _getCursoNome($id)
  {
    return App_Model_IedFinder::getCurso($id);
  }

  /**
   * @see Core_Controller_Page_EditController#_preConstruct()
   */
  public function _preConstruct()
  {
    // Popula array de disciplinas selecionadas
    $this->setOptions(array('edit_success_params' => array('id' => $this->getRequest()->cid)));
    $this->setEntries($this->getDataMapper()->findAll(array(),
      array('componenteCurricular' => $this->getRequest()->cid)));

    // Configura a��o cancelar
    $this->setOptions(array('url_cancelar' => array(
      'path' => 'view', 'options' => array(
        'query' => array('id' => $this->getRequest()->cid)
      )
    )));
  }

  /**
   * @see Core_Controller_Page_EditController#_initNovo()
   */
  protected function _initNovo()
  {
    if (!isset($this->getRequest()->cid)) {
      $this->setEntity($this->getDataMapper()->createNewEntityInstance());
      return TRUE;
    }
    return FALSE;
  }

  /**
   * @see Core_Controller_Page_EditController#_initEditar()
   */
  protected function _initEditar()
  {
   try {
      $this->setEntity($this->getDataMapper()->createNewEntityInstance(array('componenteCurricular' => $this->getRequest()->cid)));
    } catch(Exception $e) {
      $this->mensagem = $e;
      return FALSE;
    }
    return TRUE;
  }

  protected function _preRender(){

    parent::_preRender();

    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

    $localizacao = new LocalizacaoSistema();

    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Editando anos escolares"             
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('cid', $this->getEntity()->get('componenteCurricular'));

    // Cursos
    $cursos = $this->_getSeriesAgrupadasPorCurso();

    // Cria a matriz de checkboxes
    foreach ($cursos as $key => $curso) {
      $this->campoRotulo($key, $this->_getCursoNome($key), '', FALSE, '', '');
      foreach ($curso as $c => $serie) {
        $this->campoCheck('ano_escolar['.$c.']', '', $this->hasEntry($c), $serie, FALSE);

        $valor = $this->hasEntry($c) ? $this->getEntry($c)->cargaHoraria : NULL;
        $this->campoTexto('carga_horaria['.$c.']', 'Carga hor�ria',
          $valor, 5, 5, FALSE, FALSE,
          FALSE);
      }
      $this->campoQuebra();
    }
  }

  /**
   * @see Core_Controller_Page_EditController#_save()
   */
  protected function _save()
  {
    $data = $insert = $delete = $intersect = array();

    // O id de componente_curricular ser� igual ao id da request
    if ($cid = $this->getRequest()->cid) {
      $data['componenteCurricular'] = $cid;
    }

    // Cria um array de Entity geradas pela requisi��o
    foreach ($this->getRequest()->ano_escolar as $key => $val) {
      $data['anoEscolar'] = $key;
      $data['cargaHoraria'] = $this->getRequest()->carga_horaria[$key];
      $insert[$key] = $this->getDataMapper()->createNewEntityInstance($data);
    }

    // Cria um array de chaves da Entity AnoEscolar para remover
    $entries = $this->getEntries();
    $delete = array_diff(array_keys($entries), array_keys($insert));

    // Cria um array de chaves da Entity AnoEscolar para evitar inserir novamente
    $intersect = array_intersect(array_keys($entries), array_keys($insert));

    // Registros a apagar
    foreach ($delete as $id)
    {
      $this->getDataMapper()->delete($entries[$id]);
    }

    // Registros a inserir
    foreach ($insert as $key => $entity) {
      // Se o registro j� existe, passa para o pr�ximo
      if (FALSE !== array_search($key, $intersect)) {
        $entity->markOld();
      }

      try {
        $this->getDataMapper()->save($entity);
      }
      catch (Exception $e) {
        $this->mensagem = 'Erro no preenchimento do formul�rio.';
        return FALSE;
      }
    }

    return TRUE;
  }
}
