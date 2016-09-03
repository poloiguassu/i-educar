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
 * @package     Docente
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.2.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/ListController.php';
require_once 'Docente/Model/LicenciaturaDataMapper.php';

/**
 * IndexController class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Docente
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.2.0
 * @version     @@package_version@@
 */
class IndexController extends Core_Controller_Page_ListController
{
  protected $_dataMapper = 'Docente_Model_LicenciaturaDataMapper';
  protected $_titulo     = 'Listagem de licenciaturas do servidor';
  protected $_processoAp = 635;

  protected $_tableMap = array(
    'Licenciatura'     => 'licenciatura',
    'Projeto'            => 'curso',
    'Ano de conclus�o' => 'anoConclusao',
    'IES'              => 'ies'
  );

  public function getEntries()
  {
    return $this->getDataMapper()->findAll(
      array(), array('servidor' => $this->getRequest()->servidor), array('anoConclusao' => 'ASC')
    );
  }

  public function setAcao()
  {
    $this->acao = sprintf(
      'go("edit?servidor=%d&instituicao=%d")',
      $this->getRequest()->servidor, $this->getRequest()->instituicao
    );

    $this->nome_acao = 'Novo';
  }

  public function Gerar()
  {
    $headers = $this->getTableMap();

    $this->addCabecalhos(array_keys($headers));

    $entries = $this->getEntries();

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite
      : 0;

    foreach ($entries as $entry) {
      $item = array();
      $data = $entry->toArray();
      $options = array('query' => array(
        'id'          => $entry->id,
        'servidor'    => $entry->servidor,
        'instituicao' => $this->getRequest()->instituicao
      ));

      foreach ($headers as $label => $attr) {
        $item[] = CoreExt_View_Helper_UrlHelper::l(
          $entry->$attr, 'view', $options
        );
      }

      $this->addLinhas($item);
    }

    $this->addPaginador2("", count($entries), $_GET, $this->nome, $this->limite);

    $this->setAcao();

    $this->acao_voltar = sprintf(
      'go("/intranet/educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d")',
      $this->getRequest()->servidor, $this->getRequest()->instituicao
    );

    $this->largura = "100%";
  }
}