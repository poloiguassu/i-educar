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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Cliente " );
        $this->processoAp = "596";
        $this->addEstilo('localizacaoSistema');
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

    var $cod_cliente_tipo;
    var $ref_cod_biblioteca;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = "Tipo Cliente - Detalhe";
        

        $this->cod_cliente_tipo=$_GET["cod_cliente_tipo"];

        $tmp_obj = new clsPmieducarClienteTipo( $this->cod_cliente_tipo );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            header( "location: educar_cliente_tipo_lst.php" );
            die();
        }

        if( class_exists( "clsPmieducarBiblioteca" ) )
        {
            $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
            $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
            $registro["ref_cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];
            $registro["ref_cod_instituicao"] = $det_ref_cod_biblioteca["ref_cod_instituicao"];
            $registro["ref_cod_escola"] = $det_ref_cod_biblioteca["ref_cod_escola"];
            if( $registro["ref_cod_instituicao"] )
            {
                $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
            }
            if( $registro["ref_cod_escola"] )
            {
                $obj_ref_cod_escola = new clsPmieducarEscola();
                $det_ref_cod_escola = array_shift($obj_ref_cod_escola->lista($registro["ref_cod_escola"]));
                $registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];
            }
        }
        else
        {
            $registro["ref_cod_biblioteca"] = "Erro na gera&ccedil;&atilde;o";
            echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarBiblioteca\n-->";
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if( $registro["ref_cod_instituicao"] && $nivel_usuario == 1)
        {
            $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
        }
        if( $registro["ref_cod_escola"] && ($nivel_usuario == 1 || $nivel_usuario == 2) )
        {
            $this->addDetalhe( array( "Escola", "{$registro["ref_cod_escola"]}") );
        }
        if( $registro["ref_cod_biblioteca"] && ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4))
        {
            $this->addDetalhe( array( "Biblioteca", "{$registro["ref_cod_biblioteca"]}") );
        }
        if( $registro["nm_tipo"] )
        {
            $this->addDetalhe( array( "Tipo Cliente", "{$registro["nm_tipo"]}") );
        }
        if( $registro["descricao"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }

        $obj_cliente_tp_exemplar_tp = new clsPmieducarClienteTipoExemplarTipo();
        $lst_cliente_tp_exemplar_tp = $obj_cliente_tp_exemplar_tp->lista( $this->cod_cliente_tipo );
        if ($lst_cliente_tp_exemplar_tp)
        {
            $tabela = "<TABLE>
                           <TR align=center>
                               <TD bgcolor=#ccdce6><B>Tipo Exemplar</B></TD>
                               <TD bgcolor=#ccdce6><B>Dias Empr&eacute;stimo</B></TD>
                           </TR>";
            $cont = 0;

            foreach ( $lst_cliente_tp_exemplar_tp AS $valor )
            {
                if ( ($cont % 2) == 0 )
                {
                    $color = " bgcolor=#f5f9fd ";
                }
                else
                {
                    $color = " bgcolor=#FFFFFF ";
                }
                $obj_exemplar_tipo = new clsPmieducarExemplarTipo( $valor["ref_cod_exemplar_tipo"] );
                $det_exemplar_tipo = $obj_exemplar_tipo->detalhe();
                $nm_tipo = $det_exemplar_tipo["nm_tipo"];

                $tabela .= "<TR>
                                <TD {$color} align=left>{$nm_tipo}</TD>
                                <TD {$color} align=left>{$valor["dias_emprestimo"]}</TD>
                            </TR>";
                $cont++;
            }
            $tabela .= "</TABLE>";
        }
        if( $tabela )
        {
            $this->addDetalhe( array( "Tipo Exemplar", "{$tabela}") );
        }

        if( $obj_permissoes->permissao_cadastra( 596, $this->pessoa_logada, 11 ) )
        {
            $this->url_novo = "educar_cliente_tipo_cad.php";
            $this->url_editar = "educar_cliente_tipo_cad.php?cod_cliente_tipo={$registro["cod_cliente_tipo"]}";
        }

        $this->url_cancelar = "educar_cliente_tipo_lst.php";
        $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_biblioteca_index.php"                  => "Trilha Jovem Iguassu - Biblioteca",
         ""                                  => "Detalhe do tipo de clientes"
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