<?php
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once ("include/localizacaoSistema.php");
require_once ("lib/App/Model/VivenciaProfissionalSituacao.php");

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Quadro de Horário" );
        $this->processoAp = 641;
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
    var $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $offset;

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('new_schedule');
    }

    function Gerar()
    {
        @session_start();
            $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = "Quadro de Horários";

        $this->addCabecalhos(
            array(
                "Situação",
                "Número de Jovens"
            )
        );

        $registroSituacao = App_Model_VivenciaProfissionalSituacao::getInstance()->getValues();
        unset($registroSituacao[0]);

        $total_alunos = 0;

        foreach($registroSituacao as $situacao_vps => $situacao)
        {
            $sql     = "select COUNT(cod_aluno_vps) from pmieducar.aluno_vps where situacao_vps = $1";
            $options = array('params' => $situacao_vps, 'return_only' => 'first-field');
            $numero_alunos = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
            $total_alunos += $numero_alunos;

            $lista_busca = array(
                "<a href=\"educar_vps_aluno_lst.php?situacao_vps=$situacao_vps\" target=\"_blank\">{$situacao}</a>",
                "<a href=\"educar_vps_aluno_lst.php?situacao_vps=$situacao_vps\" target=\"_blank\">{$numero_alunos}</a>",
            );

            $this->addLinhas($lista_busca);
        }

        $lista_busca = array(
            "<a href=\"educar_vps_aluno_lst.php?\" target=\"_blank\">Total</a>",
            "<a href=\"educar_vps_aluno_lst.php?\" target=\"_blank\">{$total_alunos}</a>",
        );

        $this->addLinhas($lista_busca);

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
            $_SERVER['SERVER_NAME'] . "/intranet" => "Início",
            "educar_vps_index.php"                => "Trilha Jovem Iguassu - Formação",
        ));

        $this->enviaLocalizacao($localizacao->montar());

        $this->largura = "100%";
    }
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
