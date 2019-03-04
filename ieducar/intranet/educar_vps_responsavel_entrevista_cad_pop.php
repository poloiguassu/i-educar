<?php
require_once('include/clsBase.inc.php');
require_once('include/clsCadastro.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Responsável Entrevista");
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

    public $cod_vps_responsavel_entrevista;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_responsavel;
    public $email;
    public $ddd_telefone_com;
    public $telefone_com;
    public $ddd_telefone_cel;
    public $telefone_cel;
    public $observacao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $empresa_id;
    public $ref_cod_escola;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->cod_vps_responsavel_entrevista=$_GET['cod_vps_responsavel_entrevista'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11, 'educar_vps_responsavel_entrevista_lst.php');

        return $retorno;
    }

    public function Gerar()
    {
        echo '<script>window.onload=function(){parent.EscondeDiv(\'LoadImprimir\')}</script>';

        // primary keys
        $this->campoOculto('cod_vps_responsavel_entrevista', $this->cod_vps_responsavel_entrevista);

        $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);

        $this->campoOculto('empresa_id', $this->empresa_id);

        // text
        $this->campoTexto('nm_responsavel', 'Responsável Entrevista', $this->nm_responsavel, 30, 255, true);

        $this->campoTexto('email', 'E-mail', $this->email, '50', '255', false);
        $this->inputTelefone('com', 'Telefone comercial');
        $this->inputTelefone('cel', 'Celular');

        $this->campoMemo('observacao', 'Observação', $this->observacao, 60, 5, false);

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
    }

    public function Novo()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11, 'educar_vps_responsavel_entrevista_lst.php');

        $obj = new clsPmieducarVPSResponsavelEntrevista(null, null, $this->pessoa_logada, $this->nm_responsavel, $this->email, $this->ddd_telefone_com, $this->telefone_com, $this->ddd_telefone_cel, $this->telefone_cel, $this->observacao, null, null, 1, $this->ref_cod_escola, $this->empresa_id);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            echo "<script>
					parent.document.getElementById('responsavel').value = '$cadastrou';
					parent.document.getElementById('tipoacao').value = '';
					parent.document.getElementById('formcadastro').submit();
				</script>";
            die();

            return true;
        }

        $this->mensagem  = 'Cadastro não realizado.<br>';
        $this->mensagem .= "<!--\nErro ao cadastrar clsPmieducarVPSResponsavelEntrevista\nvalores obrigat�rios\nis_numeric($this->pessoa_logada) && is_string($this->nm_responsavel)\n-->";

        return false;
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
    }

    protected function inputTelefone($type, $typeLabel = '')
    {
        if (! $typeLabel) {
            $typeLabel = "Telefone {$type}";
        }

        // ddd
        $options = [
            'required'	=> false,
            'label'	   => "(ddd) / {$typeLabel}",
            'placeholder' => 'ddd',
            'value'	   => $this->{"ddd_telefone_{$type}"},
            'max_length'  => 3,
            'size'		=> 3,
            'inline'	  => true
        ];

        $this->inputsHelper()->integer("ddd_telefone_{$type}", $options);

        // telefone
        $options = [
            'required'	=> false,
            'label'	   => '',
            'placeholder' => $typeLabel,
            'value'	   => $this->{"telefone_{$type}"},
            'max_length'  => 11
        ];

        $this->inputsHelper()->integer("telefone_{$type}", $options);
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
	document.getElementById('empresa_id').value = parent.document.getElementById('empresa_id').value;
</script>
