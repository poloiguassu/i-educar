<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Caroline Salib <caroline@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'include/funcoes.inc.php';

/**
 * Class FilaUnicaController
 * @deprecated Essa versão da API pública será descontinuada
 */
class FilaUnicaController extends ApiCoreController
{

    protected function getDadosAlunoByCertidao()
    {
        $tipoCertidao = $this->getRequest()->tipo_certidao;
        $numNovaCeridao = $this->getRequest()->certidao_nascimento;
        $numTermo = $this->getRequest()->num_termo ? $this->getRequest()->num_termo : 0;
        $numLivro = $this->getRequest()->num_livro ? $this->getRequest()->num_livro : 0;
        $numFolha = $this->getRequest()->num_folha ? $this->getRequest()->num_folha : 0;

        $sql = "SELECT cod_aluno,
                       pessoa.nome,
                       pessoa.idpes,
                       to_char(fisica.data_nasc, 'dd/mm/yyyy') AS data_nasc,
                       documento.num_termo,
                       documento.num_folha,
                       documento.num_livro,
                       documento.certidao_nascimento,
                       to_char(endereco_pessoa.cep, '99999-999') AS cep,
                       endereco_pessoa.idlog,
                       endereco_pessoa.idbai,
                       bairro.nome || ' / Zona ' || CASE bairro.zona_localizacao WHEN 1 THEN 'urbana' WHEN 2 THEN 'rural' END AS nm_bairro,
                       municipio.idmun,
                       municipio.idmun || ' - ' || municipio.nome AS nm_municipio,
                       distrito.iddis,
                       distrito.iddis || ' - ' || distrito.nome AS nm_distrito,
                       tipo_logradouro.descricao || ' ' || logradouro.nome AS nm_logradouro,
                       endereco_pessoa.numero,
                       endereco_pessoa.letra,
                       endereco_pessoa.complemento,
                       endereco_pessoa.bloco,
                       endereco_pessoa.andar,
                       endereco_pessoa.apartamento
                  FROM pmieducar.aluno
                 INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                 INNER JOIN cadastro.fisica ON (fisica.idpes = aluno.ref_idpes)
                 INNER JOIN cadastro.documento ON (documento.idpes = aluno.ref_idpes)
                  LEFT JOIN cadastro.endereco_pessoa ON (endereco_pessoa.idpes = aluno.ref_idpes)
                  LEFT JOIN public.bairro ON (bairro.idbai = endereco_pessoa.idbai)
                  LEFT JOIN public.logradouro ON (logradouro.idlog = endereco_pessoa.idlog)
                  LEFT JOIN urbano.tipo_logradouro ON (tipo_logradouro.idtlog = logradouro.idtlog)
                  LEFT JOIN public.municipio ON (municipio.idmun = bairro.idmun)
                  LEFT JOIN public.distrito ON (distrito.idmun = bairro.idmun)
                 WHERE CASE WHEN {$tipoCertidao} != 1
                                 THEN num_termo = '{$numTermo}'
                                  AND num_livro = '{$numLivro}'
                                  AND num_folha = '{$numFolha}'
                            ELSE certidao_nascimento = '{$numNovaCeridao}'
                        END";

        $attrs = array(
            'cod_aluno',
            'nome',
            'idpes',
            'data_nasc',
            'num_termo',
            'num_folha',
            'num_livro',
            'certidao_nascimento',
            'cep',
            'idlog',
            'idbai',
            'nm_bairro',
            'idmun',
            'nm_municipio',
            'iddis',
            'nm_distrito',
            'nm_logradouro',
            'numero',
            'letra',
            'complemento',
            'bloco',
            'andar',
            'apartamento'
        );
        $aluno = Portabilis_Array_Utils::filterSet($this->fetchPreparedQuery($sql), $attrs);
        return array('aluno' => $aluno[0]);
    }

    protected function getMatriculaAlunoAndamento() {
        $anoLetivo = $this->getRequest()->ano_letivo;
        $aluno = $this->getRequest()->aluno_id;

        if($aluno && $anoLetivo){
            $sql = "SELECT cod_matricula,
                           ref_cod_aluno AS cod_aluno
                      FROM pmieducar.matricula
                     WHERE ativo = 1
                       AND aprovado = 3
                       AND ano = $1
                       AND ref_cod_aluno = $2";
            $matricula = $this->fetchPreparedQuery($sql, array($anoLetivo, $aluno), false, 'first-line');
            return $matricula;
        }
        return false;
    }

    protected function getSolicitacaoAndamento() {
        $anoLetivo = $this->getRequest()->ano_letivo;
        $aluno = $this->getRequest()->aluno_id;

        if($aluno && $anoLetivo){
            $sql = "SELECT ref_cod_aluno AS cod_aluno,
                           cod_candidato_fila_unica AS cod_candidato
                      FROM pmieducar.candidato_fila_unica
                     WHERE ativo = 1
                       AND ano_letivo = $1
                       AND ref_cod_aluno = $2";
            $matricula = $this->fetchPreparedQuery($sql, array($anoLetivo, $aluno), false, 'first-line');
            return $matricula;
        }
        return false;
    }

    protected function getSeriesSugeridas() {
        $idade = $this->getRequest()->idade;
        if($idade){
            $sql = "SELECT nm_serie
                      FROM pmieducar.serie
                     WHERE ativo = 1
                       AND $1 BETWEEN idade_inicial AND idade_final";
            $series = Portabilis_Array_Utils::filterSet($this->fetchPreparedQuery($sql, $idade), 'nm_serie');
            return array('series' => $series);
        }
        return false;
    }

    protected function getDadosResponsaveisAluno() {
        $aluno = $this->getRequest()->aluno_id;
        if($aluno){
            $sql = "SELECT pessoa.idpes,
                           vinculo_familiar,
                           pessoa.nome,
                           fisica.cpf,
                           fisica.tipo_trabalho,
                           fisica.local_trabalho,
                           documento.declaracao_trabalho_autonomo,
                           to_char(fisica.horario_inicial_trabalho, 'HH24:MI') AS horario_inicial_trabalho,
                           to_char(fisica.horario_final_trabalho, 'HH24:MI') AS horario_final_trabalho,
                           fpr.ddd AS ddd_telefone,
                           fpr.fone AS telefone,
                           fpc.ddd AS ddd_telefone_celular,
                           fpc.fone AS telefone_celular
                      FROM pmieducar.responsaveis_aluno
                     INNER JOIN cadastro.fisica ON (fisica.idpes = responsaveis_aluno.ref_idpes)
                     INNER JOIN cadastro.pessoa ON (pessoa.idpes = responsaveis_aluno.ref_idpes)
                      LEFT JOIN cadastro.documento ON (documento.idpes = responsaveis_aluno.ref_idpes)
                      LEFT JOIN cadastro.fone_pessoa fpr ON (fpr.idpes = responsaveis_aluno.ref_idpes
                                                             AND fpr.tipo = 1)
                      LEFT JOIN cadastro.fone_pessoa fpc ON (fpc.idpes = responsaveis_aluno.ref_idpes
                                                             AND fpc.tipo = 2)
                     WHERE ref_cod_aluno = {$aluno}";
            $attrs = array(
                'idpes',
                'vinculo_familiar',
                'nome',
                'cpf',
                'tipo_trabalho',
                'local_trabalho',
                'declaracao_trabalho_autonomo',
                'horario_inicial_trabalho',
                'horario_final_trabalho',
                'ddd_telefone',
                'telefone',
                'ddd_telefone_celular',
                'telefone_celular'
            );
            $responsaveis = Portabilis_Array_Utils::filterSet($this->fetchPreparedQuery($sql), $attrs);
            return array('responsaveis' => $responsaveis);
        }
        return false;
    }

    protected function getMontaSelectEscolasCandidato(){
        $cod_candidato_fila_unica = $this->getRequest()->cod_candidato_fila_unica;
        $user                     = $this->currentUser();
        $userId = $user['id'];
        $nivelAcesso = $this->getNivelAcesso();
        $acessoEscolar = $nivelAcesso == 4;
        if($cod_candidato_fila_unica){

            $sql = "SELECT ecdu.ref_cod_escola AS ref_cod_escola,
                           juridica.fantasia AS nome 
                      FROM pmieducar.escola_candidato_fila_unica AS ecdu
                INNER JOIN pmieducar.escola AS esc ON esc.cod_escola = ecdu.ref_cod_escola
                INNER JOIN cadastro.juridica ON juridica.idpes = esc.ref_idpes
                     WHERE ecdu.ref_cod_candidato_fila_unica = {$cod_candidato_fila_unica}";
            if ($acessoEscolar){
                $sql .= " AND EXISTS( SELECT 1 
                                        FROM pmieducar.escola_usuario 
                                       WHERE escola_usuario.ref_cod_usuario = {$userId} 
                                         AND escola_usuario.ref_cod_escola = esc.cod_escola )";
            }
            $escolas_candidato = Portabilis_Utils_Database::fetchPreparedQuery($sql);
            return array('escolas' => $escolas_candidato);
        }

        return false;
    }

    public function Gerar() {
        if ($this->isRequestFor('get', 'get-aluno-by-certidao')) {
            $this->appendResponse($this->getDadosAlunoByCertidao());
        }else if ($this->isRequestFor('get', 'matricula-andamento')){
            $this->appendResponse($this->getMatriculaAlunoAndamento());
        }else if ($this->isRequestFor('get', 'solicitacao-andamento')){
            $this->appendResponse($this->getSolicitacaoAndamento());
        }else if ($this->isRequestFor('get', 'series-sugeridas')){
            $this->appendResponse($this->getSeriesSugeridas());
        }else if ($this->isRequestFor('get', 'responsaveis-aluno')){
            $this->appendResponse($this->getDadosResponsaveisAluno());
        }else if ($this->isRequestFor('get', 'escolas-candidato')){
            $this->appendResponse($this->getMontaSelectEscolasCandidato());
        }else{
            $this->notImplementedOperationError();
        }
    }
}
