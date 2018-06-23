<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Ied_Cadastro
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';

/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo('Processo Seletivo - Jovem');
		$this->processoAp = 43;
		$this->addEstilo('localizacaoSistema');
	}
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  function Gerar()
  {
    $this->titulo = 'Detalhe da Jovem - Processo Seletivo';

    $this->addBanner('imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $cod_pessoa = @$_GET['cod_pessoa'];

    $objPessoa = new clsPessoaFisica($cod_pessoa);
    $db        = new clsBanco();

    $detalhe = $objPessoa->queryRapida(
      $cod_pessoa, 'idpes', 'complemento','nome', 'cpf', 'data_nasc',
      'logradouro', 'idtlog', 'numero', 'apartamento','cidade','sigla_uf',
      'cep', 'ddd_1', 'fone_1', 'ddd_2', 'fone_2', 'ddd_mov', 'fone_mov',
      'ddd_fax', 'fone_fax', 'email', 'url', 'tipo', 'sexo', 'zona_localizacao'
    );

    $objFoto = new clsCadastroFisicaFoto($cod_pessoa);
    $caminhoFoto = $objFoto->detalhe();
    if ($caminhoFoto!=false)
      $this->addDetalhe(array('Nome', $detalhe['nome'].'
                                  <p><img height="117" src="'.$caminhoFoto['caminho'].'"/></p>'));
    else
      $this->addDetalhe(array('Nome', $detalhe['nome']));

    $this->addDetalhe(array('CPF', int2cpf($detalhe['cpf'])));

    if ($detalhe['data_nasc']) {
      $this->addDetalhe(array('Data de Nascimento', dataFromPgToBr($detalhe['data_nasc'])));
    }

    if ($detalhe['logradouro']) {
      if ($detalhe['numero']) {
        $end = ' nº ' . $detalhe['numero'];
      }

      if ($detalhe['apartamento']) {
        $end .= ' apto ' . $detalhe['apartamento'];
      }

      $this->addDetalhe(array('Endereço',
        strtolower($detalhe['idtlog']) . ': ' . $detalhe['logradouro'] . ' ' . $end)
      );
    }

    if ($detalhe['complemento']) {
      $this->addDetalhe(array('Complemento', $detalhe['complemento']));
    }

    if ($detalhe['cidade']) {
      $this->addDetalhe(array('Cidade', $detalhe['cidade']));
    }

    if ($detalhe['sigla_uf']) {
      $this->addDetalhe(array('Estado', $detalhe['sigla_uf']));
    }

    $zona = App_Model_ZonaLocalizacao::getInstance();
    if ($detalhe['zona_localizacao']) {
      $this->addDetalhe(array(
        'Zona Localização', $zona->getValue($detalhe['zona_localizacao'])
      ));
    }

    if ($detalhe['cep']) {
      $this->addDetalhe(array('CEP', int2cep($detalhe['cep'])));
    }

    if ($detalhe['fone_1']) {
      $this->addDetalhe(
        array('Telefone 1', sprintf('(%s) %s', $detalhe['ddd_1'], $detalhe['fone_1']))
      );
    }

    if ($detalhe['fone_2']) {
      $this->addDetalhe(
        array('Telefone 2', sprintf('(%s) %s', $detalhe['ddd_2'], $detalhe['fone_2']))
      );
    }

    if ($detalhe['fone_mov']) {
      $this->addDetalhe(
        array('Celular', sprintf('(%s) %s', $detalhe['ddd_mov'], $detalhe['fone_mov']))
      );
    }

    if ($detalhe['fone_fax']) {
      $this->addDetalhe(
        array('Fax', sprintf('(%s) %s', $detalhe['ddd_fax'], $detalhe['fone_fax']))
      );
    }

    if ($detalhe['url']) {
      $this->addDetalhe(array('Site', $detalhe['url']));
    }

    if ($detalhe['email']) {
      $this->addDetalhe(array('E-mail', $detalhe['email']));
    }

    if($detalhe['sexo'])
      $this->addDetalhe(array('Sexo', $detalhe['sexo'] == 'M' ? 'Masculino' : 'Feminino'));

    $obj_permissao = new clsPermissoes();

    if($obj_permissao->permissao_cadastra(43, $this->pessoa_logada,7,null,true))
    {
      $this->url_novo     = 'atendidos_cad.php';
      $this->url_editar   = 'atendidos_cad.php?cod_pessoa_fj=' . $detalhe['idpes'];
    }

    $this->url_cancelar = 'atendidos_lst.php';

    $this->largura = '100%';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_pessoas_index.php"          => "Pessoas",
         ""                                  => "Detalhe da pessoa f&iacute;sica"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }
}

// Instancia objeto de página
$pagina = new clsIndex();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
