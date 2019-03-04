<?php

require_once('include/pmieducar/geral.inc.php');

class clsPmieducarAlunoVPS
{
    public $cod_aluno_vps;
    public $ref_cod_vps_aluno_entrevista;
    public $ref_cod_aluno;
    public $situacao_vps;
    public $motivo_desligamento;
    public $observacao;
    public $prioridade;
    public $ativo;
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
        $ref_cod_aluno = null,
        $situacao_vps = null,
        $ref_cod_vps_aluno_entrevista = null,
        $ativo = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $motivo_desligamento = null,
        $observacao = null,
        $prioridade = null,
        $data_cadastro = null,
        $data_exclusao = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}aluno_vps";

        $this->_campos_lista = $this->_todos_campos = 'cod_aluno_vps, ref_usuario_exc, ref_usuario_cad, data_cadastro, data_exclusao, ativo, ref_cod_aluno, ref_cod_vps_aluno_entrevista, situacao_vps, motivo_desligamento, observacao, prioridade';

        if (is_numeric($ref_cod_vps_aluno_entrevista)) {
            if (class_exists('clsPmieducarVPSAlunoEntrevista')) {
                $tmp_obj = new clsPmieducarVPSAlunoEntrevista($ref_cod_vps_aluno_entrevista);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_vps_aluno_entrevista = $ref_cod_vps_aluno_entrevista;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_vps_aluno_entrevista = $ref_cod_vps_aluno_entrevista;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.vps_aluno_entrevista WHERE cod_vps_aluno_entrevista = '{$ref_cod_vps_aluno_entrevista}'")) {
                    $this->ref_cod_vps_aluno_entrevista = $ref_cod_vps_aluno_entrevista;
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

        if (is_numeric($situacao_vps)) {
            $this->situacao_vps = $situacao_vps;
        }

        if (is_string($motivo_desligamento)) {
            $this->motivo_desligamento = $motivo_desligamento;
        }

        if (is_numeric($prioridade)) {
            $this->prioridade = $prioridade;
        }

        if (is_string($observacao)) {
            $this->observacao = $observacao;
        }

        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
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
        if (is_numeric($this->ref_cod_aluno)) {
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
            if (is_string($this->motivo_desligamento)) {
                $set .= "{$gruda}motivo_desligamento = '{$this->motivo_desligamento}'";
                $gruda = ', ';
            }
            if (is_numeric($this->situacao_vps)) {
                $set .= "{$gruda}situacao_vps = '{$this->situacao_vps}'";
                $gruda = ', ';
            }
            if (is_string($this->observacao)) {
                $set .= "{$gruda}observacao = '{$this->observacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->prioridade)) {
                $set .= "{$gruda}prioridade = '{$this->prioridade}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_vps_aluno_entrevista)) {
                if ($this->ref_cod_vps_aluno_entrevista > 0) {
                    $set .= "{$gruda}ref_cod_vps_aluno_entrevista = '{$this->ref_cod_vps_aluno_entrevista}'";
                } else {
                    $set .= "{$gruda}ref_cod_vps_aluno_entrevista = NULL";
                }
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
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");

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
        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->ref_cod_vps_aluno_entrevista)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_aluno)) {
                $campos .= "{$gruda}ref_cod_aluno";
                $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_vps_aluno_entrevista)) {
                $campos .= "{$gruda}ref_cod_vps_aluno_entrevista";
                $valores .= "{$gruda}'{$this->ref_cod_vps_aluno_entrevista}'";
                $gruda = ', ';
            }

            if (is_numeric($this->situacao_vps)) {
                $campos .= "{$gruda}situacao_vps";
                $valores .= "{$gruda}'{$this->situacao_vps}'";
                $gruda = ', ';
            }

            if (is_string($this->motivo_desligamento)) {
                $campos .= "{$gruda}motivo_desligamento";
                $valores .= "{$gruda}'{$this->motivo_desligamento}'";
                $gruda = ', ';
            }

            if (is_numeric($this->prioridade)) {
                $campos .= "{$gruda}prioridade";
                $valores .= "{$gruda}'{$this->prioridade}'";
                $gruda = ', ';
            }

            if (is_string($this->observacao)) {
                $campos .= "{$gruda}observacao";
                $valores .= "{$gruda}'{$this->observacao}'";
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
    public function lista($int_ref_cod_aluno = null, $int_ref_cod_vps_aluno_entrevista = null, $int_situacao_vps = null)
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
        if (is_numeric($int_ref_cod_vps_aluno_entrevista)) {
            $filtros .= "{$whereAnd} ref_cod_vps_aluno_entrevista = '{$int_ref_cod_vps_aluno_entrevista}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_situacao_vps)) {
            $filtros .= "{$whereAnd} situacao_vps = '{$int_situacao_vps}'";
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
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
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
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
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
        if (is_numeric($this->ref_cod_aluno)) {
            /*
            delete
            $db = new clsBanco();
            $db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}' AND ref_cod_vps_aluno_entrevista = '{$this->ref_cod_vps_aluno_entrevista}'" );
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
        if (is_numeric($this->ref_cod_vps_aluno_entrevista)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_vps_aluno_entrevista = '{$this->ref_cod_vps_aluno_entrevista}'");

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
