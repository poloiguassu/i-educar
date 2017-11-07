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
require_once 'lib/App/Model/PrioridadeVPS.php';

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
	var $prioridade;

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

	function Inicializar()
	{
		$retorno = "Novo";

		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_aluno = $_GET["cod_aluno"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11,  "educar_vps_aluno_cad.php");

		Portabilis_View_Helper_Application::loadJQueryDependsOnLib($this);

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

				$retorno = "Editar";
			}
		}

		$this->url_cancelar = ($retorno == "Editar") ? "educar_vps_aluno_det.php?cod_aluno={$this->cod_aluno}" : "educar_vps_aluno_lst.php";
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

		$this->campoRotulo("nm_aluno", "Aluno", $this->nome);

		$this->campoRotulo("nm_situacao_vps", "Situação VPS", App_Model_VivenciaProfissionalSituacao::getInstance()->getValue($this->situacao_vps));

		$opcaoPrioridade = App_Model_PrioridadeVPS::getInstance()->getValues();

		$this->campoLista('prioridade', 'Prioridade VPS', $opcaoPrioridade, $this->prioridade, '', FALSE, '', '', FALSE, TRUE);

		if($this->situacao_vps >= App_Model_VivenciaProfissionalSituacao::EM_CUMPRIMENTO)
		{
			$alunoEntrevista = new clsPmieducarVPSAlunoEntrevista($this->ref_cod_vps_aluno_entrevista);
			$registroAlunoEntrevista = $alunoEntrevista->detalhe();

			$this->cod_vps_entrevista = $registroAlunoEntrevista["ref_cod_vps_entrevista"];

			if($this->cod_vps_entrevista)
			{
				$entrevista = new clsPmieducarVPSEntrevista($this->cod_vps_entrevista);
				$registroEntrevista = $entrevista->detalhe();
				$nm_entrevista = $registroEntrevista["nm_entrevista"];

				$this->campoRotulo("nm_entrevista", "Cumprindo VPS em", $nm_entrevista);
			}

			if($registroAlunoEntrevista["inicio_vps"])
			{
				$inicioVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["inicio_vps"]);
				$this->campoRotulo("inicio_vps", "Iniciou VPS no dia", $inicioVPS);
			}

			if($registroAlunoEntrevista["termino_vps"])
			{
				$terminoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["termino_vps"]);
				$this->campoRotulo("termino_vps", "Conclusão VPS em", $terminoVPS);
			}

			if($registroAlunoEntrevista["insercao_vps"])
			{
				$insercaoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["insercao_vps"]);
				$this->campoRotulo("insercao_vps", "Inserção Profissional em", $insercaoVPS);
			}
		}

		$opcaoSituacao = array(
			2	=> 'Desistente',
			3	=> 'Desligado',
			4	=> 'Apto a VPS'
		);

		if($this->situacao_vps >= App_Model_VivenciaProfissionalSituacao::EM_CUMPRIMENTO)
		{
			$opcaoSituacao = array(
				2	=> 'Desistente',
				3	=> 'Desligado',
				4	=> 'Apto a VPS',
				6	=> 'Concluído (Avaliado)',
				7	=> 'Inserido'
			);
		}

		if($this->situacao_vps != App_Model_VivenciaProfissionalSituacao::EVADIDO)
		{
			$this->campoLista('alterar_situacao_vps', 'Alterar Situacao VPS', $opcaoSituacao, $this->alterar_situacao_vps, '', FALSE, '', '', FALSE, TRUE);

			$options = array(
				'required'    => false,
				'label'       => 'Motivo Desligamento',
				'value'       => $this->motivo_desligamento,
				'cols'        => 30,
				'max_length'  => 150,
			);

			$this->inputsHelper()->textArea('motivo_desligamento', $options);

			$options = array(
				'required'    => false,
				'label'       => 'Motivo Cancelamento VPS',
				'value'       => $this->motivo_termino,
				'cols'        => 30,
				'max_length'  => 150,
			);

			$this->inputsHelper()->textArea('motivo_termino', $options);

			$this->campoArquivo( "avaliacao_vps", "Avaliação VPS", $this->avaliacao_vps, "50");

			// primary keys
			$this->campoOculto("situacao_vps", $this->situacao_vps);

			$this->campoOculto("ref_cod_vps_aluno_entrevista", $this->ref_cod_vps_aluno_entrevista);
		} else {
			$this->campoRotulo("evadiu", "Este aluno evadiu o processo de formação", "Não é possível alterar o status de um aluno evadido");
		}

		$this->campoOculto("cod_aluno", $this->cod_aluno);
	}

	function Novo()
	{
		$this->mensagem .= "Edição efetuada com sucesso.<br>";
		header("Location: educar_vps_aluno_lst.php");
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

		$entrevista = new clsPmieducarAlunoVPS($this->cod_aluno);
		$entrevista->ref_usuario_exc = $this->pessoa_logada;

		if($this->cod_aluno && $entrevista->existe())
		{
			if(!empty($this->motivo_desligamento))
				$entrevista->motivo_desligamento = $this->motivo_desligamento;

			if(is_numeric($this->prioridade))
				$entrevista->prioridade = $this->prioridade;

			if($this->situacao_vps)
			{
				$entrevista->situacao_vps = $this->alterar_situacao_vps;

				if($this->situacao_vps == App_Model_VivenciaProfissionalSituacao::EM_CUMPRIMENTO &&
					$this->alterar_situacao_vps < App_Model_VivenciaProfissionalSituacao::EM_CUMPRIMENTO)
				{
					$entrevista->ref_cod_vps_aluno_entrevista = 0;

					$alunoEntrevista = new clsPmieducarVPSAlunoEntrevista($this->ref_cod_vps_aluno_entrevista);

					if($alunoEntrevista && $alunoEntrevista->existe())
					{
						$alunoEntrevista->resultado_entrevista = App_Model_EntrevistaResultado::APROVADO_ABANDONO;

						if(!empty($this->motivo_termino))
							$alunoEntrevista->motivo_termino = $this->motivo_termino;

						$cadastrou = $alunoEntrevista->edita();
					}
				}
			}

			$entrevista->edita();

			$this->mensagem .= "Edição efetuada com sucesso.<br>";
			header("Location: educar_vps_aluno_det.php?cod_aluno={$this->cod_aluno}");
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

<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#tr_motivo_desligamento, #motivo_desligamento').dependsOn({
			'#alterar_situacao_vps': {
				values: ['2', '3']
			}
		});
		$('#tr_motivo_termino').dependsOn({
			'#alterar_situacao_vps': {
				values: ['2', '3', '4']
			}
		});
		$('#tr_avaliacao_vps').dependsOn({
			'#alterar_situacao_vps': {
				values: ['6']
			}
		});
	});
</script>
