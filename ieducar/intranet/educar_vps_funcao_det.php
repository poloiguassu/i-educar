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
		$this->SetTitulo("{$this->_instituicao} - Fun��o");
		$this->processoAp = "593";
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

	var $cod_vps_funcao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_funcao;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Fun��o - Detalhe";


		$this->cod_vps_funcao=$_GET["cod_vps_funcao"];

		$tmp_obj = new clsPmieducarVPSFuncao($this->cod_vps_funcao);
		$registro = $tmp_obj->detalhe();

		if(class_exists("clsPmieducarEscola"))
		{
			$obj_ref_cod_escola = new clsPmieducarEscola($registro["ref_cod_escola"]);
			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
			$idpes = $det_ref_cod_escola["ref_idpes"];

			if(class_exists("clsPmieducarInstituicao"))
			{
				$registro["ref_cod_instituicao"] = $det_ref_cod_escola["ref_cod_instituicao"];
				$obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
				$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
				$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
			}
			else
			{
				$registro["ref_cod_instituicao"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
			}

			if($idpes)
			{
				$obj_escola = new clsPessoaJuridica($idpes);
				$obj_escola_det = $obj_escola->detalhe();
				$registro["ref_cod_escola"] = $obj_escola_det["fantasia"];
			}
			else
			{
				$obj_escola = new clsPmieducarEscolaComplemento($registro["ref_cod_escola"]);
				$obj_escola_det = $obj_escola->detalhe();
				$registro["ref_cod_escola"] = $obj_escola_det["nm_escola"];
			}
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		if(!$registro)
		{
			header("location: educar_vps_funcao_lst.php");
			die();
		}

		if($nivel_usuario == 1 || $nivel_usuario == 2)
		{
			if($registro["ref_cod_escola"])
			{
				$this->addDetalhe(array("Institui��o", "{$registro["ref_cod_escola"]}"));
			}
		}

		if($registro["cod_vps_funcao"])
		{
			$this->addDetalhe(array("C�digo da Fun��o", "{$registro["cod_vps_funcao"]}"));
		}
		if($registro["nm_funcao"])
		{
			$this->addDetalhe(array("Fun��o", "{$registro["nm_funcao"]}"));
		}
		if($registro["descricao"])
		{
			$this->addDetalhe(array("Descri��o", "{$registro["descricao"]}"));
		}

		$obj_permissoes = new clsPermissoes();

		if($obj_permissoes->permissao_cadastra(593, $this->pessoa_logada, 11))
		{
			$this->url_novo = "educar_vps_funcao_cad.php";
			$this->url_editar = "educar_vps_funcao_cad.php?cod_vps_funcao={$registro["cod_vps_funcao"]}";
		}

		$this->url_cancelar = "educar_vps_funcao_lst.php";
		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			 $_SERVER['SERVER_NAME'] . "/intranet" => "In�cio",
			 "educar_vps_index.php"                => "Trilha Jovem - VPS",
			 ""                                    => "Detalhe da Fun��o"
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
