<?php
require_once('include/clsBase.inc.php');
require_once('include/clsDetalhe.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Modelo Contratação");
        $this->processoAp = 21455;
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

    public $cod_vps_tipo_contratacao;
    public $ref_cod_instituicao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Modelo Contratação - Detalhe';

        $this->cod_vps_tipo_contratacao = $_GET['cod_vps_tipo_contratacao'];

        $tmp_obj = new clsPmieducarVPSContratacaoTipo($this->cod_vps_tipo_contratacao);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            header('location: educar_vps_tipo_contratacao_lst.php');
            die();
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($registro['ref_cod_instituicao'] && $nivel_usuario == 1) {
            $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
            $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
            $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

            $this->addDetalhe(['Instituição', "{$registro['ref_cod_instituicao']}"]);
        }
        if ($registro['nm_tipo']) {
            $this->addDetalhe(['Modelo Contratação', "{$registro['nm_tipo']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe(['Descrição', "{$registro['descricao']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(597, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_vps_tipo_contratacao_cad.php';
            $this->url_editar = "educar_vps_tipo_contratacao_cad.php?cod_vps_tipo_contratacao={$registro['cod_vps_tipo_contratacao']}";
        }

        $this->url_cancelar = 'educar_vps_tipo_contratacao_lst.php';
        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'Início',
            'educar_vps_index.php'              => 'Trilha Jovem - VPS',
            ''                                  => 'Detalhe da modalidade de contrata��o'
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
