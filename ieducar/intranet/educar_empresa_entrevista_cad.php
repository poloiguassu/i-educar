<?php

require_once('include/clsBase.inc.php');
require_once('include/clsCadastro.inc.php');
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

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

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
    public $ref_cod_biblioteca;

    public function Inicializar()
    {
        $retorno = 'Novo';
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->cod_acervo_editora=$_GET['cod_acervo_editora'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(595, $this->pessoa_logada, 11, 'educar_acervo_editora_lst.php');

        if (is_numeric($this->cod_acervo_editora)) {
            $obj = new clsPmieducarAcervoEditora($this->cod_acervo_editora);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {	// passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(595, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_acervo_editora_det.php?cod_acervo_editora={$registro['cod_acervo_editora']}" : 'educar_acervo_editora_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
         $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
         'educar_biblioteca_index.php'                  => 'Trilha Jovem Iguassu - Biblioteca',
         ''        => "{$nomeMenu} editora"
    ]);
        $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_acervo_editora', $this->cod_acervo_editora);

        //foreign keys
        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'biblioteca']);

        //text
        $this->campoTexto('nm_editora', 'Editora', $this->nm_editora, 30, 255, true);

        // foreign keys
        if ($this->cod_acervo_editora) {
            $this->cep = int2CEP($this->cep);
        }

        $this->campoCep('cep', 'CEP', $this->cep, false);

        $opcoes = [ '' => 'Selecione' ];
        if (class_exists('clsUf')) {
            $objTemp = new clsUf();
            $lista = $objTemp->lista();
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['sigla_uf']}"] = "{$registro['nome']}";
                }
            }
        } else {
            echo "<!--\nErro\nClasse clsUf nao encontrada\n-->";
            $opcoes = [ '' => 'Erro na geracao' ];
        }
        $this->campoLista('ref_sigla_uf', 'Estado', $opcoes, $this->ref_sigla_uf, '', false, '', '', false, false);

        $this->campoTexto('cidade', 'Cidade', $this->cidade, 30, 60, false);
        $this->campoTexto('bairro', 'Bairro', $this->bairro, 30, 60, false);

        $opcoes = [ '' => 'Selecione' ];
        if (class_exists('clsTipoLogradouro')) {
            $objTemp = new clsTipoLogradouro();
            $lista = $objTemp->lista();
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['idtlog']}"] = "{$registro['descricao']}";
                }
            }
        } else {
            echo "<!--\nErro\nClasse clsUrbanoTipoLogradouro nao encontrada\n-->";
            $opcoes = [ '' => 'Erro na geracao' ];
        }
        $this->campoLista('ref_idtlog', 'Tipo Logradouro', $opcoes, $this->ref_idtlog, '', false, '', '', false, false);

        $this->campoTexto('logradouro', 'Logradouro', $this->logradouro, 30, 255, false);

        $this->campoNumero('numero', 'N&uacute;mero', $this->numero, 6, 6);

        $this->campoNumero('ddd_telefone', 'DDD Telefone', $this->ddd_telefone, 2, 2, false);
        $this->campoNumero('telefone', 'Telefone', $this->telefone, 10, 15, false);

        // data
    }

    public function Novo()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(595, $this->pessoa_logada, 11, 'educar_acervo_editora_lst.php');

        $this->cep = idFederal2int($this->cep);

        $obj = new clsPmieducarAcervoEditora(null, $this->pessoa_logada, null, $this->ref_idtlog, $this->ref_sigla_uf, $this->nm_editora, $this->cep, $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->telefone, $this->ddd_telefone, null, null, 1, $this->ref_cod_biblioteca);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            header('Location: educar_acervo_editora_lst.php');
            die();

            return true;
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
        echo "<!--\nErro ao cadastrar clsPmieducarAcervoEditora\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_string( $this->ref_idtlog ) && is_string( $this->ref_sigla_uf ) && is_string( $this->nm_editora ) && is_numeric( $this->cep ) && is_string( $this->cidade ) && is_string( $this->bairro ) && is_string( $this->logradouro ) && is_numeric( $this->numero )\n-->";

        return false;
    }

    public function Editar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(595, $this->pessoa_logada, 11, 'educar_acervo_editora_lst.php');

        $this->cep = idFederal2int($this->cep);

        $obj = new clsPmieducarAcervoEditora($this->cod_acervo_editora, null, $this->pessoa_logada, $this->ref_idtlog, $this->ref_sigla_uf, $this->nm_editora, $this->cep, $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->telefone, $this->ddd_telefone, null, null, 1, $this->ref_cod_biblioteca);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            header('Location: educar_acervo_editora_lst.php');
            die();

            return true;
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
        echo "<!--\nErro ao editar clsPmieducarAcervoEditora\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_acervo_editora ) && is_numeric( $this->pessoa_logada ) )\n-->";

        return false;
    }

    public function Excluir()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(595, $this->pessoa_logada, 11, 'educar_acervo_editora_lst.php');

        $obj = new clsPmieducarAcervoEditora($this->cod_acervo_editora, null, $this->pessoa_logada, null, null, null, null, null, null, null, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            header('Location: educar_acervo_editora_lst.php');
            die();

            return true;
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';
        echo "<!--\nErro ao excluir clsPmieducarAcervoEditora\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_acervo_editora ) && is_numeric( $this->pessoa_logada ) )\n-->";

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
