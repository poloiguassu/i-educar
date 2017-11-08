<?php

/*
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
 * Este programa � distribu�do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl�cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 */

/**
 * @author   Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo dispon�vel desde a vers�o 1.0.0
 * @version  $Id$
 */

// Inclui opera��es de bootstrap.
require_once '../includes/bootstrap.php';


require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';
require_once 'include/pmieducar/clsPmieducarAlunoBeneficio.inc.php';
require_once 'include/pmieducar/clsPmieducarArredondamento.inc.php';
require_once 'include/pmieducar/clsPmieducarAvaliacao.inc.php';
require_once 'include/pmieducar/clsPmieducarAvaliacaoDesempenho.inc.php';
require_once 'include/pmieducar/clsPmieducarCalendarioAnoLetivo.inc.php';
require_once 'include/pmieducar/clsPmieducarCalendarioAtividade.inc.php';
require_once 'include/pmieducar/clsPmieducarCalendarioDia.inc.php';
require_once 'include/pmieducar/clsPmieducarCalendarioDiaMotivo.inc.php';
require_once 'include/pmieducar/clsPmieducarCoffebreakTipo.inc.php';
require_once 'include/pmieducar/clsPmieducarCurso.inc.php';
require_once 'include/pmieducar/clsPmieducarDisciplina.inc.php';
require_once 'include/pmieducar/clsPmieducarDisciplinaDisciplinaTopico.inc.php';
require_once 'include/pmieducar/clsPmieducarDisciplinaSerie.inc.php';
require_once 'include/pmieducar/clsPmieducarDisciplinaTopico.inc.php';
require_once 'include/pmieducar/clsPmieducarDispensaDisciplina.inc.php';
require_once 'include/pmieducar/clsPmieducarDocumentos.inc.php';
require_once 'include/pmieducar/clsPmieducarEndereco.inc.php';
require_once 'include/pmieducar/clsPmieducarEnderecoExterno.inc.php';
require_once 'include/pmieducar/clsPmieducarEscola.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaComplemento.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaCurso.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaDiasLetivos.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaLocalizacao.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaRedeEnsino.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaSerie.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaSerieDisciplina.inc.php';
require_once 'include/pmieducar/clsPmieducarFaltaAluno.inc.php';
require_once 'include/pmieducar/clsPmieducarFaltaAtraso.inc.php';
require_once 'include/pmieducar/clsPmieducarFaltaAtrasoCompensado.inc.php';
require_once 'include/pmieducar/clsPmieducarFuncao.inc.php';
require_once 'include/pmieducar/clsPmieducarHabilitacao.inc.php';
require_once 'include/pmieducar/clsPmieducarHabilitacaoCurso.inc.php';
require_once 'include/pmieducar/clsPmieducarHistoricoDisciplinas.inc.php';
require_once 'include/pmieducar/clsPmieducarHistoricoEscolar.inc.php';
require_once 'include/pmieducar/clsPmieducarInfraComodoFuncao.inc.php';
require_once 'include/pmieducar/clsPmieducarInfraPredio.inc.php';
require_once 'include/pmieducar/clsPmieducarInfraPredioComodo.inc.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';
require_once 'include/pmieducar/clsPmieducarMaterialDidatico.inc.php';
require_once 'include/pmieducar/clsPmieducarMaterialTipo.inc.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
require_once 'include/pmieducar/clsPmieducarMatriculaExcessao.inc.php';
require_once 'include/pmieducar/clsPmieducarMatriculaOcorrenciaDisciplinar.inc.php';
require_once 'include/pmieducar/clsPmieducarMatriculaTurma.inc.php';
require_once 'include/pmieducar/clsPmieducarMenuTipoUsuario.inc.php';
require_once 'include/pmieducar/clsPmieducarMotivoAfastamento.inc.php';
require_once 'include/pmieducar/clsPmieducarNivelEnsino.inc.php';
require_once 'include/pmieducar/clsPmieducarNotaAluno.inc.php';
require_once 'include/pmieducar/clsPmieducarOperador.inc.php';
require_once 'include/pmieducar/clsPmieducarPessoaEduc.inc.php';
require_once 'include/pmieducar/clsPmieducarPreRequisito.inc.php';
require_once 'include/pmieducar/clsPmieducarQuadroHorario.inc.php';
require_once 'include/pmieducar/clsPmieducarQuadroHorarioHorarios.inc.php';
require_once 'include/pmieducar/clsPmieducarReligiao.inc.php';
require_once 'include/pmieducar/clsPmieducarReservaVaga.inc.php';
require_once 'include/pmieducar/clsPmieducarSequenciaCurso.inc.php';
require_once 'include/pmieducar/clsPmieducarSequenciaSerie.inc.php';
require_once 'include/pmieducar/clsPmieducarSerie.inc.php';
require_once 'include/pmieducar/clsPmieducarSerieDiaSemana.inc.php';
require_once 'include/pmieducar/clsPmieducarSeriePeriodoData.inc.php';
require_once 'include/pmieducar/clsPmieducarSeriePreRequisito.inc.php';
require_once 'include/pmieducar/clsPmieducarServidor.inc.php';
require_once 'include/pmieducar/clsPmieducarServidorAfastamento.inc.php';
require_once 'include/pmieducar/clsPmieducarServidorAlocacao.inc.php';
require_once 'include/pmieducar/clsPmieducarServidorCurso.inc.php';
require_once 'include/pmieducar/clsPmieducarServidorFormacao.inc.php';
require_once 'include/pmieducar/clsPmieducarServidorTituloConcurso.inc.php';
require_once 'include/pmieducar/clsPmieducarTipoAvaliacao.inc.php';
require_once 'include/pmieducar/clsPmieducarTipoAvaliacaoValores.inc.php';
require_once 'include/pmieducar/clsPmieducarTipoDispensa.inc.php';
require_once 'include/pmieducar/clsPmieducarTipoEnsino.inc.php';
require_once 'include/pmieducar/clsPmieducarTipoOcorrenciaDisciplinar.inc.php';
require_once 'include/pmieducar/clsPmieducarTipoRegime.inc.php';
require_once 'include/pmieducar/clsPmieducarTipoUsuario.inc.php';
require_once 'include/pmieducar/clsPmieducarTransferenciaSolicitacao.inc.php';
require_once 'include/pmieducar/clsPmieducarTransferenciaTipo.inc.php';
require_once 'include/pmieducar/clsPmieducarTurma.inc.php';
require_once 'include/pmieducar/clsPmieducarTurmaTipo.inc.php';
require_once 'include/pmieducar/clsPmieducarUsuario.inc.php';
require_once 'include/pmieducar/clsPmieducarPessoaEducDeficiencia.inc.php';
require_once 'include/pmieducar/clsPmieducarTelefone.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaAnoLetivo.inc.php';
require_once 'include/pmieducar/clsPmieducarModulo.inc.php';
require_once 'include/pmieducar/clsPmieducarAnoLetivoModulo.inc.php';
require_once 'include/pmieducar/clsPmieducarCalendarioAnotacao.inc.php';
require_once 'include/pmieducar/clsPmieducarCalendarioDiaAnotacao.inc.php';
require_once 'include/pmieducar/clsPmieducarTurmaModulo.inc.php';
require_once 'include/pmieducar/clsPmieducarTurmaDiaSemana.inc.php';
require_once 'include/pmieducar/clsPmieducarFaltas.inc.php';
require_once 'include/pmieducar/clsPmieducarQuadroHorarioHorariosAux.inc.php';
require_once 'include/pmieducar/clsPmieducarServidorFuncao.inc.php';
require_once 'include/pmieducar/clsPmieducarServidorDisciplina.inc.php';
require_once 'include/pmieducar/clsPmieducarCategoriaNivel.inc.php';
require_once 'include/pmieducar/clsPmieducarNivel.inc.php';
require_once 'include/pmieducar/clsPmieducarSubnivel.inc.php';
require_once 'include/pmieducar/clsPmieducarServidorCursoMinistra.inc.php';

//VPS
require_once 'include/pmieducar/clsPmieducarVPSJornadaTrabalho.inc.php';
require_once 'include/pmieducar/clsPmieducarVPSFuncao.inc.php';
require_once 'include/pmieducar/clsPmieducarVPSIdioma.inc.php';
require_once 'include/pmieducar/clsPmieducarVPSResponsavelEntrevista.inc.php';
require_once 'include/pmieducar/clsPmieducarVPSContratacaoTipo.inc.php';
require_once 'include/pmieducar/clsPmieducarVPSEntrevista.inc.php';
require_once 'include/pmieducar/clsPmieducarVPSEntrevistaResponsavel.inc.php';
require_once 'include/pmieducar/clsPmieducarVPSAlunoEntrevista.inc.php';
require_once 'include/pmieducar/clsPmieducarVPSVisita.inc.php';
require_once 'include/pmieducar/clsPmieducarAlunoVPS.inc.php';

//Biblioteca
require_once 'include/pmieducar/clsPmieducarBiblioteca.inc.php';
require_once 'include/pmieducar/clsPmieducarClienteTipo.inc.php';
require_once 'include/pmieducar/clsPmieducarAcervoEditora.inc.php';
require_once 'include/pmieducar/clsPmieducarMotivoBaixa.inc.php';
require_once 'include/pmieducar/clsPmieducarSituacao.inc.php';
require_once 'include/pmieducar/clsPmieducarCliente.inc.php';
require_once 'include/pmieducar/clsPmieducarExemplar.inc.php';
require_once 'include/pmieducar/clsPmieducarMotivoSuspensao.inc.php';
require_once 'include/pmieducar/clsPmieducarFonte.inc.php';
require_once 'include/pmieducar/clsPmieducarReservas.inc.php';
require_once 'include/pmieducar/clsPmieducarExemplarEmprestimo.inc.php';
require_once 'include/pmieducar/clsPmieducarClienteTipoExemplarTipo.inc.php';
require_once 'include/pmieducar/clsPmieducarClienteTipoCliente.inc.php';
require_once 'include/pmieducar/clsPmieducarPagamentoMulta.inc.php';
require_once 'include/pmieducar/clsPmieducarBibliotecaUsuario.inc.php';
require_once 'include/pmieducar/clsPmieducarClienteSuspensao.inc.php';
require_once 'include/pmieducar/clsPmieducarBibliotecaDia.inc.php';
require_once 'include/pmieducar/clsPmieducarBibliotecaFeriados.inc.php';

require_once 'include/pmieducar/clsPmieducarAlunoCmf.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';
