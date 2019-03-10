<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo('Informações Alunos');
        $this->processoAp = 21469;

        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
}

class indice extends clsListagem
{
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('edit_sheet');
    }

    public function Gerar()
    {
        $this->titulo = 'Informações Alunos';

        $this->addCabecalhos(
            [
                'Nome',
                'Sexo',
                'Idade',
                'Etapa 1',
                'Cópia RG',
                'Cópia CPF',
                'Comprovante de Residência',
                'Cópia Histórico',
                'Comprovante de Renda'
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

        $script = "javascript:showExpansivelIframe(520, 120, 'educar_escola_rede_ensino_cad_pop.php');";
        $script = "<img id='img_rede_ensino' style='display:\'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";

        $this->inputsHelper()->processoSeletivo(
            array(
                'required' => true,
                'label' => 'Processo Seletivo'
            )
        );

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
        $limite = 1200;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite-$limite: 0;

        $pessoas = $objPessoa->listaAvaliacao();

        if ($pessoas) {
            $meta = [];

            foreach ($pessoas as $key => $pessoa) {
                $objEtapa = new clsPmieducarInscritoEtapa();
                $inscritoEtapa = $objEtapa->lista($pessoa['cod_inscrito']);

                $pessoa['etapa_1'] = (!empty($inscritoEtapa)) ? $inscritoEtapa[0]['situacao'] : '';

                $total = $pessoa['total'];

                $data_nasc = $pessoa['data_nasc'];

                list($ano, $mes, $dia) = explode('-', $data_nasc);

                $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $nascimento = mktime(0, 0, 0, $mes, $dia, $ano);

                $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);

                $pessoa['nome'] = trim($pessoa['nome']);

                $meta[$key] = $pessoa['cod_inscrito'];

                $sexo = array(
                    'F' => 'Feminino',
                    'M' => 'Masculino'
                );

                $this->addLinhas(
                    [
                        $pessoa['nome'],
                        $sexo[$pessoa['sexo']],
                        $idade,
                        $pessoa['etapa_1'],
                        $pessoa['copia_rg'],
                        $pessoa['copia_cpf'],
                        $pessoa['copia_residencia'],
                        $pessoa['copia_historico'],
                        $pessoa['copia_renda']
                    ]
                );
            }

            View::share('sheet_meta', json_encode($meta, JSON_UNESCAPED_SLASHES));
        }

        $this->acao = 'go("selecao_inscritos_cad.php")';
        $this->nome_acao = 'Novo';

        $this->array_botao_url[] = 'selecao_inscritos_lst.php?fullscreen=1';
        $this->array_botao[]     = 'Tela cheia';

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
