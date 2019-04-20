<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsPmieducarInscritoEtapa
{
    public $ref_cod_inscrito;
    public $etapa;
    public $situacao;
    public $ref_cod_etapa_data;

    public $_tabela;
    public $_schema;

    /**
    * Construtor.
    */
    public function __construct(
        $ref_cod_inscrito = null,
        $etapa = null,
        $situacao = null,
        $ref_cod_etapa_data = null
    ) {
        if (is_numeric($ref_cod_inscrito)) {
            $this->ref_cod_inscrito = $ref_cod_inscrito;
        }

        if (is_numeric($etapa)) {
            $this->etapa = $etapa;
        }

        if (is_numeric($situacao)) {
            $this->situacao = $situacao;
        }

        if (is_numeric($ref_cod_etapa_data)) {
            $this->ref_cod_etapa_data = $ref_cod_etapa_data;
        }

        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'inscrito_etapa';

        $this->_campos_lista = $this->_todos_campos =  'ref_cod_inscrito,
            etapa, situacao, ref_cod_etapa_data';
    }

    public function lista(
        $ref_cod_inscrito = null,
        $etapa = null,
        $situacao = null,
        $ref_cod_etapa_data = null
    ) {
        $whereAnd = '';
        $where    = '';

        if (is_numeric($ref_cod_inscrito)) {
            $where   .= "{$whereAnd} ref_cod_inscrito = '$ref_cod_inscrito'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($etapa)) {
            $where   .= "{$whereAnd} etapa = '$etapa'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($situacao)) {
            $where   .= "{$whereAnd} situacao = '$situacao'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_etapa_data)) {
            $where   .= "{$whereAnd} ref_cod_etapa_data = '$$ref_cod_etapa_data'";
            $whereAnd = ' AND ';
        }

        if ($inicio_limite !== false && $qtd_registros) {
            $limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
        }

        $orderBy = ' ORDER BY etapa';

        $db  = new clsBanco();

        if ($where) {
            $where = 'WHERE ' . $where;
        }

        $total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$where}");

        $db->Consulta(
            "SELECT
                {$this->_todos_campos}
            FROM
                {$this->_tabela}
            {$where} {$orderBy} {$limite}"
        );

        $resultado = [];

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['total'] = $total;

            $resultado[] = $tupla;
        }

        if (count($resultado) > 0) {
            return $resultado;
        }

        return false;
    }

    public function cadastra()
    {
        // verificacoes de campos obrigatorios para insercao
        if (is_numeric($this->ref_cod_inscrito)
            && is_numeric($this->etapa)
        ) {
            $db = new clsBanco();

            $campos  = '';
            $valores = '';
            $gruda   = '';

            if (is_numeric($this->ref_cod_inscrito)) {
                $campos  .= "{$gruda}ref_cod_inscrito";
                $valores .= "{$gruda}'{$this->ref_cod_inscrito}'";
                $gruda = ', ';
            }

            if (is_numeric($this->etapa)) {
                $campos  .= "{$gruda}etapa";
                $valores .= "{$gruda}'{$this->etapa}'";
                $gruda = ', ';
            }

            if (is_numeric($this->situacao)) {
                $campos  .= "{$gruda}situacao";
                $valores .= "{$gruda}'{$this->situacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_etapa_data)) {
                $campos  .= "{$gruda}ref_cod_etapa_data";
                $valores .= "{$gruda}'{$this->ref_cod_etapa_data}'";
                $gruda = ', ';
            }

            $db->Consulta(
                "INSERT INTO
                    {$this->_tabela} ($campos)
                VALUES ($valores)"
            );
        }

        return false;
    }

    public function edita()
    {
        // verificacoes de campos obrigatorios para insercao
        if (is_numeric($this->ref_cod_inscrito)
            && is_numeric($this->etapa)
        ) {
            $set = '';
            $gruda = '';

            if (is_numeric($this->situacao)) {
                $set .= "$gruda situacao = '$this->situacao' ";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_etapa_data)) {
                $set .= "$gruda ref_cod_etapa_data = '$this->ref_cod_etapa_data' ";
                $gruda = ', ';
            }

            if ($set) {
                $db = new clsBanco();
                $db->Consulta(
                    "UPDATE
                        {$this->_tabela}
                    SET
                        $set
                    WHERE
                        ref_cod_inscrito = {$this->ref_cod_inscrito}
                    AND
                        etapa = {$this->etapa}"
                );

                return true;
            }
        }

        return false;
    }

    public function detalhe()
    {
        if (is_numeric($this->ref_cod_inscrito)
            && is_numeric($this->etapa)
        ) {
            $db = new clsBanco();
            $db->Consulta(
                "SELECT
                    {$this->_todos_campos}
                FROM
                    {$this->_tabela}
                WHERE
                    ref_cod_inscrito = {$this->ref_cod_inscrito}
                AND
                    etapa = {$this->etapa}"
            );

            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        }

        return false;
    }

    public function excluir()
    {
        if (is_numeric($this->ref_cod_inscrito)
            && is_numeric($this->etapa)
        ) {
            $db  = new clsBanco();

            $db->Consulta(
                "DELETE FROM
                    {$this->_tabela}
                WHERE
                    ref_cod_inscrito = '{$this->ref_cod_inscrito}'
                AND
                    etapa = '{$this->etapa}'"
            );
        }
    }

    public function excluirTodas()
    {
        if (is_numeric($this->ref_cod_inscrito)) {
            $db  = new clsBanco();

            $retorno = $db->Consulta(
                "DELETE FROM
                    {$this->_tabela}
                WHERE
                    ref_cod_inscrito = '{$this->ref_cod_inscrito}'"
            );

            return $retorno;
        }
        return false;
    }
}
