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
require_once("include/clsBase.inc.php");
require_once("include/clsCadastro.inc.php");
require_once("include/clsBanco.inc.php");
require_once("include/pmieducar/geral.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Responsável Entrevista");
		$this->processoAp = "594";
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

	var $cod_vps_responsavel_entrevista;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_responsavel;
	var $email;
	var $ddd_telefone_com;
	var $telefone_com;
	var $ddd_telefone_cel;
	var $telefone_cel;
	var $observacao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $empresa_id;
	var $ref_cod_escola;
	var $ref_cod_instituicao;
	
	function Inicializar()
	{
		$retorno = "Novo";

		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_vps_responsavel_entrevista=$_GET["cod_vps_responsavel_entrevista"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11,  "educar_vps_responsavel_entrevista_lst.php");

		if(is_numeric($this->cod_vps_responsavel_entrevista))
		{
			$obj = new clsPmieducarVPSResponsavelEntrevista($this->cod_vps_responsavel_entrevista);
			$registro  = $obj->detalhe();
			
			if($registro)
			{
				foreach($registro AS $campo => $val)	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_escola = new clsPmieducarEscola($registro["ref_cod_escola"]);
				$obj_det = $obj_escola->detalhe();
				
				$this->ref_cod_instituicao = $obj_det["ref_cod_instituicao"];
				$this->ref_cod_escola = $obj_det["cod_escola"];
				
				$this->empresa_id = $registro["ref_idpes"];

				$this->nm_responsavel = stripslashes($this->nm_responsavel);
				$this->nm_responsavel = htmlspecialchars($this->nm_responsavel);

				$obj_permissoes = new clsPermissoes();

				if($obj_permissoes->permissao_excluir(594, $this->pessoa_logada, 11))
				{
					$this->fexcluir = true;
				}

				$retorno = "Editar";
			}
		}
		
		$this->url_cancelar = ($retorno == "Editar") ? "educar_vps_responsavel_entrevista_det.php?cod_vps_responsavel_entrevista={$registro["cod_vps_responsavel_entrevista"]}" : "educar_vps_responsavel_entrevista_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		$nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			"educar_vps_index.php"				  => "Trilha Jovem - VPS",
			""									  => "{$nomeMenu} responsável entrevista"
		));

		$this->enviaLocalizacao($localizacao->montar());

		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto("cod_vps_responsavel_entrevista", $this->cod_vps_responsavel_entrevista);

		// foreign keys
		$get_escola = true;
		$escola_obrigatorio = true;
		$instituicao_obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");

		$options = array('label' => "Empresa", 'required' => true, 'size' => 30);
		
		$helperOptions = array(
			'objectName'         => 'empresa',
			'hiddenInputOptions' => array('options' => array('value' => $this->empresa_id))
		);
		
		$this->inputsHelper()->simpleSearchPessoaj('nome', $options, $helperOptions);

		// text
		$this->campoTexto("nm_responsavel", "Responsável Entrevista", $this->nm_responsavel, 30, 255, true);
		
		$this->campoTexto('email', 'E-mail', $this->email, '50', '255', FALSE);
		$this->inputTelefone('com', 'Telefone comercial');
		$this->inputTelefone('cel', 'Celular');
		
		$this->campoMemo("observacao", "Observação", $this->observacao, 60, 5, false);
		
		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
	}

	function Novo()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->nm_responsavel = addslashes($this->nm_responsavel);

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11,  "educar_vps_responsavel_entrevista_lst.php");

		$obj = new clsPmieducarVPSResponsavelEntrevista(null, null, $this->pessoa_logada, $this->nm_responsavel, $this->email, $this->ddd_telefone_com, $this->telefone_com, $this->ddd_telefone_cel, $this->telefone_cel, $this->observacao, null, null, 1, $this->ref_cod_escola, $this->empresa_id);
		$cadastrou = $obj->cadastra();
		
		if($cadastrou)
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header("Location: educar_vps_responsavel_entrevista_lst.php");
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		$this->mensagem .= "<!--\nErro ao cadastrar clsPmieducarVPSResponsavelEntrevista\nvalores obrigatórios\nis_numeric($this->pessoa_logada) && is_string($this->nm_responsavel) && is_numeric($this->empresa_id)\n-->";
		
		return false;
	}

	function Editar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->nm_responsavel = addslashes($this->nm_responsavel);

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11,  "educar_vps_responsavel_entrevista_lst.php");


		$obj = new clsPmieducarVPSResponsavelEntrevista($this->cod_vps_responsavel_entrevista, $this->pessoa_logada, null, $this->nm_responsavel, $this->email, $this->ddd_telefone_com, $this->telefone_com, $this->ddd_telefone_cel, $this->telefone_cel, $this->observacao, null, null, 1, $this->ref_cod_escola, $this->empresa_id);
		$editou = $obj->edita();
		if($editou)
		{
			$this->mensagem .= "Edição efetuada com sucesso.<br>";
			header("Location: educar_vps_responsavel_entrevista_lst.php");
			die();
			return true;
		}

		$this->mensagem  = "Edição não realizada.<br>";
		$this->mensagem .= "<!--\nErro ao editar clsPmieducarVPSResponsavelEntrevista\nvalores obrigatorios\nif(is_numeric($this->cod_vps_responsavel_entrevista) && is_numeric($this->pessoa_logada))\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir(594, $this->pessoa_logada, 11,  "educar_vps_responsavel_entrevista_lst.php");

		$obj = new clsPmieducarVPSResponsavelEntrevista($this->cod_vps_responsavel_entrevista, $this->pessoa_logada, null, null, null, null, null, null, null, null, null, null, 0, $this->ref_cod_escola, $this->empresa_id);
		$excluiu = $obj->excluir();
		if($excluiu)
		{
			$this->mensagem .= "Exclusão efetuada com sucesso.<br>";
			header("Location: educar_vps_responsavel_entrevista_lst.php");
			die();
			return true;
		}

		$this->mensagem  = "Exclusão não realizada.<br>";
		$this->mensagem .= "<!--\nErro ao excluir clsPmieducarVPSResponsavelEntrevista\nvalores obrigatorios\nif(is_numeric($this->cod_vps_responsavel_entrevista) && is_numeric($this->pessoa_logada))\n-->";
		return false;
	}

	protected function inputTelefone($type, $typeLabel = '')
	{
		if (! $typeLabel)
			$typeLabel = "Telefone {$type}";

		// ddd
		$options = array(
			'required'	=> false,
			'label'	   => "(ddd) / {$typeLabel}",
			'placeholder' => 'ddd',
			'value'	   => $this->{"ddd_telefone_{$type}"},
			'max_length'  => 3,
			'size'		=> 3,
			'inline'	  => true
		);

		$this->inputsHelper()->integer("ddd_telefone_{$type}", $options);


		// telefone
		$options = array(
			'required'	=> false,
			'label'	   => '',
			'placeholder' => $typeLabel,
			'value'	   => $this->{"telefone_{$type}"},
			'max_length'  => 11
		);

		$this->inputsHelper()->integer("telefone_{$type}", $options);
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
