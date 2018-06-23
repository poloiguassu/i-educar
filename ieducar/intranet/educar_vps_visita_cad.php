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

	var $cod_aluno;
	var $cod_usuario;
	var $cod_vps_entrevista;
	var $nm_aluno;
	var $nm_situacao_vps;
	var $inicio_vps;
	var $termino_vps;
	var $insercao_vps;
	var $data_visita;
	var $hora_visita;
	var $motivo_visita;
	var $avaliacao_vps;
	var $situacao_vps;
	var $ref_cod_vps_aluno_entrevista;
	var $evadiu;

	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";

		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_aluno = $_GET["ref_cod_aluno"];
		$this->cod_vps_visita = $_GET["cod_vps_visita"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11,  "educar_vps_aluno_cad.php");

		if(is_numeric($this->cod_vps_visita))
		{
			$tmp_obj = new clsPmieducarVPSVisita($this->cod_vps_visita);
			$registro = $tmp_obj->detalhe();

			$obj = new clsPmieducarVPSAlunoEntrevista($registro["ref_cod_vps_aluno_entrevista"]);
			$registroAlunoEntrevista = $obj->detalhe();

			$obj = new clsPmieducarAlunoVPS($registroAlunoEntrevista["ref_cod_aluno"]);
			$registroAlunoVPS  = $obj->detalhe();

			$obj = new clsPmieducarAluno($registroAlunoEntrevista["ref_cod_aluno"]);
			$registroAluno = $obj->detalhe();

			$obj = new clsPessoaFj($registroAluno["ref_idpes"]);
			$registroPessoa = $obj->detalhe();

			$registro = array_merge($registro, $registroAlunoVPS, $registroPessoa);

			if($registro)
			{
				foreach($registro AS $campo => $val)	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
			}

			$retorno = "Editar";
		} else {
			if(is_numeric($this->cod_aluno))
			{
				$obj = new clsPmieducarAlunoVPS($this->cod_aluno);
				$registro  = $obj->detalhe();

				$obj = new clsPmieducarAluno($this->cod_aluno);
				$registroAluno = $obj->detalhe();

				$obj = new clsPessoaFj($registroAluno['ref_idpes']);
				$registroPessoa = $obj->detalhe();

				$registro = array_merge($registro, $registroPessoa);

				if($registro)
				{
					foreach($registro AS $campo => $val)	// passa todos os valores obtidos no registro para atributos do objeto
						$this->$campo = $val;
				}

				$retorno = "Novo";
			}
		}

		$this->url_cancelar = ($retorno == "Editar") ? "educar_vps_aluno_det.php?cod_aluno={$this->cod_aluno}" : "educar_vps_aluno_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		$nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "In�cio",
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

		$this->campoRotulo("nm_aluno", "Aluno", $this->nome);

		$this->campoRotulo("nm_situacao_vps", "Situa��o VPS", App_Model_VivenciaProfissionalSituacao::getInstance()->getValue($this->situacao_vps));

		if($this->situacao_vps >= App_Model_VivenciaProfissionalSituacao::EM_CUMPRIMENTO)
		{
			$alunoEntrevista = new clsPmieducarVPSAlunoEntrevista($this->ref_cod_vps_aluno_entrevista);
			$registroAlunoEntrevista = $alunoEntrevista->detalhe();

			$this->cod_vps_entrevista = $registroAlunoEntrevista["ref_cod_vps_entrevista"];

			if($this->cod_vps_entrevista)
			{
				$entrevista = new clsPmieducarVPSEntrevista($this->cod_vps_entrevista);
				$registroEntrevista = $entrevista->detalhe();

				if(class_exists("clsPessoaFj"))
				{
					$obj_ref_idpes = new clsPessoaFj($registroEntrevista["ref_idpes"]);
					$det_ref_idpes = $obj_ref_idpes->detalhe();
					$registroEntrevista["ref_idpes"] = $det_ref_idpes["nome"];
				}
				else
				{
					$registro["ref_idpes"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPessoaFj\n-->";
				}

				if(class_exists("clsPmieducarVPSFuncao"))
				{
					$obj_ref_cod_vps_funcao = new clsPmieducarVPSFuncao($registroEntrevista["ref_cod_vps_funcao"]);
					$det_ref_cod_vps_funcao = $obj_ref_cod_vps_funcao->detalhe();
					$registroEntrevista["ref_cod_vps_funcao"] = $det_ref_cod_vps_funcao["nm_funcao"];
				}
				else
				{
					$registro["ref_cod_vps_funcao"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSFuncao\n-->";
				}

				$entrevista = "{$registroEntrevista["ref_cod_vps_funcao"]} / {$registroEntrevista["ref_idpes"]}";

				$this->campoRotulo("entrevista", "Cumprindo VPS em", $entrevista);
			}

			if($registroAlunoEntrevista["inicio_vps"])
			{
				$inicioVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["inicio_vps"]);
				$this->campoRotulo("inicio_vps", "Iniciou VPS no dia", $inicioVPS);
			}

			if($registroAlunoEntrevista["termino_vps"])
			{
				$terminoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["termino_vps"]);
				$this->campoRotulo("termino_vps", "Conclus�o VPS em", $terminoVPS);
			}

			if($registroAlunoEntrevista["insercao_vps"])
			{
				$insercaoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["insercao_vps"]);
				$this->campoRotulo("insercao_vps", "Inser��o Profissional em", $insercaoVPS);
			}

			if( $this->ref_cod_usuario )
			{
				$objTemp = new clsFuncionario( $this->ref_cod_usuario );
				$detalhe = $objTemp->detalhe();
				$detalhe = $detalhe["idpes"]->detalhe();
				$opcoes["{$detalhe["idpes"]}"] = $detalhe["nome"];
			}

			$parametros = new clsParametrosPesquisas();
			$parametros->setSubmit(0);
			$parametros->adicionaCampoSelect("cod_usuario", "ref_cod_pessoa_fj", "nome");

			$this->campoListaPesq( "cod_usuario", "Usu�rio", $opcoes, $this->ref_cod_usuario, "pesquisa_funcionario_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );

			$options = array(
				'required'    => true,
				'label'       => 'Data visita',
				'placeholder' => '',
				'value'       => Portabilis_Date_Utils::pgSQLToBr($this->data_visita),
				'size'        => 7,
			);

			$this->inputsHelper()->date('data_visita', $options);

			$this->campoHora('hora_visita', 'Hora visita', $this->hora_visita, false);

			$options = array(
				'required'    => false,
				'label'       => 'Motivo da Visita',
				'value'       => $this->motivo_visita,
				'cols'        => 30,
				'max_length'  => 150,
			);

			$this->inputsHelper()->textArea('motivo_visita', $options);

			$this->campoCheck("avaliacao", "Avalia��o de VPS", $this->avaliacao, "Marcar como avalia��o de VPS");

			// primary keys
			$this->campoOculto("cod_vps_visita", $this->cod_vps_visita);

			$this->campoOculto("situacao_vps", $this->situacao_vps);

			$this->campoOculto("cod_aluno", $this->cod_aluno);

			$this->campoOculto("ref_cod_vps_aluno_entrevista", $this->ref_cod_vps_aluno_entrevista);
		} else {
			$this->campoRotulo("evadiu", "Este aluno n�o est� cumprindo VPS", "N�o � poss�vel agendar uma visita a um jovem que n�o est� em VPS");
		}
	}

	function Novo()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$alunoVPS = new clsPmieducarAlunoVPS($this->cod_aluno);

		if($this->cod_aluno && $alunoVPS->existe())
		{
			if($this->situacao_vps >= App_Model_VivenciaProfissionalSituacao::EM_CUMPRIMENTO)
			{
				if(is_string($this->data_visita))
					$this->data_visita = Portabilis_Date_Utils::brToPgSQL($this->data_visita);

				$visita = new clsPmieducarVPSVisita(null,
					$this->data_visita, $this->hora_visita, $this->motivo_visita,
					$this->cod_usuario, $this->ref_cod_vps_aluno_entrevista, $this->avaliacao,
					$this->pessoa_logada, null
				);

				if($cadastrou = $visita->cadastra())
				{
					$this->mensagem .= "Cadastro efetuada com sucesso.<br>";
					header("Location: educar_vps_visita_det.php?cod_vps_visita={$cadastrou}");
					die();
					return true;
				}

			}
		}

		$this->mensagem = "Edi��o n�o realizada.<br> ";
		echo "<!--\nErro ao editar clsPmieducarAcervo\nvalores obrigatorios\nif(is_numeric($this->cod_vps_entrevista) && is_numeric($this->ref_usuario_exc))\n-->";

		return false;
	}

	function Editar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11,  "educar_vps_visita_lst.php");

		$visita = new clsPmieducarVPSVisita($this->cod_vps_visita);

		if($this->cod_vps_visita && $visita->existe())
		{
			if($this->situacao_vps >= App_Model_VivenciaProfissionalSituacao::EM_CUMPRIMENTO)
			{
				if(is_string($this->data_visita))
					$this->data_visita = Portabilis_Date_Utils::brToPgSQL($this->data_visita);

				$visita = new clsPmieducarVPSVisita($this->cod_vps_visita,
					$this->data_visita, $this->hora_visita, $this->motivo_visita,
					$this->cod_usuario, $this->ref_cod_vps_aluno_entrevista, $this->avaliacao,
					null, $this->pessoa_logada
				);

				if($visita->edita())
				{
					$this->mensagem .= "Edi��o efetuada com sucesso.<br>";
					header("Location: educar_vps_visita_det.php?cod_vps_visita={$this->cod_vps_visita}");
					die();
					return true;
				}

			}
		}

		$this->mensagem = "Edi��o n�o realizada.<br> ";
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
