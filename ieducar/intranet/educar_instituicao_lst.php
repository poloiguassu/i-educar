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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Instituicao" );
        $this->processoAp = "559";
        $this->addEstilo("localizacaoSistema");
    }
}

class indice extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $offset;

    var $cod_instituicao;
    var $nm_instituicao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_idtlog;
    var $ref_sigla_uf;
    var $cep;
    var $cidade;
    var $bairro;
    var $logradouro;
    var $numero;
    var $complemento;
    var $nm_responsavel;
    var $ddd_telefone;
    var $telefone;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = "Institui&ccedil;&atilde;o - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;


        $this->addCabecalhos( array( "Nome da Institui&ccedil;&atilde;o" ) );

        // outros Filtros
        $this->campoTexto( "nm_instituicao", "Nome da Institui&ccedil;&atilde;o", $this->nm_instituicao, 30, 255, false );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_instituicao = new clsPmieducarInstituicao();
        $obj_instituicao->setOrderby( "nm_responsavel ASC" );
        $obj_instituicao->setLimite( $this->limite, $this->offset );
        $lista = $obj_instituicao->lista(
            $this->cod_instituicao,
            $this->ref_sigla_uf,
            $this->cep,
            $this->cidade,
            $this->bairro,
            $this->logradouro,
            $this->numero,
            $this->complemento,
            $this->nm_responsavel,
            $this->ddd_telefone,
            $this->telefone,
            $this->data_cadastro,
            $this->data_exclusao,
            1,
            $this->nm_instituicao
        );

        $total = $obj_instituicao->_total;

        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $this->addLinhas( array(
                    "<a href=\"educar_instituicao_det.php?cod_instituicao={$registro["cod_instituicao"]}\">{$registro["nm_instituicao"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_instituicao_lst.php", $total, $_GET, $this->nome, $this->limite );

        $obj_permissoes = new clsPermissoes();

        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
             ""                                  => "Listagem de institui&ccedil;&otilde;es"
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