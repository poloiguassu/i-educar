<?php

use iEducar\Modules\Inscritos\Model\AvaliacaoEtapa;
use iEducar\Modules\Inscritos\Model\AreaInteresse;
use iEducar\Modules\Inscritos\Model\SerieEstudo;
use iEducar\Modules\Inscritos\Model\TurnoEstudo;

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo('Informações Alunos');
        $this->processoAp = 21469;

        if ($_GET['fullscreen']) {
            $this->renderMenu = false;
            $this->renderMenuSuspenso = false;
        }
    }
}

class indice extends clsListagem
{
    var $etapas = [];
    var $nm_inscrito = null;
    var $processo_seletivo_id = null;
    var $turno = null;
    var $egresso = null;
    var $encaminhamento = null;
    var $inicial_min = null;
    var $inicial_max = null;

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('list');

        foreach ($_GET as $nm => $var)
        {
            $this->$nm = $var;
        }
    }

    public function Gerar()
    {
        $this->titulo = 'Informações dos Candidatos';

        $registroSelecao = array();

        if (is_null($this->processo_seletivo_id)) {
            $objSelecao = new clsPmieducarProcessoSeletivo();
            $registroSelecao = $objSelecao->getUltimoProcessoSeletivo();
            $this->processo_seletivo_id = $registroSelecao['cod_selecao_processo'];
        }

        $objSelecao = new clsPmieducarProcessoSeletivo(
            $this->processo_seletivo_id
        );

        $registroSelecao = $objSelecao->detalhe();

        $cabecalho = [
            'Nome',
            'Data Nascimento',
            'Idade',
            'Sexo',
            'CPF',
            'RG',
            'Escola',
            'Série',
            'Turno',
            'Area de Interesse',
            'Selecionado Turma',
            'Tipagem Sanguinea',
            'E-mail',
            'CEP',
            'Bairro',
            'Região'
        ];

        $etapas = array();
        $total_etapas = $registroSelecao['total_etapas'];

        for ($i = 1; $i <= $total_etapas; $i++) {
            $etapas[] = "Etapa " . $i;
        }

        array_splice($cabecalho, 10, 0, $etapas);

        $this->addCabecalhos($cabecalho);

        $this->inputsHelper()->processoSeletivo(
            array(
                'required' => true,
                'label' => 'Processo Seletivo',
                'value' => $this->processo_seletivo_id
            )
        );

        $this->campoTexto(
            'nm_inscrito',
            'Nome',
            $this->nm_inscrito,
            '50',
            '255',
            true
        );

        $this->campoCpf('id_federal', 'CPF', $_GET['id_federal'], '50', '', true);

        for ($i = 1; $i <= $total_etapas; $i++) {
            $resources = AvaliacaoEtapa::getDescriptiveValues();
            $resources = array_replace([null => $i . 'ª Etapa'], $resources);

            $options = [
                'required' => false,
                'label'    => 'Avaliação Projeto Etapa ' . $i,
                'value'     => $this->{'etapa_' . $i},
                'resources' => $resources,
            ];

            $this->inputsHelper()->select('etapa_' . $i, $options);

            $this->etapas[$i] = $this->{'etapa_' . $i};
        }

        $area_selecionado = array(
            ''  => 'Selecione uma turma',
            3   => 'T&A - Manhã',
            4   => 'T&A - Tarde',
            5   => 'Comércio - Manhã',
            6   => 'Comércio - Tarde',
            7   => 'Hospedagem - Manhã',
            8   => 'Eventos - Tarde'
        );

        $options = array(
            'required'  => false,
            'label'     => 'Turma',
            'value'     => $this->area_selecionado,
            'resources' => $area_selecionado
        );

        $this->inputsHelper()->select('area_selecionado', $options);

        $resources = TurnoEstudo::getDescriptiveValues();
        $resources = array_replace([null => 'Turno'], $resources);

        $options = array(
            'required'  => false,
            'label'     => 'Turno em que estuda',
            'value'     => $this->turno,
            'resources' => $resources
        );

        $this->inputsHelper()->select('turno', $options);

        $options = array(
            'required'  => false,
            'label'     => 'Egresso',
            'value'     => $this->egresso,
        );

        $this->inputsHelper()->booleanSelect('egresso', $options);

        $options = array(
            'required'  => false,
            'label'     => 'Encaminhamento',
            'value'     => $this->encaminhamento,
        );

        $this->inputsHelper()->booleanSelect('encaminhamento', $options);

        $options = [
            'required' => false,
            'label' => 'Letra inicial do nome entre',
            'placeholder' => '',
            'value' => $this->inicial_min,
            'max_length' => 1,
            'size' => 1,
            'inline' => true
        ];

        $this->inputsHelper()->text('inicial_min', $options);

        $options = [
            'required' => false,
            'label' => ' e',
            'placeholder' => '',
            'value' => $this->inicial_max,
            'max_length' => 1,
            'size' => 1,
            'inline' => true
        ];

        $this->inputsHelper()->text('inicial_max', $options);

        $objPessoa = new clsPmieducarInscrito();

        // Paginador
        $limite = 2000;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite-$limite: 0;

        $turno_campo = TurnoEstudo::getDescriptiveValues();

        $serie_campo = SerieEstudo::getDescriptiveValues();

        $avaliacao_campo = AvaliacaoEtapa::getDescriptiveValues();

        $area_interesse = AreaInteresse::getDescriptiveValues();

        $this->id_federal = idFederal2int($this->id_federal);

        $pessoas = $objPessoa->lista(
            $this->etapas,
            $this->processo_seletivo_id,
            $this->nm_inscrito,
            $this->id_federal,
            null,
            $this->inicial_min,
            $this->inicial_max,
            $this->turno,
            $this->egresso,
            $this->encaminhamento,
            $iniciolimit,
            $limite,
            false,
            $this->area_selecionado
        );

        if ($pessoas) {
            foreach ($pessoas as $pessoa) {
                $cod = $pessoa['cod_inscrito'];
                $total = $pessoa['total'];
                $cpf = $pessoa['cpf'] ? int2CPF($pessoa['cpf']) : '';

                if ($pessoa['egresso'] > 0) {
                    $serie = 'Egresso ' . $pessoa['egresso'];
                } else {
                    $serie = $serie_campo[$pessoa['estudando_serie']];
                }

                $turno = $turno_campo[$pessoa['estudando_turno']];

                $data_nasc = $pessoa['data_nasc'];

                list($ano, $mes, $dia) = explode('-', $data_nasc);

                $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $nascimento = mktime(0, 0, 0, $mes, $dia, $ano);

                $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);

                $pessoa['grupo_sanguineo'] .= $pessoa['fator_rh'];

                $telefone = '';
                if ($pessoa['telefone_1']) {
                    $telefone = "({$pessoa['ddd_telefone_1']}) {$pessoa['telefone_1']}";
                } else {
                    $telefone = "({$pessoa['ddd_telefone_2']}) {$pessoa['telefone_2']}";
                }

                $bairro = $pessoa['bairro'];
                $regiao = $pessoa['regiao'];

                $objEtapa = new clsPmieducarInscritoEtapa();
                $registroEtapa = $objEtapa->lista($pessoa['cod_inscrito']);

                $etapas = array();

                for ($i = 0; $i <= $total_etapas - 1; $i++) {
                    if ($i < count($registroEtapa)) {
                        $etapa = $registroEtapa[$i]['etapa'];
                        $situacao = $registroEtapa[$i]['situacao'];

                        if ($etapa && $situacao) {
                            $etapas[$etapa-1] = $avaliacao_campo[$situacao];
                        } else {
                            $etapas[$i] = '';
                        }
                    } else {
                        $etapas[$i] = '';
                    }
                }

                $registroLinha = [
                    "<a href='selecao_inscritos_det.php?cod_inscrito={$cod}'>
                    {$pessoa['nome']}</a>",
                    Portabilis_Date_Utils::pgSQLToBr($data_nasc),
                    $idade,
                    $pessoa['sexo'],
                    $cpf,
                    $pessoa['rg'],
                    $pessoa['nome_escola'],
                    $serie,
                    $turno,
                    $area_interesse[$pessoa['area_interesse']],
                    $area_selecionado[$pessoa['area_selecionado']],
                    $pessoa['grupo_sanguineo'],
                    $pessoa['email'],
                    $pessoa['cep'],
                    $bairro,
                    $regiao
                ];

                array_splice($registroLinha, 10, 0, $etapas);

                $this->addLinhas($registroLinha);
            }
        }

        $this->acao = sprintf(
            'go("/module/Cadastro/Inscrito?cod_selecao_processo=%s")',
            $this->processo_seletivo_id
        );
        $this->nome_acao = 'Novo';

        $etapaQuery = '';

        foreach ($this->etapas as $key => $etapa) {
            if (is_numeric($etapa) && is_numeric($key)) {
                $etapaQuery .= "&etapa_{$key}=$etapa";
            }
        }

        $this->array_botao = ['Tela cheia', 'Avaliar Candidatos'];
        $this->array_botao_url = array(
            "selecao_inscritos_lst.php?fullscreen=1",
            sprintf(
                "selecao_avaliacao_lst.php?busca=S&processo_seletivo_id=%s&nm_inscrito=%s&id_federal=%s&inicial_min=%s&inicial_max=%s&turno=%s&encaminhamento=%s%s",
                $this->processo_seletivo_id,
                $this->nm_inscrito,
                $this->id_federal,
                $this->inicial_min,
                $this->inicial_max,
                $this->turno,
                $this->encaminhamento,
                $etapaQuery
            )
        );

        $this->largura = '100%';
        $this->addPaginador2(
            'selecao_inscritos_lst.php',
            $total,
            $_GET,
            $this->nome,
            $limite
        );

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos(
            [
                $_SERVER['SERVER_NAME'].'/intranet' => 'Início',
                '' => 'Listagem de Inscritos Processo Seletivo'
            ]
        );

        $this->enviaLocalizacao($localizacao->montar());
    }
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm($miolo);

$pagina->MakeAll();
