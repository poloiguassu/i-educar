<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																		 *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com							 *
*																		 *
*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
*																		 *
*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
*	junto  com  este  programa. Se não, escreva para a Free Software	 *
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
		$this->SetTitulo( "{$this->_instituicao} Gestão de VPS" );
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

		$this->titulo = "Gestão de VPS - Estatísticas";

		$this->addCabecalhos(array(
			"Situação",
			"Número de Jovens"
		));

		$registroSituacao = App_Model_VivenciaProfissionalSituacao::getInstance()->getValues();

		$total_alunos = 0;

		foreach(array_slice($registroSituacao, 1) as $situacao_vps => $situacao)
		{
			$sql     = "select COUNT(cod_aluno_vps) from pmieducar.aluno_vps where situacao_vps = $1";
			$options = array('params' => $situacao_vps, 'return_only' => 'first-field');
			$numero_alunos = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
			$total_alunos += $numero_alunos;

			$lista_busca = array(
				"<a href=\"educar_vps_aluno_lst.php?\" target=\"_blank\">{$situacao}</a>",
				"<a href=\"educar_vps_aluno_lst.php?\" target=\"_blank\">{$numero_alunos}</a>",
			);

			$this->addLinhas($lista_busca);
		}

		$lista_busca = array(
			"<a href=\"educar_vps_aluno_lst.php?\" target=\"_blank\">Total</a>",
			"<a href=\"educar_vps_aluno_lst.php?\" target=\"_blank\">{$total_alunos}</a>",
		);

		$this->addLinhas($lista_busca);

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
