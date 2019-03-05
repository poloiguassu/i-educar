<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';
require_once 'include/pmieducar/clsPmieducarInscrito.inc.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';

require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/Validation.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'image_check.php';

class clsIndex extends clsBase
{
    function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Processo Seletivo - Cadastro');
        $this->processoAp = 21472;
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsCadastro
{
    var $cod_selecao_processo;
    var $ref_cod_escola;
    var $ano_letivo;
    var $ref_cod_curso;
    var $numero_selecionados;
    var $total_etapas;
    var $status;
    var $ativo;

    var $caminho_det;
    var $caminho_lst;

    function Inicializar()
    {
        $this->cod_selecao_processo = @$_GET['cod_selecao_processo'];
        $this->retorno       = 'Novo';

        if (is_numeric($this->cod_selecao_processo)) {
            $this->retorno = 'Editar';
            $objSelecao = new clsPmieducarProcessoSeletivo(
                $this->cod_selecao_processo
            );

            $registro = $objSelecao->detalhe();

            if ($registro) {
                foreach ($registro AS $campo => $val) {
                    $this->$campo = $val;
                }
            }

            $objEscola = new clsPmieducarEscola($this->ref_cod_escola);
            $detalheEscola = $objEscola->detalhe();

            $this->ref_cod_instituicao = $detalheEscola['ref_cod_instituicao'];

            $this->ano_letivo = $this->ref_ano;
        }

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $this->retorno == "Editar" ? $this->retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos(
            array(
                $_SERVER['SERVER_NAME'] . "/intranet" => "Início",
                ""                                    => "Processo Seletivo"
            )
        );

        $this->enviaLocalizacao($localizacao->montar());

        return $this->retorno;
    }

    function Gerar()
    {
        $this->url_cancelar = $this->retorno == 'Editar' ?
            'selecao_processo_det.php?cod_selecao_processo=' . $this->cod_selecao_processo : 'selecao_processo_lst.php';

        $obrigatorio              = false;
        $instituicao_obrigatorio  = true;
        $escola_curso_obrigatorio = true;
        $curso_obrigatorio        = true;
        $get_escola               = true;
        $get_escola_curso_serie   = false;
        $sem_padrao               = true;
        $get_curso                = true;

        include 'include/pmieducar/educar_campo_lista.php';

        $this->inputsHelper()->dynamic(
            'anoLetivo',
            array('disabled' => $bloqueia)
        );


        $options = array(
            'required'    => true,
            'label'       => 'Número de Selecionados',
            'placeholder' => 'Total Selecionados',
            'value'       => $this->numero_selecionados,
            'max_length'  => 5,
            'size'        => 20
        );

        $this->inputsHelper()->integer('numero_selecionados', $options);

        $options = array(
            'required'    => true,
            'label'       => 'Número de Etapas',
            'placeholder' => 'Número Etapas',
            'value'       => $this->total_etapas,
            'max_length'  => 2,
            'size'        => 20
        );

        $this->inputsHelper()->integer('total_etapas', $options);
    }

    function Novo()
    {
        @session_start();
            $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $cadastrou = $this->createOrUpdate();

        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.';
            header('Location: selecao_processo_lst.php');
            die();
        }

        $this->mensagem = Portabilis_String_utils::toLatin1('Cadastro não realizado.');

        return false;
    }

    function Editar()
    {
        @session_start();
            $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $cadastrou = $this->createOrUpdate($this->cod_inscrito);

        if ($cadastrou)
        {
            $this->mensagem .= 'Edição efetuada com sucesso.';
            header('Location: selecao_processo_lst.php');
            die();
        }
    }

    function Excluir() {
        echo '<script>document.location="selecao_processo_lst.php";</script>';
        return true;
    }

    protected function createOrUpdate($selecaoId = null) {
        $processoSeletivo = new clsPmieducarProcessoSeletivo();

        if($this->pessoa_logada)
        {
            $processoSeletivo->ref_usuario_cad = $this->pessoa_logada;
        }
        $processoSeletivo->cod_selecao_processo = $selecaoId;
        $processoSeletivo->ref_cod_escola = $this->ref_cod_escola;
        $processoSeletivo->ref_ano = $this->ano_letivo;
        $processoSeletivo->ref_cod_curso = $this->ref_cod_curso;
        $processoSeletivo->numero_selecionados = $this->numero_selecionados;
        $processoSeletivo->total_etapas = $this->total_etapas;

        $sql = "select 1 from pmieducar.selecao_processo WHERE cod_selecao_processo = $1 limit 1";

        if (!$selecaoId || Portabilis_Utils_Database::selectField($sql, $selecaoId) != 1)
            $selecaoId = $processoSeletivo->cadastra();
        else
            $processoSeletivo->edita();

        return $selecaoId;
    }

}

$pagina = new clsIndex();

$miolo = new indice();

$pagina->addForm($miolo);

$pagina->MakeAll();

?>
