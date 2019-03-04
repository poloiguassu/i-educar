<?php

require_once('include/clsBase.inc.php');
require_once('include/clsCadastro.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Funcao");
        $this->SetTemplate('base_pop');
        $this->processoAp = 21455;
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
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

    public $cod_vps_funcao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_funcao;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_escola;

    public function Inicializar()
    {
        $retorno = 'Novo';
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(593, $this->pessoa_logada, 11, 'educar_vps_funcao_lst.php');

        return $retorno;
    }

    public function Gerar()
    {
        echo '<script>window.onload=function(){parent.EscondeDiv(\'LoadImprimir\')}</script>';
        $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);

        $this->campoTexto('nm_funcao', 'Função', $this->nm_funcao, 30, 255, true);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(593, $this->pessoa_logada, 11, 'educar_vps_funcao_lst.php');

        $obj = new clsPmieducarVPSFuncao($this->cod_vps_funcao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_funcao, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_escola);
        $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';

            echo "<script>
					parent.document.getElementById('funcao').value = '$cadastrou';
					parent.document.getElementById('ref_cod_vps_funcao').disabled = false;
					parent.document.getElementById('tipoacao').value = '';
					parent.document.getElementById('formcadastro').submit();
				</script>";
            die();

            return true;
        }

        $this->mensagem = 'Cadastro não realizado.<br>';
        echo "<!--\nErro ao cadastrar clsPmieducarVPSFuncao\nvalores obrigatorios\nis_numeric($this->ref_usuario_cad) && is_string($this->nm_funcao)\n-->";

        return false;
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
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
?>
<script>
	document.getElementById('ref_cod_escola').value = parent.document.getElementById('ref_cod_escola').value;
</script>
