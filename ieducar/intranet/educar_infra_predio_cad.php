<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    *                                                                        *
    *   @author Prefeitura Municipal de ItajaÃ­                               *
    *   @updated 29/03/2007                                                  *
    *   Pacote: i-PLB Software PÃºblico Livre e Brasileiro                    *
    *                                                                        *
    *   Copyright (C) 2006  PMI - Prefeitura Municipal de ItajaÃ­             *
    *                       ctima@itajai.sc.gov.br                           *
    *                                                                        *
    *   Este  programa  Ã©  software livre, vocÃª pode redistribuÃ­-lo e/ou     *
    *   modificÃ¡-lo sob os termos da LicenÃ§a PÃºblica Geral GNU, conforme     *
    *   publicada pela Free  Software  Foundation,  tanto  a versÃ£o 2 da     *
    *   LicenÃ§a   como  (a  seu  critÃ©rio)  qualquer  versÃ£o  mais  nova.    *
    *                                                                        *
    *   Este programa  Ã© distribuÃ­do na expectativa de ser Ãºtil, mas SEM     *
    *   QUALQUER GARANTIA. Sem mesmo a garantia implÃ­cita de COMERCIALI-     *
    *   ZAÃ‡ÃƒO  ou  de ADEQUAÃ‡ÃƒO A QUALQUER PROPÃ“SITO EM PARTICULAR. Con-     *
    *   sulte  a  LicenÃ§a  PÃºblica  Geral  GNU para obter mais detalhes.     *
    *                                                                        *
    *   VocÃª  deve  ter  recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral GNU     *
    *   junto  com  este  programa. Se nÃ£o, escreva para a Free Software     *
    *   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
    *   02111-1307, USA.                                                     *
    *                                                                        *
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once ("include/modules/clsModulesAuditoriaGeral.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Infra Predio" );
        $this->processoAp = "567";
        $this->addEstilo("localizacaoSistema");
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

	var $cod_infra_predio;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_escola;
	var $nm_predio;
	var $desc_predio;
	var $endereco;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_infra_predio=$_GET["cod_infra_predio"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 567, $this->pessoa_logada,7, "educar_infra_predio_lst.php" );

		if( is_numeric( $this->cod_infra_predio ) )
		{

			$obj = new clsPmieducarInfraPredio( $this->cod_infra_predio );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;


				//** verificao de permissao para exclusao
				$this->fexcluir = $obj_permissoes->permissao_excluir(567,$this->pessoa_logada,7);
				//**
				$retorno = "Editar";
			}
			else
			{
				header( "Location: educar_infra_predio_lst.php" );
				die();
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}" : "educar_infra_predio_lst.php";

		$nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
	    $localizacao = new LocalizacaoSistema();
	    $localizacao->entradaCaminhos( array(
	         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
	         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
	         ""        => "{$nomeMenu} pr&eacute;dio"
	    ));
	    $this->enviaLocalizacao($localizacao->montar());

		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		// primary keys
		$this->campoOculto( "cod_infra_predio", $this->cod_infra_predio );

		// Sem o id da instituição ele não preenche os campos de instituição e escola
		$escola = new clsPmieducarEscola($this->ref_cod_escola);
		$escola_data = $escola->detalhe();
		$this->ref_cod_instituicao = $escola_data['ref_cod_instituicao'];

		$obrigatorio = true;
		$get_escola	 = true;
		include("include/pmieducar/educar_campo_lista.php");

		$this->campoTexto( "nm_predio", "Nome Prédio", $this->nm_predio, 30, 255, true );
		$this->campoMemo( "desc_predio", "Descrição Prédio", $this->desc_predio, 60, 10, false );
		$this->campoMemo( "endereco", "Endereço", $this->endereco, 60, 2, true );




	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarInfraPredio( $this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, null, null, 1 );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_infra_predio_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarInfraPredio\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_escola ) && is_string( $this->nm_predio ) && is_string( $this->endereco )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarInfraPredio($this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, null,null, 1);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edição efetuada com sucesso.<br>";
			header( "Location: educar_infra_predio_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edição não realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarInfraPredio\nvalores obrigatorios\nif( is_numeric( $this->cod_infra_predio ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarInfraPredio($this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, $this->data_cadastro, $this->data_exclusao, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclusão efetuada com sucesso.<br>";
			header( "Location: educar_infra_predio_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclusão não realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarInfraPredio\nvalores obrigatorios\nif( is_numeric( $this->cod_infra_predio ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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
