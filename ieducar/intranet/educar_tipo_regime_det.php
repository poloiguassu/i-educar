<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Regime" );
        $this->processoAp = "568";
        $this->addEstilo("localizacaoSistema");
    }
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $cod_tipo_regime;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Tipo Regime - Detalhe";
		

		$this->cod_tipo_regime=$_GET["cod_tipo_regime"];

		$tmp_obj = new clsPmieducarTipoRegime( $this->cod_tipo_regime );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_tipo_regime_lst.php" );
			die();
		}


		if( $registro["cod_tipo_regime"] )
		{
			$this->addDetalhe( array( "Tipo Regime", "{$registro["cod_tipo_regime"]}") );
		}
		if( $registro["ref_cod_instituicao"] )
		{
			if( class_exists( "clsPmieducarInstituicao" ) )
			{
				$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
				$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
				$registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];
			}
			else
			{
				$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
				echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
			}
			$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
		}
		if( $registro["nm_tipo"] )
		{
			$this->addDetalhe( array( "Nome Tipo", "{$registro["nm_tipo"]}") );
		}


		$this->url_cancelar = "educar_tipo_regime_lst.php";

		//** Verificacao de permissao para cadastro
		$obj_permissao = new clsPermissoes();

		if($obj_permissao->permissao_cadastra(568, $this->pessoa_logada,3))
		{
			$this->url_novo = "educar_tipo_regime_cad.php";
			$this->url_editar = "educar_tipo_regime_cad.php?cod_tipo_regime={$registro["cod_tipo_regime"]}";
		}
		//**
		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
	    $localizacao->entradaCaminhos( array(
	         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
	         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
	         ""        => "Detalhe do tipo de regime"
	    ));
	    $this->enviaLocalizacao($localizacao->montar());
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