<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsPmieducarProcessoSeletivo
{
    var $cod_selecao_processo;
    var $ref_cod_escola;
    var $ref_ano;
    var $ref_cod_curso;
    var $numero_selecionados;
    var $total_etapas;
    var $status;
    var $ativo;

    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $data_cadastro;
    var $data_exclusao;

    var $_tabela;
    var $_schema;

    /**
    * Construtor.
    */
    function __construct($cod_selecao_processo = null, $ref_cod_escola = null,
        $ref_ano = null, $ref_cod_curso = null, $numero_selecionados = null,
        $total_etapas = null, $status = null, $ref_usuario_cad = null
    ) {
        if (is_numeric($cod_selecao_processo)) {
            $this->cod_selecao_processo = $cod_selecao_processo;
        }

        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }

        if (is_numeric($ref_ano)) {
            $this->ref_ano = $ref_ano;
        }

        if (is_numeric($ref_cod_curso)) {
            $this->ref_cod_curso = $ref_cod_curso;
        }

        if (is_numeric($ref_usuario_cad)) {
            if (class_exists('clsPmieducarUsuario')) {
                $tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);

                if (method_exists($tmp_obj, 'existe')) {
                    if ($tmp_obj->existe()) {
                        $this->ref_usuario_cad = $ref_usuario_cad;
                    }
                }
                elseif (method_exists($tmp_obj, 'detalhe')) {
                    if ($tmp_obj->detalhe()) {
                        $this->ref_usuario_cad = $ref_usuario_cad;
                    }
                }
            } else {
                if ($db->CampoUnico(
                    "SELECT
                        1
                    FROM
                        pmieducar.usuario
                    WHERE cod_usuario = '{$ref_usuario_cad}'"
                )
                ) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
                }
            }
        }

        $this->numero_selecionados = $numero_selecionados;
        $this->total_etapas = $total_etapas;
        $this->status = $status;

        $this->_schema = "pmieducar.";
        $this->_tabela = $this->_schema . "selecao_processo";

        $this->_campos_lista = $this->_todos_campos =  "cod_selecao_processo,
            ref_cod_escola, ref_ano, ref_cod_curso, numero_selecionados,
            total_etapas, finalizado, ref_usuario_exc, ref_usuario_cad,
            data_cadastro, data_exclusao, ativo";
    }

    function lista($numeric_ref_cod_escola = null, $numeric_ref_ano = null,
        $inicio_limite = null, $qtd_registros = null
    ) {
        $whereAnd = '';
        $where    = '';

        if (is_numeric($numeric_ref_cod_escola)) {
            $where   .= "{$whereAnd} ref_cod_escola = '$numeric_ref_cod_escola'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($numeric_ref_ano)) {
            $where   .= "{$whereAnd} ref_ano = '$numeric_ref_ano'";
            $whereAnd = ' AND ';
        }

        if ($inicio_limite !== false && $qtd_registros) {
            $limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
        }

        $orderBy = ' ORDER BY ref_ano';

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
        if(is_numeric($this->ref_cod_escola) || is_numeric($this->ref_ano)) {
            $db = new clsBanco();

            $campos  = '';
            $valores = '';
            $gruda   = '';

            if (is_numeric($this->ref_cod_escola)) {
                $campos  .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ano)) {
                $campos  .= "{$gruda}ref_ano";
                $valores .= "{$gruda}'{$this->ref_ano}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_curso)) {
                $campos  .= "{$gruda}ref_cod_curso";
                $valores .= "{$gruda}'{$this->ref_cod_curso}'";
                $gruda = ', ';
            }

            if (is_numeric($this->numero_selecionados)) {
                $campos  .= "{$gruda}numero_selecionados";
                $valores .= "{$gruda}'{$this->numero_selecionados}'";
                $gruda = ', ';
            }

            if (is_numeric($this->total_etapas)) {
                $campos  .= "{$gruda}total_etapas";
                $valores .= "{$gruda}'{$this->total_etapas}'";
                $gruda = ', ';
            }

            if ($this->status) {
                $campos  .= "{$gruda}status";
                $valores .= "{$gruda}'{$this->status}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $campos  .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            $campos  .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos  .= "{$gruda}ativo";
            $valores .= "{$gruda}'true'";
            $gruda = ', ';

            $db->Consulta(
                "INSERT INTO
                    {$this->_tabela} ($campos)
                VALUES ($valores)"
            );
            return $db->InsertId("selecao_processo_cod_selecao_processo_seq");
        }
        return false;
    }

    function edita()
    {
        // verificacoes de campos obrigatorios para insercao
        if (is_numeric($this->cod_selecao_processo)) {
            $set = "";
            $gruda = "";

            if (is_numeric($this->ref_cod_escola)) {
                $set .= " ref_cod_escola =  '$this->ref_cod_escola' ";
                $gruda = ", ";
            }

            if (is_numeric($this->ref_ano)) {
                $set .= "$gruda ref_ano =  '$this->ref_ano' ";
                $gruda = ", ";
            }

            if (is_numeric($this->ref_cod_curso)) {
                $set .= "$gruda ref_cod_curso =  '$this->ref_cod_curso' ";
                $gruda = ", ";
            }

            if (is_numeric($this->numero_selecionados)) {
                $set .= "$gruda numero_selecionados =  '$this->numero_selecionados' ";
                $gruda = ", ";
            }

            if (is_numeric($this->total_etapas)) {
                $set .= "$gruda total_etapas =  '$this->total_etapas' ";
                $gruda = ", ";
            }

            if($this->status) {
                $set .= "$gruda status =  '$this->status' ";
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
                        cod_selecao_processo = {$this->cod_selecao_processo}"
                );
                return true;
            }
        }
        return false;
    }

    function detalhe()
    {
        if ($this->cod_selecao_processo) {
            $db = new clsBanco();
            $db->Consulta(
                "SELECT
                    {$this->_todos_campos}
                FROM
                    {$this->_tabela}
                WHERE
                    cod_selecao_processo = {$this->cod_selecao_processo}"
            );

            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        }

        return false;
    }

    function queryRapida($int_cod_selecao_processo)
    {
        $this->cod_selecao_processo = $int_cod_selecao_processo;
        $this->detalhe();

        $resultado = array();
        $pos       = 0;

        for ($i = 1; $i < func_num_args(); $i++) {
            $campo = func_get_arg($i);

            $resultado[$pos]   = $this->$campo ? $this->$campo : '';
            $resultado[$campo] = & $resultado[$pos];

            $pos++;
        }

        if (count($resultado) > 0) {
            return $resultado;
        }

        return false;
    }

    function excluir()
    {
        if ($this->cod_selecao_processo) {
            $db  = new clsBanco();
            $obj = new clsPmieducarProcessoSeletivo($this->cod_selecao_processo);

            if (! $obj->detalhe()) {
                $db->Consulta(
                    "DELETE FROM
                        {$this->_tabela}
                    WHERE
                        cod_selecao_processo = {$this->cod_selecao_processo}"
                );
            }
        }
    }
}
