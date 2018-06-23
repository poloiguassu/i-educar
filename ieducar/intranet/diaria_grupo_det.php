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
require_once ("include/clsDetalhe.inc.php");
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

class indice extends clsDetalhe
{
    function Gerar()
    {
        $this->titulo = "Detalhe do Grupo";
        

		$cod_diaria_grupo = @$_GET['cod_diaria_grupo'];
		
		$db = new clsBanco();
		$db2 = new clsBanco();
		$db->Consulta( "SELECT cod_diaria_grupo, desc_grupo FROM pmidrh.diaria_grupo WHERE cod_diaria_grupo='{$cod_diaria_grupo}'" );
		if( $db->ProximoRegistro() )
		{
			list( $cod_diaria_grupo, $desc_grupo ) = $db->Tupla();
			$this->addDetalhe( array("Grupo", $desc_grupo) );
		}
		else 
		{
			$this->addDetalhe( array( "Erro", "Codigo de di�ria grupo inv�lido" ) );
		}
		$this->url_editar = "diaria_grupo_cad.php?cod_diaria_grupo={$cod_diaria_grupo}";
		$this->url_novo = "diaria_grupo_cad.php";
		$this->url_cancelar = "diaria_grupo_lst.php";

        $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Detalhe do grupo de di&aacute;rias"
    ));
    $this->enviaLocalizacao($localizacao->montar());        
    }
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>