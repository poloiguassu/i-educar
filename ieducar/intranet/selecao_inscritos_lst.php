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
        $this->addEstilo('localizacaoSistema');
        $this->processoAp = 21469;
    }
}

class indice extends clsListagem
{
    var $nm_inscrito = null;
    var $processo_seletivo_id = null;
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
                'Ano Conclusão',
                'Area de Interesse',
                'Tipagem Sanguinea',
                'E-mail',
                'CEP',
                'Bairro',
                'Região'
            ]
        );

        $ultimaSelecao = array();

        if (is_null($this->processo_seletivo_id)) {
            $objSelecao = new clsPmieducarProcessoSeletivo();
            $ultimaSelecao = $objSelecao->getUltimoProcessoSeletivo();
            $this->processo_seletivo_id = $ultimaSelecao['cod_selecao_processo'];
        } else {
            $objSelecao = new clsPmieducarProcessoSeletivo(
                $this->processo_seletivo_id
            );
            $ultimaSelecao = $objSelecao->detalhe();
        }

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

        $etapas = array();

        for ($i = 1; $i <= 2; $i++) {
            $resources = AvaliacaoEtapa::getDescriptiveValues();
            $resources = array_replace([null => $i . 'ª Etapa'], $resources);

            $options = [
                'required' => false,
                'label'    => 'Avaliação Projeto Etapa ' . $i,
                'value'     => $this->{'etapa_' . $i},
                'resources' => $resources,
            ];

            $this->inputsHelper()->select('etapa_' . $i, $options);

            $etapas[$i] = $this->{'etapa_' . $i};
        }

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

        $dba = $db = new clsBanco();

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
            $etapas,
            $this->ref_cod_selecao_processo,
            $this->nm_inscrito,
            $this->id_federal,
            null,
            $this->inicial_min,
            $this->inicial_max,
            $iniciolimit,
            $limite
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

                $etapa_1 = $avaliacao[$pessoa['etapa_1']];
                $etapa_2 = $avaliacao[$pessoa['etapa_2']];
                $etapa_3 = $avaliacao[$pessoa['etapa_3']];

                $this->addLinhas(
                    [
                        "<a href='selecao_inscritos_det.php?cod_inscrito={$cod}'>
                        {$pessoa['nome']}</a>",
                        Portabilis_Date_Utils::pgSQLToBr($data_nasc),
                        $idade,
                        $pessoa['sexo'],
                        $cpf,
                        $pessoa['rg'],
                        'ESCOLA',
                        $serie,
                        $turno,
                        $area_interesse[$pessoa['area_interesse']],
                        $pessoa['grupo_sanguineo'],
                        $pessoa['email'],
                        $pessoa['cep'],
                        $bairro,
                        $regiao,
                    ]
                );
            }
        }

        $this->acao = sprintf(
            'go("/module/Cadastro/Inscrito?cod_selecao_processo=%s")',
            $this->processo_seletivo_id
        );
        $this->nome_acao = 'Novo';

        $this->array_botao_url[] = 'selecao_inscritos_lst.php?fullscreen=1';
        $this->array_botao[]     = 'Tela cheia';

        $this->array_botao_url[] = 'selecao_avaliacao_lst.php';
        $this->array_botao[]     = 'Avaliar Candidatos';

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
