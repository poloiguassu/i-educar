<?php
require_once('include/clsBase.inc.php');
require_once('include/clsListagem.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');
require_once('include/localizacaoSistema.php');
require_once('lib/App/Model/Meses.php');

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Gest�o de VPS");
        $this->processoAp = 21455;
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
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('report');
    }

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Gestão de VPS - Estatísticas';

        $this->addCabecalhos([
            'Situação',
            'Número de Jovens'
        ]);

        $registroMeses = App_Model_Meses::getInstance()->getValues();
        unset($registroMeses[0]);

        foreach ($registroMeses as $index => $mes) {
            $obj = new clsPmieducarVPSAlunoEntrevista();
            // HACK: colocar campo para usu�rio selecionar o ano que quer buscar pelo ano letivo
            $ano = 2017;
            $registroMes = $obj->listaMes($index, $ano);
            $numero_encaminhados = $registroMes ? count($registroMes) : 0;

            $lista_busca = [
                "<a href=\"educar_vps_aluno_lst.php?situacao_vps=\" target=\"_blank\">$ano / {$mes}</a>",
                "<a href=\"educar_vps_aluno_lst.php?situacao_vps=\" target=\"_blank\">{$numero_encaminhados}</a>",
            ];

            $this->addLinhas($lista_busca);
        }

        $this->addLinhas($lista_busca);

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            'educar_vps_index.php'                => 'Trilha Jovem Iguassu - VPS',
        ]);

        $this->enviaLocalizacao($localizacao->montar());

        $this->largura = '100%';
    }
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm($miolo);

$pagina->MakeAll();
