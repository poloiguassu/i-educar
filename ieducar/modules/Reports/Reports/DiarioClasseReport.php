<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu�do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl�cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author     Carlos M. <carlos-morais.santos@serpro.gov.br>
 * @category   i-Educar
 * @license    @@license@@
 * @package    Reports
 * @subpackage Modules
 * @since      Arquivo dispon�vel desde a vers�o 
 * @version    $Id$
 */

/**
 * DiarioClasseReport class.
 *
 * @author		Carlos M. <carlos-morais.santos@serpro.gov.br>
 * @category	i-Educar
 * @license 	@@license@@
 * @package 	Reports
 * @subpackage 	Modules
 * @since 		Classe dispon�vel desde a vers�o
 * @version 	@@package_version@@
 */

require_once "lib/Portabilis/Report/ReportCore.php";
require_once "App/Model/IedFinder.php";

class DiarioClasseReport extends Portabilis_Report_ReportCore {

	function templateName() {
		return "diario_classe";

	}
	
	function requiredArgs() {
		$this->addRequiredArg("cod_turma");
	}
}

?>
