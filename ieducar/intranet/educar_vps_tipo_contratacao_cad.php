<?php
require_once('include/clsBase.inc.php');
require_once('include/clsCadastro.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Modalidade de Contratação");
        $this->processoAp = 21455;
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_vps_tipo_contratacao;
    public $ref_cod_instituicao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->cod_vps_tipo_contratacao = $_GET['cod_vps_tipo_contratacao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(597, $this->pessoa_logada, 11, 'educar_vps_tipo_contratacao_lst.php');

        if (is_numeric($this->cod_vps_tipo_contratacao)) {
            $obj = new clsPmieducarVPSContratacaoTipo($this->cod_vps_tipo_contratacao);
            $registro  = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {	// passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(597, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_vps_tipo_contratacao_det.php?cod_vps_tipo_contratacao={$registro['cod_vps_tipo_contratacao']}" : 'educar_vps_tipo_contratacao_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            'educar_vps_index.php'                => 'Trilha Jovem - VPS',
            ''                                    => "{$nomeMenu} Modelo de Contratação"
        ]);

        $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_vps_tipo_contratacao', $this->cod_vps_tipo_contratacao);

        if ($this->cod_vps_tipo_contratacao) {
            $instituicao_desabilitado = true;
        }

        // foreign keys
        $instituicao_obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_tipo', 'Modelo de Contratação', $this->nm_tipo, 30, 255, true);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(597, $this->pessoa_logada, 11, 'educar_vps_tipo_contratacao_lst.php');

        $obj = new clsPmieducarVPSContratacaoTipo(null, $this->ref_cod_instituicao, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, null, null, 1);
        $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            header('Location: educar_vps_tipo_contratacao_lst.php');
            die();

            return true;
        }

        $this->mensagem  = 'Cadastro não realizado.<br>';
        $this->mensagem .= "<!--\nErro ao cadastrar clsPmieducarVPSContratacaoTipo\nvalores obrigat&oacute;rios\nis_numeric($this->ref_cod_instituicao) && is_numeric($this->pessoa_logada) && is_string($this->nm_tipo)\n-->";

        return false;
    }

    public function Editar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(597, $this->pessoa_logada, 11, 'educar_vps_tipo_contratacao_lst.php');

        $obj = new clsPmieducarVPSContratacaoTipo($this->cod_vps_tipo_contratacao, $this->ref_cod_instituicao, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, null, null, 1);
        $editou = $obj->edita();

        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            header('Location: educar_vps_tipo_contratacao_lst.php');
            die();

            return true;
        }

        $this->mensagem  = 'Edição não realizada.<br>';
        $this->mensagem .= "<!--\nErro ao editar clsPmieducarVPSContratacaoTipo\nvalores obrigat&oacute;rios\nif(is_numeric($this->cod_vps_tipo_contratacao) && is_numeric($this->ref_usuario_exc))\n-->";

        return false;
    }

    public function Excluir()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(597, $this->pessoa_logada, 11, 'educar_vps_tipo_contratacao_lst.php');

        $obj = new clsPmieducarVPSContratacaoTipo($this->cod_vps_tipo_contratacao, null, $this->pessoa_logada, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            header('Location: educar_vps_tipo_contratacao_lst.php');
            die();

            return true;
        }

        $this->mensagem  = 'Exclusão não realizada.<br>';
        $this->mensagem .=  "<!--\nErro ao excluir clsPmieducarVPSContratacaoTipo\nvalores obrigat&oacute;rios\nif(is_numeric($this->cod_vps_tipo_contratacao) && is_numeric($this->pessoa_logada))\n-->";

        return false;
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
