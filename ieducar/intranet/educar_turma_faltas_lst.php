<?php

require_once('include/clsBase.inc.php');
require_once('include/clsListagem.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Faltas por turma");
        $this->processoAp = '586';
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

    public $cod_turma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_ref_cod_serie;
    public $ref_ref_cod_escola;
    public $ref_cod_infra_predio_comodo;
    public $nm_turma;
    public $sgl_turma;
    public $max_aluno;
    public $multiseriada;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_turma_tipo;
    public $hora_inicial;
    public $hora_final;
    public $hora_inicio_intervalo;
    public $hora_fim_intervalo;

    public $ref_cod_instituicao;
    public $ref_cod_curso;
    public $ref_cod_escola;
    public $visivel;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Turma - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Ano',
            'Turma',
            'Turno',
            'Situação',
            //'Dias Letivos',
            //'Numero Aulas'
        ];

        $this->addCabecalhos($lista_busca);

        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'curso', 'serie', 'anoLetivo'], ['required' => false]);
        $this->inputsHelper()->date('data_aula_ini', [ 'label' => 'Período Inicial', 'value' => $this->data_aula_ini, 'placeholder' => '', 'required' => false]);
        $this->inputsHelper()->date('data_aula_fim', [ 'label' => 'Período Final', 'value' => $this->data_aula_fim, 'placeholder' => '', 'required' => false]);

        $this->campoLista('visivel', 'Situação', ['' => 'Selecione', '1' => 'Ativo', '2' => 'Inativo'], $this->visivel, null, null, null, null, null, false);

        $data_aula_ini = Portabilis_Date_Utils::brToPgSQL($this->data_aula_ini);
        $data_aula_fim = Portabilis_Date_Utils::brToPgSQL($this->data_aula_fim);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_turma = new clsPmieducarTurma();
        $obj_turma->setOrderby('nm_turma ASC');
        $obj_turma->setLimite($this->limite, $this->offset);

        if ($this->visivel == 1) {
            $visivel = true;
        } elseif ($this->visivel == 2) {
            $visivel = false;
        } else {
            $visivel = ['true', 'false'];
        }

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_turma->codUsuario = $this->pessoa_logada;
        }

        $lista = $obj_turma->lista2(
            null,
            null,
            null,
            $this->ref_cod_serie,
            $this->ref_cod_escola,
            null,
            $this->nm_turma,
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
            $this->ref_cod_instituicao,
            null,
            null,
            null,
            null,
            null,
            $visivel,
            $this->turma_turno_id,
            null,
            $this->ano_letivo
        );

        $total = $obj_turma->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            $ref_cod_escola = '';
            $nm_escola = '';

            $aulas = new clsPmieducarQuadroHorarioHorarios();

            foreach ($lista as $registro) {
                if (class_exists('clsPmieducarEscola') && $registro['ref_ref_cod_escola'] != $ref_cod_escola) {
                    $ref_cod_escola = $registro['ref_ref_cod_escola'];
                    $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
                    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                    $ref_cod_escola = $registro['ref_ref_cod_escola'] ;
                    $nm_escola = $det_ref_cod_escola['nome'];
                }

                $lista_busca = [
                    "<a href=\"educar_turma_faltas_det.php?ref_cod_turma={$registro['cod_turma']}&data_aula_ini={$this->data_aula_ini}&data_aula_fim={$this->data_aula_fim}\">{$registro['ano']}</a>",
                    "<a href=\"educar_turma_faltas_det.php?ref_cod_turma={$registro['cod_turma']}&data_aula_ini={$this->data_aula_ini}&data_aula_fim={$this->data_aula_fim}\">{$registro['nm_turma']}</a>"
                ];

                if ($registro['turma_turno_id']) {
                    $options = ['params' => $registro['turma_turno_id'], 'return_only' => 'first-field'];
                    $turno   = Portabilis_Utils_Database::fetchPreparedQuery('select nome from pmieducar.turma_turno where id = $1', $options);

                    $lista_busca[] = "<a href=\"educar_turma_faltas_det.php?ref_cod_turma={$registro['cod_turma']}&data_aula_ini={$this->data_aula_ini}&data_aula_fim={$this->data_aula_fim}\">$turno</a>";
                } else {
                    $lista_busca[] = "<a href=\"educar_turma_faltas_det.php?ref_cod_turma={$registro['cod_turma']}&data_aula_ini={$this->data_aula_ini}&data_aula_fim={$this->data_aula_fim}\"></a>";
                }

                if (dbBool($registro['visivel'])) {
                    $lista_busca[] = "<a href=\"educar_turma_faltas_det.php?ref_cod_turma={$registro['cod_turma']}&data_aula_ini={$this->data_aula_ini}&data_aula_fim={$this->data_aula_fim}\">Ativo</a>";
                } else {
                    $lista_busca[] = "<a href=\"educar_turma_faltas_det.php?ref_cod_turma={$registro['cod_turma']}&data_aula_ini={$this->data_aula_ini}&data_aula_fim={$this->data_aula_fim}\">Inativo</a>";
                }

                /*$dias_letivos = $aulas->conta(null, null, null, null, $data_aula_ini, $data_aula_fim, null, null, null, null, null, null, true, null, $registro['cod_turma'], false, true);

                $lista_busca[] = "<a href=\"educar_turma_faltas_det.php?ref_cod_turma={$registro['cod_turma']}&data_aula_ini={$this->data_aula_ini}&data_aula_fim={$this->data_aula_fim}\">{$dias_letivos}</a>";

                $numero_aulas = $aulas->conta(null, null, null, null, $data_aula_ini, $data_aula_fim, null, null, null, null, null, null, true, null, $registro['cod_turma'], false, false);

                $lista_busca[] = "<a href=\"educar_turma_faltas_det.php?ref_cod_turma={$registro['cod_turma']}&data_aula_ini={$this->data_aula_ini}&data_aula_fim={$this->data_aula_fim}\">{$numero_aulas}</a>";*/

                $this->addLinhas($lista_busca);
            }
        }

        $this->array_botao[] = "Faltas Gerais Por turma";
        $this->array_botao_url[] = "educar_turma_faltas_geral_lst.php";

        $this->addPaginador2('educar_turma_lst.php', $total, $_GET, $this->nome, $this->limite);
        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
             $_SERVER['SERVER_NAME'].'/intranet' => 'Início',
             'educar_index.php'                  => 'Escola',
             ''                                  => 'Listagem de turmas'
        ]);
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
