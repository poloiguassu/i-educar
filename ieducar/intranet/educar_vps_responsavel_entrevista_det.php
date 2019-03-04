<?php

require_once('include/clsDetalhe.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Responsável Entrevista");
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

    public $cod_vps_responsavel_entrevista;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_responsavel;
    public $ddd_telefone_com;
    public $telefone_com;
    public $ddd_telefone_cel;
    public $telefone_cel;
    public $observacao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Respons�vel Entrevista - Detalhe';

        $this->cod_vps_responsavel_entrevista = $_GET['cod_vps_responsavel_entrevista'];

        $tmp_obj = new clsPmieducarVPSResponsavelEntrevista($this->cod_vps_responsavel_entrevista);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            header('location: educar_vps_responsavel_entrevista_lst.php');
            die();
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if (class_exists('clsPmieducarEscola')) {
            $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $registro['ref_cod_escola'] = $det_ref_cod_escola['nome'];
            $registro['ref_cod_instituicao'] = $det_ref_cod_escola['ref_cod_instituicao'];

            if ($registro['ref_cod_instituicao']) {
                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];
            }
        } else {
            $registro['ref_cod_escola'] = 'Erro na geração';
            echo "<!--\nErro\nClasse não existente: clsPmieducarEscola\n-->";
        }

        if ($registro['ref_cod_escola'] && ($nivel_usuario == 1 || $nivel_usuario == 2)) {
            $this->addDetalhe(['Instituição', "{$registro['ref_cod_escola']}"]);
        }
        if ($registro['nm_responsavel']) {
            $this->addDetalhe(['Responsável', "{$registro['nm_responsavel']}"]);
        }
        if ($registro['ref_idpes'] && class_exists('clsPessoaJuridica')) {
            $obj_idpes = new clsPessoaJuridica($registro['ref_idpes']);
            $det_idpes = $obj_idpes->detalhe();
            $registro['ref_idpes'] = $det_idpes['fantasia'];
            $this->addDetalhe(['Empresa', "{$registro['ref_idpes']}"]);
        }
        if ($registro['email']) {
            $this->addDetalhe(['E-mail', "{$registro['email']}"]);
        }
        if ($registro['ddd_telefone_com'] && $registro['telefone_com']) {
            $this->addDetalhe(['Telefone comercial', "({$registro['ddd_telefone_com']}) {$registro['telefone_com']}"]);
        }
        if ($registro['ddd_telefone_cel'] && $registro['telefone_cel']) {
            $this->addDetalhe(['Telefone celular', "({$registro['ddd_telefone_cel']}) {$registro['telefone_cel']}"]);
        }
        if ($registro['observacao']) {
            $this->addDetalhe(['Observação', "{$registro['observacao']}"]);
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_vps_responsavel_entrevista_cad.php';
            $this->url_editar = "educar_vps_responsavel_entrevista_cad.php?cod_vps_responsavel_entrevista={$registro['cod_vps_responsavel_entrevista']}";
        }

        $this->url_cancelar = 'educar_vps_responsavel_entrevista_lst.php';
        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            'educar_vps_index.php'                => 'Trilha Jovem - VPS',
            ''                                    => 'Detalhe do respons�vel entrevista'
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
