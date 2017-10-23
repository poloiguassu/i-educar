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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once("include/pmieducar/geral.inc.php");
require_once ("include/localizacaoSistema.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Modelo Contratação");
		$this->processoAp = "597";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $offset;

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

		$this->titulo = "Modelo Contratação - Listagem";

		foreach($_GET AS $var => $val) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ($val === "") ? null: $val;

		$this->addCabecalhos(
			array(
				"Modelo Contratação",
				"Instituição"
			)
		);

		// Filtros de Foreign Keys
		$get_cabecalho = "lista_busca";
		include("include/pmieducar/educar_campo_lista.php");

		// outros Filtros
		$this->campoTexto("nm_tipo", "Modelo Contratação", $this->nm_tipo, 30, 255, false);

		// Paginador
		$this->limite = 20;
		$this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite: 0;

		$obj_vps_tipo_contratacao = new clsPmieducarVPSContratacaoTipo();
		$obj_vps_tipo_contratacao->setOrderby("nm_tipo ASC");
		$obj_vps_tipo_contratacao->setLimite($this->limite, $this->offset);

		$lista = $obj_vps_tipo_contratacao->lista(
			null,
			$this->ref_cod_instituicao,
			null,
			null,
			$this->nm_tipo,
			null,
			null,
			null,
			null,
			null,
			1
		);

		$total = $obj_vps_tipo_contratacao->_total;

		// monta a lista
		if(is_array($lista) && count($lista))
		{
			foreach ($lista AS $registro)
			{
				$obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
				$det_instituicao = $obj_instituicao->detalhe();
				$registro['ref_cod_instituicao'] = $det_instituicao["nm_instituicao"];

				$this->addLinhas(array(
					"<a href=\"educar_vps_tipo_contratacao_det.php?cod_vps_tipo_contratacao={$registro["cod_vps_tipo_contratacao"]}\">{$registro["nm_tipo"]}</a>",
					"<a href=\"educar_vps_tipo_contratacao_det.php?cod_vps_tipo_contratacao={$registro["cod_vps_tipo_contratacao"]}\">{$registro["ref_cod_instituicao"]}</a>"
				));
			}
		}
		
		$this->addPaginador2("educar_vps_tipo_contratacao_lst.php", $total, $_GET, $this->nome, $this->limite);
		$obj_permissoes = new clsPermissoes();

		if($obj_permissoes->permissao_cadastra(597, $this->pessoa_logada, 11))
		{
			$this->acao = "go(\"educar_vps_tipo_contratacao_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME']."/intranet" => "Início",
			"educar_vps_index.php"              => "Trilha Jovem - VPS",
			""                                  => "Listagem de modelos de contratação"
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
