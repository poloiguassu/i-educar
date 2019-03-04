<?php

require_once('include/pmieducar/geral.inc.php');

class clsPmieducarFaltaDiariaAluno
{
    public $ref_cod_quadro_horario_horarios;
    public $situacao;
    public $ref_cod_matricula;

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
        $ref_cod_matricula = null,
        $ref_cod_quadro_horario_horarios = null,
        $situacao = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}falta_aluno_diaria";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_matricula, ref_cod_quadro_horario_horarios';

        if (is_numeric($ref_cod_matricula)) {
            if (class_exists('clsPmieducarMatricula')) {
                $tmp_obj = new clsPmieducarMatricula($ref_cod_matricula);
                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_matricula = $ref_cod_matricula;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_matricula = $ref_cod_matricula;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.matricula WHERE ref_cod_matricula = '{$ref_cod_matricula}'")) {
                    $this->ref_cod_matricula = $ref_cod_matricula;
                }
            }
        }

        if (is_numeric($ref_cod_quadro_horario_horarios)) {
            if (class_exists('clsPmieducarMatricula')) {
                $tmp_obj = new clsPmieducarMatricula();
                $tmp_obj->ref_cod_quadro_horario_horarios = $ref_cod_quadro_horario_horarios;

                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_cod_quadro_horario_horarios = $ref_cod_quadro_horario_horarios;
                    }
                } elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_cod_quadro_horario_horarios = $ref_cod_quadro_horario_horarios;
                    }
                }
            } else {
                if ($db->CampoUnico("SELECT 1 FROM pmieducar.quadro_horario_horarios WHERE cod_quadro_horario_horarios = '{$ref_cod_quadro_horario_horarios}'")) {
                    $this->ref_cod_quadro_horario_horarios = $ref_cod_quadro_horario_horarios;
                }
            }
        }

        if (is_numeric($situacao)) {
            $this->situacao = $situacao;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_matricula)
            && is_numeric($this->situacao)
            && is_numeric($this->ref_cod_quadro_horario_horarios)
        ) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_matricula)) {
                $campos .= "{$gruda}ref_cod_matricula";
                $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_quadro_horario_horarios)) {
                $campos .= "{$gruda}ref_cod_quadro_horario_horarios";
                $valores .= "{$gruda}'{$this->ref_cod_quadro_horario_horarios}'";
                $gruda = ', ';
            }

            if (is_numeric($this->situacao)) {
                $campos .= "{$gruda}situacao";
                $valores .= "{$gruda}'{$this->situacao}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES($valores)");

            return $db->InsertId("{$this->_tabela}_id_seq");
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
        if (is_numeric($this->ref_cod_matricula)
            && is_numeric($this->situacao)
            && is_numeric($this->ref_cod_quadro_horario_horarios)
        ) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->situacao)) {
                $set .= "{$gruda}situacao = '{$this->situacao}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta(
                    "UPDATE
                        {$this->_tabela}
                    SET
                        $set
                    WHERE
                        ref_cod_matricula = '{$this->ref_cod_matricula}'
                    AND
                        ref_cod_quadro_horario_horarios = '{$this->ref_cod_quadro_horario_horarios}'"
                );

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
    public function lista(
        $int_ref_cod_matricula = null,
        $int_ref_cod_quadro_horario_horarios = null,
        $int_situacao = null
    ) {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} fd";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} fd.ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_quadro_horario_horarios)) {
            $filtros .= "{$whereAnd} fd.ref_cod_quadro_horario_horarios = '{$int_ref_cod_quadro_horario_horarios}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_situacao)) {
            $filtros .= "{$whereAnd} fd.situacao = '{$int_situacao}'";
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

    public function listaAulaDada(
        $int_ref_cod_matricula                  = null,
        $int_ref_cod_quadro_horario_horarios    = null,
        $int_situacao                           = null,
        $filtra_ref_cod_quadro_horario          = null,
        $filtra_data_aula_ini                   = null,
        $filtra_data_aula_fim                   = null,
        $filtra_ref_cod_disciplina              = null,
        $filtra_ref_cod_servidor                = null
    ) {
        $this->_campos_lista = 'DISTINCT ON
            (qhh.data_aula)
            qhh.data_aula,
            fd.ref_cod_matricula,
            qhh.hora_inicial,
            qhh.hora_final';

        return $this->filtra(
            $int_ref_cod_matricula,
            $int_ref_cod_quadro_horario_horarios,
            $int_situacao,
            $filtra_ref_cod_quadro_horario,
            $filtra_data_aula_ini,
            $filtra_data_aula_fim,
            $filtra_ref_cod_disciplina,
            $filtra_ref_cod_servidor,
            true
        );
    }

    public function listaFaltaQuadro(
        $int_ref_cod_matricula                  = null,
        $int_ref_cod_quadro_horario_horarios    = null,
        $int_situacao                           = null,
        $filtra_ref_cod_quadro_horario          = null,
        $filtra_data_aula_ini                   = null,
        $filtra_data_aula_fim                   = null,
        $filtra_ref_cod_disciplina              = null,
        $filtra_ref_cod_servidor                = null
    ) {
        $this->_campos_lista = 'fd.ref_cod_matricula,
            fd.ref_cod_quadro_horario_horarios,
            qhh.cod_quadro_horario_horarios,
            fd.situacao,
            qhh.ref_cod_quadro_horario,
            qhh.ref_cod_instituicao_servidor,
            qhh.hora_inicial,
            qhh.hora_final,
            qhh.data_aula,
            qhh.ref_cod_disciplina';

        return $this->filtra(
            $int_ref_cod_matricula,
            $int_ref_cod_quadro_horario_horarios,
            $int_situacao,
            $filtra_ref_cod_quadro_horario,
            $filtra_data_aula_ini,
            $filtra_data_aula_fim,
            $filtra_ref_cod_disciplina,
            $filtra_ref_cod_servidor,
            true
        );
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function filtra(
        $int_ref_cod_matricula                  = null,
        $int_ref_cod_quadro_horario_horarios    = null,
        $int_situacao                           = null,
        $filtra_ref_cod_quadro_horario          = null,
        $filtra_data_aula_ini                   = null,
        $filtra_data_aula_fim                   = null,
        $filtra_ref_cod_disciplina              = null,
        $filtra_ref_cod_servidor                = null,
        $unico                                  = false
    ) {
        $tabelas = "{$this->_tabela} fd, pmieducar.quadro_horario_horarios qhh";

        $sql = "SELECT {$this->_campos_lista} FROM {$tabelas}";

        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} fd.ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_quadro_horario_horarios)) {
            $filtros .= "{$whereAnd} fd.ref_cod_quadro_horario_horarios = '{$int_ref_cod_quadro_horario_horarios}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_situacao)) {
            $filtros .= "{$whereAnd} fd.situacao = '{$int_situacao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($filtra_ref_cod_quadro_horario)) {
            $filtros .= "{$whereAnd} qhh.ref_cod_quadro_horario = '{$filtra_ref_cod_quadro_horario}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($filtra_ref_cod_disciplina)) {
            $filtros .= "{$whereAnd} qhh.ref_cod_disciplina = '{$filtra_ref_cod_disciplina}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($filtra_ref_cod_servidor)) {
            $filtros .= "{$whereAnd} qhh.ref_cod_instituicao_servidor = '{$filtra_ref_cod_servidor}'";
            $whereAnd = ' AND ';
        }

        if (is_string($filtra_data_aula_ini)) {
            $filtros .= "{$whereAnd} qhh.data_aula >= '{$filtra_data_aula_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($filtra_data_aula_fim)) {
            $filtros .= "{$whereAnd} qhh.data_aula <= '{$filtra_data_aula_fim}'";
            $whereAnd = ' AND ';
        }

        if ($unico) {
            $filtros .= "{$whereAnd} fd.ref_cod_quadro_horario_horarios
                IN
                (
                    SELECT DISTINCT ON
                        (
                            ahh.data_aula,
                            ahh.ref_cod_quadro_horario
                        )
                        ahh.cod_quadro_horario_horarios
                    FROM
                        pmieducar.quadro_horario_horarios as ahh
                )";
            $whereAnd = ' AND ';
        }

        $filtros .= "{$whereAnd} fd.ref_cod_quadro_horario_horarios = qhh.cod_quadro_horario_horarios";
        $whereAnd = ' AND ';

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getLimite();

        if (is_string($this->getOrderby())) {
            $sql = "SELECT * FROM ({$sql}) t {$this->getOrderby()}";
        }

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$tabelas} {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $id = "{$tupla['data_aula']}{$tupla['hora_inicial']}-{$tupla['hora_final']}";

                $tupla['_total'] = $this->_total;
                $resultado[$id] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $id = "{$tupla['data_aula']}{$tupla['hora_inicial']}-{$tupla['hora_final']}";
                $resultado[$id] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function total_faltas($cod_matricula)
    {
        $db = new clsBanco();
        if (is_numeric($cod_matricula)) {
            return $db->CampoUnico("SELECT SUM(faltas) FROM {$this->_tabela} WHERE ref_cod_matricula = '{$cod_matricula}'");
        }

        return 0;
    }

    /**
     * Retorna a quantidade total de faltas da matricula $cod_matricula na disciplina $cod_disciplina
     *
     * @param int $cod_matricula
     * @param int $cod_disciplina
     *
     * @return int
     */
    public function total_faltas_disciplina($cod_matricula, $cod_disciplina, $cod_serie)
    {
        $db = new clsBanco();
        if (is_numeric($cod_matricula) && is_numeric($cod_disciplina)) {
            return $db->CampoUnico("SELECT SUM(faltas) FROM {$this->_tabela} WHERE ref_cod_matricula = '{$cod_matricula}' AND ref_cod_disciplina = '{$cod_disciplina}' AND ref_cod_serie = '{$cod_serie}'");
        }

        return 0;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->ref_cod_matricula)
            && is_numeric($this->ref_cod_quadro_horario_horarios)
        ) {
            $db = new clsBanco();
            $db->Consulta(
                "SELECT
                    {$this->_todos_campos}
                FROM
                    {$this->_tabela}
                WHERE
                    ref_cod_matricula = '{$this->ref_cod_matricula}'
                AND
                    ref_cod_quadro_horario_horarios = '{$this->ref_cod_quadro_horario_horarios}'"
            );
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
        if (is_numeric($this->ref_cod_matricula)
            && is_numeric($this->ref_cod_quadro_horario_horarios)
        ) {
            $db = new clsBanco();
            $db->Consulta(
                "SELECT
                    1
                FROM
                    {$this->_tabela}
                WHERE
                    ref_cod_matricula = '{$this->ref_cod_matricula}'
                AND
                    ref_cod_quadro_horario_horarios = '{$this->ref_cod_quadro_horario_horarios}'"
            );
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
        if (is_numeric($this->ref_cod_matricula)
            && is_numeric($this->ref_cod_quadro_horario_horarios)
        ) {
            $db = new clsBanco();

            return $db->CampoUnico(
                "DELETE FROM
                    {$this->_tabela}
                WHERE
                    ref_cod_matricula = '{$this->ref_cod_matricula}'
                AND
                    ref_cod_quadro_horario_horarios = '{$this->ref_cod_quadro_horario_horarios}'"
            );
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
