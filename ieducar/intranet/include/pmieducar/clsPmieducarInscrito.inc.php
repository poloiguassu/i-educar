<?php

class clsPmieducarInscrito
{
    public $cod_inscrito;
    public $ref_cod_selecao_processo;
    public $ref_cod_aluno;
    public $estudando_serie;
    public $egresso;
    public $estudando_turno;
    public $guarda_mirim;
    public $indicacao;
    public $copia_rg;
    public $copia_cpf;
    public $copia_residencia;
    public $copia_historico;
    public $copia_renda;
    public $encaminhamento;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $_tabela;
    public $_schema;

    /**
    * Construtor.
    */
    public function __construct(
        $cod_inscrito = null,
        $ref_cod_selecao_processo = null,
        $ref_cod_aluno = null,
        $estudando_serie = null,
        $egresso = null,
        $estudando_turno = null,
        $guarda_mirim = null,
        $indicacao = null,
        $copia_rg = null,
        $copia_cpf = null,
        $copia_residencia = null,
        $copia_historico = null,
        $copia_renda = null,
        $encaminhamento = null,
        $ref_usuario_cad = null
    ) {
        if ($cod_inscrito) {
            $this->cod_inscrito = $cod_inscrito;
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

        $this->ref_cod_selecao_processo = $ref_cod_selecao_processo;
        $this->ref_cod_aluno = $ref_cod_aluno;
        $this->estudando_serie = $estudando_serie;
        $this->egresso = $egresso;
        $this->estudando_turno = $estudando_turno;
        $this->guarda_mirim = $guarda_mirim;
        $this->indicacao = $indicacao;
        $this->copia_rg = $copia_rg;
        $this->copia_cpf = $copia_cpf;
        $this->copia_residencia = $copia_residencia;
        $this->copia_historico = $copia_historico;
        $this->copia_renda = $copia_renda;
        $this->encaminhamento = $encaminhamento;
        $this->ref_usuario_cad = $ref_usuario_cad;

        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'inscrito as i';

        $this->_campos_lista = $this->_todos_campos = 'i.cod_inscrito,
            i.ref_cod_selecao_processo, i.ref_cod_aluno, i.estudando_serie, i.egresso,
            i.estudando_turno, i.guarda_mirim, indicacao, i.copia_rg, i.copia_residencia,
            i.copia_historico, i.copia_renda, i.encaminhamento, i.ref_usuario_exc,
            i.ref_usuario_cad, i.data_cadastro, i.data_exclusao, i.ativo';
    }

    public function lista(
        $numeric_etapa_1 = false,
        $numeric_etapa_2 = false,
        $numeric_etapa_3 = false,
        $int_cod_inscrito = false,
        $str_nome = false,
        $numeric_cpf = false,
        $numeric_rg = false,
        $inicio_limite = false,
        $qtd_registros = false,
        $str_orderBy = false,
        $int_ref_cod_sistema = false
    ) {
        $whereAnd = '';
        $where    = '';

        if (is_string($str_nome) && $str_nome != '') {
            $str_nome = addslashes($str_nome);
            $str_nome = str_replace(' ', '%', $str_nome);

            $where   .= "{$whereAnd} nome ILIKE E'%{$str_nome}%' ";
            $whereAnd = ' AND ';
        }

        if (is_string($numeric_cpf)) {
            $numeric_cpf = addslashes($numeric_cpf);

            $where   .= "{$whereAnd} cpf ILIKE E'%{$numeric_cpf}%' ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cod_inscrito)) {
            $where   .= "{$whereAnd} cod_inscrito = '$int_cod_inscrito'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($numeric_etapa_1)) {
            $where   .= "{$whereAnd} etapa_1 = '$numeric_etapa_1'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($numeric_etapa_2)) {
            $where   .= "{$whereAnd} etapa_2 = '$numeric_etapa_2'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($numeric_etapa_3)) {
            $where   .= "{$whereAnd} etapa_3 = '$numeric_etapa_3'";
            $whereAnd = ' AND ';
        }

        $where   .= 'i.ref_cod_aluno = a.cod_aluno AND f.idpes = a.ref_idpes
            AND p.idpes = a.ref_idpes AND d.idpes = a.ref_idpes 
            AND e.idpes = a.ref_idpes AND m.ref_cod_aluno = i.ref_cod_aluno';
        $whereAnd = ' AND ';

        if ($inicio_limite !== false && $qtd_registros) {
            $limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
        }

        $orderBy = ' ORDER BY ';

        if ($str_orderBy) {
            $orderBy .= $str_orderBy . ' ';
        } else {
            $orderBy .= 'fcn_upper_nrm(nome) ';
        }

        $db  = new clsBanco();

        if ($where) {
            $where = 'WHERE '.$where;
        }

        $tabela = "{$this->_tabela}, cadastro.pessoa as p, cadastro.fisica f,
            cadastro.documento d, cadastro.endereco_pessoa e, pmieducar.aluno as a,
            modules.ficha_medica_aluno m";
        $campos = "{$this->_campos_lista}, p.nome, f.data_nasc, f.sexo, f.cpf,
            p.email, d.rg, e.cep, m.grupo_sanguineo, m.fator_rh";

        $total = $db->CampoUnico(
            "SELECT
                COUNT(0)
            FROM
                {$tabela}
                {$where}"
        );

        $db->Consulta(
            "SELECT
                {$campos}
            FROM
                {$tabela}
                {$where} {$orderBy} {$limite}"
        );

        $resultado = [];

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['nome']  = transforma_minusculo($tupla['nome']);
            $tupla['total'] = $total;

            $resultado[] = $tupla;
        }

        if (count($resultado) > 0) {
            return $resultado;
        }

        return false;
    }

    public function lista2(
        $numeric_etapa_1 = false,
        $numeric_etapa_2 = false,
        $numeric_etapa_3 = false,
        $int_cod_inscrito = false,
        $str_nome = false,
        $numeric_cpf = false,
        $numeric_rg = false,
        $inicio_limite = false,
        $qtd_registros = false,
        $str_orderBy = false,
        $int_ref_cod_sistema = false
    ) {
        $whereAnd = '';
        $where    = '';

        if (is_string($str_nome) && $str_nome != '') {
            $str_nome = addslashes($str_nome);
            $str_nome = str_replace(' ', '%', $str_nome);

            $where   .= "{$whereAnd} nome ILIKE E'%{$str_nome}%' ";
            $whereAnd = ' AND ';
        }

        if (is_string($numeric_cpf)) {
            $numeric_cpf = addslashes($numeric_cpf);

            $where   .= "{$whereAnd} cpf ILIKE E'%{$numeric_cpf}%' ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cod_inscrito)) {
            $where   .= "{$whereAnd} cod_inscrito = '$int_cod_inscrito'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($numeric_etapa_1)) {
            $where   .= "{$whereAnd} etapa_1 >= '$numeric_etapa_1'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($numeric_etapa_2)) {
            $where   .= "{$whereAnd} etapa_2 = '$numeric_etapa_2'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($numeric_etapa_3)) {
            $where   .= "{$whereAnd} etapa_3 = '$numeric_etapa_3'";
            $whereAnd = ' AND ';
        }

        if ($inicio_limite !== false && $qtd_registros) {
            $limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
        }

        $orderBy = ' ORDER BY ';

        if ($str_orderBy) {
            $orderBy .= $str_orderBy . ' ';
        } else {
            $orderBy .= 'fcn_upper_nrm(nome) ';
        }

        $db  = new clsBanco();

        if ($where) {
            $where = 'WHERE '.$where;
        }

        $total = $db->CampoUnico('SELECT COUNT(0) FROM {$this->_tabela} ' . $where);

        $db->Consulta(sprintf(
            'SELECT {$this->_campos_lista} FROM {$this->_tabela} %s %s %s',
            $where,
            $orderBy,
            $limite
        ));

        $resultado = [];

        while ($db->ProximoRegistro()) {
            $tupla          = $db->Tupla();
            $tupla['nome']  = transforma_minusculo($tupla['nome']);
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
        if (is_numeric($this->ref_cod_aluno)) {
            $db = new clsBanco();

            $campos  = '';
            $valores = '';
            $gruda   = '';

            if (is_numeric($this->ref_cod_selecao_processo)) {
                $campos  .= "{$gruda}ref_cod_selecao_processo";
                $valores .= "{$gruda}'{$this->ref_cod_selecao_processo}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_aluno)) {
                $campos  .= "{$gruda}ref_cod_aluno";
                $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
                $gruda = ', ';
            }

            if (is_numeric($this->estudando_serie)) {
                $campos  .= "{$gruda}estudando_serie";
                $valores .= "{$gruda}'{$this->estudando_serie}'";
                $gruda = ', ';
            }

            if (is_numeric($this->egresso)) {
                $campos  .= "{$gruda}egresso";
                $valores .= "{$gruda}'{$this->egresso}'";
                $gruda = ', ';
            }

            if (is_numeric($this->estudando_turno)) {
                $campos  .= "{$gruda}estudando_turno";
                $valores .= "{$gruda}'{$this->estudando_turno}'";
                $gruda = ', ';
            }

            if (is_numeric($this->guarda_mirim)) {
                $campos  .= "{$gruda}guarda_mirim";
                $valores .= "{$gruda}'{$this->guarda_mirim}'";
                $gruda = ', ';
            }

            if (is_string($this->indicacao)) {
                $campos  .= "{$gruda}indicacao";
                $valores .= "{$gruda}'{$this->indicacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->copia_rg)) {
                $campos  .= "{$gruda}copia_rg";
                $valores .= "{$gruda}'{$this->copia_rg}'";
                $gruda = ', ';
            }

            if (is_numeric($this->copia_cpf)) {
                $campos  .= "{$gruda}copia_cpf";
                $valores .= "{$gruda}'{$this->copia_cpf}'";
                $gruda = ', ';
            }

            if (is_numeric($this->copia_residencia)) {
                $campos  .= "{$gruda}copia_residencia";
                $valores .= "{$gruda}'{$this->copia_residencia}'";
                $gruda = ', ';
            }

            if (is_numeric($this->copia_historico)) {
                $campos  .= "{$gruda}copia_historico";
                $valores .= "{$gruda}'{$this->copia_historico}'";
                $gruda = ', ';
            }

            if (is_numeric($this->copia_renda)) {
                $campos  .= "{$gruda}copia_renda";
                $valores .= "{$gruda}'{$this->copia_renda}'";
                $gruda = ', ';
            }

            if (is_numeric($this->encaminhamento)) {
                $campos  .= "{$gruda}encaminhamento";
                $valores .= "{$gruda}'{$this->encaminhamento}'";
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
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta(
                "INSERT INTO
                    {$this->_tabela} ($campos)
                VALUES
                    ($valores)"
            );

            return $db->InsertId('pmieducar.inscrito_cod_inscrito_seq');
        }

        return false;
    }

    public function edita()
    {
        // verificacoes de campos obrigatorios para insercao
        if (is_numeric($this->cod_inscrito)) {
            $set = '';
            $gruda = '';

            if ($this->ref_cod_selecao_processo) {
                $set .= " ref_cod_selecao_processo =  '$this->ref_cod_selecao_processo' ";
                $gruda = ', ';
            }

            if ($this->ref_cod_aluno) {
                $set .= "$gruda ref_cod_aluno =  '$this->ref_cod_aluno' ";
                $gruda = ', ';
            }

            if ($this->estudando_serie) {
                $set .= "$gruda estudando_serie =  '$this->estudando_serie' ";
                $gruda = ', ';
            }

            if (is_numeric($this->egresso)) {
                $set .= "$gruda egresso =  '$this->egresso' ";
                $gruda = ', ';
            }

            if ($this->estudando_turno) {
                $set .= "$gruda estudando_turno =  '$this->estudando_turno' ";
                $gruda = ', ';
            }

            if ($this->guarda_mirim) {
                $set .= "$gruda guarda_mirim =  '$this->guarda_mirim' ";
                $gruda = ', ';
            }

            if (is_numeric($this->indicacao)) {
                $set .= "$gruda indicacao =  '$this->indicacao' ";
                $gruda = ', ';
            }

            if (is_numeric($this->copia_rg)) {
                $set .= "$gruda copia_rg =  '$this->copia_rg' ";
                $gruda = ', ';
            }

            if (is_numeric($this->copia_cpf)) {
                $set .= "$gruda copia_cpf =  '$this->copia_cpf' ";
                $gruda = ', ';
            }

            if (is_numeric($this->copia_residencia)) {
                $set .= "$gruda copia_residencia =  '$this->copia_residencia' ";
                $gruda = ', ';
            }

            if (is_numeric($this->copia_historico)) {
                $set .= "$gruda copia_historico =  '$this->copia_historico' ";
                $gruda = ', ';
            }

            if (is_numeric($this->copia_renda)) {
                $set .= "$gruda copia_renda =  '$this->copia_renda' ";
                $gruda = ', ';
            }

            if (is_numeric($this->encaminhamento)) {
                $set .= "$gruda encaminhamento =  '$this->encaminhamento' ";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "$gruda ref_usuario_exc =  '$this->ref_usuario_exc' ";
                $gruda = ', ';
            }

            if ($set) {
                $db = new clsBanco();
                $db->Consulta(
                    "UPDATE
                        {$this->_tabela}
                    SET
                        {$set}
                    WHERE
                        cod_inscrito = '$this->cod_inscrito'"
                );

                return $this->cod_inscrito;
            }
        }

        return false;
    }

    public function detalhe()
    {
        if ($this->cod_inscrito) {
            $db = new clsBanco();
            $db->Consulta(
                "SELECT
                    {$this->_campos_lista}
                FROM
                    {$this->_tabela}
                WHERE
                    cod_inscrito = '{$this->cod_inscrito}'"
            );

            $teste = "SELECT
            {$this->_campos_lista}
        FROM
            {$this->_tabela}
        WHERE
            cod_inscrito = '{$this->cod_inscrito}'";

            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                return $tupla;
            }
        }

        return false;
    }

    public function queryRapida($int_cod_inscrito)
    {
        $this->cod_inscrito = $int_cod_inscrito;
        $this->detalhe();

        $resultado = [];
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

    public function excluir()
    {
        if ($this->cod_inscrito) {
            $db  = new clsBanco();
            $obj = new clsFuncionario($this->cod_inscrito);

            if (! $obj->detalhe()) {
                $db->Consulta(
                    "DELETE FROM
                        pmieducar.inscrito
                    WHERE
                        cod_inscrito = {$this->cod_inscrito}"
                );
            }
        }
    }
}
