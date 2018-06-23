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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Ra&ccedil;a" );
        $this->processoAp = "678";
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

    var $cod_raca;
    var $idpes_exc;
    var $idpes_cad;
    var $nm_raca;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $pessoa_logada;

    function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = "Ra&ccedil;a - Detalhe";


        $this->cod_raca=$_GET["cod_raca"];

        $tmp_obj = new clsCadastroRaca( $this->cod_raca );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            header( "location: educar_raca_lst.php" );
            die();
        }

        /*if( class_exists( "clsCadastroFisica" ) )
        {
            $obj_idpes_exc = new clsCadastroFisica( $registro["idpes_exc"] );
            $det_idpes_exc = $obj_idpes_exc->detalhe();
            $registro["idpes_exc"] = $det_idpes_exc[""];
        }
        else
        {
            $registro["idpes_exc"] = "Erro na geracao";
            echo "<!--\nErro\nClasse nao existente: clsCadastroFisica\n-->";
        }
*/
		/*if( class_exists( "clsCadastroFisica" ) )
		{
			$obj_idpes_cad = new clsCadastroFisica( $registro["idpes_cad"] );
			$det_idpes_cad = $obj_idpes_cad->detalhe();
			$registro["idpes_cad"] = $det_idpes_cad[""];
		}
		else
		{
			$registro["idpes_cad"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsCadastroFisica\n-->";
		}*/


		/*if( $registro["cod_raca"] )
		{
			$this->addDetalhe( array( "Raca", "{$registro["cod_raca"]}") );
		}
		if( $registro["idpes_exc"] )
		{
			$this->addDetalhe( array( "Idpes Exc", "{$registro["idpes_exc"]}") );
		}
		if( $registro["idpes_cad"] )
		{
			$this->addDetalhe( array( "Idpes Cad", "{$registro["idpes_cad"]}") );
		}*/
		if( $registro["nm_raca"] )
		{
			$this->addDetalhe( array( "Ra&ccedil;a", "{$registro["nm_raca"]}") );
		}

		$obj_permissao = new clsPermissoes();
		if( $obj_permissao->permissao_cadastra(678, $this->pessoa_logada, 3) )
		{
			$this->url_novo = "educar_raca_cad.php";
			$this->url_editar = "educar_raca_cad.php?cod_raca={$registro["cod_raca"]}";
		}

		$this->url_cancelar = "educar_raca_lst.php";
		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
	    $localizacao->entradaCaminhos( array(
	         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
	         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
	         ""                                  => "Detalhe da ra&ccedil;a"
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