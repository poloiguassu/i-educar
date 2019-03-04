<?php

require_once('include/pmieducar/geral.inc.php');

class clsPmieducarVPSVisita
{
    public $cod_vps_visita;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_visita;
    public $hora_visita;
    public $motivo_visita;
    public $avaliacao;

    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $ref_cod_usuario;
    public $ref_cod_vps_aluno_entrevista;
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
        $cod_vps_visita = null,
        $data_visita = null,
        $hora_visita = null,
        $motivo_visita = null,
        $ref_cod_usuario = null,
        $ref_cod_vps_aluno_entrevista = null,
        $avaliacao = null,
        $ref_usuario_cad = null,
        $ref_usuario_exc = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}vps_visita";

        $this->_campos_lista = $this->_todos_campos = 'cod_vps_visita, ref_usuario_exc, ref_usuario_cad, data_cadastro, data_exclusao, ativo, data_visita, hora_visita, ref_cod_usuario, motivo_visita, ref_cod_vps_aluno_entrevista, avaliacao';

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

        if (is_numeric($ref_cod_usuario)) {
            if (class_exists('clsPmieducarUsuario')) {
                $tmp_obj = new clsPmieducarUsuario($ref_cod_usuario);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_usuario = $ref_cod_usuario;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_usuario = $ref_cod_usuario;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_cod_usuario}'")) {
                    $this->ref_cod_usuario = $ref_cod_usuario;
                }
            }
        }
        if (is_numeric($cod_vps_visita)) {
            $this->cod_vps_visita = $cod_vps_visita;
        }

        if (is_string($data_visita)) {
            $this->data_visita = $data_visita;
        }
        if (is_string($hora_visita)) {
            $this->hora_visita = $hora_visita;
        }
        if (is_string($motivo_visita)) {
            $this->motivo_visita = $motivo_visita;
        }
        if (is_numeric($avaliacao)) {
            $this->avaliacao = $avaliacao;
        }
        if (is_numeric($ref_cod_vps_aluno_entrevista)) {
            $this->ref_cod_vps_aluno_entrevista = $ref_cod_vps_aluno_entrevista;
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
        if (is_numeric($this->ref_usuario_cad)) {
            print('ENTROU CADSTRA');
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->data_visita)) {
                $campos .= "{$gruda}data_visita";
                $valores .= "{$gruda}'{$this->data_visita}'";
                $gruda = ', ';
            }
            if (is_string($this->hora_visita)) {
                $campos .= "{$gruda}hora_visita";
                $valores .= "{$gruda}'{$this->hora_visita}'";
                $gruda = ', ';
            }
            if (is_string($this->motivo_visita)) {
                $campos .= "{$gruda}motivo_visita";
                $valores .= "{$gruda}'{$this->motivo_visita}'";
                $gruda = ', ';
            }
            if (is_numeric($this->avaliacao)) {
                $campos .= "{$gruda}avaliacao";
                $valores .= "{$gruda}'{$this->avaliacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_usuario)) {
                $campos .= "{$gruda}ref_cod_usuario";
                $valores .= "{$gruda}'{$this->ref_cod_usuario}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_vps_aluno_entrevista)) {
                $campos .= "{$gruda}ref_cod_vps_aluno_entrevista";
                $valores .= "{$gruda}'{$this->ref_cod_vps_aluno_entrevista}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_vps_visita_seq");
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
        if (is_numeric($this->cod_vps_visita) && is_numeric($this->ref_usuario_exc)) {
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
            if (is_string($this->data_visita)) {
                $set .= "{$gruda}data_visita = '{$this->data_visita}'";
                $gruda = ', ';
            }
            if (is_string($this->hora_visita)) {
                $set .= "{$gruda}hora_visita = '{$this->hora_visita}'";
                $gruda = ', ';
            }
            if (is_string($this->motivo_visita)) {
                $set .= "{$gruda}motivo_visita = '{$this->motivo_visita}'";
                $gruda = ', ';
            }
            if (is_numeric($this->avaliacao)) {
                $set .= "{$gruda}avaliacao = '{$this->avaliacao}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_usuario)) {
                $set .= "{$gruda}ref_cod_usuario = '{$this->ref_cod_usuario}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_vps_aluno_entrevista)) {
                $set .= "{$gruda}ref_cod_vps_aluno_entrevista = '{$this->ref_cod_vps_aluno_entrevista}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_vps_visita = '{$this->cod_vps_visita}'");

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
    public function lista($int_cod_vps_visita = null, $int_ativo = null)
    {
        $filtros = '';
        $this->resetCamposLista();

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_vps_visita)) {
            $filtros .= "{$whereAnd} cod_vps_visita = '{$int_cod_vps_visita}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
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
        if (is_numeric($this->cod_vps_visita)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_vps_visita = '{$this->cod_vps_visita}'");
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
        if (is_numeric($this->cod_vps_visita)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_vps_visita = '{$this->cod_vps_visita}'");
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
        if (is_numeric($this->cod_vps_visita) && is_numeric($this->ref_usuario_exc)) {
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
