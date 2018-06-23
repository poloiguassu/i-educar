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
require_once("include/clsListagem.inc.php");
require_once("include/clsBanco.inc.php");
require_once("include/pmieducar/geral.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Entrevistas");
		$this->processoAp = "598";
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
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

	var $cod_acervo;
	var $ref_cod_vps_tipo_contratacao;
	var $ref_cod_acervo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_vps_funcao;
	var $ref_cod_vps_jornada_trabalho;
	var $ref_idpes;
	var $descricao;
	var $ano;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		$_SESSION["campo1"] = $_GET["campo1"] ? $_GET["campo1"] : $_SESSION["campo1"];
		$this->ref_cod_biblioteca = $_SESSION["ref_cod_biblioteca"] = $_GET["ref_cod_biblioteca"] ? $_GET["ref_cod_biblioteca"] : $_SESSION["ref_cod_biblioteca"];
		session_write_close();

		foreach ($_GET as $key => $value)
			$this->$key = $value;
		$this->titulo = "Obras - Listagem";

		//

		$this->addCabecalhos(array(
			"Obra",
			"Biblioteca"
		));

		// outros Filtros
		//$get_escola     = 1;
		//$get_biblioteca = 1;
		//$obrigatorio    = false;
		//include("include/pmieducar/educar_campo_lista.php");
		$this->campoOculto("ref_cod_biblioteca",$this->ref_cod_biblioteca);

		// Paginador
		$this->limite = 20;
		$this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_acervo = new clsPmieducarAcervo();
		$obj_acervo->setOrderby("titulo ASC");
		$obj_acervo->setLimite($this->limite, $this->offset);

		$lista = $obj_acervo->lista(
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_biblioteca
			,$this->ref_cod_instituicao
			,$this->ref_cod_escola
		);

		$total = $obj_acervo->_total;

		// monta a lista
		if(is_array($lista) && count($lista))
		{
			foreach ($lista AS $registro)
			{
				$obj_biblioteca = new clsPmieducarBiblioteca($registro["ref_cod_biblioteca"]);
				$obj_det = $obj_biblioteca->detalhe();

				$registro["ref_cod_biblioteca"] = $obj_det["nm_biblioteca"];


				$script = " onclick=\"addSel1('{$_SESSION['campo1']}','{$registro['cod_acervo']}','{$registro['titulo']}'); fecha();\"";
				$this->addLinhas(array(
					"<a href=\"javascript:void(0);\" {$script}>{$registro["titulo"]}</a>",
					"<a href=\"javascript:void(0);\" {$script}>{$registro["ref_cod_biblioteca"]}</a>"
				));
			}
		}
		$this->addPaginador2("educar_pesquisa_acervo_lst.php", $total, $_GET, $this->nome, $this->limite);
		$obj_permissoes = new clsPermissoes();

		$this->largura = "100%";
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
<script>
function addSel1(campo, valor, texto)
{
	obj = window.parent.document.getElementById(campo);
	novoIndice = obj.options.length;
	obj.options[novoIndice] = new Option(texto);
	opcao = obj.options[novoIndice];
	opcao.value = valor;
	opcao.selected = true;
	setTimeout("obj.onchange", 100);
}

function addVal1(campo,valor)
{

	obj =  window.parent.document.getElementById(campo);
	obj.value = valor;
}

function fecha()
{
    window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
    //window.parent.document.forms[0].submit();
}
</script>
