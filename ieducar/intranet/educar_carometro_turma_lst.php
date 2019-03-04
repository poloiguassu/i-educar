<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Trilha Jovem - Carômetro por Turma");
        $this->processoAp = '659';
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

    public $ref_cod_turma;
    public $ref_ref_cod_serie;
    public $ref_cod_escola;
    public $ref_ref_cod_escola;
    public $ref_cod_instituicao;
    public $ref_cod_curso;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Carômetro Turma - Listagem';

        // passa todos os valores obtidos no GET para atributos do objetos
        foreach ($_GET as $var => $val) { 
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Turma',
            'Eixo',
            'Projeto'
        ];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
            $lista_busca[] = 'Instituição Executora';
        } elseif ($nivel_usuario == 2) {
            $lista_busca[] = 'Instituição';
        }
        $this->addCabecalhos($lista_busca);

        $get_escola = true;
        $get_curso = true;
        //		$get_escola_curso = true;
        $get_escola_curso_serie = true;
        $get_turma = true;
        $sem_padrao = true;

        include 'include/pmieducar/educar_campo_lista.php';

        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? 
            $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite: 0;

        $obj_turma = new clsPmieducarTurma();
        $obj_turma->setOrderby('nm_turma ASC');
        $obj_turma->setLimite($this->limite, $this->offset);

        $lista = $obj_turma->lista3(
            $this->ref_cod_turma,
            null,
            null,
            $this->ref_ref_cod_serie,
            $this->ref_ref_cod_escola,
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
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ref_cod_curso,
            $this->ref_cod_instituicao
        );

        $total = $obj_turma->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                if (class_exists('clsPmieducarEscola')) {
                    $obj_ref_cod_escola = new clsPmieducarEscola(
                        $registro['ref_ref_cod_escola']
                    );
                    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                    $registro['nm_escola'] = $det_ref_cod_escola['nome'];
                } else {
                    $registro['ref_ref_cod_escola'] = 'Erro na geração';
                    echo "<!--\nErro\nClasse não existente: clsPmieducarEscola\n-->";
                }

                $lista_busca = [
                    "<a href=\"educar_carometro_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">{$registro['nm_turma']}</a>"
                ];

                if ($registro['ref_ref_cod_serie']) {
                    $lista_busca[] = "<a href=\"educar_carometro_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">{$registro['nm_serie']}</a>";
                } else {
                    $lista_busca[] = "<a href=\"educar_carometro_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">-</a>";
                }

                $lista_busca[] = "<a href=\"educar_carometro_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">{$registro['nm_curso']}</a>";

                if ($nivel_usuario == 1) {
                    if ($registro['ref_ref_cod_escola']) {
                        $lista_busca[] = "<a href=\"educar_carometro_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">{$registro['nm_escola']}</a>";
                    } else {
                        $lista_busca[] = "<a href=\"educar_carometro_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">-</a>";
                    }

                    $lista_busca[] = "<a href=\"educar_carometro_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">{$registro['nm_instituicao']}</a>";
                } elseif ($nivel_usuario == 2) {
                    if ($registro['ref_ref_cod_escola']) {
                        $lista_busca[] = "<a href=\"educar_carometro_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">{$registro['nm_escola']}</a>";
                    } else {
                        $lista_busca[] = "<a href=\"educar_carometro_turma_cad.php?ref_cod_turma={$registro['cod_turma']}\">-</a>";
                    }
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_carometro_turma_lst.php', $total, $_GET, $this->nome, $this->limite);
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

document.getElementById('ref_cod_escola').onchange = function()
{
    getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
    getEscolaCursoSerie();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
    getTurma();
}

</script>
