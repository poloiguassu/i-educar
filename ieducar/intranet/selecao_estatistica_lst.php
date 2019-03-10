<?php

use iEducar\Modules\Inscritos\Model\SerieEstudo;

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo("Estatísticas Processo Seletivo");
        $this->processoAp = 21470;
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsListagem
{
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('report_selection');
    }

    function Gerar()
    {
        $this->titulo = "Informações Alunos";

        $this->addCabecalhos(
            array(
                "Masculino",
                "Feminino",
                "Idade",
                "Escolaridade"
            )
        );

        $options = array(
            'required' => false,
            'label'    => "Avaliação Projeto Etapa 1",
            'value'     => $_GET['etapa_1'],
            'resources' => array(
                '' => '1ª Etapa',
                '1' => 'Não Adequado',
                '2' => 'Parcialmente Adequado',
                '3' => 'Adequado'
            ),
        );

        $this->inputsHelper()->select('etapa_1', $options);

        $options = array(
            'required' => false,
            'label'    => "Avaliação Projeto Etapa 2",
            'value'     => $_GET['etapa_2'],
            'resources' => array(
                '' => '2ª Etapa',
                '-1' => 'Não Avaliado',
                '1' => 'Não Adequado',
                '2' => 'Parcialmente Adequado',
                '3' => 'Adequado'
            ),
        );

        $this->inputsHelper()->select('etapa_2', $options);

        $where = "";

        $par_etapa_1 = null;

        if ($_GET['etapa_1']) {
            $par_etapa_1 = $_GET['etapa_1'];
        }

        if ($_GET['etapa_2']) {
            $par_etapa_2 = $_GET['etapa_2'];
        }

        // Paginador
        $limite = 200;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $limite-$limite: 0;

        $sql = "SELECT
                    COUNT(CASE WHEN sexo = 'M' THEN 1 END) as masculino,
					COUNT(CASE WHEN sexo = 'F' THEN 1 END) as feminino
                FROM
                    pmieducar.inscrito,
                    pmieducar.aluno,
                    cadastro.fisica
                WHERE
                    sexo is not null
                AND
                    ref_cod_aluno = cod_aluno
                AND
                    idpes = ref_idpes";

        $sexoInscritos = Portabilis_Utils_Database::selectRow($sql);

        $sql = "SELECT
                    date_part('year', age(data_nasc)) :: int as idade, count(*)
                FROM
                    pmieducar.inscrito,
                    pmieducar.aluno,
                    cadastro.fisica
                WHERE
                    data_nasc is not null
                AND
                    ref_cod_aluno = cod_aluno
                AND
                    idpes = ref_idpes
                GROUP BY idade
                ORDER BY idade";

        $idadeInscritos = Portabilis_Utils_Database::fetchPreparedQuery($sql);

        $sql = "SELECT
                    estudando_serie as label, COUNT(*)
                FROM
                    pmieducar.inscrito,
                    pmieducar.aluno,
                    cadastro.fisica
                WHERE
                    estudando_serie is not null
                AND
                    ref_cod_aluno = cod_aluno
                AND
                    idpes = ref_idpes
                GROUP BY estudando_serie";

        $estudando = Portabilis_Utils_Database::fetchPreparedQuery($sql);

        $sql = "SELECT
                    COUNT(egresso)
                FROM
                    pmieducar.inscrito
                WHERE
                    egresso is not null";

        $egresso = Portabilis_Utils_Database::selectField($sql);

        $idadeInscritos[] = array(
            'idade' => 'Egresso',
            'count' => $egresso
        );

        $estudandoInscritos = array();

        $serieEstudo = SerieEstudo::getDescriptiveValues();

        foreach ($estudando as $serie) {
            $key = $serieEstudo[$serie['label']];
            $estudandoInscritos["{$key}"] = $serie['count'];
        }

        $estudandoInscritos["egresso"] = $egresso;

        $this->addLinhas(
            array(
                $sexoInscritos['masculino'],
                $sexoInscritos['feminino'],
                $idadeInscritos,
                $estudandoInscritos,
            )
        );

        /*SELECT
        date_part('year', age(data_nasc)) :: int as idade, count(*)
        FROM
        pmieducar.inscrito,
        pmieducar.aluno,
        cadastro.fisica
        WHERE
        data_nasc is not null
        AND
        ref_cod_aluno = cod_aluno
        AND
        idpes = ref_idpes
        GROUP BY idade*/

        $this->largura = "100%";
        $this->addPaginador2(
            "selecao_inscritos_lst.php",
            $total, $_GET, $this->nome, $limite
        );

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos(
            array(
                $_SERVER['SERVER_NAME']."/intranet" => "Início",
                "" => "Listagem de Inscritos Processo Seletivo"
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
