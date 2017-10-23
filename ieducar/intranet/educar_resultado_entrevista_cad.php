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
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/Utils/Float.php';
require_once 'lib/App/Model/EntrevistaSituacao.php';
require_once 'lib/App/Model/EntrevistaResultado.php';
require_once 'lib/App/Model/VivenciaProfissionalSituacao.php';

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Atribuir Entrevistas");
		$this->processoAp = "598";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $cod_vps_entrevista;
	var $ref_cod_exemplar_tipo;
	var $ref_cod_vps_entrevista;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_vps_funcao;
	var $ref_cod_vps_jornada_trabalho;
	var $ref_cod_tipo_contratacao;
	var $empresa_id;
	var $nm_entrevista;
	var $descricao;
	var $data_entrevista;
	var $hora_entrevista;
	var $ano;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $salario;
	var $numero_vagas;
	var $numero_jovens;
	var $total_jovens;
	var $resultado_jovens;

	var $ref_cod_instituicao;
	var $ref_cod_escola;

	var $checked;

	var $ref_cod_vps_responsavel_entrevista;
	var $principal;
	var $incluir_responsavel;
	var $excluir_responsavel;

	var $funcao;
	var $jornada_trabalho;
	var $responsavel;

	protected function setSelectionFields()
	{

	}

	function Inicializar()
	{
		$retorno = "Novo";

		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_vps_entrevista = $_GET["cod_vps_entrevista"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11,  "educar_resultado_entrevista_lst.php");

		if(is_numeric($this->cod_vps_entrevista))
		{
			$obj = new clsPmieducarVPSEntrevista($this->cod_vps_entrevista);
			$registro  = $obj->detalhe();

			if($registro)
			{
				foreach($registro AS $campo => $val)	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
				$obj_det = $obj_escola->detalhe();

				$this->ref_cod_instituicao = $obj_det["ref_cod_instituicao"];
				$this->ref_cod_escola = $obj_det["cod_escola"];

				$obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
				$obj_det = $obj_curso->detalhe();

				$this->ref_cod_curso = $obj_det["cod_curso"];

				$this->empresa_id = $this->ref_idpes;

				$retorno = "Editar";
			}
		}

		$this->url_cancelar = ($retorno == "Editar") ? "educar_entrevista_det.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}" : "educar_entrevista_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		$nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			"educar_vps_index.php"                => "Trilha Jovem - VPS",
			""                                    => "{$nomeMenu} entrevista"
		));

		$this->enviaLocalizacao($localizacao->montar());

		return $retorno;
	}

	function Gerar()
	{
		if($_POST)
		{
			foreach($_POST AS $campo => $val)
				$this->$campo = ($this->$campo) ? $this->$campo : $val;
		}
		if(is_numeric($this->funcao))
		{
			$this->ref_cod_vps_funcao = $this->funcao;
		}
		if(is_numeric($this->jornada_trabalho))
		{
			$this->ref_cod_vps_jornada_trabalho = $this->jornada_trabalho;
		}
		if(is_numeric($this->responsavel))
		{
			$this->ref_cod_vps_responsavel_entrevista = $this->responsavel;
		}

		$this->total_jovens = $this->numero_vagas * $this->numero_jovens;

		// foreign keys
		$desabilitado = true;
		$get_escola = true;
		$escola_obrigatorio = true;
		$instituicao_obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");

		$anoVisivel = false;

		// primary keys
		$this->campoOculto("cod_vps_entrevista", $this->cod_vps_entrevista);
		$this->campoOculto("total_jovens", $this->total_jovens);
		$this->campoOculto("funcao", "");
		$this->campoOculto("jornada_trabalho", "");
		$this->campoOculto("responsavel", "");

		if ( $this->ref_cod_escola )
		{
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		}

		if ($anoVisivel)
		{
			$helperOptions = array('situacoes' => array('em_andamento'));
			$this->inputsHelper()->dynamic('anoLetivo', array('disabled' => $bloqueia), $helperOptions);
			if($bloqueia)
				$this->inputsHelper()->hidden('ano_hidden', array('value' => $this->ano));
		}

		$options = array('label' => "Empresa", 'required' => true, 'size' => 31, 'disabled' => true);

		$helperOptions = array(
			'objectName'         => 'empresa',
			'hiddenInputOptions' => array('options' => array('value' => $this->empresa_id))
		);

		$this->inputsHelper()->simpleSearchPessoaj('nome', $options, $helperOptions);

		// Coleï¿½ï¿½o
		$opcoes = array("" => "Selecione");

		if(class_exists("clsPmieducarVPSFuncao"))
		{
			$objTemp = new clsPmieducarVPSFuncao();
			$lista = $objTemp->lista();

			if (is_array($lista) && count($lista))
			{
				foreach ($lista as $registro)
				{
					$opcoes["{$registro['cod_vps_funcao']}"] = "{$registro['nm_funcao']}";
				}
			}
		} else {
			echo "<!--\nErro\nClasse clsPmieducarVPSFuncao nao encontrada\n-->";
			$opcoes = array("" => "Erro na geracao");
		}

		$this->campoLista("ref_cod_vps_funcao", "Função/Cargo", $opcoes, $this->ref_cod_vps_funcao, "", false, "", "", true, false);

		// Idioma
		$opcoes = array("" => "Selecione");
		if(class_exists("clsPmieducarVPSJornadaTrabalho"))
		{
			$objTemp = new clsPmieducarVPSJornadaTrabalho();
			$lista = $objTemp->lista();

			if (is_array($lista) && count($lista))
			{
				foreach ($lista as $registro)
				{
					$opcoes["{$registro['cod_vps_jornada_trabalho']}"] = "{$registro['nm_jornada_trabalho']}";
				}
			}
		} else {
			echo "<!--\nErro\nClasse clsPmieducarVPSJornadaTrabalho nao encontrada\n-->";
			$opcoes = array("" => "Erro na geracao");
		}

		$this->campoLista("ref_cod_vps_jornada_trabalho", "Jornada de Trabalho", $opcoes, $this->ref_cod_vps_jornada_trabalho, "", false, "", "", true);

		$helperOptions = array('objectName' => 'idiomas');

		$this->campoQuebra();

		$entrevistas = new clsPmieducarVPSAlunoEntrevista(null, null, $this->cod_vps_entrevista);
		$todasEntrevistas = $entrevistas->lista();
		$situacao_desabilitado = false;

		if($todasEntrevistas)
		{
			foreach($todasEntrevistas AS $campo => $entrevista)
			{
				if($entrevista['resultado_entrevista'] >= App_Model_EntrevistaResultado::APROVADO_EXTRA)
				{
					$situacao_desabilitado = true;
					break;
				}
			}
		}

		$this->campoLista('situacao_entrevista', 'Situacao Entrevista', App_Model_EntrevistaSituacao::getInstance()->getValues(), $this->situacao_entrevista, '', FALSE, '', '', $situacao_desabilitado, TRUE);

		$options = array(
			'required'    => false,
			'label'       => 'Data Início VPS',
			'placeholder' => '',
			'value'       => Portabilis_Date_Utils::pgSQLToBr($this->inicio_vps),
			'size'        => 7,
		);

		$this->inputsHelper()->date('inicio_vps', $options);

		$options = array(
			'required'    => false,
			'label'       => 'Data Término VPS',
			'placeholder' => '',
			'value'       => Portabilis_Date_Utils::pgSQLToBr($this->termino_vps),
			'size'        => 7,
		);

		$this->inputsHelper()->date('termino_vps', $options);

		$options = array(
			'required'    => false,
			'label'       => 'Data Inserção Profissional',
			'placeholder' => '',
			'value'       => Portabilis_Date_Utils::pgSQLToBr($this->insercao_vps),
			'size'        => 7,
		);

		$this->inputsHelper()->date('insercao_vps', $options);

		$this->campoQuebra();

		if($todasEntrevistas)
		{
			$listaResultado = App_Model_EntrevistaResultado::getInstance()->getValues();

			foreach($todasEntrevistas AS $campo => $entrevista)
			{
				$entrevistaResultado = $entrevista['resultado_entrevista'];
				$desabilitado = $entrevistaResultado >= App_Model_EntrevistaResultado::APROVADO_EXTRA;
				$this->campoLista("resultado_jovens[{$entrevista['ref_cod_aluno']}]", $entrevista['nome'], $listaResultado, $entrevistaResultado, '', FALSE, '', '', $desabilitado, TRUE);
			}
		}

		$this->campoQuebra();

		// text
		$this->campoRotulo("nm_entrevista", "Entrevista", $this->nm_entrevista);

		$this->campoMonetario('salario', 'Salário', number_format($this->salario, 2, ',', '.'), 7, 7, false, "", "", "onChange", true);

		$options = array(
			'required'    => true,
			'label'       => 'Número de Vagas',
			'placeholder' => '',
			'value'       => $this->numero_vagas,
			'max_length'  => 2,
			'inline'      => false,
			'size'        => 7,
			'disabled'    => true
		);

		$this->inputsHelper()->integer('numero_vagas', $options);

		$options = array(
			'required'    => true,
			'label'       => 'Número de Jovens por vaga',
			'placeholder' => '',
			'value'       => $this->numero_jovens,
			'max_length'  => 2,
			'inline'      => false,
			'size'        => 7,
			'disabled'    => true
		);

		$this->inputsHelper()->integer('numero_jovens', $options);

		$this->campoRotulo("data_entrevista", "Data/Hora",  Portabilis_Date_Utils::pgSQLToBr($this->data_entrevista) . " às " . $this->hora_entrevista);

		$options = array(
			'required'    => false,
			'label'       => 'Descrição',
			'value'       => $this->descricao,
			'cols'        => 30,
			'max_length'  => 150,
			'disabled'    => true
		);

		$this->inputsHelper()->textArea('descricao', $options);
	}

	function Novo()
	{
		$this->mensagem .= "Edição efetuada com sucesso.<br>";
		header("Location: educar_atribuir_entrevista_vps_lst.php");
		die();
		return true;
	}

	function Editar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11,  "educar_resultado_entrevista_lst.php");

		$entrevista = new clsPmieducarVPSEntrevista($this->cod_vps_entrevista);
		$entrevista->ref_usuario_exc = $this->pessoa_logada;

		if($this->cod_vps_entrevista && $entrevista->existe())
		{
			$str_inicio_vps = Portabilis_Date_Utils::brToPgSQL($this->inicio_vps);
			$str_termino_vps = Portabilis_Date_Utils::brToPgSQL($this->termino_vps);
			$str_insercao_vps = Portabilis_Date_Utils::brToPgSQL($this->insercao_vps);

			$entrevista->situacao_entrevista = $this->situacao_entrevista;
			if(!empty($str_inicio_vps) && !empty($str_termino_vps))
			{
				$entrevista->inicio_vps = $str_inicio_vps;
				$entrevista->termino_vps = $str_termino_vps;
				$entrevista->insercao_vps = $str_insercao_vps;
			}
			$entrevista->edita();

			foreach($this->resultado_jovens as $aluno => $resultado)
			{
				$alunoEntrevista = new clsPmieducarVPSAlunoEntrevista(null, $this->cod_vps_entrevista, $aluno);

				if($alunoEntrevista && $alunoEntrevista->existe())
				{
					$alunoEntrevista->resultado_entrevista = $resultado;
					if($resultado >= App_Model_EntrevistaResultado::APROVADO_EXTRA
						&& !empty($str_inicio_vps) && !empty($str_termino_vps))
					{
						$alunoEntrevista->inicio_vps = $str_inicio_vps;
						$alunoEntrevista->termino_vps = $str_termino_vps;
						$alunoEntrevista->insercao_vps = $str_insercao_vps;

						$detalheAlunoEntrevista = $alunoEntrevista->detalhe();

						$alunoVPS = new clsPmieducarAlunoVPS($aluno, null, $detalheAlunoEntrevista['cod_vps_aluno_entrevista']);
						$alunoVPS->situacao_vps = App_Model_VivenciaProfissionalSituacao::EM_CUMPRIMENTO;

						if($alunoVPS && $alunoVPS->existe())
						{
							$alunoVPS->edita();
						} else {
							$alunoVPS->ref_usuario_cad = $this->pessoa_logada;
							$alunoVPS->cadastra();
						}
					}
					$cadastrou = $alunoEntrevista->edita();
				}
			}

			$this->mensagem .= "Edição efetuada com sucesso.<br>";
			header("Location: educar_resultado_entrevista_lst.php");
			die();
			return true;
		}

		$this->mensagem = "Edição não realizada.<br> ";
		echo "<!--\nErro ao editar clsPmieducarAcervo\nvalores obrigatorios\nif(is_numeric($this->cod_vps_entrevista) && is_numeric($this->ref_usuario_exc))\n-->";

		return false;
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm($miolo);
// gera o html
$pagina->MakeAll();
?>

<script>
	document.getElementById('ref_cod_vps_funcao').disabled = true;
	document.getElementById('ref_cod_vps_funcao').options[0].text = 'Selecione uma escola';

	document.getElementById('ref_cod_vps_jornada_trabalho').disabled = true;
	document.getElementById('ref_cod_vps_jornada_trabalho').options[0].text = 'Selecione uma instituição';

	document.getElementById('ref_cod_vps_responsavel_entrevista').disabled = true;
	document.getElementById('ref_cod_vps_responsavel_entrevista').options[0].text = 'Selecione uma empresa';

	var tempExemplarTipo;
	var tempFuncao;
	var tempJornadaTrabalho;
	var tempResponsavel;

	if(document.getElementById('ref_cod_escola').value == "")
	{
		setVisibility(document.getElementById('img_funcao'), false);
		setVisibility(document.getElementById('img_jornada_trabalho'), false);

		tempFuncao = null;
	} else {
		ajaxEscola('novo');
	}

	if(document.getElementById('ref_cod_instituicao').value == "")
	{
		setVisibility(document.getElementById('img_jornada_trabalho'), false);

		tempJornadaTrabalho = null;
	} else {
		ajaxInstituicao('novo');
	}

	if(document.getElementById('empresa_id').value == "")
	{
		setVisibility(document.getElementById('img_responsavel'), false);

		tempResponsavel = null;
	} else {
		ajaxResponsavel('novo');
	}

	function getFuncao(xml_vps_funcao)
	{
		var campoFuncao = document.getElementById('ref_cod_vps_funcao');
		var DOM_array = xml_vps_funcao.getElementsByTagName("vps_funcao");

		if(DOM_array.length)
		{
			campoFuncao.length = 1;
			campoFuncao.options[0].text = 'Selecione uma Função/Cargo';
			campoFuncao.disabled = false;

			for(var i=0; i<DOM_array.length; i++)
			{
				campoFuncao.options[campoFuncao.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_funcao"), false, false);
			}
			setVisibility(document.getElementById('img_funcao'), true);
			if(tempFuncao != null)
				campoFuncao.value = tempFuncao;
		}
		else
		{
			if(document.getElementById('ref_cod_escola').value == "")
			{
				campoFuncao.options[0].text = 'Selecione uma escola';
				setVisibility(document.getElementById('img_funcao'), false);
			}
			else
			{
				campoFuncao.options[0].text = 'A Escola não possui função/cargo';
				setVisibility(document.getElementById('img_funcao'), true);
			}
		}
	}

	function getJornadaTrabalho(xml_vps_jornada_trabalho)
	{
		var campoJornadaTrabalho = document.getElementById('ref_cod_vps_jornada_trabalho');
		var DOM_array = xml_vps_jornada_trabalho.getElementsByTagName("vps_jornada_trabalho");

		if(DOM_array.length)
		{
			campoJornadaTrabalho.length = 1;
			campoJornadaTrabalho.options[0].text = 'Selecione uma Jornada de Trabalho';
			campoJornadaTrabalho.disabled = false;

			for(var i=0; i<DOM_array.length; i++)
			{
				campoJornadaTrabalho.options[campoJornadaTrabalho.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_jornada_trabalho"), false, false);
			}
			setVisibility(document.getElementById('img_jornada_trabalho'), true);
			if(tempJornadaTrabalho != null)
				campoJornadaTrabalho.value = tempJornadaTrabalho;
		}
		else
		{
			if(document.getElementById('ref_cod_instituicao').value == "")
			{
				campoJornadaTrabalho.options[0].text = 'Selecione uma instituição';
				setVisibility(document.getElementById('img_jornada_trabalho'), false);
			}
			else
			{
				campoJornadaTrabalho.options[0].text = 'A instituição não possui jornadas de trabalhos';
				setVisibility(document.getElementById('img_jornada_trabalho'), true);
			}
		}
	}

	function getResponsavelEntrevista(xml_vps_responsavel_entrevista)
	{
		var campoResponsavelEntrevista = document.getElementById('ref_cod_vps_responsavel_entrevista');
		var DOM_array = xml_vps_responsavel_entrevista.getElementsByTagName("vps_responsavel_entrevista");

		if(DOM_array.length)
		{
			campoResponsavelEntrevista.length = 1;
			campoResponsavelEntrevista.options[0].text = 'Selecione um responsável';
			campoResponsavelEntrevista.disabled = false;

			for(var i = 0; i < DOM_array.length; i++)
			{
				campoResponsavelEntrevista.options[campoResponsavelEntrevista.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_vps_responsavel_entrevista"), false, false);
			}

			setVisibility(document.getElementById('img_responsavel'), true);

			if(tempResponsavel != null)
				campoResponsavelEntrevista.value = tempResponsavel;
		}
		else
		{
			if(document.getElementById('empresa_id').value == "")
			{
				campoResponsavelEntrevista.options[0].text = 'Selecione uma empresa';
				setVisibility(document.getElementById('img_responsavel'), false);
			}
			else
			{
				campoResponsavelEntrevista.options[0].text = 'A escola não possui responsáveis cadastrados';
				setVisibility(document.getElementById('img_responsavel'), true);
			}
		}
	}

	jQuery(document).ready(function () {
		var empresaId = jQuery("#empresa_id").val();

		jQuery("#ref_cod_instituicao").change(function() {
			ajaxInstituicao();
		});

		jQuery("#ref_cod_escola").change(function() {
			ajaxEscola();
			ajaxResponsavel();
		});

		jQuery("#empresa_nome").bind("change keyup focusout", function() {
			setInterval(function() {
				var empresaSelecionada = jQuery("#empresa_id").val();
				if(empresaSelecionada != "" && empresaId != empresaSelecionada)
				{
					console.log("afffz " + jQuery("#empresa_id").val());
					empresaId = empresaSelecionada;
					ajaxResponsavel();
				}
			}, 300);
		});

		jQuery(".chosen-container").width(jQuery("#idiomas").width() + 14);
	});

	function ajaxEscola(acao)
	{
		var campoEscola = document.getElementById('ref_cod_escola').value;

		var campoExemplarTipo = document.getElementById('ref_cod_exemplar_tipo');

		var campoFuncao = document.getElementById('ref_cod_vps_funcao');

		if(acao == 'novo')
		{
			tempFuncao = campoFuncao.value;
		}

		campoFuncao.length = 1;
		campoFuncao.disabled = true;
		campoFuncao.options[0].text = 'Carregando coleções';

		var xml_funcao = new ajax(getFuncao);
		xml_funcao.envia("educar_vps_funcao_xml.php?esc="+campoEscola);
	}

	function ajaxInstituicao(acao)
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

		var campoJornadaTrabalho = document.getElementById('ref_cod_vps_jornada_trabalho');

		if(acao == 'novo')
		{
			tempJornadaTrabalho = campoJornadaTrabalho.value;
		}

		campoJornadaTrabalho.length = 1;
		campoJornadaTrabalho.disabled = true;
		campoJornadaTrabalho.options[0].text = 'Carregando Jornada de Trabalho';

		var xml_jornada_trabalho = new ajax(getJornadaTrabalho);
		xml_jornada_trabalho.envia("educar_vps_jornada_trabalho_xml.php?inst="+campoInstituicao);
	}

	function ajaxResponsavel(acao)
	{
		var campoEscola = document.getElementById('ref_cod_escola').value;
		var campoEmpresa = document.getElementById('empresa_id').value;

		if(campoEmpresa != "" && campoEscola != "")
		{
			var campoResponsavel = document.getElementById('ref_cod_vps_responsavel_entrevista');

			if(acao == 'novo')
			{
				tempResponsavel = campoResponsavel.value;
			}

			campoResponsavel.length = 1;
			campoResponsavel.disabled = true;
			campoResponsavel.options[0].text = 'Carregando responsável';

			var xml_jornada_trabalho = new ajax(getResponsavelEntrevista);
			console.log("valores esc=" + campoEscola + "&idpes" + campoEmpresa);
			xml_jornada_trabalho.envia("educar_entrevista_xml.php?esc=" + campoEscola + "&idpes=" + campoEmpresa);
		}
	}

	function pesquisa()
	{
		var escola = document.getElementById('ref_cod_escola').value;
		if(!escola)
		{
			alert('Por favor,\nselecione uma escola!');
			return;
		}
		pesquisa_valores_popless('educar_pesquisa_acervo_lst.php?campo1=ref_cod_vps_entrevista&ref_cod_escola=' + biblioteca , 'ref_cod_vps_entrevista')
	}


	function fixupPrincipalCheckboxes() {
		$j('#principal').hide();

		var $checkboxes = $j("input[type='checkbox']").filter("input[id^='principal_']");

		$checkboxes.change(function(){
			$checkboxes.not(this).removeAttr('checked');
		});
	}

	fixupPrincipalCheckboxes();
	function fixupIdiomasSize(){

		$j('#idiomas_chzn ul').css('width', '307px');

	}

	fixupIdiomasSize();

	$idiomas = $j('#idiomas');

	$idiomas.trigger('chosen:updated');
	var testezin;

	var handleGetIdiomas = function(dataResponse) {
		testezin = dataResponse['idiomas'];

		console.log(testezin);

		$j.each(dataResponse['idiomas'], function(id, value) {
			$idiomas.children("[value=" + value + "]").attr('selected', '');
		});

		$idiomas.trigger('chosen:updated');
	}

	var getIdiomas = function() {
		var $cod_vps_entrevista = $j('#cod_vps_entrevista').val();

		if ($j('#cod_vps_entrevista').val() != '') {
			var additionalVars = {
				id : $j('#cod_vps_entrevista').val(),
			};

			var options = {
				url      : getResourceUrlBuilder.buildUrl('/module/Api/idioma', 'idioma', additionalVars),
				dataType : 'json',
				data     : {},
				success  : handleGetIdiomas,
			};

			getResource(options);
		}
	}

	getIdiomas();

</script>
