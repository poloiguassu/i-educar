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
require_once("include/clsBase.inc.php");
require_once("include/clsDetalhe.inc.php");
require_once("include/clsBanco.inc.php");
require_once("include/pmieducar/geral.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Respons�vel Entrevista");
		$this->processoAp = "594";
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

	var $cod_vps_responsavel_entrevista;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_responsavel;
	var $ddd_telefone_com;
	var $telefone_com;
	var $ddd_telefone_cel;
	var $telefone_cel;
	var $observacao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Respons�vel Entrevista - Detalhe";

		$this->cod_vps_responsavel_entrevista = $_GET["cod_vps_responsavel_entrevista"];

		$tmp_obj = new clsPmieducarVPSResponsavelEntrevista($this->cod_vps_responsavel_entrevista);
		$registro = $tmp_obj->detalhe();

		if(!$registro)
		{
			header("location: educar_vps_responsavel_entrevista_lst.php");
			die();
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		if(class_exists("clsPmieducarEscola"))
		{
			$obj_ref_cod_escola = new clsPmieducarEscola($registro["ref_cod_escola"]);
			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
			$registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];
			$registro["ref_cod_instituicao"] = $det_ref_cod_escola["ref_cod_instituicao"];

			if($registro["ref_cod_instituicao"])
			{
				$obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
				$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
				$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
			}
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na gera��o";
			echo "<!--\nErro\nClasse n�o existente: clsPmieducarEscola\n-->";
		}

		if($registro["ref_cod_escola"] && ($nivel_usuario == 1 || $nivel_usuario == 2))
		{
			$this->addDetalhe(array("Institui��o", "{$registro["ref_cod_escola"]}"));
		}
		if($registro["nm_responsavel"])
		{
			$this->addDetalhe(array("Respons�vel", "{$registro["nm_responsavel"]}"));
		}
		if($registro["ref_idpes"] && class_exists("clsPessoaJuridica"))
		{
			$obj_idpes = new clsPessoaJuridica($registro['ref_idpes']);
			$det_idpes = $obj_idpes->detalhe();
			$registro["ref_idpes"] = $det_idpes["fantasia"];
			$this->addDetalhe(array("Empresa", "{$registro["ref_idpes"]}"));
		}
		if($registro["email"])
		{
			$this->addDetalhe(array("E-mail", "{$registro["email"]}"));
		}
		if($registro["ddd_telefone_com"] && $registro["telefone_com"])
		{
			$this->addDetalhe(array("Telefone comercial", "({$registro["ddd_telefone_com"]}) {$registro["telefone_com"]}"));
		}
		if($registro["ddd_telefone_cel"] && $registro["telefone_cel"])
		{
			$this->addDetalhe(array("Telefone celular", "({$registro["ddd_telefone_cel"]}) {$registro["telefone_cel"]}"));
		}
		if($registro["observacao"])
		{
			$this->addDetalhe(array("Observa��o", "{$registro["observacao"]}"));
		}

		$obj_permissoes = new clsPermissoes();

		if($obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11))
		{
			$this->url_novo = "educar_vps_responsavel_entrevista_cad.php";
			$this->url_editar = "educar_vps_responsavel_entrevista_cad.php?cod_vps_responsavel_entrevista={$registro["cod_vps_responsavel_entrevista"]}";
		}

		$this->url_cancelar = "educar_vps_responsavel_entrevista_lst.php";
		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "In�cio",
			"educar_vps_index.php"                => "Trilha Jovem - VPS",
			""                                    => "Detalhe do respons�vel entrevista"
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
