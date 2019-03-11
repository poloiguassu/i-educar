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

        foreach ($_GET as $nm => $var)
        {
            $this->$nm = $var;
        }
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

        $registroSelecao = array();
        $etapas = array();

        if (is_null($this->processo_seletivo_id)) {
            $objSelecao = new clsPmieducarProcessoSeletivo();
            $registroSelecao = $objSelecao->getUltimoProcessoSeletivo();
            $this->processo_seletivo_id = $registroSelecao['cod_selecao_processo'];
        }

        $objSelecao = new clsPmieducarProcessoSeletivo(
            $this->processo_seletivo_id
        );

        $registroSelecao = $objSelecao->detalhe();

        for ($i = 1; $i <= $registroSelecao['total_etapas']; $i++) {
            $etapas[$i] = $this->{'etapa_' . $i};
        }

        $objPessoa = new clsPmieducarInscrito();
        $pessoas = $objPessoa->listaAvaliacao(
            $etapas,
            $this->ref_cod_selecao_processo,
            $this->nm_inscrito,
            $this->id_federal,
            null,
            $this->inicial_min,
            $this->inicial_max
        );

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
