<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																		 *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com							 *
*																		 *
*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
*																		 *
*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once ("include/localizacaoSistema.php");
require_once ("lib/App/Model/VivenciaProfissionalSituacao.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Gest�o de VPS" );
		$this->processoAp = "625";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $offset;

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Gest�o de VPS - Estat�sticas";

		$this->template = "listagemEstatistica";

		$this->addCabecalhos(array(
			"Situa��o",
			"N�mero de Jovens"
		));

		$registroSituacao = App_Model_VivenciaProfissionalSituacao::getInstance()->getValues();
		unset($registroSituacao[0]);

		$total_alunos = 0;

		foreach($registroSituacao as $situacao_vps => $situacao)
		{
			$sql     = "select COUNT(cod_aluno_vps) from pmieducar.aluno_vps where situacao_vps = $1";
			$options = array('params' => $situacao_vps, 'return_only' => 'first-field');
			$numero_alunos = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
			$total_alunos += $numero_alunos;

			$lista_busca = array(
				"<a href=\"educar_vps_aluno_lst.php?situacao_vps=$situacao_vps\" target=\"_blank\">{$situacao}</a>",
				"<a href=\"educar_vps_aluno_lst.php?situacao_vps=$situacao_vps\" target=\"_blank\">{$numero_alunos}</a>",
			);

			$this->addLinhas($lista_busca);
		}

		$lista_busca = array(
			"<a href=\"educar_vps_aluno_lst.php?\" target=\"_blank\">Total</a>",
			"<a href=\"educar_vps_aluno_lst.php?\" target=\"_blank\">{$total_alunos}</a>",
		);

		$this->addLinhas($lista_busca);

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos( array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "In�cio",
			"educar_vps_index.php"                => "Trilha Jovem Iguassu - VPS",
		));

		$this->enviaLocalizacao($localizacao->montar());

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
