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
require_once("include/clsBase.inc.php");
require_once("include/clsCadastro.inc.php");
require_once("include/clsBanco.inc.php");
require_once("include/pmieducar/geral.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Respons�vel Entrevista");
		$this->SetTemplate("base_pop");
		$this->processoAp = "594";
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
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

		return $retorno;
	}

	function Gerar()
	{
		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir')}</script>";
		
		// primary keys
		$this->campoOculto("cod_vps_responsavel_entrevista", $this->cod_vps_responsavel_entrevista);

		$this->campoOculto("ref_cod_escola", $this->ref_cod_escola);
		
		$this->campoOculto("empresa_id", $this->empresa_id);

		// text
		$this->campoTexto("nm_responsavel", "Respons�vel Entrevista", $this->nm_responsavel, 30, 255, true);
		
		$this->campoTexto('email', 'E-mail', $this->email, '50', '255', FALSE);
		$this->inputTelefone('com', 'Telefone comercial');
		$this->inputTelefone('cel', 'Celular');

		$this->campoMemo("observacao", "Observa��o", $this->observacao, 60, 5, false);

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11,  "educar_vps_responsavel_entrevista_lst.php");


		$obj = new clsPmieducarVPSResponsavelEntrevista(null, null, $this->pessoa_logada, $this->nm_responsavel, $this->email, $this->ddd_telefone_com, $this->telefone_com, $this->ddd_telefone_cel, $this->telefone_cel, $this->observacao, null, null, 1, $this->ref_cod_escola, $this->empresa_id);
		$cadastrou = $obj->cadastra();
		if($cadastrou)
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			echo "<script>
					parent.document.getElementById('responsavel').value = '$cadastrou';
					parent.document.getElementById('tipoacao').value = '';
					parent.document.getElementById('formcadastro').submit();
				</script>";
			die();

			return true;
		}

		$this->mensagem  = "Cadastro n�o realizado.<br>";
		$this->mensagem .= "<!--\nErro ao cadastrar clsPmieducarVPSResponsavelEntrevista\nvalores obrigat�rios\nis_numeric($this->pessoa_logada) && is_string($this->nm_responsavel)\n-->";

		return false;
	}

	function Editar()
	{
	}

	function Excluir()
	{
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
<script>
	document.getElementById('ref_cod_escola').value = parent.document.getElementById('ref_cod_escola').value;
	document.getElementById('empresa_id').value = parent.document.getElementById('empresa_id').value;
</script>
