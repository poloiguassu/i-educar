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

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} - Jovens em VPS" );
		$this->processoAp = "598";
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

	var $cod_vps_entrevista;
	var $ref_cod_exemplar_tipo;
	var $ref_cod_vps_entrevista;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_vps_entrevista_colecao;
	var $ref_cod_vps_entrevista_idioma;
	var $ref_cod_vps_entrevista_editora;
	var $nm_entrevista;
	var $sub_titulo;
	var $cdu;
	var $cutter;
	var $volume;
	var $num_edicao;
	var $ano;
	var $num_paginas;
	var $isbn;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_escola;

	var $situacao_vps;

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Atribuir Entrevistas - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addCabecalhos( array(
			"Nome",
			"Número de entrevistas",
			"Situação VPS",
			"Entrevista incio VPS",
			"Início VPS",
			"Conclusão VPS",
			"Inserção"
		) );

		// Filtros de Foreign Keys
		$get_escola = true;
		$get_curso = true;
		$get_cabecalho = "lista_busca";
		include("include/pmieducar/educar_campo_lista.php");

		$this->campoTexto( "nm_entrevista", "Entrevista", $this->nm_entrevista, 30, 255, false );

		if(!is_numeric($_GET['situacao_vps']))
			$this->situacao_vps = App_Model_VivenciaProfissionalSituacao::APTO;
		else
			$this->situacao_vps = $_GET['situacao_vps'];

		$filtrosSituacao = array(
			'0' => 'Evadido',
			'1' => 'Desistente',
			'2' => 'Desligado',
			'3' => 'Apto a VPS',
			'4' => 'Em cumprimento',
			'5' => 'Concluído (Avaliado)',
			'6' => 'Inserido',
			'7' => 'Jovens com Entrevista Agendada'
		);

		$this->campoLista('situacao_vps', 'Situação VPS', $filtrosSituacao, $this->situacao_vps, '', FALSE, '', '', FALSE, FALSE);

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		if($this->situacao_vps > App_Model_VivenciaProfissionalSituacao::INSERIDO)
		{
			$obj_entrevista = new clsPmieducarVPSAlunoEntrevista();
			$obj_entrevista->setLimite( $this->limite, $this->offset );

			$lista = $obj_entrevista->listaJovens();
		} else {
			$obj_entrevista = new clsPmieducarAlunoVPS();
			$obj_entrevista->setLimite($this->limite, $this->offset);

			$lista = $obj_entrevista->lista(null, null, $this->situacao_vps);
		}

		$total = $obj_entrevista->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$ref_cod_vps_entrevista = null;
				$registroVPS	= array();
				$situacaoVPS	= "";
				$inicioVPS		= "";
				$terminoVPS		= "";
				$insercaoVPS	= "";
				$nm_entrevista	= "";

				$ref_cod_aluno =  $registro["ref_cod_aluno"];

				$alunoVPS = new clsPmieducarAlunoVPS($ref_cod_aluno);

				if($alunoVPS && $alunoVPS->existe())
					$registroVPS = $alunoVPS->detalhe();

				if($registroVPS["situacao_vps"])
					$situacaoVPS = App_Model_VivenciaProfissionalSituacao::getInstance()->getValue($registroVPS["situacao_vps"]);

				if($registroVPS["ref_cod_vps_aluno_entrevista"])
				{
					$alunoEntrevista = new clsPmieducarVPSAlunoEntrevista($registroVPS["ref_cod_vps_aluno_entrevista"]);
					$registroAlunoEntrevista = $alunoEntrevista->detalhe();

					$ref_cod_vps_entrevista = $registroAlunoEntrevista["ref_cod_vps_entrevista"];

					if($ref_cod_vps_entrevista)
					{
						$entrevista = new clsPmieducarVPSEntrevista($ref_cod_vps_entrevista);
						$registroEntrevista = $entrevista->detalhe();
						$nm_entrevista = $registroEntrevista["nm_entrevista"];
					}

					if($registroAlunoEntrevista["inicio_vps"])
						$inicioVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["inicio_vps"]);

					if($registroAlunoEntrevista["termino_vps"])
						$terminoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["termino_vps"]);

					if($registroAlunoEntrevista["insercao_vps"])
						$insercaoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["insercao_vps"]);
				}

				$sql     = "SELECT COUNT(ref_cod_aluno) from pmieducar.vps_aluno_entrevista where ref_cod_aluno = $1";
				$options = array('params' => $ref_cod_aluno, 'return_only' => 'first-field');
				$numero_entrevistas    = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

				$lista_busca = array(
					"<a href=\"educar_vps_aluno_det.php?cod_aluno={$ref_cod_aluno}\">{$registro["nome"]}</a>",
					"<a href=\"educar_vps_aluno_det.php?cod_aluno={$ref_cod_aluno}\">{$numero_entrevistas}</a>",
					"<a href=\"educar_vps_aluno_det.php?cod_aluno={$ref_cod_aluno}\">{$situacaoVPS}</a>",
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$ref_cod_vps_entrevista}\" target=\"_blank\">{$nm_entrevista}</a>",
					"<a href=\"educar_vps_aluno_det.php?cod_aluno={$ref_cod_aluno}\">{$inicioVPS}</a>",
					"<a href=\"educar_vps_aluno_det.php?cod_aluno={$ref_cod_aluno}\">{$terminoVPS}</a>",
					"<a href=\"educar_vps_aluno_det.php?cod_aluno={$ref_cod_aluno}\">{$insercaoVPS}</a>",
				);

				$this->addLinhas($lista_busca);
			}
		}

		$this->addPaginador2( "educar_vps_aluno_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();

		if( $obj_permissoes->permissao_cadastra( 598, $this->pessoa_logada, 11 ) )
		{
			$this->acao = "go(\"educar_entrevista_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos( array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			"educar_vps_index.php"                => "Trilha Jovem Iguassu - VPS",
			""                                    => "Listagem de jovens em Entrevista"
		));

		$this->enviaLocalizacao($localizacao->montar());
	}
}
// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
