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
		$this->SetTitulo("{$this->_instituicao} - Idiomas");
		$this->processoAp = "592";
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

	var $cod_vps_idioma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_idioma;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_vps_idioma=$_GET["cod_vps_idioma"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11,  "educar_vps_idioma_lst.php");

		if(is_numeric($this->cod_vps_idioma))
		{

			$obj = new clsPmieducarVPSIdioma($this->cod_vps_idioma);
			$registro  = $obj->detalhe();
			if($registro)
			{
				foreach($registro AS $campo => $val)	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_permissoes = new clsPermissoes();
				if($obj_permissoes->permissao_excluir(592, $this->pessoa_logada, 11))
				{
					$this->fexcluir = true;
				}

				$retorno = "Editar";
			}
		}
		
		$this->url_cancelar = ($retorno == "Editar") ? "educar_vps_idioma_det.php?cod_vps_idioma={$registro["cod_vps_idioma"]}" : "educar_vps_idioma_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		$nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			"educar_vps_index.php"                => "Trilha Jovem - VPS",
			""                                    => "{$nomeMenu} Idiomas"
		));

		$this->enviaLocalizacao($localizacao->montar());

		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto("cod_vps_idioma", $this->cod_vps_idioma);

		//foreign keys
		$this->inputsHelper()->dynamic(array('instituicao'));

		// text
		$this->campoTexto("nm_idioma", "Idioma", $this->nm_idioma, 30, 255, true);
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11,  "educar_vps_idioma_lst.php");


		$obj = new clsPmieducarVPSIdioma(null, null, $this->pessoa_logada, $this->nm_idioma, null, null, 1, $this->ref_cod_instituicao);
		$cadastrou = $obj->cadastra();
		if($cadastrou)
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header("Location: educar_vps_idioma_lst.php");
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarVPSIdioma\nvalores obrigatórios\nis_numeric($this->pessoa_logada) && is_string($this->nm_idioma)\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11,  "educar_vps_idioma_lst.php");


		$obj = new clsPmieducarVPSIdioma($this->cod_vps_idioma, $this->pessoa_logada, null, $this->nm_idioma, null, null, 1, $this->ref_cod_instituicao);
		$editou = $obj->edita();
		
		if($editou)
		{
			$this->mensagem .= "Edição efetuada com sucesso.<br>";
			header("Location: educar_vps_idioma_lst.php");
			die();
			return true;
		}

		$this->mensagem = "Edição não realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarVPSIdioma\nvalores obrigatórios\nif(is_numeric($this->cod_vps_idioma) && is_numeric($this->pessoa_logada))\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir(592, $this->pessoa_logada, 11,  "educar_vps_idioma_lst.php");


		$obj = new clsPmieducarVPSIdioma($this->cod_vps_idioma, $this->pessoa_logada, null, null, null, null, 0, $this->ref_cod_instituicao);
		$excluiu = $obj->excluir();
		if($excluiu)
		{
			$this->mensagem .= "Exclusão efetuada com sucesso.<br>";
			header("Location: educar_vps_idioma_lst.php");
			die();
			return true;
		}

		$this->mensagem = "Exclusão não realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarVPSIdioma\nvalores obrigatórios\nif(is_numeric($this->cod_vps_idioma) && is_numeric($this->pessoa_logada))\n-->";
		
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
