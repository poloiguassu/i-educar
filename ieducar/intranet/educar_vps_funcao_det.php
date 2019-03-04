<?php

require_once('include/clsBase.inc.php');
require_once('include/clsDetalhe.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Fun��o");
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

    public $cod_vps_funcao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_funcao;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Função - Detalhe';

        $this->cod_vps_funcao=$_GET['cod_vps_funcao'];

        $tmp_obj = new clsPmieducarVPSFuncao($this->cod_vps_funcao);
        $registro = $tmp_obj->detalhe();

        if (class_exists('clsPmieducarEscola')) {
            $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $idpes = $det_ref_cod_escola['ref_idpes'];

            if (class_exists('clsPmieducarInstituicao')) {
                $registro['ref_cod_instituicao'] = $det_ref_cod_escola['ref_cod_instituicao'];
                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];
            } else {
                $registro['ref_cod_instituicao'] = 'Erro na geracao';
                echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
            }

            if ($idpes) {
                $obj_escola = new clsPessoaJuridica($idpes);
                $obj_escola_det = $obj_escola->detalhe();
                $registro['ref_cod_escola'] = $obj_escola_det['fantasia'];
            } else {
                $obj_escola = new clsPmieducarEscolaComplemento($registro['ref_cod_escola']);
                $obj_escola_det = $obj_escola->detalhe();
                $registro['ref_cod_escola'] = $obj_escola_det['nm_escola'];
            }
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if (!$registro) {
            header('location: educar_vps_funcao_lst.php');
            die();
        }

        if ($nivel_usuario == 1 || $nivel_usuario == 2) {
            if ($registro['ref_cod_escola']) {
                $this->addDetalhe(['Instituição', "{$registro['ref_cod_escola']}"]);
            }
        }

        if ($registro['cod_vps_funcao']) {
            $this->addDetalhe(['Código da Função', "{$registro['cod_vps_funcao']}"]);
        }
        if ($registro['nm_funcao']) {
            $this->addDetalhe(['Função', "{$registro['nm_funcao']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe(['Descrição', "{$registro['descricao']}"]);
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(593, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_vps_funcao_cad.php';
            $this->url_editar = "educar_vps_funcao_cad.php?cod_vps_funcao={$registro['cod_vps_funcao']}";
        }

        $this->url_cancelar = 'educar_vps_funcao_lst.php';
        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
             $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
             'educar_vps_index.php'                => 'Trilha Jovem - VPS',
             ''                                    => 'Detalhe da Função'
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
