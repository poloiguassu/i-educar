<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    *                                                                        *
    *   @author Prefeitura Municipal de Itajaí                               *
    *   @updated 29/03/2007                                                  *
    *   Pacote: i-PLB Software Público Livre e Brasileiro                    *
    *                                                                        *
    *   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí             *
    *                       ctima@itajai.sc.gov.br                           *
    *                                                                        *
    *   Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
    *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
    *   publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
    *   Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
    *                                                                        *
    *   Este programa  é distribuído na expectativa de ser útil, mas SEM     *
    *   QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
    *   ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
    *   sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
    *                                                                        *
    *   Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
    *   junto  com  este  programa. Se não, escreva para a Free Software     *
    *   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
    *   02111-1307, USA.                                                     *
    *                                                                        *
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmicontrolesis/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Telefones" );
        $this->processoAp = "611";
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

	var $cod_telefones;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $nome;
	var $ddd_numero;
	var $numero;
	var $responsavel;
	var $ddd_celular;
	var $celular;
	var $email;
	var $endereco;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

    function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

		$this->titulo = "Agenda Telefonica - Detalhe";
		

        $this->cod_telefones=$_GET["cod_telefones"];

        $tmp_obj = new clsPmicontrolesisTelefones( $this->cod_telefones );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            header( "location: controlesis_telefones_lst.php" );
            die();
        }




		if( $registro["nome"] )
		{
			$this->addDetalhe( array( "Institui��o", "{$registro["nome"]}") );
		}
		if( $registro["responsavel"] )
		{
			$this->addDetalhe( array( "Respons�vel", "{$registro["responsavel"]}") );
		}
		if( $registro["numero"] )
		{
			if($registro["ddd_numero"])
			{
				$this->addDetalhe(
					array('Telefone', sprintf('(%s) %s', $registro['ddd_numero'], $registro['numero']))
				);
			} else {
				$this->addDetalhe( array( "Telefone", "{$registro["numero"]}") );
			}
		}
		if( $registro["celular"] )
		{
			if($registro["ddd_celular"])
			{
				$this->addDetalhe(
					array('Celular', sprintf('(%s) %s', $registro['ddd_celular'], $registro['celular']))
				);
			} else {
				$this->addDetalhe( array( "Celular", "{$registro["celular"]}") );
			}
		}
		if( $registro["email"] )
		{
			$this->addDetalhe( array( "Email", "{$registro["email"]}") );
		}
		if( $registro["endereco"] )
		{
			$this->addDetalhe( array( "Endere�o", "{$registro["endereco"]}") );
		}

        $this->url_novo = "controlesis_telefones_cad.php";
        $this->url_editar = "controlesis_telefones_cad.php?cod_telefones={$registro["cod_telefones"]}";
        $this->url_cancelar = "controlesis_telefones_lst.php";
        $this->largura = "100%";
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
