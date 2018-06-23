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
 * @package     Core
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'clsConfigItajai.inc.php';


/**
 * clsConfigItajaiTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Core
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.0.1
 * @version     @@package_version@@
 */
class ClsConfigItajai extends UnitBaseTest
{
  protected $config = NULL;

  protected function setUp()
  {
    $this->config = new clsConfig();
  }

  public function testConfigInstituicao()
  {
    $this->assertEquals('Trilha Jovem Iguassu - ', $this->config->_instituicao);
  }

  public function testArrayConfigHasEmailsAdministradores()
  {
    $this->assertTrue((bool) count($this->config->arrayConfig['ArrStrEmailsAdministradores']));
  }

  public function testArrayCheckEmailAdministradores()
  {
    $this->assertEquals('seu.email@example.com',
      $this->config->arrayConfig['ArrStrEmailsAdministradores'][0]);
  }

  public function testArrayConfigDirectoryTemplates()
  {
    $this->assertEquals('templates/', $this->config->arrayConfig['strDirTemplates']);
  }

  public function testArrayConfigIntSegundosQuerySql()
  {
    $this->assertEquals(3, $this->config->arrayConfig['intSegundosQuerySQL']);
  }

  public function testArrayConfigIntSegundosPagina()
  {
    $this->assertEquals(5, $this->config->arrayConfig['intSegundosProcessaPagina']);
  }
}