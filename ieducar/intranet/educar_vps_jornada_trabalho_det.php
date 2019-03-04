<?php
require_once('include/clsBase.inc.php');
require_once('include/clsDetalhe.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Jornada de Trabalho");
        $this->processoAp = '21455';
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_vps_jornada_trabalho;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_jornada_trabalho;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Jornada de Trabalho - Detalhe';

        $this->cod_vps_jornada_trabalho = $_GET['cod_vps_jornada_trabalho'];

        $tmp_obj = new clsPmieducarVPSJornadaTrabalho($this->cod_vps_jornada_trabalho);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            header('location: educar_vps_jornada_trabalho_lst.php');
            die();
        }

        if ($registro['cod_vps_jornada_trabalho']) {
            $this->addDetalhe(['Código Jornada de Trabalho', "{$registro['cod_vps_jornada_trabalho']}"]);
        }
        if ($registro['nm_jornada_trabalho']) {
            $this->addDetalhe(['Jornada de Trabalho', "{$registro['nm_jornada_trabalho']}"]);
        }
        if ($registro['carga_horaria_semana']) {
            $this->addDetalhe(['Carga Horária Semanal', "{$registro['carga_horaria_semana']} horas"]);
        }
        if ($registro['carga_horaria_diaria']) {
            $this->addDetalhe(['Carga Horária Diária', "{$registro['nm_jornada_trabalho']} horas"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(590, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_vps_jornada_trabalho_cad.php';
            $this->url_editar = "educar_vps_jornada_trabalho_cad.php?cod_vps_jornada_trabalho={$registro['cod_vps_jornada_trabalho']}";
        }

        $this->url_cancelar = 'educar_vps_jornada_trabalho_lst.php';
        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            'educar_vps_index.php'                => 'Trilha Jovem - Jornada de Trabalho',
            ''                                    => 'Detalhe da Jornada de Trabalho'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm($miolo);
// gera o html
$pagina->MakeAll();
