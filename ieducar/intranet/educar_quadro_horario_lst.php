<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'include/localizacaoSistema.php';
require_once 'include/pmieducar/clsPmieducarEscolaUsuario.inc.php';
require_once 'Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . 'Quadro de Horário');
        $this->processoAp = '641';
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsListagem
{
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;

    public $cod_calendario_ano_letivo;
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_serie;
    public $ref_cod_turma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ano;
    public $data_cadastra;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;
    public $busca;

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('cronograma');
    }

    public function Gerar()
    {
        @session_start();
            $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $lista_busca = array(
            "Cronograma",
        );

        $this->addCabecalhos($lista_busca);


        $this->largura = '100%';

        $this->titulo = 'Turma - Quadro Horario';

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->nivel_acesso($this->pessoa_logada) > 7) {
            $retorno .= '
                <table width="100%" height="40%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
                    <tbody>
                    <tr>
                        <td colspan="2" valig="center" height="50">
                        <center class="formdktd">Usuário sem permissão para acessar esta página</center>
                        </td>
                    </tr>
                    </tbody>
                </table>';

            return $retorno;
        }

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos(
            [
                $_SERVER['SERVER_NAME'].'/intranet' => 'Início',
                'educar_servidores_index.php'       => 'Servidores',
                ''                                  => 'Quadros de horários'
            ]
        );

        $this->locale = $localizacao->montar();

        $retorno .= '
            <table width="100%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
            <tbody>';

        if ($_POST) {
            $this->ref_cod_turma       = $_POST['ref_cod_turma'] ? $_POST['ref_cod_turma'] : null;
            $this->ref_cod_serie       = $_POST['ref_cod_serie'] ? $_POST['ref_cod_serie'] : null;
            $this->ref_cod_curso       = $_POST['ref_cod_curso'] ? $_POST['ref_cod_curso'] : null;
            $this->ref_cod_escola      = $_POST['ref_cod_escola'] ? $_POST['ref_cod_escola'] : null;
            $this->ref_cod_instituicao = $_POST['ref_cod_instituicao'] ? $_POST['ref_cod_instituicao'] : null;
            $this->etapa               = $_POST['etapa'] ? $_POST['etapa'] : null;
            $this->ano                 = $_POST['ano'] ? $_POST['ano'] : null;
            $this->busca               = $_GET['busca'] ? $_GET['busca'] : null;
        } else {
            if ($_GET) {
                // Passa todos os valores obtidos no GET para atributos do objeto
                foreach ($_GET as $var => $val) {
                    $this->$var = $val === '' ? null : $val;
                }
            }
        }

        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if (!$this->ref_cod_escola) {
            $this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);
        }

        if (!is_numeric($this->ref_cod_instituicao)) {
            $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
        }

        // Componente curricular
        $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

        //include 'educar_quadro_horarios_pesquisas.php';

        $this->inputsHelper()->input('ano', 'ano');
        $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'curso', 'serie', 'turma'));

        $this->inputsHelper()->dynamic(
            'etapa',
            array(
                'required' => true,
                'value' => $this->etapa
            )
        );

        if ($this->busca == 'S') {
            if (is_numeric($this->ref_cod_turma)) {
                $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
                $det_turma = $obj_turma->detalhe();

                $obj_quadro = new clsPmieducarQuadroHorario(
                    null,
                    null,
                    null,
                    $this->ref_cod_turma,
                    null,
                    null,
                    1
                );
                $det_quadro = $obj_quadro->detalhe();

                if (is_array($det_quadro)) {
                    $quadro_horario = "<table class='calendar' cellspacing='0' cellpadding='0' border='0'>
                            <tr>
                              <td class='cal_esq_qh' width='40px'><i class='fa fa-calendar' aria-hidden='true'></i></td>
                              <td width='100%' class='mes'>Turma: {$det_turma['nm_turma']}</td>
                              <td align='right' class='cal_dir'>&nbsp;</td>
                              </tr>
                            <tr>
                              <td colspan='3'  align='center'>
                                <table width='100%' cellspacing='2' cellpadding='0'  border='0' >
                                  <tr class='header'>
                                    <td style='width: 100px;'>SEG</td>
                                    <td style='width: 100px;'>TER</td>
                                    <td style='width: 100px;'>QUA</td>
                                    <td style='width: 100px;'>QUI</td>
                                    <td style='width: 100px;'>SEX</td>
                                    <td style='width: 100px;'>SAB</td>
                                  </tr>";
                    $texto = '<tr>';

                    $obj_modulo = new clsPmieducarTurmaModulo($this->ref_cod_turma, null, $this->etapa);
                    $det_modulo = $obj_modulo->detalhe();

                    $begin = new DateTime($det_modulo['data_inicio']);
                    $end = new DateTime($det_modulo['data_fim']);
                    
                    $interval = new DateInterval('P1D');
                    $daterange = new DatePeriod($begin, $interval ,$end);

                    $obj_horarios = new clsPmieducarQuadroHorarioHorarios();
                    $resultado    = $obj_horarios->retornaHorario(
                        $this->ref_cod_instituicao,
                        $this->ref_cod_escola,
                        $this->ref_cod_serie,
                        $this->ref_cod_turma,
                        null
                    );

                    $texto .= '<tr>';

                    $primeiroDia = $daterange->getStartDate()->format('N');

                    for ($i = 1; $i < $primeiroDia; $i++) {
                        $texto .= '<td class="horario" style="background: #47728f; color: #fff">Fora da grade</td>';
                    }

                    if (is_array($resultado)
                        && $daterange != null
                    ) {
                        foreach ($daterange as $date) {

                            if ($date->format('N') == 7) {
                                continue;
                            }

                            $dia_semana = $date->format('N') + 1;

                            $texto .= "<td valign=top align='center' width='100' style='cursor: pointer;'>";
                            $texto .= "<div onclick='envia( this, {$this->ref_cod_turma}, {$this->ref_cod_serie}, {$this->ref_cod_curso}, {$this->ref_cod_escola}, {$this->ref_cod_instituicao}, {$det_quadro['cod_quadro_horario']}, {$dia_semana}, {$this->ano} );'>";
                            $texto .= sprintf(
                                '<div class="horario" style="background: #47728f; color: #fff">%s</div>',
                                $date->format("d/m/Y")
                            );

                            $data_aula = $date->format("Y-m-d");

                            if ($resultado[$data_aula] != null) {
                                foreach($resultado[$data_aula] as $registro) { 
                                    $componente = $componenteMapper->find($registro['ref_cod_disciplina']);
    
                                    // Servidor
                                    $obj_servidor = new clsPmieducarServidor();
    
                                    if ($registro['ref_servidor_substituto']) {
                                        $det_servidor =  array_shift(
                                            $obj_servidor->lista(
                                                $registro['ref_servidor_substituto'],
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                true
                                            )
                                        );
                                    } else {
                                        $det_servidor = array_shift(
                                            $obj_servidor->lista(
                                                $registro['ref_servidor'],
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                true
                                            )
                                        );
                                    }
    
                                    $det_servidor['nome'] = array_shift(explode(' ', $det_servidor['nome']));
    
                                    //$texto .= "<div  style='text-align: center;background-color: #F6F6F6;font-size: 11px; width: 100px; margin: 3px; border: 1px solid #CCCCCC; padding:5px; '>". substr($registro['hora_inicial'], 0, 5) . ' - ' . substr($registro['hora_final'], 0, 5) . " <br> {$componente->abreviatura} <br> {$det_servidor["nome"]}</div>";
                                    $detalhes = sprintf(
                                        '%s - %s<br />%s<br />%s',
                                        substr($registro['hora_inicial'], 0, 5),
                                        substr($registro['hora_final'], 0, 5),
                                        $componente->abreviatura,
                                        $det_servidor['nome']
                                    );
    
                                    if (!empty($registro['cor'])) {
                                        $cor = "background: #{$registro['cor']}; color: #fff;";
                                    }
    
                                    $texto .= sprintf(
                                        '<div class="horario" style="min-height: 51px; %s">%s</div>',
                                        $cor,
                                        $detalhes
                                    );
                                }
                            } else {
                                $texto .= 
                                    '<div class="horario" style="min-height: 51px;">
                                        Sem Aula
                                    </div>';
                            }

                            $texto .= "</div></td>";

                            if ($date->format('N') == 6) {
                                $texto .= '</tr><tr>';
                            }

                            /*$texto .= "<td valign=top align='center' width='100' style='cursor: pointer; ' onclick='envia( this, {$this->ref_cod_turma}, {$this->ref_cod_serie}, {$this->ref_cod_curso}, {$this->ref_cod_escola}, {$this->ref_cod_instituicao}, {$det_quadro['cod_quadro_horario']}, {$c}, {$this->ano} );'>";
                            $texto .= '<div>';*/

                            $texto .= '</div>';
                            $texto .= '</td>';
                        }
                    } else {
                        $texto .= '<div  class=\'horario\' style=\'background: #47728f; color: #fff; min-height: 17px;\'><i class=\'fa fa-plus-square\' aria-hidden=\'true\'></i></div>';
                    }

                    $texto .= '<tr><td colspan="7">&nbsp;</td></tr>';
                    $quadro_horario .= $texto;

                    $quadro_horario .= '</table></td></tr></table>';
                    $retorno .= "<tr><td colspan='2' ><center><b></b>{$quadro_horario}</center></td></tr>";
                } else {
                    $retorno .= '<tr><td colspan=\'2\' ><b><center>N&atilde;o existe nenhum quadro de hor&aacute;rio cadastrado para esta turma.</center></b></td></tr>';
                }
            }
        }

        if ($obj_permissoes->permissao_cadastra(641, $this->pessoa_logada, 7)) {
            $retorno .= '<tr><td>&nbsp;</td></tr><tr>
            <td align="center" colspan="2">';

            if (!$det_quadro) {
                $retorno .= "<input type=\"button\" value=\"Novo Quadro de Hor&aacute;rios\" onclick=\"window.location='educar_quadro_horario_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}'\" class=\"botaolistagem\"/>";
            } else {
                if ($obj_permissoes->permissao_excluir(641, $this->pessoa_logada, 7)) {
                    $retorno .= "<input type=\"button\" value=\"Excluir Quadro de Hor&aacute;rios\" onclick=\"window.location='educar_quadro_horario_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}&ref_cod_quadro_horario={$det_quadro['cod_quadro_horario']}'\" class=\"botaolistagem btn-green\"/>";
                }
            }

            $retorno .= '</td>
            </tr>';
        }

        $retorno .='</tbody>
            </table>';

        $this->addLinhas($retorno);
    }

    public function organizarHorariosIguais($valores)
    {
        $x = 1;
        $quantidadeElementos = count($valores);
        while ($x < $quantidadeElementos) {
            $mesmoHorario = (($valores[0]['hora_inicial'] == $valores[$x]['hora_inicial']) &&
                         ($valores[0]['hora_final'] == $valores[$x]['hora_final']));

            if ($mesmoHorario) {
                unset($valores[$x]);
                $valores[0]['ref_cod_disciplina'] = 0;
            }
            $x++;
        }

        return $valores;
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();