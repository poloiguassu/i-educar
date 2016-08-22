<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu��do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl��cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  pmieducar
 * @subpackage  Matricula
 * @subpackage  Rematricula
 * @since       Arquivo dispon�vel desde a vers�o 1.0.0
 * @todo        Refatorar a l�gica de indice::Novo() para uma classe na camada de dom�nio
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar');
    $this->processoAp = '561';
    $this->addEstilo('localizacaoSistema');
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;
  var $data_matricula;

  function Inicializar()
  {
    $retorno = 'Novo';
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "Rematr&iacute;cula autom&aacute;tica"             
    ));
    $this->enviaLocalizacao($localizacao->montar());    

    return $retorno;
  }

  function Gerar() {
    // inputs

    $anoLetivoHelperOptions = array('situacoes' => array('em_andamento', 'nao_iniciado'));

    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'curso', 'serie'));
    $this->inputsHelper()->dynamic('turma', array('label' => 'Selecione a turma do ano anterior'));
    $this->inputsHelper()->dynamic('anoLetivo', array('label' => 'Ano destino'), $anoLetivoHelperOptions);
    $this->inputsHelper()->date('data_matricula', array('label' => 'Data da matr�cula', 'placeholder' => 'dd/mm/yyyy'));
    $this->inputsHelper()->hidden('nao_filtrar_ano', array('value' => '1'));
  }

  /**
   * @todo Refatorar a l�gica para uma classe na camada de dom�nio.
   */
  function Novo()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->db  = new clsBanco();
    $this->db2 = new clsBanco();

    $this->data_matricula = Portabilis_Date_Utils::brToPgSQL($this->data_matricula);

    $result = $this->rematricularALunos($this->ref_cod_escola, $this->ref_cod_curso,
                                        $this->ref_cod_serie, $this->ref_cod_turma, $_POST['ano']);

    return $result;
  }


  function Editar() {
    return TRUE;
  }


  protected function rematricularALunos($escolaId, $cursoId, $serieId, $turmaId, $ano) {
    $result = $this->selectMatriculas($escolaId, $cursoId, $serieId, $turmaId, $ano);
    $count = 0;
    $nomesAlunos;

    while ($result && $this->db->ProximoRegistro()) {
      list($matriculaId, $alunoId, $situacao, $nomeAluno) = $this->db->Tupla();
      $nomesAlunos[] = $nomeAluno;

      $this->db2->Consulta("UPDATE pmieducar.matricula SET ultima_matricula = '0' WHERE cod_matricula = $matriculaId");

      if ($result && $situacao == 1)
        $result = $this->rematricularAlunoAprovado($escolaId, $serieId, $ano, $alunoId);
      elseif ($result && $situacao == 2)
        $result = $this->rematricularAlunoReprovado($escolaId, $cursoId, $serieId, $ano, $alunoId);

      if (! $result)
        break;

      $count += 1;
    }

    if ($result && empty($this->mensagem)){
      if ($count > 0){
        $mensagem = "<span class='success'>Rematriculado os seguinte(s) $count aluno(s) com sucesso em $ano: </br></br>";
        foreach ($nomesAlunos as $nome) {
          $mensagem .= "{$nome} </br>";
        }
        $mensagem .= "</br> As enturma��es podem ser realizadas em: Movimenta��o > Enturma��o.</span>";
        $this->mensagem = $mensagem;
      }else{
        $this->mensagem = "<span class='notice'>Nenhum aluno rematriculado. Certifique-se que a turma possui alunos aprovados ou reprovados n�o matriculados em ".($ano-1).".</span>";
      }
    }elseif(empty($this->mensagem))
      $this->mensagem = "Ocorreu algum erro inesperado durante as rematr�culas, por favor, tente novamente.";

    return $result;
  }


  protected function selectMatriculas($escolaId, $cursoId, $serieId, $turmaId, $ano) {
    try {
      $anoAnterior = $ano - 1;

      $this->db->Consulta("SELECT cod_matricula, ref_cod_aluno, aprovado, 
                                      (SELECT upper(nome) 
                                            FROM cadastro.pessoa, pmieducar.aluno 
                                                WHERE pessoa.idpes = aluno.ref_idpes AND 
                                                          aluno.cod_aluno = ref_cod_aluno) as nome
                   FROM
                     pmieducar.matricula m, pmieducar.matricula_turma
                   WHERE aprovado in (1, 2) AND m.ativo = 1 AND ref_ref_cod_escola = $escolaId AND
                     ref_ref_cod_serie = $serieId AND ref_cod_curso = $cursoId AND
                     cod_matricula = ref_cod_matricula AND ref_cod_turma = $turmaId AND
                     matricula_turma.ativo = 1 AND
                     ano  = $anoAnterior AND
                     NOT EXISTS(select 1 from pmieducar.matricula m2 where
    			           m2.ref_cod_aluno = m.ref_cod_aluno AND
     			           m2.ano = $ano AND
     			           m2.ativo = 1 AND
     			           m2.ref_ref_cod_escola = m.ref_ref_cod_escola)");
    }
    catch (Exception $e) {
      $this->mensagem = "Erro ao selecionar matr�culas ano anterior: $anoAnterior";
      error_log("Erro ao selecionar matr�culas ano anterior, no processo rematr�cula autom�tica:" . $e->getMessage());
      return false;
    }

    return true;
  }


  protected function rematricularAlunoAprovado($escolaId, $serieId, $ano, $alunoId) {
    $nextSerieId = $this->db2->campoUnico("SELECT ref_serie_destino FROM pmieducar.sequencia_serie
                                           WHERE ref_serie_origem = $serieId AND ativo = 1");

    if (is_numeric($nextSerieId)) {
      $nextCursoId = $this->db2->CampoUnico("SELECT ref_cod_curso FROM pmieducar.serie
                                            WHERE cod_serie = $nextSerieId");

      return $this->matricularAluno($escolaId, $nextCursoId, $nextSerieId, $ano, $alunoId);
    }
    else
      $this->mensagem = "N�o foi poss�vel obter a pr�xima s�rie da sequ�ncia de enturma��o";

    return false;
  }


  protected function rematricularAlunoReprovado($escolaId, $cursoId, $serieId, $ano, $alunoId) {
    return $this->matricularAluno($escolaId, $cursoId, $serieId, $ano, $alunoId);
  }


  protected function matricularAluno($escolaId, $cursoId, $serieId, $ano, $alunoId) {
    try {
      $this->db2->Consulta(sprintf("INSERT INTO pmieducar.matricula
        (ref_ref_cod_escola, ref_ref_cod_serie, ref_usuario_cad, ref_cod_aluno, aprovado, data_cadastro, ano, ref_cod_curso, ultima_matricula, data_matricula) VALUES ('%d', '%d', '%d', '%d', '3', 'NOW()', '%d', '%d', '1','{$this->data_matricula}')",
      $escolaId, $serieId, $this->pessoa_logada, $alunoId, $ano, $cursoId));
    }
    catch (Exception $e) {
      $this->mensagem = "Erro durante matr�cula do aluno: $alunoId";
      error_log("Erro durante a matr�cula do aluno $alunoId, no processo de rematr�cula autom�tica:" . $e->getMessage());
      return false;
    }

    return true;
  }
}

// Instancia objeto de p�gina
$pagina = new clsIndexBase();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do �  p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
?>
