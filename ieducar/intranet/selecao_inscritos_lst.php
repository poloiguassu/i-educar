<?php

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
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('list_filter');
    }

    public function Gerar()
    {
        $this->titulo = 'Informações Alunos';

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

        $this->campoTexto(
            'nm_inscrito',
            'Nome',
            $_GET['nm_inscrito'],
            '50',
            '255',
            true
        );

        $this->campoCpf('id_federal', 'CPF', $_GET['id_federal'], '50', '', true);

        $options = [
            'required' => false,
            'label'    => 'Avaliação Projeto Etapa 1',
            'value'     => $_GET['etapa_1'],
            'resources' => [
                '' => '1ª Etapa',
                '1' => 'Não Adequado',
                '2' => 'Parcialmente Adequado',
                '3' => 'Adequado'
            ],
        ];

        $this->inputsHelper()->select('etapa_1', $options);

        $options = [
            'required' => false,
            'label'    => 'Avaliação Projeto Etapa 2',
            'value'     => $_GET['etapa_2'],
            'resources' => [
                '' => '2ª Etapa',
                '-1' => 'Não Avaliado',
                '1' => 'Não Adequado',
                '2' => 'Parcialmente Adequado',
                '3' => 'Adequado'
            ],
        ];

        $this->inputsHelper()->select('etapa_2', $options);

        $where = '';

        $par_nome = false;

        if ($_GET['nm_inscrito']) {
            $par_nome = $_GET['nm_inscrito'];
        }

        $par_id_federal = false;

        if ($_GET['id_federal']) {
            $par_id_federal = idFederal2Int($_GET['id_federal']);
        }

        $par_etapa_1 = null;

        if ($_GET['etapa_1']) {
            $par_etapa_1 = $_GET['etapa_1'];
        }

        if ($_GET['etapa_2']) {
            $par_etapa_2 = $_GET['etapa_2'];
        }

        $dba = $db = new clsBanco();

        $objPessoa = new clsPmieducarInscrito();

        // Paginador
        $limite = 200;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite-$limite: 0;

        $turno_campo = [
            '0' => 'Não definido',
            '1' => 'Manhã',
            '2' => 'Tarde',
            '3' => 'Noite'
        ];

        $avaliacao = [
            '1' => 'Não Adequado',
            '2' => 'Parcialmente Adequado',
            '3' => 'Adequado'
        ];

        $pessoas = $objPessoa->lista(
            $par_etapa_1,
            $par_etapa_2,
            null,
            null,
            $par_nome,
            $par_id_federal,
            null,
            $iniciolimit,
            $limite
        );

        if ($pessoas) {
            foreach ($pessoas as $pessoa) {
                $cod = $pessoa['cod_inscrito'];
                $total = $pessoa['total'];
                $cpf = $pessoa['cpf'] ? int2CPF($pessoa['cpf']) : '';

                if ($pessoa['egresso'] > 0) {
                    $turno = 'Egresso ' . $pessoa['egresso'];
                } else {
                    $turno = $turno_campo[$pessoa['turno']];
                }

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
                        "<img src='imagens/noticia.jpg' border=0>
                        <a href='selecao_inscritos_det.php?cod_pessoa={$cod}'>
                        {$pessoa['nome']}</a>",
                        $data_nasc,
                        $idade,
                        $pessoa['sexo'],
                        $cpf,
                        $pessoa['rg'],
                        'ESCOLA',
                        $pessoa['estudando_serie'],
                        $pessoa['estudando_turno'],
                        $pessoa['egresso'],
                        'Area Interesse',
                        $pessoa['grupo_sanguineo'],
                        $pessoa['email'],
                        $pessoa['cep'],
                        $bairro,
                        $regiao,
                    ]
                );
            }
        }

        $this->acao = 'go("selecao_inscritos_cad.php")';
        $this->nome_acao = 'Novo';

        $this->array_botao_url[] = 'selecao_estatistica_lst.php';
        $this->array_botao[]     = 'Relatórios';

        $this->array_botao_url[] = 'selecao_importar_inscritos.php';
        $this->array_botao[]     = 'Importar inscritos';

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
