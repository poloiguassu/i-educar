<?php

// O tempo máximo default (30) pode ser atingido ao carregar as matriculas sem selecionar componente curricular,
// o ideal seria fazer o caregamento assincrono das matriculas.
/*if (ini_get("max_execution_time") < 120)
  ini_set("max_execution_time", 120);
*/

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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Avaliacao
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

//require_once 'Core/Controller/Page/EditController.php';

require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'App/Model/MatriculaSituacao.php';
require_once 'RegraAvaliacao/Model/TipoPresenca.php';
require_once 'RegraAvaliacao/Model/TipoParecerDescritivo.php';

require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
require_once 'include/modules/clsModulesNotaExame.inc.php';

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Object/Utils.php';

class DiarioApiController extends ApiCoreController
{
  protected $_dataMapper  = 'Avaliacao_Model_NotaComponenteDataMapper';
  protected $_processoAp  = 642;

  // validations

  // post nota validations

  protected function validatesValueOfAttValueIsInOpcoesNotas() {
    //$expectedValues = array_keys($this->getOpcoesNotas());
    //return $this->validator->validatesValueInSetOf($this->getRequest()->att_value, $expectedValues, 'att_value');
    return true;
  }


  protected function validatesCanChangeDiarioForAno() {
    $escola = App_Model_IedFinder::getEscola($this->getRequest()->escola_id);

    $ano                 = new clsPmieducarEscolaAnoLetivo();
    $ano->ref_cod_escola = $this->getRequest()->escola_id;
    $ano->ano            = $this->getRequest()->ano;
    $ano                 = $ano->detalhe();

    $anoLetivoEncerrado = is_array($ano)     && count($ano) > 0    &&
                          $ano['ativo'] == 1 && $ano['andamento'] == 2;

    if ($escola['bloquear_lancamento_diario_anos_letivos_encerrados'] == '1' && $anoLetivoEncerrado) {
      $this->messenger->append("O ano letivo '{$this->getRequest()->ano}' est� encerrado, esta escola est� configurada para n�o permitir alterar o diário de anos letivos encerrados.");
      return false;
    }

    return true;
  }


  protected function validatesRegraAvaliacaoHasNota() {
    $isValid = $this->serviceBoletim()->getRegra()->get('tipoNota') != RegraAvaliacao_Model_Nota_TipoValor::NENHUM;

    if (! $isValid)
      $this->messenger->append("Nota não lançada, pois a regra de avaliação não utiliza nota.");

    return $isValid;
  }


  protected function validatesRegraAvaliacaoHasFormulaRecuperacao() {
    $isValid = $this->getRequest()->etapa != 'Rc' ||
               ! is_null($this->serviceBoletim()->getRegra()->formulaRecuperacao);

    if (! $isValid)
      $this->messenger->append("Nota de recuperação não lançada, pois a fórmula de recuperação não possui fórmula de recuperação.");

    return $isValid;
  }


  protected function validatesRegraAvaliacaoHasFormulaRecuperacaoWithTypeRecuperacao() {
    $isValid = $this->getRequest()->etapa != 'Rc' ||
               ($this->serviceBoletim()->getRegra()->formulaRecuperacao->get('tipoFormula') ==
                FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO);

    if (! $isValid)
      $this->messenger->append("Nota de recuperação não lançada, pois a fórmula de recuperação é diferente do tipo média recuperação.");

    return $isValid;
  }


  protected function validatesPreviousNotasHasBeenSet() {
    $hasPreviousNotas   = true;
    $etapasWithoutNotas = array();

    if($this->getRequest()->etapa == 'Rc')
      $etapaRequest = $this->serviceBoletim()->getOption('etapas');
    else
      $etapaRequest = $this->getRequest()->etapa;


    for($etapa = 1; $etapa <= $etapaRequest; $etapa++) {
      $nota = $this->getNotaAtual($etapa);

      if(($etapa != $this->getRequest()->etapa || $this->getRequest()->etapa == 'Rc') &&
         empty($nota) && ! is_numeric($nota)) {
        $hasPreviousNotas     = false;
        $etapasWithoutNotas[] = $etapa;
      }
    }

    if (! $hasPreviousNotas) {
      $this->messenger->append("Nota somente pode ser lançada após lançar notas nas etapas: " .
                               join(', ', $etapasWithoutNotas) . ' deste componente curricular.');
    }

    return $hasPreviousNotas;
  }


  // post falta validations

  protected function validatesPreviousFaltasHasBeenSet() {
    $hasPreviousFaltas   = true;
    $etapasWithoutFaltas = array();

    for($etapa = 1; $etapa <= $this->getRequest()->etapa; $etapa++) {
      $falta = $this->getFaltaAtual($etapa);

      if($etapa != $this->getRequest()->etapa && empty($falta) && ! is_numeric($falta)) {
        $hasPreviousFaltas     = false;
        $etapasWithoutFaltas[] = $etapa;
      }
    }

    if (! $hasPreviousFaltas) {
      if ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
        $this->messenger->append("Falta somente pode ser lançada após lançar faltas nas etapas anteriores: " .
                                 join(', ', $etapasWithoutFaltas) . ' deste componente curricular.');
      }
      else{
        $this->messenger->append("Falta somente pode ser lançada após lançar faltas nas etapas anteriores: " .
                                 join(', ', $etapasWithoutFaltas) . '.');
      }
    }

    return $hasPreviousFaltas;
  }


  // post/ delete parecer validations

  protected function validatesEtapaParecer() {
    $isValid           = false;
    $etapa             = $this->getRequest()->etapa;

    $tiposParecerAnual = array(RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
                               RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL);

    $parecerAnual      = in_array($this->serviceBoletim()->getRegra()->get('parecerDescritivo'),
                                  $tiposParecerAnual);

    if($parecerAnual && $etapa != 'An')
      $this->messenger->append("Valor inválido para o atributo 'etapa', é esperado 'An' e foi recebido '{$etapa}'.");
    elseif(! $parecerAnual && $etapa == 'An')
      $this->messenger->append("Valor inválido para o atributo 'etapa', é esperado um valor diferente de 'An'.");
    else
      $isValid = true;

    return $isValid;
  }


  protected function validatesPresenceOfComponenteCurricularIdIfParecerComponente() {
    $tiposParecerComponente = array(RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
                                    RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE);

    $parecerPorComponente   = in_array($this->serviceBoletim()->getRegra()->get('parecerDescritivo'),
                                       $tiposParecerComponente);

    return (! $parecerPorComponente) || $this->validatesPresenceOf('componente_curricular_id');
  }


  // post parecer validations

  protected function validatesRegraAvaliacaoHasParecer() {
    $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
    $isValid   = $tpParecer != RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM;

    if (! $isValid)
      $this->messenger->append("Parecer descritivo não lançado, pois a regra de avaliação não utiliza parecer.");

    return $isValid;
  }



  // delete nota validations

  protected function validatesInexistenceOfNotaExame() {
    $isValid = true;

    if ($this->getRequest()->etapa != 'Rc') {
      $notaExame = $this->getNotaAtual($etapa = 'Rc');
      $isValid   = empty($notaExame);

      if(! $isValid)
        $this->messenger->append('Nota da matrícula '. $this->getRequest()->matricula_id .' somente pode ser removida, após remover nota do exame.', 'error');
    }

    return $isValid;
  }


  protected function validatesInexistenceNotasInNextEtapas() {
    $etapasComNota = array();

    if (is_numeric($this->getRequest()->etapa)) {
      $etapas = $this->serviceBoletim()->getOption('etapas');
      $etapa  = $this->getRequest()->etapa + 1;

      for($etapa; $etapa <= $etapas; $etapa++) {
        $nota = $this->getNotaAtual($etapa);

        if (! empty($nota))
          $etapasComNota[] = $etapa;
      }

      if (! empty($etapasComNota)) {
        $msg = "Nota somente pode ser removida, após remover as notas lançadas nas etapas posteriores: " .
               join(', ', $etapasComNota) . '.';
        $this->messenger->append($msg, 'error');
      }
    }

    return empty($etapasComNota);
  }


  // delete falta validations


  protected function validatesInexistenceFaltasInNextEtapas() {
    $etapasComFalta = array();

    if (is_numeric($this->getRequest()->etapa)) {
      $etapas = $this->serviceBoletim()->getOption('etapas');
      $etapa  = $this->getRequest()->etapa + 1;

      for($etapa; $etapa <= $etapas; $etapa++) {
        $falta = $this->getFaltaAtual($etapa);

        if(! empty($falta))
          $etapasComFalta[] = $etapa;

      }

      if (! empty($etapasComFalta))
        $this->messenger->append("Falta somente pode ser removida, após remover as faltas lançadas nas etapas posteriores: " . join(', ', $etapasComFalta) . '.', 'error');
    }

    return empty($etapasComFalta);
  }


  protected function validatesPresenceOfMatriculaIdOrComponenteCurricularId() {
    if (empty($this->getRequest()->componente_curricular_id) && empty($this->getRequest()->matricula_id)) {
      $this->messenger->append('É necessário receber matricula_id ou componente_curricular_id.', 'error');
      return false;
    }

    return true;
  }


  // responders validations


  protected function canGetMatriculas() {
    return $this->validatesPresenceOf(array('instituicao_id',
                                     'escola_id',
                                     'curso_id',
                                     'curso_id',
                                     'serie_id',
                                     'turma_id',
                                     'ano',
                                     'etapa',
								     'data_aula')) &&
          $this->validatesPresenceOfMatriculaIdOrComponenteCurricularId() &&
          $this->validatesCanChangeDiarioForAno();
  }


  protected function canPost() {
    return $this->validatesPresenceOf('etapa') &&
           $this->validatesPresenceOf('matricula_id');
  }


  protected function canPostNota() {
    return $this->canPost() &&
           $this->validatesIsNumeric('att_value') &&
           $this->validatesValueOfAttValueIsInOpcoesNotas(false) &&
           $this->validatesPresenceOf('componente_curricular_id') &&
           $this->validatesRegraAvaliacaoHasNota() &&
           $this->validatesRegraAvaliacaoHasFormulaRecuperacao() &&
           $this->validatesRegraAvaliacaoHasFormulaRecuperacaoWithTypeRecuperacao() &&
           $this->validatesPreviousNotasHasBeenSet();
  }


  protected function canPostFalta() {
    return $this->canPost() &&
           $this->validatesIsNumeric('att_value') &&
           $this->validatesPreviousFaltasHasBeenSet();
  }


  protected function canPostParecer() {

    return $this->canPost() &&
           $this->validatesPresenceOf('att_value') &&
           $this->validatesEtapaParecer() &&
           $this->validatesRegraAvaliacaoHasParecer() &&
           $this->validatesPresenceOfComponenteCurricularIdIfParecerComponente();
  }


  protected function canDelete() {
    return $this->validatesPresenceOf('etapa');
  }


  protected function canDeleteNota() {
    return $this->canDelete() &&
           $this->validatesPresenceOf('componente_curricular_id') &&
           $this->validatesInexistenceOfNotaExame() &&
           $this->validatesInexistenceNotasInNextEtapas();
  }


  protected function canDeleteFalta() {
    return $this->canDelete() &&
           $this->validatesInexistenceFaltasInNextEtapas();
  }


  protected function canDeleteParecer() {
    return $this->canDelete() &&
           $this->validatesEtapaParecer() &&
           $this->validatesPresenceOfComponenteCurricularIdIfParecerComponente();
  }


  // responders

  // post

  protected function postNota() {
    if ($this->canPostNota()) {

      $nota = new Avaliacao_Model_NotaComponente(array(
                  'componenteCurricular' => $this->getRequest()->componente_curricular_id,
                  'nota'                 => urldecode($this->getRequest()->att_value),
                  'etapa'                => $this->getRequest()->etapa));

      $this->serviceBoletim()->addNota($nota);
      $this->trySaveServiceBoletim();
      $this->messenger->append('Nota matrícula '. $this->getRequest()->matricula_id .' alterada com sucesso.', 'success');
    }

    $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
    $this->appendResponse('nota_necessaria_exame', $notaNecessariaExame = $this->getNotaNecessariaExame($this->getRequest()->componente_curricular_id));

    if (!empty($notaNecessariaExame) && $this->getSituacaoMatricula()=='Em Exame')
      $this->createOrUpdateNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id, $notaNecessariaExame);
    else
      $this->deleteNotaExame($this->getRequest()->matricula_id, $this->getRequest()->componente_curricular_id);
  }


  // TODO mover validacao para canPostFalta
  protected function postFalta() {

    $canPost = $this->canPostFalta();
    if ($canPost && $this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
      $canPost = $this->validatesPresenceOf('componente_curricular_id');

    if ($canPost) {
      if ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)        $falta = $this->getFaltaComponente();
      elseif ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
        $falta = $this->getFaltaGeral();

      $this->serviceBoletim()->addFalta($falta);
      $this->trySaveServiceBoletim();
      $this->messenger->append('Falta matr�cula '. $this->getRequest()->matricula_id .' alterada com sucesso.', 'success');
    }

    $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
  }


  protected function postParecer() {

    if ($this->canPostParecer()) {
      $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
      $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

      if ($tpParecer == $cnsParecer::ETAPA_COMPONENTE || $tpParecer == $cnsParecer::ANUAL_COMPONENTE)
        $parecer = $this->getParecerComponente();
      else
        $parecer = $this->getParecerGeral();

      $this->serviceBoletim()->addParecer($parecer);
      $this->trySaveServiceBoletim();
      $this->messenger->append('Parecer descritivo matricula '. $this->getRequest()->matricula_id .' alterado com sucesso.', 'success');
    }

    $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
  }


  // delete

  protected function deleteNota() {
    if ($this->canDeleteNota()) {

      $nota = $this->getNotaAtual();
      if (empty($nota) && ! is_numeric($nota))
        $this->messenger->append('Nota matrícula '. $this->getRequest()->matricula_id .' inexistente ou já removida.', 'notice');
      else
      {
        $this->serviceBoletim()->deleteNota($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
        $this->trySaveServiceBoletim();
        $this->messenger->append('Nota matrícula '. $this->getRequest()->matricula_id .' removida com sucesso.', 'success');
      }
    }

    $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
  }


  protected function deleteFalta() {
    $canDelete = $this->canDeleteFalta();
    $cnsPresenca = RegraAvaliacao_Model_TipoPresenca;
    $tpPresenca = $this->serviceBoletim()->getRegra()->get('tipoPresenca');

    if ($canDelete && $tpPresenca == $cnsPresenca::POR_COMPONENTE) {
      $canDelete              = $this->validatesPresenceOf('componente_curricular_id');
      $componenteCurricularId = $this->getRequest()->componente_curricular_id;
    }
    else
      $componenteCurricularId = null;

    if ($canDelete && is_null($this->getFaltaAtual())) {
      $this->messenger->append('Falta matr��cula '. $this->getRequest()->matricula_id .' inexistente ou j� removida.', 'notice');
    }
    elseif ($canDelete) {
      $this->serviceBoletim()->deleteFalta($this->getRequest()->etapa, $componenteCurricularId, $this->getRequest()->data_aula);
      $this->trySaveServiceBoletim();
      $this->messenger->append('Falta matr��cula '. $this->getRequest()->matricula_id .' removida com sucesso.', 'success');
    }

    $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
  }


  protected function deleteParecer() {
    if ($this->canDeleteParecer()) {
      $parecerAtual = $this->getParecerAtual();

      if ((is_null($parecerAtual) || $parecerAtual == '')) {
        $this->messenger->append('Parecer descritivo matrícula '. $this->getRequest()->matricula_id .' inexistente ou j� removido.', 'notice');
      }
      else{
        $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
        $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

        if ($tpParecer == $cnsParecer::ANUAL_COMPONENTE || $tpParecer == $cnsParecer::ETAPA_COMPONENTE)
          $this->serviceBoletim()->deleteParecer($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
        else
          $this->serviceBoletim()->deleteParecer($this->getRequest()->etapa);

        $this->trySaveServiceBoletim();
        $this->messenger->append('Parecer descritivo matrícula '. $this->getRequest()->matricula_id .' removido com sucesso.', 'success');
      }
    }

    $this->appendResponse('componente_curricular_id', $this->getRequest()->componente_curricular_id);
    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
  }


  // get

  protected function getMatriculas() {
    $matriculas = array();

    if ($this->canGetMatriculas()) {
      $alunos = new clsPmieducarMatriculaTurma();
      $alunos->setOrderby("sequencial_fechamento , translate(pessoa.nome,'".Portabilis_String_Utils::toLatin1(åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ)."', '".Portabilis_String_Utils::toLatin1(aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN)."')");

      $alunos = $alunos->lista(
        $this->getRequest()->matricula_id,
        $this->getRequest()->turma_id,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        1,
        $this->getRequest()->serie_id,
        $this->getRequest()->curso_id,
        $this->getRequest()->escola_id,
        $this->getRequest()->instituicao_id,
        $this->getRequest()->aluno_id,
        NULL,
        array(1, 2, 3),
        NULL,
        NULL,
        $this->getRequest()->ano,
        NULL,
        TRUE,
        NULL,
        NULL,
        TRUE,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL
      );

      if (! is_array($alunos))
        $alunos = array();

      foreach($alunos as $aluno) {
        $matricula   = array();
        $matriculaId = $aluno['ref_cod_matricula'];

        // seta id da matricula a ser usado pelo metodo serviceBoletim
        $this->setCurrentMatriculaId($matriculaId);

        $matricula['componentes_curriculares'] = $this->loadComponentesCurricularesForMatricula($matriculaId);
        $matricula['matricula_id']             = $aluno['ref_cod_matricula'];
        $matricula['aluno_id']                 = $aluno['ref_cod_aluno'];
        $matricula['nome']                     = $this->safeString($aluno['nome_aluno']);

        $matriculas[] = $matricula;
      }
    }

    // adiciona regras de avaliacao
    if(! empty($matriculas)) {
      $this->appendResponse('details', $this->getRegraAvaliacao());
      $this->appendResponse('situacao', $this->getSituacaoMatricula());
    }

    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);

    return $matriculas;
  }

  // metodos auxiliares responders


  // TODO usar esta funcao onde é verificado se parecer geral
  protected function parecerGeral() {
    $tiposParecerGeral = array(RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
                               RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL);

    return in_array($this->serviceBoletim()->getRegra()->get('parecerDescritivo'), $tiposParecerGeral);
  }


  protected function setCurrentMatriculaId($matriculaId) {
    $this->_currentMatriculaId = $matriculaId;
  }


  protected function getCurrentMatriculaId() {
    // caso tenha setado _currentMatriculaId, ignora matricula_id recebido nos parametros
    if(! is_null($this->_currentMatriculaId))
      $matriculaId = $this->_currentMatriculaId;
    elseif (! is_null($this->getRequest()->matricula_id))
      $matriculaId = $this->getRequest()->matricula_id;
    else
      throw new CoreExt_Exception("Não foi possivel recuperar o id da matricula atual.");

    return $matriculaId;
  }


  protected function serviceBoletim($reload = false) {
    $matriculaId = $this->getCurrentMatriculaId();

    if (! isset($this->_boletimServiceInstances))
      $this->_boletimServiceInstances = array();

    // set service
    if (! isset($this->_boletimServiceInstances[$matriculaId]) || $reload) {
      try {
        $params = array('matricula' => $matriculaId, 'usuario' => $this->getSession()->id_pessoa);
        $this->_boletimServiceInstances[$matriculaId] = new Avaliacao_Service_Boletim($params);
      }
      catch (Exception $e){
        $this->messenger->append("Erro ao instanciar servi�o boletim para matricula {$matriculaId}: " . $e->getMessage(), 'error', true);
      }
    }

    // validates service
    if (is_null($this->_boletimServiceInstances[$matriculaId]))
      throw new CoreExt_Exception("Não foi possivel instanciar o serviço boletim para a matricula $matriculaId.");

    return $this->_boletimServiceInstances[$matriculaId];
  }


  protected function trySaveServiceBoletim() {
    try {
      $this->serviceBoletim()->save();
    }
    catch (CoreExt_Service_Exception $e) {
      // excecoes ignoradas :( pois servico lanca excecoes de alertas, que não são exatamente erros.
      // error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
    }
  }



  // metodos auxiliares getFalta

  protected function getQuantidadeFalta() {
    $quantidade = (int) $this->getRequest()->att_value;

    if ($quantidade < 0)
      $quantidade = 0;

    return $quantidade;
  }


  protected function getFaltaGeral() {
    return new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => $this->getQuantidadeFalta(),
        'etapa'      => $this->getRequest()->etapa
    ));
  }


  protected function getFaltaComponente() {
    return new Avaliacao_Model_FaltaComponente(array(
            'componenteCurricular' => $this->getRequest()->componente_curricular_id,
            'quantidade'           => $this->getQuantidadeFalta(),
            'etapa'                => $this->getRequest()->etapa,
			'data_falta'		   => $this->getRequest()->data_aula
    ));
  }


  // metodos auxiliares getParecer

  protected function getParecerComponente() {
    return new Avaliacao_Model_ParecerDescritivoComponente(array(
              'componenteCurricular' => $this->getRequest()->componente_curricular_id,
              'parecer'              => $this->safeStringForDb($this->getRequest()->att_value),
              'etapa'                => $this->getRequest()->etapa
    ));
  }


  protected function getParecerGeral() {
    return new Avaliacao_Model_ParecerDescritivoGeral(array(
              'parecer' => $this->safeStringForDb($this->getRequest()->att_value),
              'etapa'   => $this->getRequest()->etapa
    ));
  }


  // metodos auxiliares getSituacaoMatricula

  protected function getSituacaoMatricula($ccId = null) {
    if (is_null($ccId))
      $ccId = $this->getRequest()->componente_curricular_id;

    $situacao = 'Situação não recuperada';

    try {
      $situacaoCc = $this->serviceBoletim()->getSituacaoComponentesCurriculares()->componentesCurriculares[$ccId];
      $situacao   = App_Model_MatriculaSituacao::getInstance()->getValue($situacaoCc->situacao);
    }
    catch (Exception $e) {
      $matriculaId = $this->getRequest()->matricula_id;
      $this->messenger->append("Erro ao recuperar situação da matrícula '$matriculaId': " .
                               $e->getMessage());
    }

    return $this->safeString($situacao);
  }


  // outros metodos auxiliares

  protected function loadComponentesCurricularesForMatricula($matriculaId) {
    $componentesCurriculares  = array();

    $componenteCurricularId   = $this->getRequest()->componente_curricular_id;
    $_componentesCurriculares = App_Model_IedFinder::getComponentesPorMatricula($matriculaId, null, null, $componenteCurricularId);

    foreach($_componentesCurriculares as $_componente) {
      $componente                          = array();

      $componente['id']                    = $_componente->get('id');
      $componente['nome']                  = $this->safeString(strtoupper($_componente->get('nome')), false);
      $componente['nota_atual']            = $this->getNotaAtual($etapa = null, $componente['id']);
      $componente['nota_exame']            = $this->getNotaExame($componente['id']);
      $componente['falta_atual']           = $this->getFaltaAtual($etapa = null, $componente['id']);
      $componente['parecer_atual']         = $this->getParecerAtual($componente['id']);
      $componente['situacao']              = $this->getSituacaoMatricula($componente['id']);
      $componente['nota_necessaria_exame'] = ($componente['situacao'] == 'Em Exame' ? $this->getNotaNecessariaExame($componente['id']) : null );

      if (!empty($componente['nota_necessaria_exame']))
        $this->createOrUpdateNotaExame($matriculaId, $componente['id'], $componente['nota_necessaria_exame']);
      else
        $this->deleteNotaExame($matriculaId, $componente['id']);

      //buscando pela área do conhecimento
      $area                                = $this->getAreaConhecimento($componente['id']);
      $nomeArea                            = (($area->secao != '') ? $area->secao . ' - ' : '') . $area->nome;
      $componente['area_id']               = $area->id;
      $componente['area_nome']             = $this->safeString(strtoupper($nomeArea), false);

      //criando chave para ordenamento temporário
      //área de conhecimento + componente curricular
      $componente['my_order']              = Portabilis_String_Utils::unaccent(strtoupper($nomeArea)) . Portabilis_String_Utils::unaccent(strtoupper($_componente->get('nome')));

      $componentesCurriculares[]           = $componente;
    }

    //ordenando pela chave temporária criada
    $componentesCurriculares = Portabilis_Array_Utils::sortByKey('my_order', $componentesCurriculares);

    //removendo chave temporária
    $len = count($componentesCurriculares);
    for ($i = 0; $i < $len; $i++) {
    	unset($componentesCurriculares[$i]['my_order']);
    }
    return $componentesCurriculares;
  }

  protected function getAreaConhecimento($componenteCurricularId = null) {
  	if (is_null($componenteCurricularId))
  		$componenteCurricularId = $this->getRequest()->componente_curricular_id;

  	if (! is_numeric($componenteCurricularId)) {
  		throw new Exception('Não foi possível obter a área de conhecimento pois não foi recebido o id do componente curricular.');
  	}

  	require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
  	$mapper = new ComponenteCurricular_Model_ComponenteDataMapper();

    $where = array('id' => $componenteCurricularId);

    $area = $mapper->findAll(array('area_conhecimento'), $where);

    $areaConhecimento        = new stdClass();
    $areaConhecimento->id    = $area[0]->area_conhecimento->id;
    $areaConhecimento->nome  = $area[0]->area_conhecimento->nome;
    $areaConhecimento->secao = $area[0]->area_conhecimento->secao;

    return $areaConhecimento;
  }

  protected function createOrUpdateNotaExame($matriculaId, $componenteCurricularId, $notaExame) {

    $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId, $notaExame);

    return ($obj->existe() ? $obj->edita() : $obj->cadastra());
  }

  protected function deleteNotaExame($matriculaId, $componenteCurricularId){
    $obj = new clsModulesNotaExame($matriculaId, $componenteCurricularId);
    return ($obj->excluir());
  }

  protected function getNotaAtual($etapa = null, $componenteCurricularId = null) {
    // defaults
    if (is_null($componenteCurricularId))
      $componenteCurricularId = $this->getRequest()->componente_curricular_id;

    if (is_null($etapa))
      $etapa = $this->getRequest()->etapa;

    // validacao
    if (! is_numeric($componenteCurricularId)) {
      throw new Exception('Não foi possivel obter a nota atual, pois não foi recebido o id do componente curricular.');
    }

    $nota = urldecode($this->serviceBoletim()->getNotaComponente($componenteCurricularId, $etapa)->nota);

    return str_replace(',', '.', $nota);
  }


  protected function getNotaExame($componenteCurricularId = null) {
    // somente recupera nota de exame se estiver buscando as matriculas da ultima etapa
    // se existe nota de exame, esta é recuperada mesmo que a regra de avaliação não use mais exame
    if($this->getRequest()->etapa == $this->serviceBoletim()->getOption('etapas'))
      $nota = $this->getNotaAtual($etapa = 'Rc', $componenteCurricularId);
    else
      $nota = '';

    return $nota;
  }

  protected function getNotaNecessariaExame($componenteCurricularId = null) {
    if (is_null($componenteCurricularId))
      $componenteCurricularId = $this->getRequest()->componente_curricular_id;

    $nota = urldecode($this->serviceBoletim()->preverNotaRecuperacao($componenteCurricularId));

    return str_replace(',', '.', $nota);
  }


  protected function getFaltaAtual($etapa = null, $componenteCurricularId = null) {
    // defaults
    if (is_null($componenteCurricularId))
      $componenteCurricularId = $this->getRequest()->componente_curricular_id;

    if (is_null($etapa))
      $etapa = $this->getRequest()->etapa;

    if ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
      if (! is_numeric($componenteCurricularId))
        throw new Exception('Não foi possivel obter a falta atual, pois não foi recebido o id do componente curricular.');

      $falta = $this->serviceBoletim()->getFalta($etapa, $componenteCurricularId)->quantidade;
    }

    elseif ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
      $falta = $this->serviceBoletim()->getFalta($etapa)->quantidade;

    return $falta;
  }


  protected function getEtapaParecer() {
    if($this->getRequest()->etapa != 'An' && ($this->serviceBoletim()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE || $this->serviceBoletim()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL)) {
      return 'An';
    }
    else
      return $this->getRequest()->etapa;
  }


  protected function getParecerAtual($componenteCurricularId = null) {
    // defaults
    if (is_null($componenteCurricularId))
      $componenteCurricularId = $this->getRequest()->componente_curricular_id;

    $etapaComponente = $this->serviceBoletim()->getRegra()->get('parecerDescritivo') ==
                       RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE;

    $anualComponente = $this->serviceBoletim()->getRegra()->get('parecerDescritivo') ==
                       RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE;

    if ($etapaComponente or $anualComponente) {
      if (! is_numeric($componenteCurricularId))
        throw new Exception('Não foi possivel obter o parecer descritivo atual, pois não foi recebido o id do componente curricular.');

      $parecer =  $this->serviceBoletim()->getParecerDescritivo($this->getEtapaParecer(), $componenteCurricularId)->parecer;
    }
    else
      $parecer = $this->serviceBoletim()->getParecerDescritivo($this->getEtapaParecer())->parecer;

    return $this->safeString($parecer, $transform = false);
  }


  protected function getOpcoesFaltas() {
    $opcoes = array();

    foreach (range(0, 100, 1) as $f)
      $opcoes[$f] = $f;

    return $opcoes;
  }


  protected function canGetOpcoesNotas() {
    return true;
  }


  protected function getOpcoesNotas() {
    $opcoes = array();

    if ($this->canGetOpcoesNotas()) {
      $tpNota  = $this->serviceBoletim()->getRegra()->get('tipoNota');
      $cnsNota = RegraAvaliacao_Model_Nota_TipoValor;

      if ($tpNota != $cnsNota::NENHUM) {
        $tabela = $this->serviceBoletim()->getRegra()->tabelaArredondamento->findTabelaValor();

        foreach ($tabela as $item) {
          if ($tpNota == $cnsNota::NUMERICA) {
            $nota = str_replace(',', '.', (string) $item->nome);
            $opcoes[$nota] = $nota;
          }
          else {
            $nota                   = str_replace(',', '.', (string) $item->valorMaximo);
            $opcoes[$nota] = $this->safeString($item->nome . ' (' . $item->descricao .  ')');
          }
        }
      }
    }

    return $opcoes;
  }


  protected function canGetRegraAvaliacao() {
    return true;
  }


  protected function getRegraAvaliacao() {
    $itensRegra = array();

    if ($this->canGetRegraAvaliacao()) {
      $regra              = $this->serviceBoletim()->getRegra();
      $itensRegra['id']   = $regra->get('id');
      $itensRegra['nome'] = $this->safeString($regra->get('nome'));


      // tipo presença
      $cnsPresenca = RegraAvaliacao_Model_TipoPresenca;
      $tpPresenca  = $this->serviceBoletim()->getRegra()->get('tipoPresenca');

      if($tpPresenca == $cnsPresenca::GERAL)
        $itensRegra['tipo_presenca'] = 'geral';
      elseif($tpPresenca == $cnsPresenca::POR_COMPONENTE)
        $itensRegra['tipo_presenca'] = 'por_componente';
      else
        $itensRegra['tipo_presenca'] = $tpPresenca;


      // tipo nota
      $cnsNota = RegraAvaliacao_Model_Nota_TipoValor;
      $tpNota  = $this->serviceBoletim()->getRegra()->get('tipoNota');

      if ($tpNota == $cnsNota::NENHUM)
        $itensRegra['tipo_nota'] = 'nenhum';
      elseif ($tpNota == $cnsNota::NUMERICA)
        $itensRegra['tipo_nota'] = 'numerica';
      elseif ($tpNota == $cnsNota::CONCEITUAL) {
        $itensRegra['tipo_nota'] = 'conceitual';
        //incluido opcoes notas, pois notas conceituais requer isto para visualizar os nomes
      }
      else
        $itensRegra['tipo_nota'] = $tpNota;


      // tipo parecer
      $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;
      $tpParecer  = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');

      if ($tpParecer == $cnsParecer::NENHUM)
        $itensRegra['tipo_parecer_descritivo'] = 'nenhum';
      elseif ($tpParecer == $cnsParecer::ETAPA_COMPONENTE)
        $itensRegra['tipo_parecer_descritivo'] = 'etapa_componente';
      elseif ($tpParecer == $cnsParecer::ETAPA_GERAL)
        $itensRegra['tipo_parecer_descritivo'] = 'etapa_geral';
      elseif ($tpParecer == $cnsParecer::ANUAL_COMPONENTE)
        $itensRegra['tipo_parecer_descritivo'] = 'anual_componente';
      elseif ($tpParecer == $cnsParecer::ANUAL_GERAL)
        $itensRegra['tipo_parecer_descritivo'] = 'anual_geral';
      else
        $itensRegra['tipo_parecer_descritivo'] = $tpParecer;

      // opcoes notas
      $itensRegra['opcoes_notas']      = $this->getOpcoesNotas();

      // etapas
      $itensRegra['quantidade_etapas'] = $this->serviceBoletim()->getOption('etapas');
    }

    $itensRegra['nomenclatura_exame'] = ($GLOBALS['coreExt']['Config']->app->diario->nomenclatura_exame == 0 ? 'exame' : 'conselho');

    return $itensRegra;
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'matriculas'))
      $this->appendResponse('matriculas', $this->getMatriculas());

    elseif ($this->isRequestFor('post', 'nota') || $this->isRequestFor('post', 'nota_exame'))
      $this->postNota();

    elseif ($this->isRequestFor('post', 'falta'))
      $this->postFalta();

    elseif ($this->isRequestFor('post', 'parecer'))
      $this->postParecer();

    elseif ($this->isRequestFor('delete', 'nota') || $this->isRequestFor('delete', 'nota_exame'))
        $this->deleteNota();

    elseif ($this->isRequestFor('delete', 'falta'))
        $this->deleteFalta();

    elseif ($this->isRequestFor('delete', 'parecer'))
        $this->deleteParecer();

    else
      $this->notImplementedOperationError();
  }
}
