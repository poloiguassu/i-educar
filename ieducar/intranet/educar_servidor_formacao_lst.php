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
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

require_once 'CoreExt/View/Helper/UrlHelper.php';

/**
 * clsIndexBase class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor Forma��o');
    $this->processoAp = 635;
  }
}

/**
 * indice class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsListagem
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;

  var $cod_formacao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_servidor;
  var $nm_formacao;
  var $tipo;
  var $descricao;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_cod_instituicao;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->ref_cod_servidor    = $_GET['ref_cod_servidor'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $this->titulo = 'Servidor Formacao - Listagem';

    // passa todos os valores obtidos no GET para atributos do objeto
    foreach( $_GET AS $var => $val ) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    

    $this->addCabecalhos(array(
      'Nome Forma��o',
      'Tipo'
    ));

    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
    $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);

    // Filtros
    $this->campoTexto('nm_formacao', 'Nome da Forma��o', $this->nm_formacao,
      30, 255, FALSE);

    $opcoes = array(
      ''  => 'Selecione',
      'C' => 'Cursos',
      'T' => 'T�tulos',
      'O' => 'Concursos'
    );

    $this->campoLista('tipo', 'Tipo de Forma��o', $opcoes, $this->tipo);

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

    $obj_servidor_formacao = new clsPmieducarServidorFormacao();
    $obj_servidor_formacao->setOrderby('nm_formacao ASC');
    $obj_servidor_formacao->setLimite($this->limite, $this->offset);

    if (! isset($this->tipo)) {
      $this->tipo = NULL;
    }

    $lista = $obj_servidor_formacao->lista(
      NULL,
      NULL,
      NULL,
      $this->ref_cod_servidor,
      $this->nm_formacao,
      $this->tipo,
      NULL,
      NULL,
      NULL,
      1
    );

    $total = $obj_servidor_formacao->_total;

    // UrlHelper
    $url  = CoreExt_View_Helper_UrlHelper::getInstance();
    $path = 'educar_servidor_formacao_det.php';

    // Monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        // Pega detalhes de foreign_keys
        if (class_exists('clsPmieducarUsuario')) {
          $obj_ref_usuario_exc = new clsPmieducarUsuario($registro['ref_usuario_exc']);
          $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();

          $registro['ref_usuario_exc'] = $det_ref_usuario_exc['data_cadastro'];
        }
        else {
          $registro['ref_usuario_exc'] = 'Erro na geracao';
        }

        if (class_exists('clsPmieducarServidor')) {
          $obj_ref_cod_servidor = new clsPmieducarServidor($registro['ref_cod_servidor']);
          $det_ref_cod_servidor = $obj_ref_cod_servidor->detalhe();

          $registro['ref_cod_servidor'] = $det_ref_cod_servidor['cod_servidor'];
        }
        else {
          $registro['ref_cod_servidor'] = 'Erro na geracao';
        }

        if ($registro['tipo'] == 'C') {
          $registro['tipo'] = 'Projeto';
        }
        elseif ($registro['tipo'] == 'T') {
          $registro['tipo'] = 'T�tulo';
        }
        else {
          $registro['tipo'] = 'Concurso';
        }

        $options = array(
          'query' => array(
            'cod_formacao' => $registro['cod_formacao']
        ));

        $this->addLinhas(array(
          $url->l($registro['nm_formacao'], $path, $options),
          $url->l($registro['tipo'], $path, $options)
        ));

        $this->tipo = '';
      }
    }

    $this->addPaginador2('educar_servidor_formacao_lst.php', $total, $_GET, $this->nome, $this->limite);
    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
      $this->array_botao[]     = 'Novo';
      $this->array_botao_url[] = sprintf(
        'educar_servidor_formacao_cad.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_instituicao
      );
    }

    $this->array_botao[]     = 'Voltar';
    $this->array_botao_url[] = sprintf(
      'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_cod_instituicao
    );

    $this->largura = '100%';
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