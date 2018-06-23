<?php

/*
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
 */

require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'Portabilis/Messenger.php';
require_once 'Portabilis/Mailer.php';
require_once 'Portabilis/Utils/User.php';
require_once 'Portabilis/Utils/ReCaptcha.php';
require_once 'Portabilis/Utils/ReCaptcha.php';
require_once 'Core/View/TemplateRenderer.php';

/**
 * clsControlador class.
 *
 * @author   Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Classe disponível desde a versão 1.0.0
 * @version  $Id: /ieducar/branches/1.1.0-avaliacao/ieducar/intranet/include/clsControlador.inc.php 662 2009-11-17T18:28:48.404882Z eriksen  $
 */
class clsControlador
{

  /**
   * @var boolean
   */
  public $logado;

  /**
   * @var string
   */
  public $erroMsg;


  /**
   * Construtor.
   */
  public function __construct()
  {

    /*
      Desabilitado esta linha para usar o valor setado no php.ini > session.cookie_lifetime
      @session_set_cookie_params(200);
    */

    @session_start();
	
    if ('logado' == $_SESSION['itj_controle']) {
      $this->logado = TRUE;
    }
    else {
      $this->logado = FALSE;
    }

    // Controle dos menus
    if (isset($_GET['mudamenu']) && isset($_GET['categoria']) && isset($_GET['acao']))
    {
      if ($_GET['acao']) {
        $_SESSION['menu_opt'][$_GET['categoria']] = 1;
        $_SESSION['menu_atual'] = $_GET['categoria'];
      }
      else {
        // Está apagando variável session com o índice dado por $_GET
        unset($_SESSION['menu_opt'][$_GET['categoria']]);
        if ($_SESSION['menu_atual'] == $_GET['categoria']) {
          unset($_SESSION['menu_atual']);
        }
      }

      $db = new clsBanco();
      if (isset($_SESSION['id_pessoa'])) {
        $db->Consulta("UPDATE funcionario SET opcao_menu = '" . serialize( $_SESSION['menu_opt'] ) . "' WHERE ref_cod_pessoa_fj = '" . $_SESSION['id_pessoa'] . "'");
      }
    }

    session_write_close();

    $this->_maximoTentativasFalhas = 7;
    $this->messenger = new Portabilis_Messenger();
  }


  /**
   * Retorna TRUE para usuário logado
   * @return  boolean
   */
  public function Logado()
  {
    return $this->logado;
  }


  /**
   * Executa o login do usuário.
   */
  public function obriga_Login()
  {
    if (! $this->logado)
      $validateUserCredentials = false;

    elseif ($_POST['login'] && $_POST['senha']) {
      $validateUserCredentials = true;
    }

    $this->logar($validateUserCredentials);
  }


  // novo metodo login, logica quebrada em metodos menores
  public function Logar($validateUserCredentials) {
    if ($validateUserCredentials) {
      $user = $this->validateUserCredentials($username = @$_POST['login'], $password = md5(@$_POST['senha']));

      if ($this->canStartLoginSession($user)) {
        $this->startLoginSession($user);
        return null;
      }
    }

    $this->renderLoginPage();
  }


  // valida se o usuário e senha informados, existem no banco de dados.
  protected function validateUserCredentials($username, $password) {
    if (! $this->validateHumanAccess()) {
      $msg = "Você errou a senha muitas vezes, por favor, preencha o campo de " .
             "confirmação visual ou <a class='light decorated' href='/module/Usuario/Rede" .
             "finirSenha'>redefina sua senha</a>.";
      $this->messenger->append($msg, "error", false, "error");
    }

    else {
      $user = Portabilis_Utils_User::loadUsingCredentials($username, $password);

      if (is_null($user)) {
        $this->messenger->append("Usu�rio ou senha incorretos, tente novamente.", "error");
        $this->incrementTentativasLogin();
      }
      else {
        $this->unsetTentativasLogin();

        $permissoes = new clsPermissoes();
        $user['nivel'] = $permissoes->nivel_acesso($user['id']);

        return $user;
      }
    }

    return false;
  }


  public function startLoginSession($user, $redirectTo = '') {
    // unsetting login attempts here, because when the password is recovered the login attempts should be reseted.
    $this->unsetTentativasLogin();

    @session_start();
    $_SESSION = array();
    $_SESSION['itj_controle'] = 'logado';
    $_SESSION['id_pessoa'] = $user['id'];
    $_SESSION['pessoa_setor'] = $user['ref_cod_setor_new'];
    $_SESSION['menu_opt'] = unserialize($user['opcao_menu']);
    $_SESSION['tipo_menu'] = $user['tipo_menu'];
    $_SESSION['nivel'] = $user['nivel'];
    @session_write_close();

    Portabilis_Utils_User::logAccessFor($user['id'], $this->getClientIP());
    Portabilis_Utils_User::destroyStatusTokenFor($user['id'], 'redefinir_senha');

    $this->logado = true;
    $this->messenger->append("Usuário logado com sucesso.", "success");

    // solicita email para recuperação de senha, caso usuário ainda não tenha informado.
    if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        header("Location: /module/Usuario/AlterarEmail");
    } elseif ($user['expired_password']) {
        header("Location: /module/Usuario/AlterarSenha");
    } elseif (!empty($redirectTo)) {
        header("Location: $redirectTo");
    }
  }


  public function canStartLoginSession($user) {
    if (! $this->messenger->hasMsgWithType("error")) {
      $this->checkForSuspended($user);
      $this->checkForDisabledAccount($user);
      $this->checkForBannedAccount($user);
      $this->checkForExpiredAccount($user);
      $this->checkForMultipleAccess($user);
      // #TODO verificar se conta nunca usada (exibir "Sua conta n&atilde;o est&aacute; ativa. Use a op&ccedil;&atilde;o 'Nunca usei a intrenet'." ?)
    }

    return ! $this->messenger->hasMsgWithType("error");
  }


  // renderiza o template de login, com as mensagens adicionadas durante validações
  protected function renderLoginPage() {
    $this->destroyLoginSession();

    $templateName = 'login';
	
	if ($GLOBALS['coreExt']['Config']->app->template->loginpage) {
		$templateNamePath = PROJECT_ROOT . '/views/' . $GLOBALS['coreExt']['Config']->app->template->loginpage;
		
		if (file_exists($templateNamePath) && is_readable($templateNamePath))
			$templateName = basename($templateNamePath, '.twig.html');
	}
	
    $requiresHumanAccessValidation = isset($_SESSION['tentativas_login_falhas']) &&
                                     is_numeric($_SESSION['tentativas_login_falhas']) &&
                                     $_SESSION['tentativas_login_falhas'] >= $this->_maximoTentativasFalhas;
									 
	// TODO: reativar captcha no login
	
	$params = array(
		'message' => $this->messenger->toText(),
	);
	
	$twig = new TemplateRenderer();
	$templateText = $twig->render($templateName, $params);
	
    die($templateText);
  }


  protected function destroyLoginSession($addMsg = false) {
    $tentativasLoginFalhas = $_SESSION['tentativas_login_falhas'];

    @session_start();
    $_SESSION = array();
    @session_destroy();

    //mantem tentativas_login_falhas, até que senha senha informada corretamente
    @session_start();
    $_SESSION['tentativas_login_falhas'] = $tentativasLoginFalhas;
    @session_write_close();

    if ($addMsg)
      $this->messenger->append("Usuário deslogado com sucesso.", "success");
  }


  protected function getClientIP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
      // pega o (ultimo) IP real caso o host esteja atrás de um proxy
      $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
      $ip = trim(array_pop($ip));
    }
    else
      $ip = $_SERVER['REMOTE_ADDR'];

    return $ip;
  }


  protected function validateHumanAccess() {
    $result = false;

    if (! $this->atingiuTentativasLogin())
      $result = true;

    elseif (Portabilis_Utils_ReCaptcha::check($_POST['g-recaptcha-response'])) {
      $this->unsetTentativasLogin();
      $result = true;
    }

    return $result;
  }


  protected function atingiuTentativasLogin() {
    return isset($_SESSION['tentativas_login_falhas']) &&
                 is_numeric($_SESSION['tentativas_login_falhas']) &&
                 $_SESSION['tentativas_login_falhas'] >= $this->_maximoTentativasFalhas;
  }


  protected function incrementTentativasLogin() {
    @session_start();
    if (! isset($_SESSION['tentativas_login_falhas']) or ! is_numeric($_SESSION['tentativas_login_falhas']))
      $_SESSION['tentativas_login_falhas'] = 1;
    else
      $_SESSION['tentativas_login_falhas'] += 1;
    @session_write_close();
  }


  protected function unsetTentativasLogin() {
    @session_start();
    unset($_SESSION['tentativas_login_falhas']);
    @session_write_close();
  }


  protected function checkForDisabledAccount($user) {
    if ($user['ativo'] != '1') {
      $this->messenger->append("Sua conta de usuário foi desativada ou expirou, por favor, " .
                              "entre em contato com o responsável pelo sistema do seu município.", "error", false, "error");
    }
  }

  protected function checkForSuspended($user) {
    $configuracoes = new clsPmieducarConfiguracoesGerais();
    $configuracoes = $configuracoes->detalhe();

    $nivel = (int) $user['nivel'];

    if (!$configuracoes['active_on_ieducar'] && $nivel !== 1) {
        $this->messenger->append("Sua conta de usuário não pode acessar o sistema, " .
            "por favor, entre em contato com o responsável pelo sistema do seu município.", "error", false, "error");
    }
  }

  protected function checkForBannedAccount($user) {
    if ($user['proibido'] != '0') {
      $this->messenger->append("Sua conta de usuário não pode mais acessar o sistema, " .
                              "por favor, entre em contato com o responsável pelo sistema do seu município.",
                              "error", false, "error");
    }
  }


  protected function checkForExpiredAccount($user) {
    if($user['expired_account']) {

      if ($user['ativo'] == 1)
        Portabilis_Utils_User::disableAccount($user['id']);

      $this->messenger->append("Sua conta de usuário expirou, por favor, " .
                              "entre em contato com o responsável pelo sistema do seu município.", "error", false, "error");
    }
  }


  protected function checkForMultipleAccess($user) {
    // considera como acesso multiplo, acesso em diferentes IPs em menos de $tempoMultiploAcesso minutos
    $tempoMultiploAcesso = 10;
    $tempoEmEspera       = abs(time() - strftime("now") - strtotime($user['data_login'])) / 60;

    $multiploAcesso = $tempoEmEspera <= $tempoMultiploAcesso &&
                      $user['ip_ultimo_acesso'] != $this->getClientIP();

    if ($multiploAcesso and $user['super']) {

      // #TODO mover lógica email, para mailer especifico

      $subject = "Conta do super usuário {$_SERVER['HTTP_HOST']} acessada em mais de um local";

      $message = ("Aparentemente a conta do super usuário {$user['matricula']} foi acessada em " .
                  "outro computador nos últimos $tempoMultiploAcesso " .
                  "minutos, caso não tenha sido você, por favor, altere sua senha.\n\n" .
                  "Endereço IP último acesso: {$user['ip_ultimo_acesso']}\n".
                  "Endereço IP acesso atual: {$this->getClientIP()}");

      $mailer = new Portabilis_Mailer();
      $mailer->sendMail($user['email'], $subject, $message);
    }
    elseif ($multiploAcesso) {
      $minutosEmEspera = round($tempoMultiploAcesso - $tempoEmEspera) + 1;
      $this->messenger->append("Aparentemente sua conta foi acessada em outro computador nos últimos " .
                              "$tempoMultiploAcesso minutos, caso não tenha sido você, " .
                              "por favor, altere sua senha ou tente novamente em $minutosEmEspera minutos",
                              "error", false, "error");
    }
  }

    public function getSocialMediaLinks($configuracoes){
        $socialMedia = "";

        if($configuracoes['facebook_url'] || $configuracoes['linkedin_url'] || $configuracoes['twitter_url']){
            $socialMedia .= "<p> Siga-nos nas redes sociais&nbsp;&nbsp;</p>";
        }

        if($configuracoes['facebook_url']){
            $socialMedia .= '<a target="_blank" href="'.$configuracoes['facebook_url'].'"><img src="/intranet/imagens/icon-social-facebook.png"></a> ';
        }
        if($configuracoes['linkedin_url']){
            $socialMedia .= '<a target="_blank" href="'.$configuracoes['linkedin_url'].'"><img src="/intranet/imagens/icon-social-linkedin.png"></a> ';
        }
        if($configuracoes['twitter_url']){
            $socialMedia .= '<a target="_blank" href="'.$configuracoes['twitter_url'].'"><img src="/intranet/imagens/icon-social-twitter.png"></a> ';
        }

        return $socialMedia;
    }

    public function getLoginLogo($configuracoes){
        $logoUrl = "/intranet/imagens/brasao-republica.png";

        if(!empty($configuracoes['ieducar_image'])){
            $logoUrl = $configuracoes['ieducar_image'];
        }

        return '<img width="150px" height="150px" src="'.$logoUrl.'"/>';
    }

}
