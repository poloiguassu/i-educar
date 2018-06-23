<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com					    	 *
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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once("include/pmieducar/geral.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Jornada de Trabalho");
		$this->SetTemplate("base_pop");
		$this->processoAp = "590";
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

	var $cod_vps_jornada_trabalho;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_vps_jornada_trabalho;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(590, $this->pessoa_logada, 11,  "educar_vps_jornada_trabalho_lst.php");

		return $retorno;
	}

	function Gerar()
	{
		echo "<script>window.onload=function() { parent.EscondeDiv('LoadImprimir') }</script>";
		
		// primary keys
		$this->campoOculto("cod_vps_jornada_trabalho", $this->cod_vps_jornada_trabalho);
		$this->campoOculto("ref_cod_instituicao", $this->ref_cod_instituicao);
		
		// text
		$this->campoTexto("nm_jornada_trabalho", "Jornada de Trabalho", $this->nm_vps_jornada_trabalho, 30, 255, true);
		
		if (is_numeric($this->carga_horaria_semana))
		{
			$this->campoMonetario('carga_horaria_semana', 'Carga Hor�ria Semanal', number_format($this->carga_horaria_semana, 2, ',', ''), 7, 7, false);
		} else {
			$this->campoMonetario('carga_horaria_semana', 'Carga Hor�ria Semanal', $this->carga_horaria_semana, 7, 7, false);
		}

		if (is_numeric($this->carga_horaria_diaria))
		{
			$this->campoMonetario('carga_horaria_diaria', 'Carga Hor�ria Di�ria', number_format($this->carga_horaria_diaria, 2, ',', ''), 7, 7, false);
		} else {
			$this->campoMonetario('carga_horaria_diaria', 'Carga Hor�ria Di�ria', $this->carga_horaria_semana, 7, 7, false);
		}
	}

	function Novo()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->carga_horaria_semana = str_replace(".", "",  $this->carga_horaria_semana);
		$this->carga_horaria_semana = str_replace(",", ".", $this->carga_horaria_semana);
		$this->carga_horaria_diaria = str_replace(".", "",  $this->carga_horaria_diaria);
		$this->carga_horaria_diaria = str_replace(",", ".", $this->carga_horaria_diaria);

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(590, $this->pessoa_logada, 11,  "educar_vps_jornada_trabalho_lst.php");

		$obj = new clsPmieducarVPSJornadaTrabalho($this->cod_vps_jornada_trabalho, null, $this->pessoa_logada, $this->nm_vps_jornada_trabalho, $this->carga_horaria_semana, $this->carga_horaria_diaria, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_instituicao);
		$cadastrou = $obj->cadastra();
		
		if($cadastrou)
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			
			echo "<script>
					parent.document.getElementById('jornada_trabalho').value = '$cadastrou';
					parent.document.getElementById('tipoacao').value = '';
					parent.document.getElementById('formcadastro').submit();
				</script>";
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarVPSJornadaTrabalho\nvalores obrigatorios\nis_numeric($this->ref_usuario_cad) && is_string($this->nm_vps_jornada_trabalho)\n-->";
		return false;
	}

	function Editar()
	{
	}

	function Excluir()
	{
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
	document.getElementById('ref_cod_instituicao').value = parent.document.getElementById('ref_cod_instituicao').value;
</script>
