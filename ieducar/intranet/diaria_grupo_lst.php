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
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Di�ria Grupo" );
		$this->processoAp = "297";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Di�ria Grupo";
		
	
		$this->addCabecalhos( array( "Grupo" ) );
		
		
		$where = "";
		$gruda = "";

        $db = new clsBanco();
        $db2 = new clsBanco();
        $total = $db->UnicoCampo( "SELECT count(0) FROM pmidrh.diaria_grupo $where" );
        
        // Paginador
        $limite = 20;
        $iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
        
        $objPessoa = new clsPessoaFisica();
        
        $sql = "SELECT cod_diaria_grupo, desc_grupo FROM pmidrh.diaria_grupo $where ORDER BY desc_grupo ASC";
        $db->Consulta( $sql );
        while ( $db->ProximoRegistro() )
        {
            list ( $cod_diaria_grupo, $desc_grupo ) = $db->Tupla();
        
            $this->addLinhas( array( 
            "<a href='diaria_grupo_det.php?cod_diaria_grupo={$cod_diaria_grupo}'><img src='imagens/noticia.jpg' border=0>$desc_grupo</a>"));
        }
        
        // Paginador
        $this->addPaginador2( "diaria_grupo_lst.php", $total, $_GET, $this->nome, $limite );
        
        $this->acao = "go(\"diaria_grupo_cad.php\")";
        $this->nome_acao = "Novo";

        $this->largura = "100%";

	    $localizacao = new LocalizacaoSistema();
	    $localizacao->entradaCaminhos( array(
	         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
	         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
	         ""                                  => "Listagem de grupos de di&aacute;rias"
	    ));
	    $this->enviaLocalizacao($localizacao->montar());
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>