<?php

require_once('include/pmieducar/geral.inc.php');
require_once('lib/Portabilis/Date/Utils.php');

class clsPmieducarVPSEntrevista
{
    public $cod_vps_entrevista;
    public $ref_cod_vps_tipo_contratacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_vps_funcao;
    public $ref_cod_vps_jornada_trabalho;
    public $ref_idpes;
    public $salario;
    public $numero_vagas;
    public $numero_jovens;
    public $situacao_entrevista;
    public $data_entrevista;
    public $hora_entrevista;
    public $descricao;
    public $ano;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $inicio_vps;
    public $termino_vps;
    public $insercao_vps;

    // propriedades padrao

    /**
     * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
     *
     * @var int
     */
    public $_total;

    /**
     * Nome do schema
     *
     * @var string
     */
    public $_schema;

    /**
     * Nome da tabela
     *
     * @var string
     */
    public $_tabela;

    /**
     * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
     *
     * @var string
     */
    public $_campos_lista;

    /**
     * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
     *
     * @var string
     */
    public $_todos_campos;

    /**
     * Valor que define a quantidade de registros a ser retornada pelo metodo lista
     *
     * @var int
     */
    public $_limite_quantidade;

    /**
     * Define o valor de offset no retorno dos registros no metodo lista
     *
     * @var int
     */
    public $_limite_offset;

    /**
     * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
     *
     * @var string
     */
    public $_campo_order_by;

    /**
     * Construtor (PHP 4)
     *
     * @return object
     */
    public function __construct(
        $cod_vps_entrevista = null,
        $ref_cod_vps_tipo_contratacao = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $ref_cod_vps_funcao = null,
        $ref_cod_vps_jornada_trabalho = null,
        $ref_idpes = null,
        $descricao = null,
        $ano = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $ref_cod_escola = null,
        $ref_cod_curso = null,
        $salario = null,
        $data_entrevista = null,
        $hora_entrevista = null,
        $numero_vagas = null,
        $numero_jovens = null,
        $situacao_entrevista = null,
        $inicio_vps = null,
        $termino_vps = null,
        $insercao_vps = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}vps_entrevista";

        $this->_campos_lista = $this->_todos_campos = 'a.cod_vps_entrevista, a.ref_cod_vps_tipo_contratacao, a.ref_usuario_exc, a.ref_usuario_cad, a.ref_cod_vps_funcao, a.ref_cod_vps_jornada_trabalho, a.ref_idpes, a.salario, a.data_entrevista, a.hora_entrevista, a.descricao, a.ano, a.data_cadastro, a.data_exclusao, a.ativo, a.ref_cod_escola, a.ref_cod_curso, a.numero_vagas, a.numero_jovens, a.situacao_entrevista, a.inicio_vps, a.termino_vps, a.insercao_vps';

        if (is_numeric($ref_cod_escola)) {
            if (class_exists('clsPmieducarEscola')) {
                $tmp_obj = new clsPmieducarEscola($ref_cod_escola);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_escola = $ref_cod_escola;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_escola = $ref_cod_escola;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.escola WHERE cod_escola = '{$ref_cod_escola}'")) {
                    $this->ref_cod_escola = $ref_cod_escola;
                }
            }
        }
        if (is_numeric($ref_cod_curso)) {
            if (class_exists('clsPmieducarCurso')) {
                $tmp_obj = new clsPmieducarCurso($ref_cod_curso);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_curso = $ref_cod_curso;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_curso = $ref_cod_curso;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.curso WHERE cod_curso = '{$ref_cod_curso}'")) {
                    $this->ref_cod_curso = $ref_cod_curso;
                }
            }
        }
        if (is_numeric($ref_cod_vps_tipo_contratacao)) {
            if (class_exists('clsPmieducarVPSContratacaoTipo')) {
                $tmp_obj = new clsPmieducarVPSContratacaoTipo($ref_cod_vps_tipo_contratacao);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_vps_tipo_contratacao = $ref_cod_vps_tipo_contratacao;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_vps_tipo_contratacao = $ref_cod_vps_tipo_contratacao;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.vps_tipo_contratacao WHERE cod_vps_tipo_contratacao = '{$ref_cod_vps_tipo_contratacao}'")) {
                    $this->ref_cod_vps_tipo_contratacao = $ref_cod_vps_tipo_contratacao;
                }
            }
        }

        if (is_numeric($ref_usuario_cad)) {
            if (class_exists('clsPmieducarUsuario')) {
                $tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_usuario_cad = $ref_usuario_cad;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_usuario_cad = $ref_usuario_cad;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'")) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
                }
            }
        }
        if (is_numeric($ref_usuario_exc)) {
            if (class_exists('clsPmieducarUsuario')) {
                $tmp_obj = new clsPmieducarUsuario($ref_usuario_exc);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_usuario_exc = $ref_usuario_exc;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_usuario_exc = $ref_usuario_exc;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'")) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
                }
            }
        }
        if (is_numeric($ref_cod_vps_funcao)) {
            if (class_exists('clsPmieducarVPSFuncao')) {
                $tmp_obj = new clsPmieducarVPSFuncao($ref_cod_vps_funcao);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_vps_funcao = $ref_cod_vps_funcao;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_vps_funcao = $ref_cod_vps_funcao;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.vps_funcao WHERE cod_vps_funcao = '{$ref_cod_vps_funcao}'")) {
                    $this->ref_cod_vps_funcao = $ref_cod_vps_funcao;
                }
            }
        }
        if (is_numeric($ref_cod_vps_jornada_trabalho)) {
            if (class_exists('clsPmieducarVPSJornadaTrabalho')) {
                $tmp_obj = new clsPmieducarVPSJornadaTrabalho($ref_cod_vps_jornada_trabalho);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_vps_jornada_trabalho = $ref_cod_vps_jornada_trabalho;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_vps_jornada_trabalho = $ref_cod_vps_jornada_trabalho;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.vps_jornada_trabalho WHERE cod_vps_jornada_trabalho = '{$ref_cod_vps_jornada_trabalho}'")) {
                    $this->ref_cod_vps_jornada_trabalho = $ref_cod_vps_jornada_trabalho;
                }
            }
        }
        if (is_numeric($ref_idpes)) {
            if (class_exists('clsPessoaJuridica')) {
                $tmp_obj = new clsPessoaJuridica($ref_idpes);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_idpes = $ref_idpes;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_idpes = $ref_idpes;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM cadastro.juridica WHERE idpes = '{$ref_idpes}'")) {
                    $this->ref_idpes = $ref_idpes;
                }
            }
        }

        if (is_numeric($cod_vps_entrevista)) {
            $this->cod_vps_entrevista = $cod_vps_entrevista;
        }
        if (is_numeric($salario)) {
            $this->salario = $salario;
        }
        if (is_numeric($numero_vagas)) {
            $this->numero_vagas = $numero_vagas;
        }
        if (is_numeric($numero_jovens)) {
            $this->numero_jovens = $numero_jovens;
        }
        if (is_numeric($situacao_entrevista)) {
            $this->situacao_entrevista = $situacao_entrevista;
        }
        if (is_string($inicio_vps)) {
            $this->inicio_vps = $inicio_vps;
        }
        if (is_string($termino_vps)) {
            $this->termino_vps = $termino_vps;
        }
        if (is_string($insercao_vps)) {
            $this->insercao_vps = $insercao_vps;
        }
        if (is_string($data_entrevista)) {
            $this->data_entrevista = $data_entrevista;
        }
        if (is_string($hora_entrevista)) {
            $this->hora_entrevista = $hora_entrevista;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
        if (is_numeric($ano)) {
            $this->ano = $ano;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_vps_jornada_trabalho) && is_numeric($this->ref_idpes) && is_numeric($this->ano) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_vps_tipo_contratacao)) {
                $campos .= "{$gruda}ref_cod_vps_tipo_contratacao";
                $valores .= "{$gruda}'{$this->ref_cod_vps_tipo_contratacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_vps_funcao)) {
                $campos .= "{$gruda}ref_cod_vps_funcao";
                $valores .= "{$gruda}'{$this->ref_cod_vps_funcao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_vps_jornada_trabalho)) {
                $campos .= "{$gruda}ref_cod_vps_jornada_trabalho";
                $valores .= "{$gruda}'{$this->ref_cod_vps_jornada_trabalho}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idpes)) {
                $campos .= "{$gruda}ref_idpes";
                $valores .= "{$gruda}'{$this->ref_idpes}'";
                $gruda = ', ';
            }
            if (is_numeric($this->salario)) {
                $campos .= "{$gruda}salario";
                $valores .= "{$gruda}'{$this->salario}'";
                $gruda = ', ';
            }
            if (is_numeric($this->numero_vagas)) {
                $campos .= "{$gruda}numero_vagas";
                $valores .= "{$gruda}'{$this->numero_vagas}'";
                $gruda = ', ';
            }
            if (is_numeric($this->numero_jovens)) {
                $campos .= "{$gruda}numero_jovens";
                $valores .= "{$gruda}'{$this->numero_jovens}'";
                $gruda = ', ';
            }
            if (is_numeric($this->situacao_entrevista)) {
                $campos .= "{$gruda}situacao_entrevista";
                $valores .= "{$gruda}'{$this->situacao_entrevista}'";
                $gruda = ', ';
            }
            if (is_string($this->inicio_vps)) {
                $campos .= "{$gruda}inicio_vps";
                $valores .= "{$gruda}'{$this->inicio_vps}'";
                $gruda = ', ';
            }
            if (is_string($this->termino_vps)) {
                $campos .= "{$gruda}termino_vps";
                $valores .= "{$gruda}'{$this->termino_vps}'";
                $gruda = ', ';
            }
            if (is_string($this->insercao_vps)) {
                $campos .= "{$gruda}insercao_vps";
                $valores .= "{$gruda}'{$this->insercao_vps}'";
                $gruda = ', ';
            }
            if (is_string($this->data_entrevista)) {
                $campos .= "{$gruda}data_entrevista";
                $valores .= "{$gruda}'{$this->data_entrevista}'";
                $gruda = ', ';
            }
            if (is_string($this->hora_entrevista)) {
                $campos .= "{$gruda}hora_entrevista";
                $valores .= "{$gruda}'{$this->hora_entrevista}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$this->descricao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ano)) {
                $campos .= "{$gruda}ano";
                $valores .= "{$gruda}'{$this->ano}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            if (is_numeric($this->ref_cod_curso)) {
                $campos .= "{$gruda}ref_cod_curso";
                $valores .= "{$gruda}'{$this->ref_cod_curso}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES($valores)");

            return $db->InsertId("{$this->_tabela}_cod_vps_entrevista_seq");
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cod_vps_entrevista) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_vps_tipo_contratacao)) {
                $set .= "{$gruda}ref_cod_vps_tipo_contratacao = '{$this->ref_cod_vps_tipo_contratacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_vps_funcao)) {
                $set .= "{$gruda}ref_cod_vps_funcao = '{$this->ref_cod_vps_funcao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_vps_jornada_trabalho)) {
                $set .= "{$gruda}ref_cod_vps_jornada_trabalho = '{$this->ref_cod_vps_jornada_trabalho}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idpes)) {
                $set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
                $gruda = ', ';
            }
            if (is_numeric($this->salario)) {
                $set .= "{$gruda}salario = '{$this->salario}'";
                $gruda = ', ';
            }
            if (is_numeric($this->numero_vagas)) {
                $set .= "{$gruda}numero_vagas = '{$this->numero_vagas}'";
                $gruda = ', ';
            }
            if (is_numeric($this->numero_jovens)) {
                $set .= "{$gruda}numero_jovens = '{$this->numero_jovens}'";
                $gruda = ', ';
            }
            if (is_numeric($this->situacao_entrevista)) {
                $set .= "{$gruda}situacao_entrevista = '{$this->situacao_entrevista}'";
                $gruda = ', ';
            }
            if (is_string($this->inicio_vps)) {
                $set .= "{$gruda}inicio_vps = '{$this->inicio_vps}'";
                $gruda = ', ';
            }
            if (is_string($this->termino_vps)) {
                $set .= "{$gruda}termino_vps = '{$this->termino_vps}'";
                $gruda = ', ';
            }
            if (is_string($this->insercao_vps)) {
                $set .= "{$gruda}insercao_vps = '{$this->insercao_vps}'";
                $gruda = ', ';
            }
            if (is_string($this->data_entrevista)) {
                $set .= "{$gruda}data_entrevista = '{$this->data_entrevista}'";
                $gruda = ', ';
            }
            if (is_string($this->hora_entrevista)) {
                $set .= "{$gruda}hora_entrevista = '{$this->hora_entrevista}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $set .= "{$gruda}descricao = '{$this->descricao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ano)) {
                $set .= "{$gruda}ano = '{$this->ano}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_vps_entrevista = '{$this->cod_vps_entrevista}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($int_cod_vps_entrevista = null, $int_ref_cod_vps_tipo_contratacao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_vps_funcao = null, $int_ref_cod_vps_jornada_trabalho = null, $int_ref_idpes = null, $str_descricao = null, $int_ano = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_escola = null, $int_ref_cod_instituicao = null, $str_nm_responsavel = null, $int_situacao_entrevista = null)
    {
        $sql = "SELECT {$this->_campos_lista}, aa.cod_vps_responsavel_entrevista FROM {$this->_tabela} a, {$this->_schema}acervo_acervo_autor aaa, {$this->_schema}vps_responsavel_entrevista aa";

        $whereAnd = ' WHERE ';
        $filtros = '';

        if (is_numeric($int_cod_vps_entrevista)) {
            $filtros .= "{$whereAnd} a.cod_vps_entrevista = '{$int_cod_vps_entrevista}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_vps_tipo_contratacao)) {
            $filtros .= "{$whereAnd} a.ref_cod_vps_tipo_contratacao = '{$int_ref_cod_vps_tipo_contratacao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} a.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} a.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_vps_funcao)) {
            $filtros .= "{$whereAnd} a.ref_cod_vps_funcao = '{$int_ref_cod_vps_funcao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_vps_jornada_trabalho)) {
            $filtros .= "{$whereAnd} a.ref_cod_vps_jornada_trabalho = '{$int_ref_cod_vps_jornada_trabalho}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_idpes)) {
            $filtros .= "{$whereAnd} a.ref_idpes = '{$int_ref_idpes}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $filtros .= "{$whereAnd} a.descricao LIKE '%{$str_descricao}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ano)) {
            $filtros .= "{$whereAnd} a.ano = '{$int_ano}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($situacao_entrevista)) {
            $filtros .= "{$whereAnd} a.situacao_entrevista = '{$int_situacao_entrevista}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} a.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} a.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} a.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} a.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} a.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} a.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_array($int_ref_cod_escola)) {
            $bibs = implode(', ', $int_ref_cod_escola);
            $filtros .= "{$whereAnd} (a.ref_cod_escola IN ($bibs) OR a.ref_cod_escola IS NULL)";
            $whereAnd = ' AND ';
        } elseif (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} a.ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_responsavel)) {
            $filtros .= "{$whereAnd} aa.nm_responavel LIKE '%{$str_nm_responsavel}%'";
            $whereAnd = ' AND ';
        }
        /*else
        {
            $filtros .= "{$whereAnd} aaa.principal = '1'";
            $whereAnd = " AND ";
        }*/

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} a, {$this->_schema}acervo_acervo_autor aaa, {$this->_schema}vps_responsavel_entrevista aa {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function listaEntrevista(
        $int_ref_cod_escola = null,
        $ativo = null,
        $int_ref_cod_vps_funcao = null,
        $int_ref_cod_vps_tipo_contratacao = null,
        $int_ref_idpes = null,
        $int_ref_cod_curso = null,
        $int_ano = null,
        $int_situacao_entrevista = null
    ) {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} a";

        $whereAnd = ' WHERE ';
        if (is_array($int_ref_cod_escola)) {
            $bibs = implode(', ', $int_ref_cod_escola);
            $filtros .= "{$whereAnd} (ref_cod_escola IN ($bibs) OR ref_cod_escola IS NULL)";
            $whereAnd = ' AND ';
        } elseif (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ativo)) {
            $filtros .= "{$whereAnd} ativo = {$ativo}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_vps_funcao)) {
            $filtros .= "{$whereAnd} ref_cod_vps_funcao = {$int_ref_cod_vps_funcao}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_vps_tipo_contratacao)) {
            $filtros .= "{$whereAnd} ref_cod_vps_tipo_contratacao = {$int_ref_cod_vps_tipo_contratacao}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_idpes)) {
            $filtros .= "{$whereAnd} ref_idpes = {$int_ref_idpes}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} ref_cod_curso = {$int_ref_cod_curso}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ano)) {
            $filtros .= "{$whereAnd} ano = {$int_ano}";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_situacao_entrevista)) {
            $filtros .= "{$whereAnd} situacao_entrevista = {$int_situacao_entrevista}";
            $whereAnd = ' AND ';
        }

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $db = new clsBanco();
        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} a {$filtros}");

        $db->Consulta($sql);
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_vps_entrevista)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} a WHERE a.cod_vps_entrevista = '{$this->cod_vps_entrevista}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_vps_entrevista)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_vps_entrevista = '{$this->cod_vps_entrevista}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_vps_entrevista) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Define quais campos da tabela serao selecionados na invocacao do metodo lista
     *
     * @return null
     */
    public function setCamposLista($str_campos)
    {
        $this->_campos_lista = $str_campos;
    }

    /**
     * Define que o metodo Lista devera retornoar todos os campos da tabela
     *
     * @return null
     */
    public function resetCamposLista()
    {
        $this->_campos_lista = $this->_todos_campos;
    }

    /**
     * Define limites de retorno para o metodo lista
     *
     * @return null
     */
    public function setLimite($intLimiteQtd, $intLimiteOffset = null)
    {
        $this->_limite_quantidade = $intLimiteQtd;
        $this->_limite_offset = $intLimiteOffset;
    }

    /**
     * Retorna a string com o trecho da query resposavel pelo Limite de registros
     *
     * @return string
     */
    public function getLimite()
    {
        if (is_numeric($this->_limite_quantidade)) {
            $retorno = " LIMIT {$this->_limite_quantidade}";
            if (is_numeric($this->_limite_offset)) {
                $retorno .= " OFFSET {$this->_limite_offset} ";
            }

            return $retorno;
        }

        return '';
    }

    /**
     * Define campo para ser utilizado como ordenacao no metolo lista
     *
     * @return null
     */
    public function setOrderby($strNomeCampo)
    {
        // limpa a string de possiveis erros (delete, insert, etc)
        //$strNomeCampo = eregi_replace();

        if (is_string($strNomeCampo) && $strNomeCampo) {
            $this->_campo_order_by = $strNomeCampo;
        }
    }

    /**
     * Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
     *
     * @return string
     */
    public function getOrderby()
    {
        if (is_string($this->_campo_order_by)) {
            return " ORDER BY {$this->_campo_order_by} ";
        }

        return '';
    }
}
