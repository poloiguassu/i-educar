<?php

use iEducar\Modules\Inscritos\Model\SerieEstudo;
use iEducar\Modules\Inscritos\Model\TurnoEstudo;

require_once('include/clsBase.inc.php');
require_once('include/clsListagem.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');
require_once('include/localizacaoSistema.php');
require_once('lib/App/Model/PrioridadeVPSHTML.php');
require_once('lib/App/Model/VivenciaProfissionalSituacao.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Jovens em VPS");
        $this->processoAp = 21455;
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

    public $cod_vps_entrevista;
    public $ref_cod_exemplar_tipo;
    public $ref_cod_vps_entrevista;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_vps_entrevista_colecao;
    public $ref_cod_vps_entrevista_idioma;
    public $ref_cod_vps_entrevista_editora;
    public $sub_titulo;
    public $cdu;
    public $cutter;
    public $volume;
    public $num_edicao;
    public $ano;
    public $num_paginas;
    public $isbn;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_escola;

    public $situacao_vps;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Atribuir Entrevistas - Listagem';

        $this->setTemplate('list_filter');

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'ID',
            'Nome',
            'Prioridade',
            'Número de entrevistas',
            'Situação VPS',
            'Idade',
            'Sexo',
            'Estudando?',
            'Turno Colégio',
            'Bairro'
        ]);

        // Filtros de Foreign Keys
        $get_escola = true;
        $get_curso = true;
        $get_cabecalho = 'lista_busca';
        include('include/pmieducar/educar_campo_lista.php');

        if (!is_numeric($_GET['situacao_vps'])) {
            $this->situacao_vps = null;
        } else {
            $this->situacao_vps = $_GET['situacao_vps'];
        }

        $filtrosSituacao = [
            '1' => 'Evadido',
            '2' => 'Desistente',
            '3' => 'Desligado',
            '4' => 'Apto a VPS',
            '5' => 'Em cumprimento',
            '6' => 'Concluído (Avaliado)',
            '7' => 'Inserido',
            '8' => 'Jovens com Entrevista Agendada'
        ];

        $this->campoLista('situacao_vps', 'Situação VPS', $filtrosSituacao, $this->situacao_vps, '', false, '', '', false, false);

        if ($this->situacao_vps > App_Model_VivenciaProfissionalSituacao::INSERIDO) {
            $obj_entrevista = new clsPmieducarVPSAlunoEntrevista();
            $obj_entrevista->setLimite($this->limite, $this->offset);
            $obj_entrevista->setOrderBy('nome ASC');

            $lista = $obj_entrevista->listaJovens();
        } else {
            $obj_entrevista = new clsPmieducarAlunoVPS();
            $obj_entrevista->setLimite($this->limite, $this->offset);
            $obj_entrevista->setOrderBy('nome ASC');

            $lista = $obj_entrevista->lista(null, null, $this->situacao_vps);
        }

        $total = $obj_entrevista->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $ref_cod_vps_entrevista = null;
                $registroVPS	= [];
                $situacaoVPS	= '';
                $inicioVPS		= '';
                $terminoVPS		= '';
                $insercaoVPS	= '';
                $estudando		= '';
                $turno			= '';

                $ref_cod_aluno =  $registro['ref_cod_aluno'];

                $alunoVPS = new clsPmieducarAlunoVPS($ref_cod_aluno);
                $aluno = new clsPmieducarAluno($ref_cod_aluno);

                if ($alunoVPS && $alunoVPS->existe()) {
                    $registroVPS = $alunoVPS->detalhe();
                }

                if ($aluno && $aluno->existe()) {
                    $registroAluno = $aluno->detalhe();

                    $objPessoa = new clsPessoaFj($registroAluno['ref_idpes']);
                    $registroPessoa = $objPessoa->detalhe();

                    $alunoSelecao = new clsPmieducarInscrito($registroAluno['ref_cod_inscrito']);
                    $registroSelecao = $alunoSelecao->detalhe();
                }

                if ($registroVPS['situacao_vps']) {
                    $situacaoVPS = App_Model_VivenciaProfissionalSituacao::getInstance()->getValue($registroVPS['situacao_vps']);
                }

                if ($registroPessoa['data_nasc']) {
                    $hoje = new DateTime();
                    $data_nasc = new DateTime($registroPessoa['data_nasc']);
                    $idade = $hoje->diff($data_nasc);
                    $idade = $idade->format('%y');
                }

                if ($registroPessoa['sexo']) {
                    if ($registroPessoa['sexo'] == 'F') {
                        $registroPessoa['sexo'] = 'Feminio';
                    } else {
                        $registroPessoa['sexo'] = 'Masculino';
                    }
                }

                if ($registroSelecao['serie'] || $registroSelecao['egresso']) {
                    if ($registroSelecao['egresso']) {
                        $estudando = 'Egresso';
                    } else {
                        $serieEstudo = AvaliacaoEtapa::getDescriptiveValue(
                            $registroSelecao['serie']
                        );
                        $estudando = "Sim ({$serieEstudo})";

                        if ($registroSelecao['turno']) {
                            $turno = SerieTurno::getDescriptiveValue(
                                $registroSelecao['turno']
                            );
                        }
                    }
                }

                if ($registroVPS['ref_cod_vps_aluno_entrevista']) {
                    $alunoEntrevista = new clsPmieducarVPSAlunoEntrevista($registroVPS['ref_cod_vps_aluno_entrevista']);
                    $registroAlunoEntrevista = $alunoEntrevista->detalhe();

                    $ref_cod_vps_entrevista = $registroAlunoEntrevista['ref_cod_vps_entrevista'];

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

                if (is_numeric($registro['prioridade'])) {
                    $registro['prioridade'] = App_Model_PrioridadeVPSHTML::getInstance()->getValue($registro['prioridade']);
                }

                $sql     = 'SELECT COUNT(ref_cod_aluno) from pmieducar.vps_aluno_entrevista where ref_cod_aluno = $1';
                $options = ['params' => $ref_cod_aluno, 'return_only' => 'first-field'];
                $numero_entrevistas    = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

                $lista_busca = [
                    "{$ref_cod_aluno}",
                    "{$registro['nome']}",
                    "{$registro['prioridade']}",
                    "{$numero_entrevistas}",
                    "{$situacaoVPS}",
                    "{$idade}",
                    "{$registroPessoa['sexo']}",
                    "{$estudando}",
                    "{$turno}",
                    "{$registroSelecao['bairro']}"
                ];

                $this->addLinhas($lista_busca);
            }
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11)) {
            $this->acao = 'go("educar_entrevista_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            'educar_vps_index.php'                => 'Trilha Jovem Iguassu - VPS',
            ''                                    => 'Listagem de jovens em Entrevista'
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
