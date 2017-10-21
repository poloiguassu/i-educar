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

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} - Resultados Entrevistas" );
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

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Atribuir Entrevistas - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addCabecalhos( array(
			"Entrevista",
			"Ano",
			"N�mero de vagas",
			"Situa��o",
			"N�mero de contratados",
			"Data Entrevista",
			"Hor�rio",
			"Escola"
		) );

		// Filtros de Foreign Keys
		$get_escola = true;
		$get_curso = true;
		$get_cabecalho = "lista_busca";
		include("include/pmieducar/educar_campo_lista.php");

		$this->campoTexto( "nm_entrevista", "Entrevista", $this->nm_entrevista, 30, 255, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_entrevista = new clsPmieducarVPSEntrevista();
		$obj_entrevista->setOrderby( "nm_entrevista ASC" );
		$obj_entrevista->setLimite( $this->limite, $this->offset );

		$lista = $obj_entrevista->listaEntrevista($this->ref_cod_escola, $this->nm_entrevista, 1, null, null, null);

		$total = $obj_entrevista->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarEscola" ) )
				{
					$obj_ref_cod_escola = new clsPmieducarEscola();
					$det_ref_cod_escola = array_shift($obj_ref_cod_escola->lista($registro["ref_cod_escola"]));
					$registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];
				}
				else
				{
					$registro["ref_cod_escola"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
				}

				if($registro["data_entrevista"])
					$registro["data_entrevista"] = Portabilis_Date_Utils::pgSQLToBr($registro["data_entrevista"]);

				$sql     = "select COUNT(ref_cod_aluno) from pmieducar.vps_aluno_entrevista where ref_cod_vps_entrevista = $1 AND resultado_entrevista = 4";
				$options = array('params' => $registro["cod_vps_entrevista"], 'return_only' => 'first-field');
				$numero_jovens    = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

				$opcoesSituacao = array(
					'' => 'Informe a situa��o desta entrevista',
					0  => 'Aguardando entrevista',
					1  => 'Nenhum jovem selecionado',
					2  => 'Entrevista Cancelada',
					3  => 'Jovens Contratados'
				);

				$lista_busca = array(
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["nm_entrevista"]}</a>",
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["ano"]}</a>",
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["numero_vagas"]}</a>",
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$opcoesSituacao[$registro["situacao_entrevista"]]}</a>",
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$numero_jovens}</a>",
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["data_entrevista"]}</a>",
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["hora_entrevista"]}</a>",
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["ref_cod_escola"]}</a>"
				);

				$this->addLinhas($lista_busca);
			}
		}

		$this->addPaginador2( "educar_atribuir_entrevista_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();

		if( $obj_permissoes->permissao_cadastra( 598, $this->pessoa_logada, 11 ) )
		{
			$this->acao = "go(\"educar_entrevista_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos( array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "In�cio",
			"educar_vps_index.php"                => "Trilha Jovem Iguassu - VPS",
			""                                    => "Listagem de entrevistas"
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
