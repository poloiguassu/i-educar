<?php
require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo("Processo Seletivo");
        $this->processoAp = 21472;
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsListagem
{
    public function __construct()
    {
        parent::__construct();
    }

    function Gerar()
    {
        $this->titulo = "Processo Seletivo";


        $this->addCabecalhos(
            array(
                "Ano", "Número Selecionados", "Número de Etapas", "Situação"
            )
        );

        $objSelecao = new clsPmieducarProcessoSeletivo();

        // Paginador
        $limite = 200;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite-$limite: 0;

        $seletivos = $objSelecao->lista();

        if ($seletivos) {
            foreach ($seletivos as $selecao) {
                $cod = $selecao['cod_selecao_processo'];
                $ano = $selecao['ref_ano'];
                $selecionados = $selecao['numero_selecionados'];
                $totalEtapas = $selecao['total_etapas'];
                $finalizado = $selecao['finalizado'];

                $this->addLinhas(
                    array(
                        "<a href='selecao_processo_det.php?cod_selecao_processo={$cod}'>
                            {$ano}
                        </a>",
                        $selecionados,
                        $totalEtapas,
                        $finalizado
                    )
                );
            }
        }

        $this->acao = "go(\"selecao_processo_cad.php\")";
        $this->nome_acao = "Novo";

        $this->array_botao_url[] = 'selecao_importar_inscritos.php';
        $this->array_botao[]     = 'Importar inscritos';

        $this->largura = "100%";
        $this->addPaginador2(
            "selecao_processo_lst.php",
            $total, $_GET, $this->nome, $limite
        );

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos(
            array(
                $_SERVER['SERVER_NAME']."/intranet" => "Início",
                "" => "Listagem de Processos Seletivos"
            )
        );

        $this->enviaLocalizacao($localizacao->montar());
    }
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm($miolo);

$pagina->MakeAll();

?>
