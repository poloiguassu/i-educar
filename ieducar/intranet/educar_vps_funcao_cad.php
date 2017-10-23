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
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} - Função" );
		$this->processoAp = "593";
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

		$this->cod_vps_funcao = $_GET["cod_vps_funcao"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 593, $this->pessoa_logada, 11,  "educar_vps_funcao_lst.php" );
		
		if( is_numeric( $this->cod_vps_funcao ) )
		{

			$obj = new clsPmieducarVPSFuncao( $this->cod_vps_funcao );
			$registro = $obj->detalhe();
			
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_escola = new clsPmieducarEscola($registro["ref_cod_escola"]);
				$obj_det = $obj_escola->detalhe();
				
				$this->ref_cod_instituicao = $obj_det["ref_cod_instituicao"];
				$this->ref_cod_escola = $obj_det["cod_escola"];

				$obj_permissoes = new clsPermissoes();

				if( $obj_permissoes->permissao_excluir( 593, $this->pessoa_logada, 11 ) )
				{
					$this->fexcluir = true;
				}

				$retorno = "Editar";
			}
		}
		
		$this->url_cancelar = ($retorno == "Editar") ? "educar_vps_funcao_det.php?cod_vps_funcao={$registro["cod_vps_funcao"]}" : "educar_vps_funcao_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		$nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos( array(
			 $_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			 "educar_vps_index.php"                => "Trilha Jovem - VPS",
			 ""                                    => "{$nomeMenu} Função"
		));
		
		$this->enviaLocalizacao($localizacao->montar());

		return $retorno;
	}

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		// primary keys
		$this->campoOculto( "cod_vps_funcao", $this->cod_vps_funcao );

		// foreign keys
		
		$get_escola = true;
		$escola_obrigatorio = true;
		$instituicao_obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");

		// text
		$this->campoTexto( "nm_funcao", "Função", $this->nm_funcao, 30, 255, true );
		$this->campoMemo( "descricao", "Descrição", $this->descricao, 60, 5, false );

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 593, $this->pessoa_logada, 11,  "educar_vps_funcao_lst.php" );


		$obj = new clsPmieducarVPSFuncao( $this->cod_vps_funcao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_funcao, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_escola );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_vps_funcao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarVPSFuncao\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_string( $this->nm_funcao )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 593, $this->pessoa_logada, 11,  "educar_vps_funcao_lst.php" );


		$obj = new clsPmieducarVPSFuncao($this->cod_vps_funcao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_funcao, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_escola);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edição efetuada com sucesso.<br>";
			header( "Location: educar_vps_funcao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edição não realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarVPSFuncao\nvalores obrigatorios\nif( is_numeric( $this->cod_vps_funcao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 593, $this->pessoa_logada, 11,  "educar_vps_funcao_lst.php" );


		$obj = new clsPmieducarVPSFuncao($this->cod_vps_funcao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_funcao, $this->descricao, $this->data_cadastro, $this->data_exclusao, 0);
		$excluiu = $obj->excluir();
		
		if( $excluiu )
		{
			$this->mensagem .= "Exclusão efetuada com sucesso.<br>";
			header( "Location: educar_vps_funcao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclusão não realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarVPSFuncao\nvalores obrigatorios\nif( is_numeric( $this->cod_vps_funcao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
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
