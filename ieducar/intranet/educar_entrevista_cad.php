<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/Utils/Float.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Entrevistas");
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

    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public $checked;

    public $vps_entrevista_responsavel;
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
        $obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11, 'educar_entrevista_lst.php');

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

                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(598, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }

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

        // foreign keys
        $get_escola = true;
        $escola_obrigatorio = true;
        $instituicao_obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        $anoVisivel = true;

        // primary keys
        $this->campoOculto('cod_vps_entrevista', $this->cod_vps_entrevista);
        $this->campoOculto('funcao', '');
        $this->campoOculto('jornada_trabalho', '');
        $this->campoOculto('responsavel', '');

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

        $options = ['label' => 'Empresa', 'required' => true, 'size' => 30];

        $helperOptions = [
            'objectName'         => 'empresa',
            'hiddenInputOptions' => ['options' => ['value' => $this->empresa_id]]
        ];

        $this->inputsHelper()->simpleSearchPessoaj('nome', $options, $helperOptions);

        $opcoes = ['NULL' => 'Selecione'];

        // Cole��o
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

        $this->campoLista('ref_cod_vps_jornada_trabalho', 'Jornada de Trabalho', $opcoes, $this->ref_cod_vps_jornada_trabalho, '', false, '', '<img id=\'img_jornada_trabalho\' src=\'imagens/banco_imagens/escreve.gif\' style=\'cursor:hand; cursor:pointer;\' border=\'0\' onclick="showExpansivelImprimir(400, 150,\'educar_vps_jornada_trabalho_cad_pop.php\',[], \'Jornada de Trabalho\')" />');

        $this->campoLista('ref_cod_vps_funcao', 'Função/Cargo', $opcoes, $this->ref_cod_vps_funcao, '', false, '', '<img id=\'img_funcao\' src=\'imagens/banco_imagens/escreve.gif\' style=\'cursor:hand; cursor:pointer;\' border=\'0\' onclick="showExpansivelImprimir(500, 200,\'educar_vps_funcao_cad_pop.php\',[], \'Fun��o/Cargo\')" />', false, false);

        $helperOptions = ['objectName' => 'idiomas'];

        $options = [
            'label' => 'Idiomas',
            'size' => 150,
            'required' => false,
            'options' => ['value' => null]
        ];

        $this->inputsHelper()->multipleSearchIdiomas('', $options, $helperOptions);

        $this->campoQuebra();

        if ($_POST['vps_entrevista_responsavel']) {
            $this->vps_entrevista_responsavel = unserialize(urldecode($_POST['vps_entrevista_responsavel']));
        }

        if (is_numeric($this->cod_vps_entrevista) && !$_POST) {
            $obj = new clsPmieducarVPSEntrevistaResponsavel();
            $registros = $obj->lista(null, $this->cod_vps_entrevista);

            if ($registros) {
                foreach ($registros as $campo) {
                    $aux['ref_cod_vps_responsavel_entrevista_'] = $campo['ref_cod_vps_responsavel_entrevista'];
                    $aux['principal_']= $campo['principal'];
                    $this->vps_entrevista_responsavel[] = $aux;
                }

                // verifica se ja existe um responsavel principal
                if (is_array($this->vps_entrevista_responsavel)) {
                    foreach ($this->vps_entrevista_responsavel as $entrevistadores) {
                        if ($entrevistadores['principal_'] == 1) {
                            $this->checked = 1;
                            $this->campoOculto('checked', $this->checked);
                        }
                    }
                }
            }
        }

        unset($aux);

        if ($_POST['ref_cod_vps_responsavel_entrevista']) {
            if ($_POST['principal']) {
                $this->checked = 1;
                $this->campoOculto('checked', $this->checked);
            }

            $aux['ref_cod_vps_responsavel_entrevista_'] = $_POST['ref_cod_vps_responsavel_entrevista'];
            $aux['principal_'] = $_POST['principal'];
            $this->vps_entrevista_responsavel[] = $aux;

            if (is_array($this->vps_entrevista_responsavel)) {
                foreach ($this->vps_entrevista_responsavel as $responsaveis) {
                    if ($responsaveis['principal_'] == 'on') {
                        $this->checked = 1;
                        $this->campoOculto('checked', $this->checked);
                    }
                }
            }
            unset($this->ref_cod_vps_responsavel_entrevista);
            unset($this->principal);
        }

        $this->campoOculto('excluir_responsavel', '');
        unset($aux);

        if ($this->vps_entrevista_responsavel) {
            foreach ($this->vps_entrevista_responsavel as $key => $responsavel) {
                if ($this->excluir_responsavel == $responsavel['ref_cod_vps_responsavel_entrevista_']) {
                    unset($this->vps_entrevista_responsavel[$key]);
                    unset($this->excluir_responsavel);
                } else {
                    $obj_vps_entrevista_responsavel = new clsPmieducarVPSResponsavelEntrevista($responsavel['ref_cod_vps_responsavel_entrevista_']);
                    $det_vps_entrevista_responsavel = $obj_vps_entrevista_responsavel->detalhe();
                    $nm_responsavel = $det_vps_entrevista_responsavel['nm_responsavel'];
                    $this->campoTextoInv("ref_cod_exemplar_tipo_{$responsavel['ref_cod_vps_responsavel_entrevista_']}", '', $nm_responsavel, 30, 255, false, false, true);
                    $this->campoCheck("principal_{$responsavel['ref_cod_vps_responsavel_entrevista_']}", '', $responsavel['principal_'], "<a href='#' onclick=\"getElementById('excluir_responsavel').value = '{$responsavel['ref_cod_vps_responsavel_entrevista_']}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>", false, false, false);
                    $aux['ref_cod_vps_responsavel_entrevista_'] = $responsavel['ref_cod_vps_responsavel_entrevista_'];
                    $aux['principal_'] = $responsavel['principal_'];
                }
            }
        }

        $this->campoOculto('vps_entrevista_responsavel', serialize($this->vps_entrevista_responsavel));

        if (class_exists('clsPmieducarVPSResponsavelEntrevista')) {
            if (is_numeric($this->empresa_id) && is_numeric($this->ref_cod_escola)) {
                $opcoes = ['' => 'Selecione'];
                $objTemp = new clsPmieducarVPSResponsavelEntrevista();
                $objTemp->setOrderby('nm_responsavel ASC');
                $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_escola, $this->empresa_id);

                if (is_array($lista) && count($lista)) {
                    foreach ($lista as $registro) {
                        $opcoes["{$registro['cod_vps_entrevista_responsavel']}"] = "{$registro['nm_responsavel']}";
                    }
                }
            }
        } else {
            echo "<!--\nErro\nClasse clsPmieducarVPSResponsavelEntrevista n�o encontrada\n-->";
            $opcoes = ['' => 'Erro na geração'];
        }

        if (is_array($this->vps_entrevista_responsavel)) {
            $qtd_responsavel = count($this->vps_entrevista_responsavel);
        }

        // n�o existe um responsavel principal nem responsavel
        if (($this->checked != 1) && (!$qtd_responsavel || ($qtd_responsavel == 0))) {
            $this->campoLista('ref_cod_vps_responsavel_entrevista', 'Responsável', $opcoes, $this->ref_cod_vps_responsavel_entrevista, null, true, '', '', false, false);

            $this->campoCheck('principal', '&nbsp;&nbsp;<img id=\'img_responsavel\' src=\'imagens/banco_imagens/escreve.gif\' style=\'cursor:hand; cursor:pointer;\' border=\'0\' onclick="showExpansivelImprimir(500, 250,\'educar_vps_responsavel_entrevista_cad_pop.php\',[], \'Respons�vel\')" />', $this->principal, "<a href='#' onclick=\"getElementById('incluir_responsavel').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>");
        }
        // n�o existe um responsavel principal, mas existe um responsavel
        elseif (($this->checked != 1) && ($qtd_responsavel > 0)) {
            $this->campoLista('ref_cod_vps_responsavel_entrevista', 'Responsável', $opcoes, $this->ref_cod_vps_responsavel_entrevista, null, true, null, null, null, false);
            $this->campoCheck('principal', '&nbsp;&nbsp;<img src=\'imagens/banco_imagens/escreve.gif\' style=\'cursor:hand; cursor:pointer;\' border=\'0\' onclick="pesquisa_valores_popless(\'educar_vps_responsavel_entrevista_cad_pop.php\')" />', $this->principal, "<a href='#' onclick=\"getElementById('incluir_responsavel').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>");
        }
        // existe um responsavel principal
        else {
            $this->campoLista('ref_cod_vps_responsavel_entrevista', 'Responsável', $opcoes, $this->ref_cod_vps_responsavel_entrevista, '', false, '', "<img src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"pesquisa_valores_popless('educar_vps_responsavel_entrevista_cad_pop.php')\" />&nbsp;&nbsp;<a href='#' onclick=\"getElementById('incluir_responsavel').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>", false, false);
        }

        $this->campoOculto('incluir_responsavel', '');

        $this->campoQuebra();
        //-----------------------FIM RESPONSAVEL------------------------//

        $this->campoMonetario('salario', 'Salário', number_format($this->salario, 2, ',', '.'), 7, 7, false);

        $options = [
            'required'    => true,
            'label'       => 'Número de Vagas Disponíveis',
            'placeholder' => '',
            'value'       => $this->numero_vagas,
            'max_length'  => 2,
            'inline'      => false,
            'size'        => 7
        ];

        $this->inputsHelper()->integer('numero_vagas', $options);

        $options = [
            'required'    => true,
            'label'       => 'Número de Jovens por vaga disponível',
            'placeholder' => '',
            'value'       => $this->numero_jovens,
            'max_length'  => 2,
            'inline'      => false,
            'size'        => 7
        ];

        $this->inputsHelper()->integer('numero_jovens', $options);

        $options = [
            'required'    => true,
            'label'       => 'Data Entrevista',
            'placeholder' => '',
            'value'       => Portabilis_Date_Utils::pgSQLToBr($this->data_entrevista),
            'size'        => 7,
        ];

        $this->inputsHelper()->date('data_entrevista', $options);

        $this->campoHora('hora_entrevista', 'Hora entrevista', $this->hora_entrevista, true);

        $options = [
            'required'    => false,
            'label'       => 'Descrição',
            'value'       => $this->descricao,
            'cols'        => 30,
            'max_length'  => 150
        ];

        $this->inputsHelper()->textArea('descricao', $options);
    }

    public function Novo()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11, 'educar_entrevista_lst.php');

        $data_entrevista = Portabilis_Date_Utils::brToPgSQL($this->data_entrevista);
        $salario = Portabilis_Utils_Float::brToPgSQL($this->salario);

        $obj = new clsPmieducarVPSEntrevista(
            null,
            $this->ref_cod_tipo_contratacao,
            null,
            $this->pessoa_logada,
            $this->ref_cod_vps_funcao,
            $this->ref_cod_vps_jornada_trabalho,
            $this->empresa_id,
            $this->descricao,
            $this->ano,
            null,
            null,
            1,
            $this->ref_cod_escola,
            $this->ref_cod_curso,
            $salario,
            $data_entrevista,
            $this->hora_entrevista,
            $this->numero_vagas,
            $this->numero_jovens
        );

        $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $this->gravarIdiomas($cadastrou);

            $this->vps_entrevista_responsavel = unserialize(urldecode($this->vps_entrevista_responsavel));

            if ($this->vps_entrevista_responsavel) {
                foreach ($this->vps_entrevista_responsavel as $responsavel) {
                    $responsavelPrincipal = $_POST["principal_{$responsavel['ref_cod_vps_responsavel_entrevista_']}"];
                    $responsavel['principal_'] = is_null($responsavelPrincipal) ? 0 : 1;

                    $obj = new clsPmieducarVPSEntrevistaResponsavel($responsavel['ref_cod_vps_responsavel_entrevista_'], $cadastrou, $responsavel['principal_']);
                    $cadastrou2  = $obj->cadastra();

                    if (!$cadastrou2) {
                        $this->mensagem  = 'Cadastro não realizado.<br>';
                        $this->mensagem .= "<!--\nErro ao cadastrar clsPmieducarVPSEntrevistaResponsavel\nvalores obrigat�rios\nis_numeric($cadastrou) && is_numeric({$responsavel['ref_cod_vps_responsavel_entrevista_']}) && is_numeric({$responsavel['principal_']})\n-->";

                        return false;
                    }
                }
            }
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            header('Location: educar_entrevista_lst.php');
            die();

            return true;
        }
        $this->mensagem  = 'Cadastro não realizado.<br>';
        $this->mensagem .= "<!--\nErro ao cadastrar clsPmieducarVPSEntrevista\nvalores obrigatorios\nis_numeric($this->ref_cod_escola) && is_numeric($this->pessoa_logada) && is_numeric($this->ref_cod_vps_funcao) && is_numeric($this->ref_cod_vps_jornada_trabalho) && is_numeric($this->empresa_id) && is_numeric($this->ano)\n-->";

        return false;
    }

    public function Editar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11, 'educar_entrevista_lst.php');

        $this->data_entrevista = Portabilis_Date_Utils::brToPgSQL($this->data_entrevista);
        $salario = Portabilis_Utils_Float::brToPgSQL($this->salario);

        $this->vps_entrevista_responsavel = unserialize(urldecode($this->vps_entrevista_responsavel));
        $obj = new clsPmieducarVPSEntrevista(
            $this->cod_vps_entrevista,
            null,
            $this->pessoa_logada,
            null,
            $this->ref_cod_vps_funcao,
            $this->ref_cod_vps_jornada_trabalho,
            $this->empresa_id,
            $this->descricao,
            $this->ano,
            null,
            null,
            1,
            $this->ref_cod_escola,
            $this->ref_cod_curso,
            $salario,
            $this->data_entrevista,
            $this->hora_entrevista,
            $this->numero_vagas,
            $this->numero_jovens
        );

        $editou = $obj->edita();

        if ($editou) {
            $this->gravarIdiomas($this->cod_vps_entrevista);

            if ($this->vps_entrevista_responsavel) {
                $obj  = new clsPmieducarVPSEntrevistaResponsavel(null, $this->cod_vps_entrevista);
                $excluiu = $obj->excluirTodos();
                if ($excluiu) {
                    foreach ($this->vps_entrevista_responsavel as $responsavel) {
                        $responsavelPrincipal = $_POST["principal_{$responsavel['ref_cod_vps_responsavel_entrevista_']}"];
                        $responsavel['principal_'] = is_null($responsavelPrincipal) ? 0 : 1;

                        $obj = new clsPmieducarVPSEntrevistaResponsavel($responsavel['ref_cod_vps_responsavel_entrevista_'], $this->cod_vps_entrevista, $responsavel['principal_']);
                        $cadastrou2  = $obj->cadastra();

                        if (!$cadastrou2) {
                            $this->mensagem = 'Editar não realizado.<br>';
                            echo "<!--\nErro ao editar clsPmieducarVPSEntrevistaResponsavel\nvalores obrigat�rios\nis_numeric($cadastrou) && is_numeric({$responsavel['ref_cod_vps_responsavel_entrevista_']}) && is_numeric({$responsavel['principal_']})\n-->";

                            return false;
                        }
                    }
                }
                $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                header('Location: educar_entrevista_lst.php');
                die();

                return true;
            }

            return false;
        }

        $this->mensagem = 'Edição não realizada.<br>';
        echo "<!--\nErro ao editar clsPmieducarAcervo\nvalores obrigatorios\nif(is_numeric($this->cod_vps_entrevista) && is_numeric($this->ref_usuario_exc))\n-->";

        return false;
    }

    public function Excluir()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(598, $this->pessoa_logada, 11, 'educar_entrevista_lst.php');

        $obj = new clsPmieducarVPSEntrevista($this->cod_vps_entrevista, null, $this->pessoa_logada, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 0, $this->ref_cod_escola);
        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            header('Location: educar_entrevista_lst.php');
            die();

            return true;
        }

        $this->mensagem = 'Exclusão não realizada.<br>';
        echo "<!--\nErro ao excluir clsPmieducarAcervo\nvalores obrigatorios\nif(is_numeric($this->cod_vps_entrevista) && is_numeric($this->pessoa_logada))\n-->";

        return false;
    }

    public function gravarIdiomas($cod_vps_entrevista)
    {
        $objAssunto = new clsPmieducarVPSIdioma();
        $objAssunto->deletaIdiomasEntrevista($cod_vps_entrevista);

        foreach ($this->getRequest()->idiomas as $idiomaId) {
            if (! empty($idiomaId)) {
                $objAssunto = new clsPmieducarVPSIdioma();
                $objAssunto->cadastraIdiomaEntrevista($cod_vps_entrevista, $idiomaId);
            }
        }
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
	document.getElementById('ref_cod_vps_funcao').disabled = true;
	document.getElementById('ref_cod_vps_funcao').options[0].text = 'Selecione uma escola';

	document.getElementById('ref_cod_vps_jornada_trabalho').disabled = true;
	document.getElementById('ref_cod_vps_jornada_trabalho').options[0].text = 'Selecione uma instituição';

	document.getElementById('ref_cod_vps_responsavel_entrevista').disabled = true;
	document.getElementById('ref_cod_vps_responsavel_entrevista').options[0].text = 'Selecione uma empresa';

	var tempExemplarTipo;
	var tempFuncao;
	var tempJornadaTrabalho;
	var tempResponsavel;

	if(document.getElementById('ref_cod_escola').value == "")
	{
		setVisibility(document.getElementById('img_funcao'), false);
		setVisibility(document.getElementById('img_jornada_trabalho'), false);

		tempFuncao = null;
	} else {
		ajaxEscola('novo');
	}

	if(document.getElementById('ref_cod_instituicao').value == "")
	{
		setVisibility(document.getElementById('img_jornada_trabalho'), false);

		tempJornadaTrabalho = null;
	} else {
		ajaxInstituicao('novo');
	}

	if(document.getElementById('empresa_id').value == "")
	{
		setVisibility(document.getElementById('img_responsavel'), false);

		tempResponsavel = null;
	} else {
		ajaxResponsavel('novo');
	}

	function getFuncao(xml_vps_funcao)
	{
		var campoFuncao = document.getElementById('ref_cod_vps_funcao');
		var DOM_array = xml_vps_funcao.getElementsByTagName("vps_funcao");

		if(DOM_array.length)
		{
			campoFuncao.length = 1;
			campoFuncao.options[0].text = 'Selecione uma Função/Cargo';
			campoFuncao.disabled = false;

			for(var i=0; i<DOM_array.length; i++)
			{
				campoFuncao.options[campoFuncao.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_funcao"), false, false);
			}
			setVisibility(document.getElementById('img_funcao'), true);
			if(tempFuncao != null)
				campoFuncao.value = tempFuncao;
		}
		else
		{
			if(document.getElementById('ref_cod_escola').value == "")
			{
				campoFuncao.options[0].text = 'Selecione uma escola';
				setVisibility(document.getElementById('img_funcao'), false);
			}
			else
			{
				campoFuncao.options[0].text = 'A Escola não possui função/cargo';
				setVisibility(document.getElementById('img_funcao'), true);
			}
		}
	}

	function getJornadaTrabalho(xml_vps_jornada_trabalho)
	{
		var campoJornadaTrabalho = document.getElementById('ref_cod_vps_jornada_trabalho');
		var DOM_array = xml_vps_jornada_trabalho.getElementsByTagName("vps_jornada_trabalho");

		if(DOM_array.length)
		{
			campoJornadaTrabalho.length = 1;
			campoJornadaTrabalho.options[0].text = 'Selecione uma Jornada de Trabalho';
			campoJornadaTrabalho.disabled = false;

			for(var i=0; i<DOM_array.length; i++)
			{
				campoJornadaTrabalho.options[campoJornadaTrabalho.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_jornada_trabalho"), false, false);
			}
			setVisibility(document.getElementById('img_jornada_trabalho'), true);
			if(tempJornadaTrabalho != null)
				campoJornadaTrabalho.value = tempJornadaTrabalho;
		}
		else
		{
			if(document.getElementById('ref_cod_instituicao').value == "")
			{
				campoJornadaTrabalho.options[0].text = 'Selecione uma instituição';
				setVisibility(document.getElementById('img_jornada_trabalho'), false);
			}
			else
			{
				campoJornadaTrabalho.options[0].text = 'A instituição não possui jornadas de trabalhos';
				setVisibility(document.getElementById('img_jornada_trabalho'), true);
			}
		}
	}

	function getResponsavelEntrevista(xml_vps_responsavel_entrevista)
	{
		var campoResponsavelEntrevista = document.getElementById('ref_cod_vps_responsavel_entrevista');
		var DOM_array = xml_vps_responsavel_entrevista.getElementsByTagName("vps_responsavel_entrevista");

		if(DOM_array.length)
		{
			campoResponsavelEntrevista.length = 1;
			campoResponsavelEntrevista.options[0].text = 'Selecione um responsável';
			campoResponsavelEntrevista.disabled = false;

			for(var i = 0; i < DOM_array.length; i++)
			{
				campoResponsavelEntrevista.options[campoResponsavelEntrevista.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_vps_responsavel_entrevista"), false, false);
			}

			setVisibility(document.getElementById('img_responsavel'), true);

			if(tempResponsavel != null)
				campoResponsavelEntrevista.value = tempResponsavel;
		}
		else
		{
			if(document.getElementById('empresa_id').value == "")
			{
				campoResponsavelEntrevista.options[0].text = 'Selecione uma empresa';
				setVisibility(document.getElementById('img_responsavel'), false);
			}
			else
			{
				campoResponsavelEntrevista.options[0].text = 'A escola não possui responsáveis cadastrados';
				setVisibility(document.getElementById('img_responsavel'), true);
			}
		}
	}

	jQuery(document).ready(function () {
		var empresaId = jQuery("#empresa_id").val();

		jQuery("#ref_cod_instituicao").change(function() {
			ajaxInstituicao();
		});

		jQuery("#ref_cod_escola").change(function() {
			ajaxEscola();
			ajaxResponsavel();
		});

		jQuery("#empresa_nome").bind("change keyup focusout", function() {
			setInterval(function() {
				var empresaSelecionada = jQuery("#empresa_id").val();
				if(empresaSelecionada != "" && empresaId != empresaSelecionada)
				{
					console.log("afffz " + jQuery("#empresa_id").val());
					empresaId = empresaSelecionada;
					ajaxResponsavel();
				}
			}, 300);
		});

		jQuery(".chosen-container").width(jQuery("#idiomas").width() + 14);
	});

	function ajaxEscola(acao)
	{
		var campoEscola = document.getElementById('ref_cod_escola').value;

		var campoExemplarTipo = document.getElementById('ref_cod_exemplar_tipo');

		var campoFuncao = document.getElementById('ref_cod_vps_funcao');

		if(acao == 'novo')
		{
			tempFuncao = campoFuncao.value;
		}

		campoFuncao.length = 1;
		campoFuncao.disabled = true;
		campoFuncao.options[0].text = 'Carregando coleções';

		var xml_funcao = new ajax(getFuncao);
		xml_funcao.envia("educar_vps_funcao_xml.php?esc="+campoEscola);
	}

	function ajaxInstituicao(acao)
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

		var campoJornadaTrabalho = document.getElementById('ref_cod_vps_jornada_trabalho');

		if(acao == 'novo')
		{
			tempJornadaTrabalho = campoJornadaTrabalho.value;
		}

		campoJornadaTrabalho.length = 1;
		campoJornadaTrabalho.disabled = true;
		campoJornadaTrabalho.options[0].text = 'Carregando Jornada de Trabalho';

		var xml_jornada_trabalho = new ajax(getJornadaTrabalho);
		xml_jornada_trabalho.envia("educar_vps_jornada_trabalho_xml.php?inst="+campoInstituicao);
	}

	function ajaxResponsavel(acao)
	{
		var campoEscola = document.getElementById('ref_cod_escola').value;
		var campoEmpresa = document.getElementById('empresa_id').value;

		if(campoEmpresa != "" && campoEscola != "")
		{
			var campoResponsavel = document.getElementById('ref_cod_vps_responsavel_entrevista');

			if(acao == 'novo')
			{
				tempResponsavel = campoResponsavel.value;
			}

			campoResponsavel.length = 1;
			campoResponsavel.disabled = true;
			campoResponsavel.options[0].text = 'Carregando responsável';

			var xml_jornada_trabalho = new ajax(getResponsavelEntrevista);
			console.log("valores esc=" + campoEscola + "&idpes" + campoEmpresa);
			xml_jornada_trabalho.envia("educar_entrevista_xml.php?esc=" + campoEscola + "&idpes=" + campoEmpresa);
		}
	}

	function fixupPrincipalCheckboxes() {
		$j('#principal').hide();

		var $checkboxes = $j("input[type='checkbox']").filter("input[id^='principal_']");

		$checkboxes.change(function(){
			$checkboxes.not(this).removeAttr('checked');
		});
	}

	fixupPrincipalCheckboxes();
	function fixupIdiomasSize(){

		$j('#idiomas_chzn ul').css('width', '307px');

	}

	fixupIdiomasSize();

	$idiomas = $j('#idiomas');

	$idiomas.trigger('chosen:updated');
	var testezin;

	var handleGetIdiomas = function(dataResponse) {
		testezin = dataResponse['idiomas'];

		console.log(testezin);

		$j.each(dataResponse['idiomas'], function(id, value) {
			$idiomas.children("[value=" + value + "]").attr('selected', '');
		});

		$idiomas.trigger('chosen:updated');
	}

	var getIdiomas = function() {
		var $cod_vps_entrevista = $j('#cod_vps_entrevista').val();

		if ($j('#cod_vps_entrevista').val() != '') {
			var additionalVars = {
				id : $j('#cod_vps_entrevista').val(),
			};

			var options = {
				url      : getResourceUrlBuilder.buildUrl('/module/Api/idioma', 'idioma', additionalVars),
				dataType : 'json',
				data     : {},
				success  : handleGetIdiomas,
			};

			getResource(options);
		}
	}

	getIdiomas();

</script>
