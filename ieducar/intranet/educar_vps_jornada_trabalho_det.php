<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com					    	 *
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
		$this->SetTitulo("{$this->_instituicao} - Jornada de Trabalho");
		$this->processoAp = "590";
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

	var $cod_vps_jornada_trabalho;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_jornada_trabalho;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Jornada de Trabalho - Detalhe";
		

		$this->cod_vps_jornada_trabalho = $_GET["cod_vps_jornada_trabalho"];

		$tmp_obj = new clsPmieducarVPSJornadaTrabalho($this->cod_vps_jornada_trabalho);
		$registro = $tmp_obj->detalhe();

		if(! $registro)
		{
			header("location: educar_vps_jornada_trabalho_lst.php");
			die();
		}

		if($registro["cod_vps_jornada_trabalho"])
		{
			$this->addDetalhe(array("Código Jornada de Trabalho", "{$registro["cod_vps_jornada_trabalho"]}"));
		}
		if($registro["nm_jornada_trabalho"])
		{
			$this->addDetalhe(array("Jornada de Trabalho", "{$registro["nm_jornada_trabalho"]}"));
		}
		if($registro["carga_horaria_semana"])
		{
			$this->addDetalhe(array("Carga Horária Semanal", "{$registro["carga_horaria_semana"]} horas"));
		}
		if($registro["carga_horaria_diaria"])
		{
			$this->addDetalhe(array("Carga Horária Diária", "{$registro["nm_jornada_trabalho"]} horas"));
		}

		$obj_permissoes = new clsPermissoes();
		if($obj_permissoes->permissao_cadastra(590, $this->pessoa_logada, 11))
		{
		$this->url_novo = "educar_vps_jornada_trabalho_cad.php";
		$this->url_editar = "educar_vps_jornada_trabalho_cad.php?cod_vps_jornada_trabalho={$registro["cod_vps_jornada_trabalho"]}";
		}

		$this->url_cancelar = "educar_vps_jornada_trabalho_lst.php";
		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			"educar_vps_index.php"                => "Trilha Jovem - Jornada de Trabalho",
			""                                    => "Detalhe da Jornada de Trabalho"
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
