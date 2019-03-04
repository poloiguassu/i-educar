<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/Utils/Float.php';
require_once 'lib/App/Model/EntrevistaSituacao.php';
require_once 'lib/App/Model/EntrevistaResultado.php';
require_once 'lib/App/Model/VivenciaProfissionalSituacao.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Atribuir Entrevistas");
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

    public $cod_vps_entrevista;
    public $ref_cod_exemplar_tipo;
    public $ref_cod_vps_entrevista;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_vps_funcao;
    public $ref_cod_vps_jornada_trabalho;
    public $ref_cod_tipo_contratacao;
    public $empresa_id;
    public $descricao;
    public $data_entrevista;
    public $hora_entrevista;
    public $ano;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $salario;
    public $numero_vagas;
    public $numero_jovens;
    public $total_jovens;
    public $resultado_jovens;

    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public $checked;

    public $ref_cod_vps_responsavel_entrevista;
    public $principal;
    public $incluir_responsavel;
    public $excluir_responsavel;

    public $funcao;
    public $jornada_trabalho;
    public $responsavel;

    protected function setSelectionFields()
    {
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->cod_vps_entrevista = $_GET['cod_vps_entrevista'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11, 'educar_resultado_entrevista_lst.php');

        Portabilis_View_Helper_Application::loadMomentJsLib($this);

        if (is_numeric($this->cod_vps_entrevista)) {
            $obj = new clsPmieducarVPSEntrevista($this->cod_vps_entrevista);
            $registro  = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {	// passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
                $obj_det = $obj_escola->detalhe();

                $this->ref_cod_instituicao = $obj_det['ref_cod_instituicao'];
                $this->ref_cod_escola = $obj_det['cod_escola'];

                $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
                $obj_det = $obj_curso->detalhe();

                $this->ref_cod_curso = $obj_det['cod_curso'];

                $this->empresa_id = $this->ref_idpes;

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_entrevista_det.php?cod_vps_entrevista={$registro['cod_vps_entrevista']}" : 'educar_entrevista_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            'educar_vps_index.php'                => 'Trilha Jovem - VPS',
            ''                                    => "{$nomeMenu} entrevista"
        ]);

        $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }
        if (is_numeric($this->funcao)) {
            $this->ref_cod_vps_funcao = $this->funcao;
        }
        if (is_numeric($this->jornada_trabalho)) {
            $this->ref_cod_vps_jornada_trabalho = $this->jornada_trabalho;
        }
        if (is_numeric($this->responsavel)) {
            $this->ref_cod_vps_responsavel_entrevista = $this->responsavel;
        }

        $this->total_jovens = $this->numero_vagas * $this->numero_jovens;

        // foreign keys
        $desabilitado = true;
        $get_escola = true;
        $escola_obrigatorio = true;
        $instituicao_obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        $anoVisivel = false;

        // primary keys
        $this->campoOculto('cod_vps_entrevista', $this->cod_vps_entrevista);
        $this->campoOculto('total_jovens', $this->total_jovens);
        $this->campoOculto('funcao', '');
        $this->campoOculto('jornada_trabalho', '');
        $this->campoOculto('responsavel', '');
        $this->campoOculto('termino_vps', $this->termino_vps);
        $this->campoOculto('insercao_vps', $this->insercao_vps);

        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        if ($anoVisivel) {
            $helperOptions = ['situacoes' => ['em_andamento']];
            $this->inputsHelper()->dynamic('anoLetivo', ['disabled' => $bloqueia], $helperOptions);
            if ($bloqueia) {
                $this->inputsHelper()->hidden('ano_hidden', ['value' => $this->ano]);
            }
        }

        $options = ['label' => 'Empresa', 'required' => true, 'size' => 31, 'disabled' => true];

        $helperOptions = [
            'objectName'         => 'empresa',
            'hiddenInputOptions' => ['options' => ['value' => $this->empresa_id]]
        ];

        $this->inputsHelper()->simpleSearchPessoaj('nome', $options, $helperOptions);

        $opcoes = ['' => 'Selecione'];

        if (class_exists('clsPmieducarVPSFuncao')) {
            $objTemp = new clsPmieducarVPSFuncao();
            $lista = $objTemp->lista();

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['cod_vps_funcao']}"] = "{$registro['nm_funcao']}";
                }
            }
        } else {
            echo "<!--\nErro\nClasse clsPmieducarVPSFuncao nao encontrada\n-->";
            $opcoes = ['' => 'Erro na geracao'];
        }

        $this->campoLista('ref_cod_vps_funcao', 'Função/Cargo', $opcoes, $this->ref_cod_vps_funcao, '', false, '', '', true, false);

        // Idioma
        $opcoes = ['' => 'Selecione'];
        if (class_exists('clsPmieducarVPSJornadaTrabalho')) {
            $objTemp = new clsPmieducarVPSJornadaTrabalho();
            $lista = $objTemp->lista();

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['cod_vps_jornada_trabalho']}"] = "{$registro['nm_jornada_trabalho']}";
                }
            }
        } else {
            echo "<!--\nErro\nClasse clsPmieducarVPSJornadaTrabalho nao encontrada\n-->";
            $opcoes = ['' => 'Erro na geracao'];
        }

        $this->campoLista('ref_cod_vps_jornada_trabalho', 'Jornada de Trabalho', $opcoes, $this->ref_cod_vps_jornada_trabalho, '', false, '', '', true);

        $helperOptions = ['objectName' => 'idiomas'];

        $this->campoQuebra();

        $entrevistas = new clsPmieducarVPSAlunoEntrevista(null, null, $this->cod_vps_entrevista);
        $todasEntrevistas = $entrevistas->lista();
        $situacao_desabilitado = false;

        if ($todasEntrevistas) {
            foreach ($todasEntrevistas as $campo => $entrevista) {
                if ($entrevista['resultado_entrevista'] >= App_Model_EntrevistaResultado::APROVADO_EXTRA) {
                    $situacao_desabilitado = true;
                    break;
                }
            }
        }

        $this->campoLista('situacao_entrevista', 'Situacao Entrevista', App_Model_EntrevistaSituacao::getInstance()->getValues(), $this->situacao_entrevista, '', false, '', '', $situacao_desabilitado, true);

        $options = [
            'required'    => false,
            'label'       => 'Data Início VPS',
            'placeholder' => '',
            'value'       => Portabilis_Date_Utils::pgSQLToBr($this->inicio_vps),
            'size'        => 7,
        ];

        $this->inputsHelper()->date('inicio_vps', $options);

        $options = [
            'required'    => false,
            'label'       => 'Data Término VPS',
            'placeholder' => '',
            'value'       => Portabilis_Date_Utils::pgSQLToBr($this->termino_vps),
            'size'        => 7,
            'disabled'    => true
        ];

        $this->inputsHelper()->date('data_termino_vps', $options);

        $options = [
            'required'    => false,
            'label'       => 'Data Inserção Profissional',
            'placeholder' => '',
            'value'       => Portabilis_Date_Utils::pgSQLToBr($this->insercao_vps),
            'size'        => 7,
            'disabled'    => true
        ];

        $this->inputsHelper()->date('data_insercao_vps', $options);

        $this->campoQuebra();

        if ($todasEntrevistas) {
            $listaResultado = App_Model_EntrevistaResultado::getInstance()->getValues();

            foreach ($todasEntrevistas as $campo => $entrevista) {
                $entrevistaResultado = $entrevista['resultado_entrevista'];
                $desabilitado = $entrevistaResultado >= App_Model_EntrevistaResultado::APROVADO_EXTRA;
                $this->campoLista("resultado_jovens[{$entrevista['ref_cod_aluno']}]", $entrevista['nome'], $listaResultado, $entrevistaResultado, '', false, '', '', $desabilitado, true);
            }
        }

        $this->campoQuebra();

        $this->campoMonetario('salario', 'Salário', number_format($this->salario, 2, ',', '.'), 7, 7, false, '', '', 'onChange', true);

        $options = [
            'required'    => true,
            'label'       => 'Número de Vagas',
            'placeholder' => '',
            'value'       => $this->numero_vagas,
            'max_length'  => 2,
            'inline'      => false,
            'size'        => 7,
            'disabled'    => true
        ];

        $this->inputsHelper()->integer('numero_vagas', $options);

        $options = [
            'required'    => true,
            'label'       => 'Número de Jovens por vaga',
            'placeholder' => '',
            'value'       => $this->numero_jovens,
            'max_length'  => 2,
            'inline'      => false,
            'size'        => 7,
            'disabled'    => true
        ];

        $this->inputsHelper()->integer('numero_jovens', $options);

        $this->campoRotulo('data_entrevista', 'Data/Hora', Portabilis_Date_Utils::pgSQLToBr($this->data_entrevista) . ' �s ' . $this->hora_entrevista);

        $options = [
            'required'    => false,
            'label'       => 'Descrição',
            'value'       => $this->descricao,
            'cols'        => 30,
            'max_length'  => 150,
            'disabled'    => true
        ];

        $this->inputsHelper()->textArea('descricao', $options);
    }

    public function Novo()
    {
        $this->mensagem .= 'Edi��o efetuada com sucesso.<br>';
        header('Location: educar_atribuir_entrevista_vps_lst.php');
        die();

        return true;
    }

    public function Editar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11, 'educar_resultado_entrevista_lst.php');

        $entrevista = new clsPmieducarVPSEntrevista($this->cod_vps_entrevista);
        $entrevista->ref_usuario_exc = $this->pessoa_logada;

        if ($this->cod_vps_entrevista && $entrevista->existe()) {
            $str_inicio_vps = $this->inicio_vps;
            $str_termino_vps = $this->termino_vps;
            $str_insercao_vps = $this->insercao_vps;

            if (strrpos($str_inicio_vps, '/') > -1) {
                $str_inicio_vps = Portabilis_Date_Utils::brToPgSQL($this->inicio_vps);
            }

            if (strrpos($str_termino_vps, '/') > -1) {
                $str_termino_vps = Portabilis_Date_Utils::brToPgSQL($this->termino_vps);
            }

            if (strrpos($str_insercao_vps, '/') > -1) {
                $str_insercao_vps = Portabilis_Date_Utils::brToPgSQL($this->insercao_vps);
            }

            $entrevista->situacao_entrevista = $this->situacao_entrevista;
            if (!empty($str_inicio_vps)) {
                $entrevista->inicio_vps = $str_inicio_vps;
                $entrevista->termino_vps = $str_termino_vps;
                $entrevista->insercao_vps = $str_insercao_vps;
            }
            $entrevista->edita();

            foreach ($this->resultado_jovens as $aluno => $resultado) {
                $alunoEntrevista = new clsPmieducarVPSAlunoEntrevista(null, $this->cod_vps_entrevista, $aluno);

                if ($alunoEntrevista && $alunoEntrevista->existe()) {
                    $alunoEntrevista->resultado_entrevista = $resultado;
                    if ($resultado >= App_Model_EntrevistaResultado::APROVADO_EXTRA
                        && !empty($str_inicio_vps)) {
                        $alunoEntrevista->inicio_vps = $str_inicio_vps;
                        $alunoEntrevista->termino_vps = $str_termino_vps;
                        $alunoEntrevista->insercao_vps = $str_insercao_vps;

                        $detalheAlunoEntrevista = $alunoEntrevista->detalhe();

                        $alunoVPS = new clsPmieducarAlunoVPS($aluno, null, $detalheAlunoEntrevista['cod_vps_aluno_entrevista']);
                        $alunoVPS->situacao_vps = App_Model_VivenciaProfissionalSituacao::EM_CUMPRIMENTO;

                        if ($alunoVPS && $alunoVPS->existe()) {
                            $alunoVPS->edita();
                        } else {
                            $alunoVPS->ref_usuario_cad = $this->pessoa_logada;
                            $alunoVPS->cadastra();
                        }
                    }
                    $cadastrou = $alunoEntrevista->edita();
                }
            }

            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            header('Location: educar_resultado_entrevista_lst.php');
            die();

            return true;
        }

        $this->mensagem = 'Edição não realizada.<br> ';
        echo "<!--\nErro ao editar clsPmieducarAcervo\nvalores obrigatorios\nif(is_numeric($this->cod_vps_entrevista) && is_numeric($this->ref_usuario_exc))\n-->";

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
?>

<script type="text/javascript">
	(function($) {
		$(document).ready(function() {
			$.noConflict();
			console.log("valor qualquer");

			$("#inicio_vps").on("change", function() {
				var dataInicio = $("#inicio_vps").val();

				var dataVPS = moment(dataInicio, 'DD/MM/YYYY').addWeekdaysFromSet({
					'workdays': 11,
					'weekdays': [1,2,3,4,5,6],
					'exclusions': ['2017-10-12', '2017-11-02', '2017-11-15', '2017-12-25']
				});
				var dataInsercao = moment(dataInicio, 'DD/MM/YYYY').addWeekdaysFromSet({
					'workdays': 90,
					'weekdays': [0,1,2,3,4,5,6],
				});
				var termino_vps = dataVPS.format('DD/MM/YYYY');
				document.getElementById('termino_vps').value = termino_vps;
				document.getElementById('data_termino_vps').value = termino_vps;
				var insercao_vps = dataInsercao.format('DD/MM/YYYY');
				document.getElementById('insercao_vps').value = insercao_vps;
				document.getElementById('data_insercao_vps').value = insercao_vps;
			});
		}); // ready
	})(jQuery);
</script>
