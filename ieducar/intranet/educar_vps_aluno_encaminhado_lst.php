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
require_once ("lib/App/Model/EntrevistaResultado.php");
require_once ("lib/App/Model/VivenciaProfissionalSituacao.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} - Jovens Encaminhados" );
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
	var $data_entrevista_inicio;
	var $data_entrevista_final;

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Jovens Encaminhados - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addCabecalhos( array(
			"Nome",
			"Situação VPS",
			"Função",
			"Empresa",
			"Resultado Entrevista",
			"Data Entrevista",
			"Hora Entrevista",
		) );

		// Filtros de Foreign Keys
		$get_escola = true;
		$get_curso = true;
		$get_cabecalho = "lista_busca";
		include("include/pmieducar/educar_campo_lista.php");

		if(!is_numeric($_GET['situacao_vps']))
			$this->situacao_vps = null;
		else
			$this->situacao_vps = $_GET['situacao_vps'];

		$filtrosResultado = App_Model_EntrevistaResultado::getInstance()->getValues();

		$this->campoLista('resultado_entrevista', 'Resultado Entrevista', $filtrosResultado, $this->resultado_entrevista, '', FALSE, '', '', FALSE, FALSE);

		$options = array(
			'required'    => false,
			'label'       => 'Data Entrevista Início',
			'placeholder' => '',
			'value'       => $this->data_entrevista_inicio,
			'size'        => 7,
		);

		$this->inputsHelper()->date('data_entrevista_inicio', $options);

		$options = array(
			'required'    => false,
			'label'       => 'Data Entrevista Final',
			'placeholder' => '',
			'value'       => $this->data_entrevista_final,
			'size'        => 7,
		);

		$this->inputsHelper()->date('data_entrevista_final', $options);

		$obj_entrevista = new clsPmieducarVPSAlunoEntrevista();
		$obj_entrevista->setLimite($this->limite, $this->offset);
		$obj_entrevista->setOrderBy("nome ASC");

		if($this->data_entrevista_inicio)
			$this->data_entrevista_inicio = Portabilis_Date_Utils::brToPgSQL($this->data_entrevista_inicio);

		if($this->data_entrevista_final)
			$this->data_entrevista_final = Portabilis_Date_Utils::brToPgSQL($this->data_entrevista_final);

		$lista = $obj_entrevista->listaData($this->data_entrevista_inicio, $this->data_entrevista_final, $this->resultado_entrevista);

		$total = $obj_entrevista->_total;

		// monta a lista
		if(is_array($lista) && count($lista))
		{
			foreach ( $lista AS $registro )
			{
				$ref_cod_vps_entrevista = null;
				$registroVPS			= array();
				$situacaoVPS			= "";
				$data_entrevista		= "";
				$hora_entrevista		= "";
				$funcao					= "";
				$nm_empresa				= "";
				$nm_resultado			= "";

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

					$nm_resultado = App_Model_EntrevistaResultado::getInstance()->getValue($registroAlunoEntrevista["resultado_entrevista"]);

					if($ref_cod_vps_entrevista)
					{
						$entrevista = new clsPmieducarVPSEntrevista($ref_cod_vps_entrevista);
						$registroEntrevista = $entrevista->detalhe();

						if(class_exists("clsPmieducarVPSFuncao"))
						{
							$obj_ref_cod_vps_funcao = new clsPmieducarVPSFuncao($registroEntrevista["ref_cod_vps_funcao"]);
							$det_ref_cod_vps_funcao = $obj_ref_cod_vps_funcao->detalhe();
							$funcao = $det_ref_cod_vps_funcao["nm_funcao"];
						}
						else
						{
							$funcao = "Erro na geracao";
							echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSFuncao\n-->";
						}

						if(class_exists("clsPessoaFj"))
						{
							$obj_ref_idpes = new clsPessoaFj($registroEntrevista["ref_idpes"]);
							$det_ref_idpes = $obj_ref_idpes->detalhe();
							$nm_empresa = $det_ref_idpes["nome"];
						}
						else
						{
							$nm_empresa = "Erro na geracao";
							echo "<!--\nErro\nClasse nao existente: clsPessoaFj\n-->";
						}

						if($registroEntrevista["data_entrevista"])
							$data_entrevista = Portabilis_Date_Utils::pgSQLToBr($registroEntrevista["data_entrevista"]);

						if($registroEntrevista["hora_entrevista"])
							$hora_entrevista = $registroEntrevista["hora_entrevista"];
					}
				}

				$lista_busca = array(
					"<a href=\"educar_vps_aluno_det.php?cod_aluno={$ref_cod_aluno}\">{$registro["nome"]}</a>",
					"<a href=\"educar_vps_aluno_det.php?cod_aluno={$ref_cod_aluno}\">{$situacaoVPS}</a>",
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$ref_cod_vps_entrevista}\" target=\"_blank\">{$nm_empresa}</a>",
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$ref_cod_vps_entrevista}\" target=\"_blank\">{$funcao}</a>",
					"<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$ref_cod_vps_entrevista}\" target=\"_blank\">{$nm_resultado}</a>",
					"<a href=\"educar_vps_aluno_det.php?cod_aluno={$ref_cod_aluno}\">{$data_entrevista}</a>",
					"<a href=\"educar_vps_aluno_det.php?cod_aluno={$ref_cod_aluno}\">{$hora_entrevista}</a>",
				);

				$this->addLinhas($lista_busca);
			}
		}

		$obj_permissoes = new clsPermissoes();

		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos( array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			"educar_vps_index.php"                => "Trilha Jovem Iguassu - VPS",
			""                                    => "Listagem de Jovens Encaminhados"
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
