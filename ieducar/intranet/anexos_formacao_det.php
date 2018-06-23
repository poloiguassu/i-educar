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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Publicações!" );
        $this->processoAp = "209";
    }
}

class indice extends clsDetalhe
{
    function Gerar()
    {
        $this->titulo = "Detalhe de concurso";
        $this->addBanner( "/intranet/imagens/nvp_top_intranet.jpg", "/intranet/imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_anexos_formacao = @$_GET['cod_anexos_formacao'];

        $objPessoa = new clsPessoaFisica();

		$db = new clsBanco();
		$db->Consulta( "SELECT nm_anexo, descricao, data_hora, ref_ref_cod_pessoa_fj, caminho, tipo_arquivo FROM anexos_formacao WHERE cod_anexos_formacao = '{$cod_anexos_formacao}'" );
		if ($db->ProximoRegistro())
		{
			list ( $nome, $descricao, $data, $pessoa, $caminho, $tipo ) = $db->Tupla();
			//$pessoa = $db->CampoUnico( "SELECT nm_pessoa FROM pessoa_fj WHERE cod_pessoa_fj = '$pessoa'" );
			list($pessoa) = $objPessoa->queryRapida($pessoa, "nome");

			$this->addDetalhe( array("Respons�vel", $pessoa ) );
			$this->addDetalhe( array("Data", date( "d/m/Y H:i", strtotime(substr( $data,0,19) ) ) ) );
			$this->addDetalhe( array("Nome", $nome) );
			$this->addDetalhe( array("Descri��o", $descricao) );
			$this->addDetalhe( array("Arquivo", "<a href='arquivos/AnexosFormacao/$caminho' target='_self' '><img src='/intranet/imagens/nvp_icon_{$tipo}.gif' border='0'></a>") );
		}
		$this->url_novo = "anexos_formacao_cad.php";
		$this->url_editar = "anexos_formacao_cad.php?cod_anexos_formacao=$cod_anexos_formacao";
		$this->url_cancelar = "anexos_formacao_lst.php";

        $this->largura = "100%";
    }
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
