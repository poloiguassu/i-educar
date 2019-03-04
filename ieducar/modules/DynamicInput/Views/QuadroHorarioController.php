<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Business/Professor.php';

class QuadroHorarioController extends ApiCoreController
{
    protected function canGetQuadroHorarios()
    {
        return $this->validatesId('instituicao') &&
           $this->validatesId('curso') &&
           $this->validatesId('escola') &&
           $this->validatesId('turma');
        //$this->validatesId('componente_curricular');
    }

    protected function getQuadroHorarios()
    {
        if ($this->canGetQuadroHorarios()) {
            $userId               = $this->getSession()->id_pessoa;
            $instituicaoId        = $this->getRequest()->instituicao_id;
            $escolaId             = $this->getRequest()->escola_id;
            $cursoId              = $this->getRequest()->curso_id;
            $turmaId              = $this->getRequest()->turma_id;
            $componente_curricular = $this->getRequest()->componente_curricular;

           // $isProfessor          = Portabilis_Business_Professor::isProfessor($instituicaoId, $userId);
            //$canLoadSeriesAlocado = Portabilis_Business_Professor::canLoadSeriesAlocado($instituicaoId);

            /*if ($isProfessor && $canLoadSeriesAlocado) {
                $resources = Portabilis_Business_Professor::seriesAlocado($instituicaoId, $escolaId, $cursoId, $userId);
                $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'id', 'nome');
            } elseif ($escolaId && $cursoId && empty($resources)) {
                $resources = App_Model_IedFinder::getSeries($instituicaoId = null, $escolaId, $cursoId);
            }*/

            $sql = "SELECT 
                        hh.ref_cod_quadro_horario,
                        hh.cod_quadro_horario_horarios,
                        hh.data_aula,
                        hh.hora_inicial,
                        hh.hora_final,
                        hh.ref_cod_disciplina,
                        qh.cod_quadro_horario,
                        qh.ref_cod_turma
                    FROM
                        pmieducar.quadro_horario_horarios as hh,
                        pmieducar.quadro_horario as qh
                    WHERE
                        hh.ref_cod_quadro_horario = qh.cod_quadro_horario
                    AND
                        hh.ref_cod_quadro_horario = (
                            SELECT 
                                cod_quadro_horario
                            FROM 
                                pmieducar.quadro_horario 
                            WHERE
                                ref_cod_turma = $1
                        )
                    AND
                        hh.ref_cod_disciplina = $2
                    ORDER BY
                        data_aula,
                        hora_inicial ASC;";

            $aulas = $this->fetchPreparedQuery($sql, array($turmaId, $componente_curricular));

            $options = [];

            foreach ($aulas as $aula) {
                $options['__' . $aula['cod_quadro_horario_horarios']] = dataFromPgToBr($aula['data_aula']) . ' - ' . $aula['hora_inicial'] . ' às ' . $aula['hora_final'];
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'quadroHorarios')) {
            $this->appendResponse($this->getQuadroHorarios());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
