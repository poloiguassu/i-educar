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
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Anexos" );
		$this->processoAp = "209";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Anexos";
		$this->addBanner( "/intranet/imagens/nvp_top_intranet.jpg", "/intranet/imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array( "Nome", "Descri��o" ) );

        $db = new clsBanco();
        $dba = new clsBanco();

        // Paginador
        $this->limite = 10;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;


		$total = $dba->UnicoCampo( "SELECT count(0) FROM anexos_formacao" );

		$db->Consulta( "SELECT cod_anexos_formacao, nm_anexo, descricao FROM anexos_formacao ORDER BY data_hora DESC limit $this->limite offset $this->offset " );
		
		while ($db->ProximoRegistro())
		{
			list ( $cod, $nm_concurso, $descricao ) = $db->Tupla();
			$this->addLinhas( array( "<a href='anexos_formacao_det.php?cod_anexos_formacao={$cod}'><img src='imagens/noticia.jpg' border=0>$nm_concurso</a>", $descricao ) );
		}

		$this->addPaginador2( "anexos_formacao_lst.php", $total, $_GET, $this->nome, $this->limite );

		$this->acao = "go(\"anexos_formacao_cad.php\")";
		$this->nome_acao = "Novo";

        $this->largura = "100%";
    }
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
