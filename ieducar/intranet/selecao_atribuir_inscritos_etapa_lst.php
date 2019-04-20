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
    }
}

class indice extends clsListagem
{
    var $etapas = [];
    var $nm_inscrito = null;
    var $processo_seletivo_id = null;
    var $turno = null;
    var $encaminhamento = null;
    var $inicial_min = null;
    var $inicial_max = null;

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('list_filter');

        foreach ($_GET as $nm => $var)
        {
            $this->$nm = $var;
        }
    }

    public function Gerar()
    {
        $this->titulo = 'Informações dos Candidatos';

        $this->addCabecalhos(
            [
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
                'Turma',
                'Data da etapa'
            ]
        );

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

        $this->inputsHelper()->processoSeletivo(
            array(
                'required' => true,
                'label' => 'Processo Seletivo',
                'value' => $this->processo_seletivo_id
            )
        );

        $options = [
            'required' => true,
            'label'    => 'Etapa',
            'value'     => $this->etapa,
            'resources' => array(1 => 'Etapa 1', 2 => 'Etapa 2'),
        ];

        $this->inputsHelper()->select('etapa', $options);

        $resources = AvaliacaoEtapa::getDescriptiveValues();
        $resources = array_replace([null => 'Situação Etapa'], $resources);

        $options = [
            'required' => false,
            'label'    => 'Situação da Etapa ',
            'value'     => $this->etapa_situacao,
            'resources' => $resources,
        ];

        $this->inputsHelper()->select('etapa_situacao', $options);

        $this->inputsHelper()->selecaoDataEtapa(
            array(
                'required' => true,
                'label' => 'Data da Etapa',
                'value' => $this->selecao_data_etapa_id
            )
        );

        // numero
        $options = array(
            'required' => true,
            'label' => 'Número de jovens para etapa',
            'placeholder' => 'Número de jovens',
            'value' => $this->total_atribuidos,
            'max_length' => 3
        );

        $this->inputsHelper()->integer('total_atribuidos', $options);

        $resources = TurnoEstudo::getDescriptiveValues();
        $resources = array_replace([null => 'Turno'], $resources);

        $options = array(
            'required'  => false,
            'label'     => 'Turno em que estuda',
            'value'     => $this->turno,
            'resources' => $resources
        );

        $this->inputsHelper()->select('turno', $options);

        $this->inputsHelper()->select('turno', $options);

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

        if (is_numeric($this->selecao_data_etapa_id)
            && is_numeric($this->etapa) && is_numeric($this->total_atribuidos)
        ) {
            $turno_campo = TurnoEstudo::getDescriptiveValues();

            $serie_campo = SerieEstudo::getDescriptiveValues();

            $avaliacao_campo = AvaliacaoEtapa::getDescriptiveValues();

            $area_interesse = AreaInteresse::getDescriptiveValues();

            $etapa_data = false;

            if ($_GET['busca'] == 'S') {
                $this->nome_acao = 'Confirmar Atribuição';
                $this->acao = sprintf(
                    'go("selecao_atribuir_inscritos_etapa_lst.php?busca=C&processo_seletivo_id=%s&etapa=%s&etapa_situacao=%s&selecao_data_etapa_id=%s&total_atribuidos=%s&turno=%s&egresso=%s&encaminhamento=%s&inicial_min=%s&inicial_max=%s&area_selecionado=%s")',
                    $this->processo_seletivo_id,
                    $this->etapa,
                    $this->etapa_situacao,
                    $this->selecao_data_etapa_id,
                    $this->total_atribuidos,
                    $this->turno,
                    $this->egresso,
                    $this->encaminhamento,
                    $this->inicial_min,
                    $this->inicial_max,
                    $this->area_selecionado
                );
            } elseif ($_GET['busca'] == 'C') {
                $etapa_data = $this->selecao_data_etapa_id;

                $objPessoa = new clsPmieducarInscrito();
                $pessoas = $objPessoa->listaDataEtapa(
                    $this->processo_seletivo_id,
                    $this->etapa,
                    $this->etapa_situacao,
                    null,
                    null,
                    null,
                    $this->inicial_min,
                    $this->inicial_max,
                    $this->turno,
                    $this->egresso,
                    $this->encaminhamento,
                    0,
                    $this->total_atribuidos,
                    false,
                    $this->area_selecionado
                );

                //print_r($pessoas);

                if ($pessoas) {
                    foreach ($pessoas as $pessoa) {
                        if (is_numeric($pessoa['cod_inscrito'])) {
                            $objEtapa = new clsPmieducarInscritoEtapa();
                            $objEtapa->ref_cod_inscrito = $pessoa['cod_inscrito'];
                            $objEtapa->etapa = $this->etapa;
                            $objEtapa->ref_cod_etapa_data = $etapa_data;
                            $retorno = $objEtapa->edita();
                            print('editando ' + $retorno);
                        }
                    }
                }
            }

            $objPessoa = new clsPmieducarInscrito();
            $pessoas = $objPessoa->listaDataEtapa(
                $this->processo_seletivo_id,
                $this->etapa,
                $this->etapa_situacao,
                $etapa_data,
                null,
                null,
                $this->inicial_min,
                $this->inicial_max,
                $this->turno,
                $this->egresso,
                $this->encaminhamento,
                0,
                $this->total_atribuidos,
                false,
                $this->area_selecionado
            );

            if ($pessoas) {
                foreach ($pessoas as $pessoa) {
                    $etapa_horario = '';
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

                    $pessoa['data_etapa']
                        = Portabilis_Date_Utils::pgSQLToBr($pessoa['data_etapa']);


                    if ($pessoa['ref_cod_etapa_data']) {
                        $etapa_horario = "{$pessoa['data_etapa']} {$pessoa['horario']}";
                    }

                    $this->addLinhas(
                        [
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
                            $etapa_horario
                        ]
                    );
                }
            }
        }

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
