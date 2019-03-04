<?php

require_once('include/clsBase.inc.php');
require_once('include/clsDetalhe.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Editora");
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

    public $cod_acervo_editora;
    public $ref_usuario_cad;
    public $ref_usuario_exc;
    public $ref_idtlog;
    public $ref_sigla_uf;
    public $nm_editora;
    public $cep;
    public $cidade;
    public $bairro;
    public $logradouro;
    public $numero;
    public $telefone;
    public $ddd_telefone;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Editora - Detalhe';

        $this->cod_acervo_editora=$_GET['cod_acervo_editora'];

        $tmp_obj = new clsPmieducarAcervoEditora($this->cod_acervo_editora);
        $registro = $tmp_obj->detalhe();

        if (class_exists('clsPmieducarBiblioteca')) {
            $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
            $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
            $registro['ref_cod_biblioteca'] = $det_ref_cod_biblioteca['nm_biblioteca'];
            if (class_exists('clsPmieducarInstituicao')) {
                $registro['ref_cod_instituicao'] = $det_ref_cod_biblioteca['ref_cod_instituicao'];
                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];
            } else {
                $registro['ref_cod_instituicao'] = 'Erro na geracao';
                echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
            }
        }

        if (class_exists('clsPmieducarEscola')) {
            $registro['ref_cod_escola'] = $det_ref_cod_biblioteca['ref_cod_escola'];
            $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $idpes = $det_ref_cod_escola['ref_idpes'];
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

        if (! $registro) {
            header('location: educar_acervo_editora_lst.php');
            die();
        }

        if (class_exists('clsTipoLogradouro')) {
            $obj_ref_idtlog = new clsTipoLogradouro($registro['ref_idtlog']);
            $det_ref_idtlog = $obj_ref_idtlog->detalhe();
            $registro['ref_idtlog'] = $det_ref_idtlog['descricao'];
        } else {
            $registro['ref_idtlog'] = 'Erro na geracao';
            echo "<!--\nErro\nClasse nao existente: clsUrbanoTipoLogradouro\n-->";
        }

        if (class_exists('clsUf')) {
            $obj_ref_sigla_uf = new clsUf($registro['ref_sigla_uf']);
            $det_ref_sigla_uf = $obj_ref_sigla_uf->detalhe();
            $registro['ref_sigla_uf'] = $det_ref_sigla_uf['nome'];
        } else {
            $registro['ref_sigla_uf'] = 'Erro na geracao';
            echo "<!--\nErro\nClasse nao existente: clsUf\n-->";
        }

        if ($registro['nm_editora']) {
            $this->addDetalhe([ 'Editora', "{$registro['nm_editora']}"]);
        }

        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['ref_cod_instituicao']}"]);
            }
        }

        if ($nivel_usuario == 1 || $nivel_usuario == 2) {
            if ($registro['ref_cod_escola']) {
                $this->addDetalhe([ 'Escola', "{$registro['ref_cod_escola']}"]);
            }
        }

        if ($registro['ref_cod_biblioteca']) {
            $this->addDetalhe([ 'Biblioteca', "{$registro['ref_cod_biblioteca']}"]);
        }

        if ($registro['cep']) {
            $registro['cep'] = int2CEP($registro['cep']);
            $this->addDetalhe([ 'CEP', "{$registro['cep']}"]);
        }
        if ($registro['ref_sigla_uf']) {
            $this->addDetalhe([ 'Estado', "{$registro['ref_sigla_uf']}"]);
        }
        if ($registro['cidade']) {
            $this->addDetalhe([ 'Cidade', "{$registro['cidade']}"]);
        }
        if ($registro['bairro']) {
            $this->addDetalhe([ 'Bairro', "{$registro['bairro']}"]);
        }
        if ($registro['ref_idtlog']) {
            $this->addDetalhe([ 'Tipo Logradouro', "{$registro['ref_idtlog']}"]);
        }
        if ($registro['logradouro']) {
            $this->addDetalhe([ 'Logradouro', "{$registro['logradouro']}"]);
        }
        if ($registro['numero']) {
            $this->addDetalhe([ 'N&uacute;mero', "{$registro['numero']}"]);
        }
        if ($registro['ddd_telefone']) {
            $this->addDetalhe([ 'DDD Telefone', "{$registro['ddd_telefone']}"]);
        }
        if ($registro['telefone']) {
            $this->addDetalhe([ 'Telefone', "{$registro['telefone']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(595, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_acervo_editora_cad.php';
            $this->url_editar = "educar_acervo_editora_cad.php?cod_acervo_editora={$registro['cod_acervo_editora']}";
        }

        $this->url_cancelar = 'educar_acervo_editora_lst.php';
        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
         $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
         'educar_biblioteca_index.php'                  => 'Trilha Jovem Iguassu - Biblioteca',
         ''                                  => 'Detalhe da editora'
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
