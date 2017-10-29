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
require_once ("lib/App/Model/EntrevistaSituacao.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} - Atribuir Entrevistas" );
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
	var $ref_cod_vps_entrevista;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_vps_entrevista_idioma;
	var $nm_entrevista;
	var $ano;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_escola;
	var $situacao_entrevista;
	var $empresa_id;

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Atribuir Entrevistas - Listagem";

		$filtros = $_GET;

		if(empty($filtros))
		{
			// HACK: Criar método para cada usuário definir seus filtros defaults
			$filtros = array(
				'busca'					=> 'S',
				'ref_cod_instituicao'	=> 1,
				'ref_cod_escola'		=> 2,
				'ref_cod_curso'			=> 2
			);
		}

		foreach($filtros AS $var => $val) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addCabecalhos( array(
			"Entrevista",
			"Empresa",
			"Ano",
			"Número de vagas",
			"Vagas Preenchidas",
			"Data Entrevista",
			"Função",
			"Jornada de Trabalho",
			"Situação"
		) );

		// Filtros de Foreign Keys
		$get_escola = true;
		$get_curso = true;
		$get_cabecalho = "lista_busca";
		include("include/pmieducar/educar_campo_lista.php");

		if(!$this->ano && $this->ref_cod_escola)
		{
			$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
			$ano_andamento = $obj_ano_letivo->lista($this->ref_cod_escola, null, null, null, 1, null, null, null, null, 1);
			$ano_andamento = reset($ano_andamento);

			if($ano_andamento)
				$this->ano = $ano_andamento['ano'];
		}

		$this->inputsHelper()->dynamic('anoLetivo');

		$filtrosSituacao = array(
			'0'		=> 'Todas',
			'1'		=> 'Aguardando entrevista',
			'2'		=> 'Nenhum jovem selecionado',
			'3'		=> 'Jovens Contratados',
		);

		$this->campoLista('situacao_entrevista', 'Situação Entrevista', $filtrosSituacao, $this->situacao_entrevista, '', FALSE, '', '', FALSE, FALSE);

		$helperOptions = array(
			'objectName'         => 'empresa',
			'hiddenInputOptions' => array('options' => array('value' => $this->empresa_id))
		);

		$options = array('label' => "Empresa", 'required' => false, 'size' => 30);

		$this->inputsHelper()->simpleSearchPessoaj('nome', $options, $helperOptions);

		$this->campoTexto("nm_entrevista", "Entrevista", $this->nm_entrevista, 30, 255, false);

		if($this->situacao_entrevista < App_Model_EntrevistaSituacao::EM_ANDAMENTO)
			$this->situacao_entrevista = null;

		$obj_entrevista = new clsPmieducarVPSEntrevista();
		$obj_entrevista->setOrderby( "nm_entrevista ASC" );

		$lista = $obj_entrevista->listaEntrevista($this->ref_cod_escola, $this->nm_entrevista, 1, null, null,
			$this->empresa_id, $this->ref_cod_curso, $this->ano, $this->situacao_entrevista
		);

		$total = $obj_entrevista->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$total_jovens = "";

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

				if(class_exists("clsPessoaFj"))
				{
					$obj_ref_idpes = new clsPessoaFj($registro["ref_idpes"]);
					$det_ref_idpes = $obj_ref_idpes->detalhe();
					$registro["ref_idpes"] = $det_ref_idpes["nome"];
				}
				else
				{
					$registro["ref_idpes"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPessoaFj\n-->";
				}

				if(class_exists("clsPmieducarVPSFuncao"))
				{
					$obj_ref_cod_vps_funcao = new clsPmieducarVPSFuncao($registro["ref_cod_vps_funcao"]);
					$det_ref_cod_vps_funcao = $obj_ref_cod_vps_funcao->detalhe();
					$registro["ref_cod_vps_funcao"] = $det_ref_cod_vps_funcao["nm_funcao"];
				}
				else
				{
					$registro["ref_cod_vps_funcao"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSFuncao\n-->";
				}

				if(class_exists("clsPmieducarVPSJornadaTrabalho"))
				{
					$obj_ref_cod_vps_jornada_trabalho = new clsPmieducarVPSJornadaTrabalho($registro["ref_cod_vps_jornada_trabalho"]);
					$det_ref_cod_vps_jornada_trabalho = $obj_ref_cod_vps_jornada_trabalho->detalhe();
					$registro["ref_cod_vps_jornada_trabalho"] = $det_ref_cod_vps_jornada_trabalho["nm_jornada_trabalho"];
				}
				else
				{
					$registro["ref_cod_vps_jornada_trabalho"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSJornadaTrabalho\n-->";
				}

				if($registro["data_entrevista"])
				{
					$registro["data_entrevista"] = Portabilis_Date_Utils::pgSQLToBr($registro["data_entrevista"]);

					if($registro["hora_entrevista"])
						$registro["data_entrevista"] = "{$registro["data_entrevista"]} às {$registro["hora_entrevista"]}";
				}

				if($registro["numero_vagas"] && $registro["numero_jovens"])
				{
					$numero_total = $registro["numero_vagas"] * $registro["numero_jovens"];
					$total_jovens = "{$registro["numero_vagas"]} vagas / $numero_total jovens";
				}

				$sql     = "select COUNT(ref_cod_aluno) from pmieducar.vps_aluno_entrevista where ref_cod_vps_entrevista = $1";
				$options = array('params' => $registro["cod_vps_entrevista"], 'return_only' => 'first-field');
				$numero_jovens    = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

				$registro["situacao_entrevista"] = App_Model_EntrevistaSituacao::getInstance()->getValue($registro["situacao_entrevista"]);

				$lista_busca = array(
					"<a href=\"educar_atribuir_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["nm_entrevista"]}</a>",
					"<a href=\"educar_atribuir_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["ref_idpes"]}</a>",
					"<a href=\"educar_atribuir_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["ano"]}</a>",
					"<a href=\"educar_atribuir_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$total_jovens}</a>",
					"<a href=\"educar_atribuir_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$numero_jovens}</a>",
					"<a href=\"educar_atribuir_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["data_entrevista"]}</a>",
					"<a href=\"educar_atribuir_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["ref_cod_vps_funcao"]}</a>",
					"<a href=\"educar_atribuir_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["ref_cod_vps_jornada_trabalho"]}</a>",
					"<a href=\"educar_atribuir_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}\">{$registro["situacao_entrevista"]}</a>"
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
			$_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			"educar_vps_index.php"                => "Trilha Jovem Iguassu - VPS",
			""                                    => "Atribuir Jovens a Entrevista"
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
