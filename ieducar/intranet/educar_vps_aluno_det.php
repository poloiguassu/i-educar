<?php

require_once('include/clsBase.inc.php');
require_once('include/clsDetalhe.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');
require_once('lib/App/Model/PrioridadeVPS.php');
require_once('lib/App/Model/VivenciaProfissionalSituacao.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Aluno VPS");
        $this->processoAp = 21455;
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
    public $titulo;

    public $cod_vps_entrevista;
    public $ref_cod_exemplar_tipo;
    public $ref_cod_vps_entrevista;
    public $ref_cod_vps_funcao;
    public $ref_cod_vps_jornada_trabalho;
    public $ref_cod_tipo_contratacao;
    public $descricao;
    public $data_entrevista;
    public $hora_entrevista;
    public $ano;

    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public $vps_entrevista_responsavel;
    public $ref_cod_vps_responsavel_entrevista;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Aluno VPS - Detalhe';

        $this->cod_aluno = $_GET['cod_aluno'];

        $tmp_obj = new clsPmieducarAluno($this->cod_aluno);
        $tmp_obj = new clsPmieducarMatricula();
        $registro = $tmp_obj->lista(null, null, null, null, null, null, $this->cod_aluno);
        $registro = $registro[0];

        if (!$registro) {
            header('location: educar_vps_aluno_lst.php');
            die();
        }

        if (class_exists('clsPmieducarEscola')) {
            $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $idpes = $det_ref_cod_escola['ref_idpes'];
            if ($idpes) {
                $obj_escola = new clsPessoaJuridica($idpes);
                $obj_escola_det = $obj_escola->detalhe();
                $registro['ref_cod_escola'] = $obj_escola_det['fantasia'];
            } else {
                $obj_escola = new clsPmieducarEscolaComplemento($registro['ref_cod_escola']);
                $obj_escola_det = $obj_escola->detalhe();
                $registro['ref_cod_escola'] = $obj_escola_det['nm_escola'];
            }
            if (class_exists('clsPmieducarInstituicao')) {
                $registro['ref_cod_instituicao'] = $det_ref_cod_escola['ref_cod_instituicao'];
                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];
            } else {
                $registro['ref_cod_instituicao'] = 'Erro na geracao';
                echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
            }
        } else {
            $registro['ref_cod_escola'] = 'Erro na geracao';
            echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
        }

        if (class_exists('clsPmieducarCurso')) {
            $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
            $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
            $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];
        } else {
            $registro['ref_cod_curso'] = 'Erro na geracao';
            echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
        }

        if (class_exists('clsPessoaFj')) {
            $obj_ref_idpes = new clsPessoaFj($registro['ref_idpes']);
            $det_ref_idpes = $obj_ref_idpes->detalhe();
            $registro['ref_idpes'] = $det_ref_idpes['nome'];
        } else {
            $registro['ref_idpes'] = 'Erro na geracao';
            echo "<!--\nErro\nClasse nao existente: clsPessoaFj\n-->";
        }

        $alunoVPS = new clsPmieducarAlunoVPS($this->cod_aluno);

        if ($alunoVPS && $alunoVPS->existe()) {
            $registroVPS = $alunoVPS->detalhe();
        }

        if ($registroVPS['situacao_vps']) {
            $situacaoVPS = App_Model_VivenciaProfissionalSituacao::getInstance()->getValue($registroVPS['situacao_vps']);
        }

        if ($registroVPS['ref_cod_vps_aluno_entrevista']) {
            $alunoEntrevista = new clsPmieducarVPSAlunoEntrevista($registroVPS['ref_cod_vps_aluno_entrevista']);
            $registroAlunoEntrevista = $alunoEntrevista->detalhe();

            $ref_cod_vps_entrevista = $registroAlunoEntrevista['ref_cod_vps_entrevista'];

            if ($ref_cod_vps_entrevista) {
                $entrevista = new clsPmieducarVPSEntrevista($ref_cod_vps_entrevista);
                $registroEntrevista = $entrevista->detalhe();

                if (class_exists('clsPmieducarVPSFuncao')) {
                    $obj_ref_cod_vps_funcao = new clsPmieducarVPSFuncao($registroEntrevista['ref_cod_vps_funcao']);
                    $det_ref_cod_vps_funcao = $obj_ref_cod_vps_funcao->detalhe();
                    $registro['ref_cod_vps_funcao'] = $det_ref_cod_vps_funcao['nm_funcao'];
                } else {
                    $registro['ref_cod_vps_funcao'] = 'Erro na geracao';
                    echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSFuncao\n-->";
                }
            }

            if ($registroAlunoEntrevista['inicio_vps']) {
                $inicioVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista['inicio_vps']);
            }

            if ($registroAlunoEntrevista['termino_vps']) {
                $terminoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista['termino_vps']);
            }

            if ($registroAlunoEntrevista['insercao_vps']) {
                $insercaoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista['insercao_vps']);
            }
        }

        $sql     = 'SELECT COUNT(ref_cod_aluno) from pmieducar.vps_aluno_entrevista where ref_cod_aluno = $1';
        $options = ['params' => $this->cod_aluno, 'return_only' => 'first-field'];
        $numero_entrevistas    = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

        if (is_numeric($registroVPS['prioridade'])) {
            $prioridadeVPS = App_Model_PrioridadeVPS::getInstance()->getValue($registroVPS['prioridade']);
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe(['Instituição', "{$registro['ref_cod_instituicao']}"]);
            }
        }
        if ($nivel_usuario == 1 || $nivel_usuario == 2) {
            if ($registro['ref_cod_escola']) {
                $this->addDetalhe(['Escola', "{$registro['ref_cod_escola']}"]);
            }
        }
        if ($registro['ref_cod_curso']) {
            $this->addDetalhe(['Projeto', "{$registro['ref_cod_curso']}"]);
        }
        if ($registro['ano']) {
            $this->addDetalhe(['Ano', "{$registro['ano']}"]);
        }
        if ($registro['ref_idpes']) {
            $this->addDetalhe(['Aluno', "{$registro['ref_idpes']}"]);
        }
        if ($numero_entrevistas) {
            $this->addDetalhe(['Número de Entrevistas', "{$numero_entrevistas}"]);
        }
        if ($situacaoVPS) {
            $this->addDetalhe(['Situação VPS', "{$situacaoVPS}"]);
        }
        if ($prioridadeVPS) {
            $this->addDetalhe(['Prioridade VPS', "{$prioridadeVPS}"]);
        }
        if ($registroVPS['motivo_desligamento']) {
            $this->addDetalhe(['Motivo Desligamento VPS', "{$registroVPS['motivo_desligamento']}"]);
        }
        if ($registro['ref_cod_vps_funcao']) {
            $this->addDetalhe(['Entrevista in�cio VPS', "<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$ref_cod_vps_entrevista}\" target=\"_blank\">{$registro['ref_cod_vps_funcao']}</a>"]);
        }
        if ($inicioVPS) {
            $this->addDetalhe(['Data Início VPS', "$inicioVPS"]);
        }
        if ($terminoVPS) {
            $this->addDetalhe(['Data Término VPS', "$terminoVPS"]);
        }
        if ($insercaoVPS) {
            $this->addDetalhe(['Data Inserção Profissional', "$insercaoVPS"]);
        }

        $obj = new clsPmieducarVPSAlunoEntrevista();
        $entrevistas = $obj->lista($this->cod_aluno);

        if ($entrevistas) {
            $cont = 0;

            $this->addDetalhe(['Todas as Entrevistas', '']);

            foreach ($entrevistas as $valor) {
                $entrevista = '';

                $ref_cod_vps_entrevista = $valor['ref_cod_vps_entrevista'];
                $obj = new clsPmieducarVPSEntrevista($ref_cod_vps_entrevista);
                $det = $obj->detalhe();

                if (class_exists('clsPmieducarVPSFuncao')) {
                    $obj_ref_cod_vps_funcao = new clsPmieducarVPSFuncao($det['ref_cod_vps_funcao']);
                    $det_ref_cod_vps_funcao = $obj_ref_cod_vps_funcao->detalhe();
                    $registro['ref_cod_vps_funcao'] = $det_ref_cod_vps_funcao['nm_funcao'];
                } else {
                    $registro['ref_cod_vps_funcao'] = 'Erro na geracao';
                    echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSFuncao\n-->";
                }

                if (class_exists('clsPessoaFj')) {
                    $obj_ref_idpes = new clsPessoaFj($det['ref_idpes']);
                    $det_ref_idpes = $obj_ref_idpes->detalhe();
                    $registro['ref_idpes'] = $det_ref_idpes['nome'];
                } else {
                    $registro['ref_idpes'] = 'Erro na geracao';
                    echo "<!--\nErro\nClasse nao existente: clsPessoaFj\n-->";
                }

                $data_entrevista = Portabilis_Date_Utils::pgSQLToBr($det['data_entrevista']);
                $hora_entrevista = $det['hora_entrevista'];

                $entrevista .= "<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$ref_cod_vps_entrevista}\" target=\"_blank\">
									{$registro['ref_cod_vps_funcao']} / {$registro['ref_idpes']} - {$data_entrevista} �s {$hora_entrevista}
								</a>";
                $cont++;
                $this->addDetalhe([" - Entrevista {$cont}", "{$entrevista}"], 'entrevistas');
            }
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11)) {
            $this->array_botao = ['Atualizar Situação', 'Agendar Visita VPS'];

            $this->array_botao_url_script = [
                sprintf('go("educar_vps_aluno_cad.php?cod_aluno=%d");', $this->cod_aluno),
                sprintf('go("educar_vps_visita_cad.php?ref_cod_aluno=%d");', $this->cod_aluno)
            ];
        }

        $this->url_cancelar = 'educar_vps_aluno_lst.php';
        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            'educar_vps_index.php'                => 'Trilha Jovem Iguassu - VPS',
            ''                                    => 'Detalhe do Aluno'
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
