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
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/ViewController.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

/**
 * ViewController class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class ViewController extends Core_Controller_Page_ViewController
{
  protected $_dataMapper = 'RegraAvaliacao_Model_RegraDataMapper';
  protected $_titulo     = 'Detalhes da regra de avalia��o';
  protected $_processoAp = 947;
  protected $_tableMap   = array(
    'Nome' => 'nome',
    'Sistema de nota' => 'tipoNota',
    'Tabela de arredondamento' => 'tabelaArredondamento',
    'Progress�o' => 'tipoProgressao',
    'M�dia para promo��o' => 'media',
    'M�dia exame para promo��o' => 'mediaRecuperacao',
    'F�rmula de c�lculo de m�dia final' => 'formulaMedia',
    'F�rmula de c�lculo de recupera��o' => 'formulaRecuperacao',
    'Porcentagem presen�a' => 'porcentagemPresenca',
    'Parecer descritivo' => 'parecerDescritivo',
    'Tipo de presen�a' => 'tipoPresenca'
  );
  protected function _preRender(){

    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

    $localizacao = new LocalizacaoSistema();

    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Detalhe da regra de avalia&ccedil;&otilde;o"             
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }
}
