<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';

class clsIndex extends clsBase
{
    function Formular()
    {
        $this->SetTitulo('Processo Seletivo');
        $this->processoAp = 21472;
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsDetalhe
{
    function Gerar()
    {
        $this->titulo = 'Detalhe da Jovem - Processo Seletivo';

        $cod_selecao_processo = @$_GET['cod_selecao_processo'];

        $objSelecao = new clsPmieducarProcessoSeletivo($cod_selecao_processo);

        $detalhe = $objSelecao->detalhe();

        $this->addDetalhe(array('Ano', $detalhe['ref_ano']));

        if ($detalhe['numero_selecionados']) {
            $this->addDetalhe(
                array(
                    'Total de Selecionados',
                    $detalhe['numero_selecionados']
                )
            );
        }

        if ($detalhe['total_etapas']) {
            $this->addDetalhe(
                array(
                    'Número de Etapas',
                    $detalhe['total_etapas']
                )
            );
        }

        $this->url_novo     = 'selecao_processo_cad.php';
        $this->url_editar   = 'selecao_processo_cad.php?cod_selecao_processo=' . $cod_selecao_processo;
        $this->url_cancelar = 'selecao_processo_lst.php';

        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos(
            array(
                $_SERVER['SERVER_NAME'] . "/intranet" => "Início",
                ""                                    => "Detalhe Processo Seletivo"
            )
        );

        $this->enviaLocalizacao($localizacao->montar());
    }
}

$pagina = new clsIndex();

$miolo = new indice();

$pagina->addForm($miolo);

$pagina->MakeAll();
