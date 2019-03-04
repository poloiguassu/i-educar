<?php

require_once('include/pmieducar/geral.inc.php');

class clsPmieducarVPSEntrevistaResponsavel
{
    public $ref_cod_vps_responsavel_entrevista;
    public $ref_cod_vps_entrevista;
    public $principal;

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
    public function __construct($ref_cod_vps_responsavel_entrevista = null, $ref_cod_vps_entrevista = null, $principal = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}vps_entrevista_responsavel";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_vps_responsavel_entrevista, ref_cod_vps_entrevista, principal';

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
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.entrevista WHERE cod_vps_entrevista = '{$ref_cod_vps_entrevista}'")) {
                    $this->ref_cod_vps_entrevista = $ref_cod_vps_entrevista;
                }
            }
        }
        if (is_numeric($ref_cod_vps_responsavel_entrevista)) {
            if (class_exists('clsPmieducarVPSResponsavelEntrevista')) {
                $tmp_obj = new clsPmieducarVPSResponsavelEntrevista($ref_cod_vps_responsavel_entrevista);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_vps_responsavel_entrevista = $ref_cod_vps_responsavel_entrevista;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_vps_responsavel_entrevista = $ref_cod_vps_responsavel_entrevista;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.vps_entrevista_responsavel WHERE cod_vps_entrevista_responsavel = '{$ref_cod_vps_responsavel_entrevista}'")) {
                    $this->ref_cod_vps_responsavel_entrevista = $ref_cod_vps_responsavel_entrevista;
                }
            }
        }

        if (is_numeric($principal)) {
            $this->principal = $principal;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_vps_responsavel_entrevista) && is_numeric($this->ref_cod_vps_entrevista) && is_numeric($this->principal)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_vps_responsavel_entrevista)) {
                $campos .= "{$gruda}ref_cod_vps_responsavel_entrevista";
                $valores .= "{$gruda}'{$this->ref_cod_vps_responsavel_entrevista}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_vps_entrevista)) {
                $campos .= "{$gruda}ref_cod_vps_entrevista";
                $valores .= "{$gruda}'{$this->ref_cod_vps_entrevista}'";
                $gruda = ', ';
            }
            if (is_numeric($this->principal)) {
                $campos .= "{$gruda}principal";
                $valores .= "{$gruda}'{$this->principal}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return true;
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
        if (is_numeric($this->ref_cod_vps_responsavel_entrevista) && is_numeric($this->ref_cod_vps_entrevista)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->principal)) {
                $set .= "{$gruda}principal = '{$this->principal}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_vps_responsavel_entrevista = '{$this->ref_cod_vps_responsavel_entrevista}' AND ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'");

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
    public function lista($int_ref_cod_vps_responsavel_entrevista = null, $int_ref_cod_vps_entrevista = null, $int_principal = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_vps_responsavel_entrevista)) {
            $filtros .= "{$whereAnd} ref_cod_vps_responsavel_entrevista = '{$int_ref_cod_vps_responsavel_entrevista}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_vps_entrevista)) {
            $filtros .= "{$whereAnd} ref_cod_vps_entrevista = '{$int_ref_cod_vps_entrevista}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_principal)) {
            $filtros .= "{$whereAnd} principal = '{$int_principal}'";
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
        if (is_numeric($this->ref_cod_vps_responsavel_entrevista) && is_numeric($this->ref_cod_vps_entrevista)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_vps_responsavel_entrevista = '{$this->ref_cod_vps_responsavel_entrevista}' AND ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'");
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
        if (is_numeric($this->ref_cod_vps_responsavel_entrevista) && is_numeric($this->ref_cod_vps_entrevista)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_vps_responsavel_entrevista = '{$this->ref_cod_vps_responsavel_entrevista}' AND ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'");
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
        if (is_numeric($this->ref_cod_vps_responsavel_entrevista) && is_numeric($this->ref_cod_vps_entrevista)) {

        /*
            delete
        $db = new clsBanco();
        $db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_vps_responsavel_entrevista = '{$this->ref_cod_vps_responsavel_entrevista}' AND ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'" );
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
