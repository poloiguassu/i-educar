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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");


class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Anexos" );
		$this->processoAp = "209";
	}
}

class indice extends clsCadastro
{
	var $cod_anexos_formacao;
	var $nm_anexo;
	var $descricao;
	var $caminho;
	var $tipo_arquivo;
	var $ref_ref_pessoa_fj;
	var $datahora;
	var $documento;

	function Inicializar()
	{
		@session_start();
		$id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();
		
		$retorno = "Novo";
		$this->ref_ref_pessoa_fj = $id_pessoa;
		if (@$_GET['cod_anexos_formacao'])
		{
			$this->cod_anexos_formacao = @$_GET['cod_anexos_formacao'];
			$db = new clsBanco();
			$db->Consulta( "SELECT nm_anexo, descricao, caminho, tipo_arquivo, data_hora FROM anexos_formacao WHERE cod_anexos_formacao = '{$this->cod_anexos_formacao}'" );
			if ($db->ProximoRegistro())
			{
				list( $this->nm_anexo, $this->descricao, $this->caminho, $this->tipo_arquivo, $this->datahora ) = $db->Tupla();
				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "anexos_formacao_det.php?cod_anexos_formacao=$this->cod_anexos_formacao" : "anexos_formacao_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		$objPessoa = new clsPessoaFisica();
		
		$db = new clsBanco();
		$this->campoOculto( "ref_ref_pessoa_fj", $this->ref_ref_pessoa_fj );
		$this->campoOculto( "cod_anexos_formacao", $this->cod_anexos_formacao );
		
		//$nome = $db->CampoUnico( "SELECT nm_pessoa FROM pessoa_fj WHERE cod_pessoa_fj = '{$this->ref_ref_pessoa_fj}'" );
		list($nome) = $objPessoa->queryRapida($this->ref_ref_pessoa_fj, "nome");
		$this->campoRotulo( "pessoa", "Respons�vel", $nome );
		$this->campoTexto( "nm_anexo", "T�tulo", $this->nm_anexo, "50", "100", true );
		$this->campoMemo( "descricao", "Descri��o",  $this->descricao, "50", "4", false );
		
		$this->campoArquivo( "documento", "Documento", $this->documento, "50");
		
	}

	function Novo() 
	{
		global $_FILES;
		if ( !empty($_FILES['documento']['name']) )
		{
			$tipos = array();
			$tipos["pdf"] = true;
			$tipos["zip"] = true;
			$tipos["doc"] = true;
			
			$arquivoOriginal = "tmp/".$_FILES['documento']['name'];
			if (file_exists($arquivoOriginal))
			{
				@unlink($arquivoOriginal);
			}
			copy($_FILES['documento']['tmp_name'], $arquivoOriginal);
			$this->tipo_arquivo = substr( $_FILES['documento']['name'], -3 );
			if( isset( $tipos[$this->tipo_arquivo] ) )
			{
				$this->caminho = date('Y-m-d')."-".substr(md5($arquivoOriginal), 0, 10). "." . $this->tipo_arquivo;
				$caminho = "arquivos/AnexosFormacao/" . $this->caminho;
			
				if ( !file_exists($this->caminho) )
				{
					copy ($arquivoOriginal, $caminho);
				}
				if( ! file_exists( $caminho ) )
				{
					$this->mensagem = "Um erro ocorreu ao inserir o documento.<br>";
				}
				else 
				{
					@session_start();
					$this->ref_ref_pessoa_fj = @$_SESSION['id_pessoa'];
					session_write_close();
			
					$db = new clsBanco();
					$db->Consulta( "INSERT INTO anexos_formacao( ref_ref_cod_pessoa_fj, nm_anexo, descricao, caminho, tipo_arquivo, data_hora ) VALUES( '{$this->ref_ref_pessoa_fj}', '{$this->nm_anexo}', '{$this->descricao}', '{$this->caminho}', '{$this->tipo_arquivo}', NOW() )" );
					die( "<script>document.location.href='anexos_formacao_lst.php';</script>" );
					return true;
				}
			}
			else 
			{
				$this->mensagem .= "Tipo de arquivo nao reconhecido, Apenas .doc .zip e .pdf s�o aceitos.<br>";
			}
		}
		return false;
	}

	function Editar() 
	{
		$db = new clsBanco();
		$db->Consulta( "UPDATE anexos_formacao SET ref_ref_cod_pessoa_fj='{$this->ref_ref_pessoa_fj}', descricao='{$this->descricao}', nm_anexo = '{$this->nm_anexo}', data_hora=NOW() WHERE cod_anexos_formacao='{$this->cod_anexos_formacao}'" );

		echo "<script>document.location='anexos_formacao_lst.php';</script>";

		return true;
	}

	function Excluir()
	{
		{
			$db = new clsBanco();
			$caminho = $db->CampoUnico("SELECT caminho FROM anexos_formacao WHERE cod_anexos_formacao = {$this->cod_anexos_formacao}");
			$db->Consulta( "DELETE FROM anexos_formacao WHERE cod_anexos_formacao = {$this->cod_anexos_formacao}" );
			@unlink("arquivos/AnexosFormacao/{$caminho}");
			
			echo "<script>document.location='anexos_formacao_lst.php';</script>";			
		}
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
