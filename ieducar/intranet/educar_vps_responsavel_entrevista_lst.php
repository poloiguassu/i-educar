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
require_once("include/clsBase.inc.php");
require_once("include/clsListagem.inc.php");
require_once("include/clsBanco.inc.php");
require_once("include/pmieducar/geral.inc.php");
require_once("include/localizacaoSistema.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Responsável Entrevista");
		$this->processoAp = "594";
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

	var $cod_vps_responsavel_entrevista;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_responsavel;
	var $email;
	var $telefone;
	var $observacao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $empresa_id;
	var $ref_cod_escola;
	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Responsável Entrevista - Listagem";
		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		foreach($_GET AS $var => $val) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ($val === "") ? null: $val;

		// Filtros de Foreign Keys
		$get_escola = false;
		$get_cabecalho = "lista_busca";
		include("include/pmieducar/educar_campo_lista.php");

		$this->addCabecalhos(array(
			"Responsável",
			"Empresa",
			"Telefone Comercial",
			"Telefone Celular",
			"E-mail",
		));

		$helperOptions = array(
			'objectName'         => 'empresa',
			'hiddenInputOptions' => array('options' => array('value' => $this->empresa_id))
		);

		$options = array('label' => "Empresa", 'required' => true, 'size' => 30);

		$this->inputsHelper()->simpleSearchPessoaj('nome', $options, $helperOptions);

		$this->campoTexto("nm_responsavel", "Responsável", $this->nm_responsavel, 30, 255, false);

		$this->campoTexto("email", "E-mail", $this->email, 30, 255, false);

		$this->campoTexto("telefone", "Telefone s/ ddd", $this->telefone, 30, 255, false);

		// Paginador
		$this->limite = 20;
		$this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_vps_responsavel_entrevista = new clsPmieducarVPSResponsavelEntrevista();
		$obj_vps_responsavel_entrevista->setOrderby("nm_responsavel ASC");
		$obj_vps_responsavel_entrevista->setLimite($this->limite, $this->offset);


		$lista = $obj_vps_responsavel_entrevista->lista(
			null,
			null,
			null,
			$this->nm_responsavel,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_biblioteca,
			$this->empresa_id,
			$this->ref_cod_instituicao,
			$this->email,
			$this->telefone
		);


		$total = $obj_vps_responsavel_entrevista->_total;

		// monta a lista
		if(is_array($lista) && count($lista))
		{
			foreach ($lista AS $registro)
			{
				$obj_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
				$det_escola = $obj_escola->detalhe();
				$registro["ref_cod_escola"] = $det_escola["nome"];

				if(class_exists("clsPessoaJuridica"))
				{
					$obj_idpes = new clsPessoaJuridica($registro['ref_idpes']);
					$det_idpes = $obj_idpes->detalhe();
					$registro["ref_idpes"] = $det_idpes["fantasia"];
				}
				else
				{
					$registro["ref_cod_escola"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPessoaJuridica\n-->";
				}

				if($registro["ddd_telefone_com"] && $registro["telefone_com"])
				{
					$registro["telefone_com"] = "{$registro["ddd_telefone_com"]}) {$registro["telefone_com"]}";
				}
				if($registro["ddd_telefone_cel"] && $registro["telefone_cel"])
				{
					$registro["telefone_cel"] = "({$registro["ddd_telefone_cel"]}) {$registro["telefone_cel"]}";
				}
				if($registro["email"] && $registro["email"])
				{
					$registro["email"] = "{$registro["email"]}";
				}

				$this->addLinhas(array(
					"<a href=\"educar_vps_responsavel_entrevista_det.php?cod_vps_responsavel_entrevista={$registro["cod_vps_responsavel_entrevista"]}\">{$registro["nm_responsavel"]}</a>",
					"<a href=\"educar_vps_responsavel_entrevista_det.php?cod_vps_responsavel_entrevista={$registro["cod_vps_responsavel_entrevista"]}\">{$registro["ref_idpes"]}</a>",
					"<a href=\"educar_vps_responsavel_entrevista_det.php?cod_vps_responsavel_entrevista={$registro["cod_vps_responsavel_entrevista"]}\">{$registro["telefone_com"]}</a>",
					"<a href=\"educar_vps_responsavel_entrevista_det.php?cod_vps_responsavel_entrevista={$registro["cod_vps_responsavel_entrevista"]}\">{$registro["telefone_cel"]}</a>",
					"<a href=\"educar_vps_responsavel_entrevista_det.php?cod_vps_responsavel_entrevista={$registro["cod_vps_responsavel_entrevista"]}\">{$registro["email"]}</a>"
				));
			}
		}

		$this->addPaginador2("educar_vps_responsavel_entrevista_lst.php", $total, $_GET, $this->nome, $this->limite);

		if($obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11))
		{
			$this->acao = "go(\"educar_vps_responsavel_entrevista_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			"educar_vps_index.php"                => "Trilha Jovem Iguassu - Biblioteca",
			""                                    => "Listagem de responsáveis"
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
