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
 * @subpackage  ReservaVaga
 * @since       Arquivo dispon�vel desde a vers�o 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
  function Formular() {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Reserva Vaga');
    $this->processoAp = '639';
    $this->addEstilo("localizacaoSistema");
  }
}

class indice extends clsCadastro
{
  /**
   * Refer�ncia a usu�rio da sess�o
   * @var int
   */
  var $pessoa_logada = NULL;

  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_cod_aluno;
  var $nm_aluno;
  var $nm_aluno_;

  var $ref_cod_instituicao;
  var $ref_cod_curso;

  var $passo;

  var $nm_aluno_ext;
  var $cpf_responsavel;
  var $tipo_aluno;

  function Inicializar()
  {
    $retorno = 'Novo';
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->ref_cod_serie  = $_GET['ref_cod_serie'];
    $this->ref_cod_escola = $_GET['ref_cod_escola'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(639, $this->pessoa_logada, 7,
      'educar_reserva_vaga_lst.php');

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "{$nomeMenu} reserva de vaga"             
    ));
    $this->enviaLocalizacao($localizacao->montar());    

    return $retorno;
  }

  function Gerar()
  {
    if ($this->ref_cod_aluno) {
      $obj_reserva_vaga = new clsPmieducarReservaVaga();
      $lst_reserva_vaga = $obj_reserva_vaga->lista(NULL, NULL, NULL, NULL, NULL,
        $this->ref_cod_aluno, NULL, NULL, NULL, NULL, 1);

      // Verifica se o aluno j� possui reserva alguma reserva ativa no sistema
      if (is_array($lst_reserva_vaga)) {
        echo "
          <script type='text/javascript'>
            alert('Aluno j� possui reserva de vaga!\\nN�o � possivel realizar a reserva.');
            window.location = 'educar_reserva_vaga_lst.php';
          </script>";
        die();
      }

      echo "
        <script type='text/javascript'>
          alert('A reserva do aluno permanecer� ativa por apenas 2 dias!');
        </script>";
    }

    $this->campoOculto('ref_cod_serie', $this->ref_cod_serie);
    $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);

    $this->nm_aluno = $this->nm_aluno_;

    $this->campoTexto('nm_aluno', 'Aluno', $this->nm_aluno, 30, 255, FALSE,
      FALSE, FALSE, '', "<img border=\"0\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupa.png\"\/><span style='padding-left:20px;'><input type='button' value='Aluno externo' onclick='showAlunoExt(true);' class='botaolistagem'></span>",
      '', '', TRUE);

    $this->campoOculto('nm_aluno_', $this->nm_aluno_);
    $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);

    $this->campoOculto('tipo_aluno', 'i');

    $this->campoTexto('nm_aluno_ext', 'Nome aluno', $this->nm_aluno_ext, 50, 255, FALSE);
    $this->campoCpf('cpf_responsavel', 'CPF respons&aacute;vel',
      $this->cpf_responsavel, FALSE, "<span style='padding-left:20px;'><input type='button' value='Aluno interno' onclick='showAlunoExt(false);' class='botaolistagem'></span>");

    $this->campoOculto('passo', 1);

    $this->acao_enviar = 'acao2()';

    $this->url_cancelar = 'educar_reserva_vaga_lst.php';
    $this->nome_url_cancelar = 'Cancelar';
  }

  function Novo()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    if ($this->passo == 2) {
      return true;
    }

    $obj_reserva_vaga = new clsPmieducarReservaVaga(NULL, $this->ref_cod_escola,
      $this->ref_cod_serie, NULL, $this->pessoa_logada, $this->ref_cod_aluno, NULL,
      NULL, 1, $this->nm_aluno_ext, idFederal2int($this->cpf_responsavel));

    $cadastrou = $obj_reserva_vaga->cadastra();

    if ($cadastrou) {
      $this->mensagem .= 'Reserva de Vaga efetuada com sucesso.<br>';
      header('Location: educar_reservada_vaga_det.php?cod_reserva_vaga=' . $cadastrou);
      die();
    }

    $this->mensagem = 'Reserva de Vaga n&atilde;o realizada.<br>';
    return FALSE;
  }
}

// Instancia objeto de p�gina
$pagina = new clsIndexBase();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do �� p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
?>

<script type='text/javascript'>
function pesquisa_aluno() {
  pesquisa_valores_popless('educar_pesquisa_aluno.php')
}

function showAlunoExt(acao) {
  setVisibility('tr_nm_aluno_ext',acao);
  setVisibility('tr_cpf_responsavel',acao);
  setVisibility('tr_nm_aluno',!acao);

  document.getElementById('nm_aluno_ext').disabled = !acao;
  document.getElementById('cpf_responsavel').disabled = !acao;

  document.getElementById('tipo_aluno').value = (acao == true ? 'e' : 'i');
}

setVisibility('tr_nm_aluno_ext', false);
setVisibility('tr_cpf_responsavel', false);

function acao2() {
  if (document.getElementById('tipo_aluno').value == 'e') {
    if (document.getElementById('nm_aluno_ext').value == '') {
      alert('Preencha o campo \'Nome aluno\' Corretamente');
      return false;
    }

    if (! (/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/.test(document.formcadastro.cpf_responsavel.value))) {
      alert('Preencha o campo \'CPF respons�vel\' Corretamente');
      return false;
    }
    else {
      if(! DvCpfOk( document.formcadastro.cpf_responsavel) )
        return false;
    }

    document.getElementById('nm_aluno_').value = '';
    document.getElementById('ref_cod_aluno').value = '';

    document.formcadastro.submit();
  }
  else {
    document.getElementById('nm_aluno_ext').value = '';
    document.getElementById('cpf_responsavel').value =  '';
  }
  acao();
}
</script>