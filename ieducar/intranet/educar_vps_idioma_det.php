<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																		 *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com							 *
*																		 *
*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
*																		 *
*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
*	junto  com  este  programa. Se não, escreva para a Free Software	 *
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
		$this->SetTitulo("{$this->_instituicao} - Idiomas");
		$this->processoAp = "592";
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

	var $cod_vps_idioma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_idioma;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Idiomas - Detalhe";

		$this->cod_vps_idioma = $_GET["cod_vps_idioma"];

		$tmp_obj = new clsPmieducarVPSIdioma($this->cod_vps_idioma);
		$registro = $tmp_obj->detalhe();
		
		if(class_exists("clsPmieducarInstituicao"))
		{
			$obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
			$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
		} else {
			$registro["ref_cod_instituicao"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		if(!$registro)
		{
			header("location: educar_vps_idioma_lst.php");
			die();
		}

		if ($nivel_usuario == 1)
		{
			if($registro["ref_cod_instituicao"])
			{
				$this->addDetalhe(array("Instituição", "{$registro["ref_cod_instituicao"]}"));
			}
		}

		if($registro["nm_idioma"])
		{
			$this->addDetalhe(array("Idioma", "{$registro["nm_idioma"]}"));
		}

		$obj_permissoes = new clsPermissoes();
		if($obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11))
		{
			$this->url_novo = "educar_vps_idioma_cad.php";
			$this->url_editar = "educar_vps_idioma_cad.php?cod_vps_idioma={$registro["cod_vps_idioma"]}";
		}

		$this->url_cancelar = "educar_vps_idioma_lst.php";
		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			"educar_vps_index.php"                => "Trilha Jovem - VPS",
			""                                    => "Listagem de idiomas"
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
