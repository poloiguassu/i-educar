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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once("include/pmieducar/geral.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Funcao");
		$this->SetTemplate("base_pop");
		$this->processoAp = "593";
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

	var $cod_vps_funcao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_funcao;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_escola;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(593, $this->pessoa_logada, 11,  "educar_vps_funcao_lst.php");

		return $retorno;
	}

	function Gerar()
	{
		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir')}</script>";
		$this->campoOculto("ref_cod_escola", $this->ref_cod_escola);
		
		$this->campoTexto("nm_funcao", "Função", $this->nm_funcao, 30, 255, true);
		$this->campoMemo("descricao", "Descrição", $this->descricao, 60, 5, false);

	}

	function Novo()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(593, $this->pessoa_logada, 11,  "educar_vps_funcao_lst.php");

		$obj = new clsPmieducarVPSFuncao($this->cod_vps_funcao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_funcao, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_escola);
		$cadastrou = $obj->cadastra();

		if($cadastrou)
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";

			echo "<script>
					parent.document.getElementById('funcao').value = '$cadastrou';
					parent.document.getElementById('ref_cod_vps_funcao').disabled = false;
					parent.document.getElementById('tipoacao').value = '';
					parent.document.getElementById('formcadastro').submit();
				</script>";
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarVPSFuncao\nvalores obrigatorios\nis_numeric($this->ref_usuario_cad) && is_string($this->nm_funcao)\n-->";
		
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
	document.getElementById('ref_cod_escola').value = parent.document.getElementById('ref_cod_escola').value;
</script>
