<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																		 *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com							 *
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
	header('Content-type: text/xml');

	require_once("include/clsBanco.inc.php");
	require_once("include/funcoes.inc.php");

	require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
	Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"colecoes\">\n";

	if(is_numeric($_GET["esc"]) && is_numeric($_GET["idpes"]))
	{
		$db = new clsBanco();
		$db->Consulta("
			SELECT
				cod_vps_responsavel_entrevista,
				nm_responsavel
			FROM
				pmieducar.vps_responsavel_entrevista
			WHERE
				ativo = 1
				AND ref_cod_escola = '{$_GET["esc"]}'
				AND ref_idpes = '{$_GET["idpes"]}'
			ORDER BY
				nm_responsavel ASC
		");

		if ($db->numLinhas())
		{
			while ($db->ProximoRegistro())
			{
				list($cod, $nome) = $db->Tupla();
				$nome = str_replace('&', 'e', $nome);
				echo "	<vps_responsavel_entrevista cod_vps_responsavel_entrevista=\"{$cod}\" >{$nome}</vps_responsavel_entrevista>\n";
			}
		}
	}
	echo "</query>";
?>
