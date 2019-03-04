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
            "Aluno",
            "EixoI P/F",
            "EixoII P/F",
            "EixoIII P/F"
        ];

        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        $this->inputsHelper()->date('data_aula_ini', [ 'label' => 'Período Inicial', 'value' => $this->data_aula_ini, 'placeholder' => '', 'required' => false]);
        $this->inputsHelper()->date('data_aula_fim', [ 'label' => 'Período Final', 'value' => $this->data_aula_fim, 'placeholder' => '', 'required' => false]);

        $data_aula_ini = Portabilis_Date_Utils::brToPgSQL($this->data_aula_ini);
        $data_aula_fim = Portabilis_Date_Utils::brToPgSQL($this->data_aula_fim);

        $obj_quadroHorario = new clsPmieducarQuadroHorario(
            null,
            null,
            null,
            $this->ref_cod_turma
        );
        $det_quadro = $obj_quadroHorario->detalhe();

        $cod_quadro_horario = $det_quadro['cod_quadro_horario'];

        //print($cod_quadro_horario);

        // Paginador
        $this->limite = 200;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_faltas = new clsPmieducarFaltaDiariaAluno();

        $lista = $obj_faltas->listaAulaDada(
            null,
            null,
            null,
            $cod_quadro_horario,
            $data_aula_ini,
            $data_aula_fim
        );

        //print_r($lista);

        $obj_matriculas_turma = new clsPmieducarMatriculaTurma();

        $lst_matriculas_turma = $obj_matriculas_turma->lista(
            null,
            $this->ref_cod_turma,
            null,
            null,
            null,
            null,
            null,
            null,
            null,  // ativo
            null,
            null,
            null,
            null,
            null,
            null,
            null, 
            null,
            null,
            2018,
            null,
            false,
            null,
            null, // ultima matriccula
            true, // maatricculaa ativa
            null,
            false,
            false,
            false,
            false,
            true, // ano andamentno
            null,
            false,
            false,
            false,
            null,
            false
        );

        $obj_modulo = new clsPmieducarTurmaModulo();
        $obj_modulo->setOrderby('sequencial ASC');
        $lst_modulo = $obj_modulo->lista($this->ref_cod_turma);

        //print_r($lst_modulo);

        // monta a lista
        if (is_array($lista)
            && count($lista)
            && is_array($lst_matriculas_turma)
            && count($lst_matriculas_turma)
        ) {
            $ref_cod_escola = '';
            $nm_escola = '';

            foreach ($lista as $registro) {
                $lista_busca[] = Portabilis_Date_Utils::pgSQLToBr($registro['data_aula']);
            }

            $this->addCabecalhos($lista_busca);

            $total_faltas = array();
            $total_presencas = array();

            foreach ($lst_matriculas_turma as $registro) {
                $lista_busca = array($registro["nome"]);
            
                foreach($lst_modulo as $eixo) {

                    $lst_falta_eixo = $obj_faltas->listaFaltaQuadro(
                        $registro['ref_cod_matricula'],
                        null,
                        1,
                        null,
                        $eixo['data_inicio'],
                        $eixo['data_fim']
                    );
                    
                    $numero_presencas = $obj_faltas->_total;
                    $total_presencas[$eixo['sequencial']] += $numero_presencas;
                    
                    $lst_falta_eixo = $obj_faltas->listaFaltaQuadro(
                        $registro['ref_cod_matricula'],
                        null,
                        0,
                        null,
                        $eixo['data_inicio'],
                        $eixo['data_fim']
                    );

                    $numero_faltas = $obj_faltas->_total;
                    $total_faltas[$eixo['sequencial']] += $numero_faltas;
                    
                    $lista_busca[] = "{$numero_presencas} / {$numero_faltas}";
                }

                $lista_faltas = $obj_faltas->listaFaltaQuadro(
                    $registro['ref_cod_matricula'],
                    null,
                    null,
                    null,
                    $data_aula_ini,
                    $data_aula_fim
                );

                //print_r($lista_faltas);

                foreach ($lista as $falta_data) {
                    $id = "{$falta_data['data_aula']}{$falta_data['hora_inicial']}-{$falta_data['hora_final']}";

                    if ($lista_faltas[$id]['situacao'] >= 1) {
                        $lista_busca[] = '<span class="badge badge-success">P</span>';
                    } else if($lista_faltas[$id]['situacao'] == 0) {
                        $lista_busca[] = '<span class="badge badge-warning">F</span>';
                    } else {
                        $lista_busca[] = '<span class="badge badge-dark">N/A</span>';
                    }
                }

                $this->addLinhas($lista_busca);
            }
        }

        if ($cod_quadro_horario) {
            // total
            $lista_busca = array(
                "YZ TOTAL",
                "",
                "",
                ""
            );

            foreach ($lista as $falta_data) {
                $lista_faltas = $obj_faltas->listaAulaDada(
                    null,
                    null,
                    1,
                    $cod_quadro_horario,
                    $falta_data['data_aula'],
                    $falta_data['data_aula']
                );

                $lista_busca[] = $obj_faltas->_total;
            }
        }

        $this->addLinhas($lista_busca);

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
