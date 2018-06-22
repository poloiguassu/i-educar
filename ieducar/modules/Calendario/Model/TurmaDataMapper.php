<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Calendario
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'Calendario/Model/Turma.php';

/**
 * Calendario_Model_TurmaDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Calendario
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Calendario_Model_TurmaDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'Calendario_Model_Turma';
  protected $_tableName   = 'calendario_turma';
  protected $_tableSchema = 'modules';

  protected $_attributeMap = array(
    'calendarioAnoLetivo'   => 'calendario_ano_letivo_id',
    'ano'                   => 'ano',
    'mes'                   => 'mes',
    'dia'                   => 'dia',
    'turma'                 => 'turma_id'
  );

  protected $_primaryKey = array(
    'calendarioAnoLetivo'   => 'calendario_ano_letivo_id',
    'ano'                   => 'ano',
    'mes'                   => 'mes',
    'dia'                   => 'dia',
    'turma'                 => 'turma_id'
  );
}
