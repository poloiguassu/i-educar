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
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once("include/pmieducar/geral.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Modelo Contrata��o");
		$this->processoAp = "597";
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

	var $cod_vps_tipo_contratacao;
	var $ref_cod_instituicao;
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

		$this->titulo = "Modelo Contrata��o - Detalhe";

		$this->cod_vps_tipo_contratacao = $_GET["cod_vps_tipo_contratacao"];

		$tmp_obj = new clsPmieducarVPSContratacaoTipo($this->cod_vps_tipo_contratacao);
		$registro = $tmp_obj->detalhe();

		if(!$registro)
		{
			header("location: educar_vps_tipo_contratacao_lst.php");
			die();
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		if($registro["ref_cod_instituicao"] && $nivel_usuario == 1)
		{
			$obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
			$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];

			$this->addDetalhe(array("Institui��o", "{$registro["ref_cod_instituicao"]}"));
		}
		if($registro["nm_tipo"])
		{
			$this->addDetalhe(array("Modelo Contrata��o", "{$registro["nm_tipo"]}"));
		}
		if($registro["descricao"])
		{
			$this->addDetalhe(array("Descri��o", "{$registro["descricao"]}"));
		}

		$obj_permissoes = new clsPermissoes();
		if($obj_permissoes->permissao_cadastra(597, $this->pessoa_logada, 11))
		{
			$this->url_novo = "educar_vps_tipo_contratacao_cad.php";
			$this->url_editar = "educar_vps_tipo_contratacao_cad.php?cod_vps_tipo_contratacao={$registro["cod_vps_tipo_contratacao"]}";
		}

		$this->url_cancelar = "educar_vps_tipo_contratacao_lst.php";
		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME']."/intranet" => "In�cio",
			"educar_vps_index.php"              => "Trilha Jovem - VPS",
			""                                  => "Detalhe da modalidade de contrata��o"
		));
		
		$this->enviaLocalizacao($localizacao->montar());		
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm($miolo);
// gera o html
$pagina->MakeAll();
?>
