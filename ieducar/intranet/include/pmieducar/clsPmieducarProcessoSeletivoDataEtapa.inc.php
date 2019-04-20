<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsPmieducarProcessoSeletivoDataEtapa
{
    var $cod_etapa_data;
    var $ref_cod_selecao_processo;
    var $etapa;
    var $data_etapa;
    var $horario;
    var $ativo;

    var $_tabela;
    var $_schema;


    /**
    * Construtor.
    */
    function __construct($cod_etapa_data = null, $ref_cod_selecao_processo = null,
        $etapa = null, $data_etapa = null, $horario = null
    ) {
        if (is_numeric($cod_etapa_data)) {
            $this->cod_etapa_data = $cod_etapa_data;
        }

        if (is_numeric($ref_cod_selecao_processo)) {
            $this->ref_cod_selecao_processo = $ref_cod_selecao_processo;
        }

        if (is_numeric($etapa)) {
            $this->etapa = $etapa;
        }

        if (is_numeric($data_etapa)) {
            $this->data_etapa = $data_etapa;
        }

        $this->horario = $horario;

        $this->_schema = "pmieducar.";
        $this->_tabela = $this->_schema . "selecao_etapa_data";

        $this->_campos_lista = $this->_todos_campos =  "cod_etapa_data,
            ref_cod_selecao_processo, etapa, data_etapa, horario, ativo";
    }

    function lista($cod_etapa_data = null, $ref_cod_selecao_processo = null,
        $etapa = null, $data_etapa = null, $horario = null, $inicio_limite = null,
        $qtd_registros = null
    ) {
        $whereAnd = '';
        $where    = '';
        $limite   = '';

        if (is_numeric($cod_etapa_data)) {
            $where   .= "{$whereAnd} cod_etapa_data = '$cod_etapa_data'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_selecao_processo)) {
            $where   .= "{$whereAnd} ref_cod_selecao_processo = '$ref_cod_selecao_processo'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($etapa)) {
            $where   .= "{$whereAnd} etapa = '$etapa'";
            $whereAnd = ' AND ';
        }

        if (is_string($data_etapa)) {
            $where   .= "{$whereAnd} data_etapa = '$data_etapa'";
            $whereAnd = ' AND ';
        }

        if ($inicio_limite !== false && $qtd_registros) {
            $limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
        }

        $orderBy = ' ORDER BY etapa';

        $db  = new clsBanco();

        if ($where) {
            $where = "WHERE " . $where;
        }

        $total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$where}");

        $db->Consulta(
            "SELECT
                {$this->_todos_campos}
            FROM
                {$this->_tabela}
            {$where} {$orderBy} {$limite}"
        );

        $resultado = array();

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

    function cadastra()
    {
        // verificacoes de campos obrigatorios para insercao
        if (is_numeric($this->ref_cod_selecao_processo)
            && is_numeric($this->etapa)
        ) {
            $db = new clsBanco();

            $campos  = '';
            $valores = '';
            $gruda   = '';

            if (is_numeric($this->cod_etapa_data)) {
                $campos  .= "{$gruda}cod_etapa_data";
                $valores .= "{$gruda}'{$this->cod_etapa_data}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_selecao_processo)) {
                $campos  .= "{$gruda}ref_cod_selecao_processo";
                $valores .= "{$gruda}'{$this->ref_cod_selecao_processo}'";
                $gruda = ', ';
            }

            if (is_numeric($this->etapa)) {
                $campos  .= "{$gruda}etapa";
                $valores .= "{$gruda}'{$this->etapa}'";
                $gruda = ', ';
            }

            if (is_string($this->data_etapa)) {
                $campos  .= "{$gruda}data_etapa";
                $valores .= "{$gruda}'{$this->data_etapa}'";
                $gruda = ', ';
            }

            if (is_string($this->horario)) {
                $campos  .= "{$gruda}horario";
                $valores .= "{$gruda}'{$this->horario}'";
                $gruda = ', ';
            }

            $campos  .= "{$gruda}ativo";
            $valores .= "{$gruda}'true'";
            $gruda = ', ';

            $db->Consulta(
                "INSERT INTO
                    {$this->_tabela} ($campos)
                VALUES ($valores)"
            );
            return $db->InsertId("selecao_etapa_data_cod_etapa_data_seq");
        }
        return false;
    }

    function edita()
    {
        // verificacoes de campos obrigatorios para insercao
        if (is_numeric($this->cod_etapa_data)) {
            $set = "";
            $gruda = "";

            if (is_numeric($this->ref_cod_selecao_processo)) {
                $set .= " ref_cod_selecao_processo =  '$this->ref_cod_selecao_processo' ";
                $gruda = ", ";
            }

            if (is_numeric($this->etapa)) {
                $set .= "$gruda etapa =  '$this->etapa' ";
                $gruda = ", ";
            }

            if (is_string($this->data_etapa)) {
                $set .= "$gruda data_etapa =  '$this->data_etapa' ";
                $gruda = ", ";
            }

            if (is_string($this->horario)) {
                $set .= "$gruda horario =  '$this->horario' ";
                $gruda = ", ";
            }

            if($this->ativo) {
                $set .= "$gruda ativo =  '$this->ativo' ";
                $gruda = ", ";
            }

            if ($set) {
                $db = new clsBanco();
                $db->Consulta(
                    "UPDATE
                        {$this->_tabela}
                    SET
                        $set
                    WHERE
                        cod_etapa_data = {$this->cod_etapa_data}"
                );
                return true;
            }
        }
        return false;
    }

    function detalhe()
    {
        if ($this->cod_etapa_data) {
            $db = new clsBanco();
            $db->Consulta(
                "SELECT
                    {$this->_todos_campos}
                FROM
                    {$this->_tabela}
                WHERE
                    cod_etapa_data = {$this->cod_etapa_data}"
            );

            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        }

        return false;
    }

    function excluir()
    {
        if ($this->cod_etapa_data) {
            $db  = new clsBanco();
            $obj = new clsPmieducarProcessoSeletivoDataEtapa($this->cod_etapa_data);

            if (! $obj->detalhe()) {
                $db->Consulta(
                    "DELETE FROM
                        {$this->_tabela}
                    WHERE
                        cod_etapa_data = {$this->cod_etapa_data}"
                );
            }
        }
    }
}
