<?php

require_once('include/clsBase.inc.php');
require_once('include/clsListagem.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Entrevistas");
        $this->processoAp = 21455;
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
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $cod_acervo;
    public $ref_cod_vps_tipo_contratacao;
    public $ref_cod_acervo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_vps_funcao;
    public $ref_cod_vps_jornada_trabalho;
    public $ref_idpes;
    public $descricao;
    public $ano;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        $_SESSION['campo1'] = $_GET['campo1'] ? $_GET['campo1'] : $_SESSION['campo1'];
        $this->ref_cod_biblioteca = $_SESSION['ref_cod_biblioteca'] = $_GET['ref_cod_biblioteca'] ? $_GET['ref_cod_biblioteca'] : $_SESSION['ref_cod_biblioteca'];
        session_write_close();

        foreach ($_GET as $key => $value) {
            $this->$key = $value;
        }
        $this->titulo = 'Obras - Listagem';

        //

        $this->addCabecalhos([
            'Obra',
            'Biblioteca'
        ]);

        // outros Filtros
        //$get_escola     = 1;
        //$get_biblioteca = 1;
        //$obrigatorio    = false;
        //include("include/pmieducar/educar_campo_lista.php");
        $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_acervo = new clsPmieducarAcervo();
        $obj_acervo->setOrderby('titulo ASC');
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
            $this->ref_cod_biblioteca,
            $this->ref_cod_instituicao,
            $this->ref_cod_escola
        );

        $total = $obj_acervo->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
                $obj_det = $obj_biblioteca->detalhe();

                $registro['ref_cod_biblioteca'] = $obj_det['nm_biblioteca'];

                $script = " onclick=\"addSel1('{$_SESSION['campo1']}','{$registro['cod_acervo']}','{$registro['titulo']}'); fecha();\"";
                $this->addLinhas([
                    "<a href=\"javascript:void(0);\" {$script}>{$registro['titulo']}</a>",
                    "<a href=\"javascript:void(0);\" {$script}>{$registro['ref_cod_biblioteca']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_pesquisa_acervo_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();

        $this->largura = '100%';
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
