<?php

require_once('include/pmieducar/geral.inc.php');

class clsPmieducarVPSAlunoEntrevista
{
    public $cod_vps_aluno_entrevista;
    public $ref_cod_vps_entrevista;
    public $ref_cod_aluno;
    public $resultado_entrevista;
    public $ativo;
    public $inicio_vps;
    public $termino_vps;
    public $insercao_vps;
    public $motivo_termino;
    public $ref_usuario_exc;
    public $ref_usuario_cad;

    public $data_cadastro;
    public $data_exclusao;

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
        $cod_vps_aluno_entrevista = null,
        $ref_cod_vps_entrevista = null,
        $ref_cod_aluno = null,
        $resultado_entrevista = null,
        $ativo = null,
        $inicio_vps = null,
        $termino_vps = null,
        $insercao_vps = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $motivo_termino = null,
        $data_cadastro = null,
        $data_exclusao = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}vps_aluno_entrevista";

        $this->_campos_lista = $this->_todos_campos = 'cod_vps_aluno_entrevista, ref_usuario_exc, ref_usuario_cad, data_cadastro, data_exclusao, ativo, ref_cod_aluno, ref_cod_vps_entrevista, resultado_entrevista, inicio_vps, termino_vps, insercao_vps, motivo_termino';

        if (is_numeric($cod_vps_aluno_entrevista)) {
            $this->cod_vps_aluno_entrevista = $cod_vps_aluno_entrevista;
        }

        if (is_numeric($ref_cod_vps_entrevista)) {
            if (class_exists('clsPmieducarVPSEntrevista')) {
                $tmp_obj = new clsPmieducarVPSEntrevista($ref_cod_vps_entrevista);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_vps_entrevista = $ref_cod_vps_entrevista;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_vps_entrevista = $ref_cod_vps_entrevista;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.vps_entrevista WHERE cod_vps_entrevista = '{$ref_cod_vps_entrevista}'")) {
                    $this->ref_cod_vps_entrevista = $ref_cod_vps_entrevista;
                }
            }
        }
        if (is_numeric($ref_cod_aluno)) {
            if (class_exists('clsPmieducarAluno')) {
                $tmp_obj = new clsPmieducarAluno($ref_cod_aluno);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_aluno = $ref_cod_aluno;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_aluno = $ref_cod_aluno;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.aluno WHERE cod_aluno = '{$ref_cod_aluno}'")) {
                    $this->ref_cod_aluno = $ref_cod_aluno;
                }
            }
        }

        if (is_numeric($resultado_entrevista)) {
            $this->resultado_entrevista = $$resultado_entrevista;
        }

        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
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

        if (is_string($motivo_termino)) {
            $this->motivo_termino = $motivo_termino;
        }

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }

        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
    }

    public function edita()
    {
        if ((is_numeric($this->ref_cod_aluno) && is_numeric($this->ref_cod_vps_entrevista)) || is_numeric($this->cod_vps_aluno_entrevista)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->resultado_entrevista)) {
                $set .= "{$gruda}resultado_entrevista = '{$this->resultado_entrevista}'";
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
            if (is_string($this->motivo_termino)) {
                $set .= "{$gruda}motivo_termino = '{$this->motivo_termino}'";
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
            if ($set) {
                if (is_numeric($this->ref_cod_aluno) && is_numeric($this->ref_cod_vps_entrevista)) {
                    $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}' AND ref_cod_aluno = '{$this->ref_cod_aluno}'");
                } elseif (is_numeric($this->cod_vps_aluno_entrevista)) {
                    $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_vps_aluno_entrevista = '{$this->cod_vps_aluno_entrevista}'");
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->ref_cod_vps_entrevista)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_aluno)) {
                $campos .= "{$gruda}ref_cod_aluno";
                $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_vps_entrevista)) {
                $campos .= "{$gruda}ref_cod_vps_entrevista";
                $valores .= "{$gruda}'{$this->ref_cod_vps_entrevista}'";
                $gruda = ', ';
            }

            if (is_numeric($this->resultado_entrevista)) {
                $campos .= "{$gruda}resultado_entrevista";
                $valores .= "{$gruda}'{$this->resultado_entrevista}'";
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

            if (is_string($this->motivo_termino)) {
                $campos .= "{$gruda}motivo_termino";
                $valores .= "{$gruda}'{$this->motivo_termino}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES($valores)");

            return true;
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($int_ref_cod_aluno = null, $int_ref_cod_vps_entrevista = null, $int_cod_vps_aluno_entrevista = null)
    {
        $filtros = '';
        $this->resetCamposLista();

        $this->_campos_lista .= '
			, (
				SELECT
					nome
				FROM
					cadastro.pessoa, pmieducar.aluno
				WHERE
					idpes = ref_idpes and cod_aluno = ref_cod_aluno
			) AS nome';

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_aluno)) {
            $filtros .= "{$whereAnd} ref_cod_aluno = '{$int_ref_cod_aluno}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_vps_entrevista)) {
            $filtros .= "{$whereAnd} ref_cod_vps_entrevista = '{$int_ref_cod_vps_entrevista}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_cod_vps_aluno_entrevista)) {
            $filtros .= "{$whereAnd} cod_vps_aluno_entrevista = '{$int_cod_vps_aluno_entrevista}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

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

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function listaData($data_entrevista_inicio = null, $data_entrevista_final = null, $int_resultado_entrevista = null)
    {
        $filtros = '';
        $this->resetCamposLista();

        $this->_campos_lista .= '
			, (
				SELECT
					nome
				FROM
					cadastro.pessoa, pmieducar.aluno
				WHERE
					idpes = ref_idpes and cod_aluno = ref_cod_aluno
			) AS nome
			, (
				SELECT
					data_entrevista
				FROM
					pmieducar.vps_entrevista a
				WHERE
					a.cod_vps_entrevista = ref_cod_vps_entrevista
			) AS data_entrevista';

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_resultado_entrevista)) {
            $filtros .= "{$whereAnd} resultado_entrevista = '{$int_resultado_entrevista}'";
            $whereAnd = ' AND ';
        }

        if (is_string($data_entrevista_inicio) || is_string($data_entrevista_final)) {
            $filtrosData = '';
            $whereAndData = ' WHERE ';

            if (is_string($data_entrevista_inicio)) {
                $filtrosData .= "{$whereAndData} data_entrevista >= '{$data_entrevista_inicio}'";
                $whereAndData = ' AND ';
            }
            if (is_string($data_entrevista_final)) {
                $filtrosData .= "{$whereAndData} data_entrevista <= '{$data_entrevista_final}'";
                $whereAndData = ' AND ';
            }

            $filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM pmieducar.vps_entrevista {$filtrosData})";
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

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

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function listaMes($data_entrevista_mes = null, $data_entrevista_ano = null, $int_resultado_entrevista = null)
    {
        $filtros = '';
        $this->resetCamposLista();

        $this->_campos_lista .= '
			, (
				SELECT
					nome
				FROM
					cadastro.pessoa, pmieducar.aluno
				WHERE
					idpes = ref_idpes and cod_aluno = ref_cod_aluno
			) AS nome
			, (
				SELECT
					data_entrevista
				FROM
					pmieducar.vps_entrevista a
				WHERE
					a.cod_vps_entrevista = ref_cod_vps_entrevista
			) AS data_entrevista';

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_resultado_entrevista)) {
            $filtros .= "{$whereAnd} resultado_entrevista = '{$int_resultado_entrevista}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($data_entrevista_mes) &&  is_numeric($data_entrevista_ano)) {
            $filtrosData = '';
            $whereAndData = ' WHERE ';

            if (is_numeric($data_entrevista_mes)) {
                $filtrosData .= "{$whereAndData} EXTRACT(MONTH FROM data_entrevista) = '{$data_entrevista_mes}'";
                $whereAndData = ' AND ';
            }

            if (is_numeric($data_entrevista_ano)) {
                $filtrosData .= "{$whereAndData} EXTRACT(YEAR FROM data_entrevista) = '{$data_entrevista_ano}'";
                $whereAndData = ' AND ';
            }

            $filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM pmieducar.vps_entrevista {$filtrosData})";
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

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

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function listaJovens()
    {
        $filtros = '';
        $this->resetCamposLista();

        $this->_campos_lista = '
			(
				SELECT
					nome
				FROM
					cadastro.pessoa, pmieducar.aluno
				WHERE
					idpes = ref_idpes and cod_aluno = ref_cod_aluno
			) AS nome';

        $sql = "SELECT DISTINCT(ref_cod_aluno), {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

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

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        $filtros = '';

        $whereAnd = ' WHERE ';
        if (is_numeric($this->cod_vps_aluno_entrevista)) {
            $filtros .= "{$whereAnd} cod_vps_aluno_entrevista = '{$this->cod_vps_aluno_entrevista}'";
            $whereAnd = ' AND ';
        } elseif (is_numeric($this->ref_cod_aluno) && is_numeric($this->ref_cod_vps_entrevista)) {
            $filtros .= "{$whereAnd} ref_cod_aluno = '{$this->ref_cod_aluno}'";
            $whereAnd = ' AND ';

            $filtros .= "{$whereAnd} ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'";
            $whereAnd = ' AND ';
        } else {
            return false;
        }

        $db = new clsBanco();
        $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} {$filtros}");
        $db->ProximoRegistro();

        return $db->Tupla();
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe()
    {
        $filtros = '';

        $whereAnd = ' WHERE ';
        if (is_numeric($this->cod_vps_aluno_entrevista)) {
            $filtros .= "{$whereAnd} cod_vps_aluno_entrevista = '{$this->cod_vps_aluno_entrevista}'";
            $whereAnd = ' AND ';
        } elseif (is_numeric($this->ref_cod_aluno) && is_numeric($this->ref_cod_vps_entrevista)) {
            $filtros .= "{$whereAnd} ref_cod_aluno = '{$this->ref_cod_aluno}'";
            $whereAnd = ' AND ';

            $filtros .= "{$whereAnd} ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'";
            $whereAnd = ' AND ';
        } else {
            return false;
        }

        $db = new clsBanco();
        $db->Consulta("SELECT 1 FROM {$this->_tabela} {$filtros}");
        $db->ProximoRegistro();

        return $db->Tupla();
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->ref_cod_vps_entrevista)) {

        /*
            delete
        $db = new clsBanco();
        $db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}' AND ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'" );
        return true;
        */
        }

        return false;
    }

    /**
     * Exclui todos os registros referentes a um tipo de avaliacao
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_cod_vps_entrevista)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'");

            return true;
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
