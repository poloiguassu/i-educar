<?php

use iEducar\Modules\ErrorTracking\TrackerFactory;
use iEducar\Support\Navigation\TopMenu;
use Illuminate\Support\Facades\View;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/bootstrap.php';
require_once 'include/clsCronometro.inc.php';
require_once 'clsConfigItajai.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsControlador.inc.php';
require_once 'include/clsLogAcesso.inc.php';
require_once 'include/Geral.inc.php';
require_once 'include/pmicontrolesis/geral.inc.php';
require_once 'include/funcoes.inc.php';
require_once 'Portabilis/Utils/Database.php';
require_once 'Portabilis/Utils/User.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Assets/Version.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';

if ($GLOBALS['coreExt']['Config']->app->ambiente_inexistente) {
    header("Location: /404.html");
}


/**
 * clsBase class.
 *
 * Provê uma API para criação de páginas HTML programaticamente.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Include
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsBase extends clsConfig
{
    var $titulo = 'Prefeitura Cobra Tecnologia';
    var $template;
    var $clsForm = array();
    var $bodyscript = NULL;
    var $processoAp;
    var $refresh = FALSE;

    var $convidado = FALSE;
    var $renderMenu = TRUE;
    var $renderMenuSuspenso = TRUE;
    var $renderBanner = TRUE;
    var $estilos;
    var $scripts;

    var $script_header;
    var $script_footer;
    var $prog_alert;

    public $configuracoes;

    protected function setupConfigs()
    {
        $configuracoes = new clsPmieducarConfiguracoesGerais();
        $this->configuracoes = $configuracoes->detalhe();
    }

    protected function mostraSupenso()
    {
        if (empty($this->configuracoes)) {
            $this->setupConfigs();
        }

        $nivel = !empty($_SESSION['nivel']) ? (int)$_SESSION['nivel'] : null;

        if (!$this->configuracoes['active_on_ieducar'] && $nivel !== 1) {
            header('HTTP/1.1 503 Service Temporarily Unavailable');
            header("Location: suspenso.php");

            die();
        }
    }

    function OpenTpl($template)
    {

        $prefix = 'nvp_';
        $file = $this->arrayConfig['strDirTemplates'] . $prefix . $template . '.tpl';

        ob_start();
        include $file;
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    function SetTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    function AddForm($form)
    {
        $this->clsForm[] = $form;
    }

    function MakeHeadHtml()
    {

        /*$saida = $this->OpenTpl('htmlhead');
        $saida = str_replace("<!-- #&CORE_EXT_CONFIGURATION_ENV&# -->", CORE_EXT_CONFIGURATION_ENV, $saida);
        $saida = str_replace("<!-- #&USER_ID&# -->", $_SESSION['id_pessoa'], $saida);
        $saida = str_replace("<!-- #&TITULO&# -->", $this->titulo, $saida);

        if ($this->refresh) {
            $saida = str_replace("<!-- #&REFRESH&# -->", "<meta http-equiv='refresh' content='60'>", $saida);
        }

        if (is_array($this->estilos) && count($this->estilos)) {
            $estilos = '';
            foreach ($this->estilos as $estilo) {
                $estilos .= "<link rel=stylesheet type='text/css' href='/intranet/styles/{$estilo}.css?assets_version=" . Portabilis_Assets_Version::VERSION . "' />";
            }
            $saida = str_replace("<!-- #&ESTILO&# -->", $estilos, $saida);
        }

        if (is_array($this->scripts) && count($this->scripts)) {
            $estilos = '';
            foreach ($this->scripts as $script) {
                $scripts .= "<script type='text/javascript' src='/intranet/scripts/{$script}.js?assets_version=" . Portabilis_Assets_Version::VERSION . "' ></script>";
            }
            $saida = str_replace("<!-- #&SCRIPT&# -->", $scripts, $saida);
        }

        if ($this->bodyscript) {
            $saida = str_replace("<!-- #&BODYSCRIPTS&# -->", $this->bodyscript, $saida);
        } else {
            $saida = str_replace("<!-- #&BODYSCRIPTS&# -->", "", $saida);
        }

        if ($this->script_header) {
            $saida = str_replace("<!-- #&SCRIPT_HEADER&# -->", $this->script_header, $saida);
        } else {
            $saida = str_replace("<!-- #&SCRIPT_HEADER&# -->", "", $saida);
        }

        $saida = str_replace("<!-- #&GOOGLE_TAG_MANAGER_ID&# -->", $GLOBALS['coreExt']['Config']->app->gtm->id, $saida);*/

        // nome completo usuario
        // @TODO: jeito mais eficiente de usar estes dados, já que eles são
        //         usados em mais um método aqui...
        $nomePessoa = new clsPessoaFisica();
        list($nomePessoa, $email) = $nomePessoa->queryRapida($this->currentUserId(), "nome", "email");
        $nomePessoa = ($nomePessoa) ? $nomePessoa : 'Visitante';

        $retorno = array(
            'ambiente' => CORE_EXT_CONFIGURATION_ENV,
            'user_id' => $_SESSION['id_pessoa'],
            'titulo' => $this->titulo,
            'meta' => array(
                'equiv' => 'refresh',
                'content' => 60
            ),
            'assets' => array(
                'styles' => $this->estilos,
                'scripts' => $this->scripts,
                'version' => Portabilis_Assets_Version::VERSION
            ),
            'body_script' => $this->bodyscript,
            'head_script' => $this->script_header,
            'google_tag_manager' => $GLOBALS['coreExt']['Config']->app->gtm->id,
            'slug' => $GLOBALS['coreExt']['Config']->app->database->dbname,
            'user_name' => trim($nomePessoa),
            'user_email' => trim($email)
        );

        /*$saida = str_replace("<!-- #&SLUG&# -->", $GLOBALS['coreExt']['Config']->app->database->dbname, $saida);
        $saida = str_replace("<!-- #&USERLOGADO&# -->", trim($nomePessoa), $saida);
        $saida = str_replace("<!-- #&USEREMAIL&# -->", trim($email), $saida);*/

        return $retorno;
    }

    function addEstilo($estilo_nome)
    {
        $this->estilos[$estilo_nome] = $estilo_nome;
    }

    function addScript($script_nome)
    {
        $this->scripts[$script_nome] = $script_nome;
    }

    function MakeFootHtml()
    {
        $saida = array(
            'script' => $this->script_footer,
        );

        /*$saida = $this->OpenTpl('htmlfoot');

        if ($this->script_footer) {
            $saida = str_replace("<!-- #&SCRIPT_FOOTER&# -->", $this->script_footer, $saida);
        } else {
            $saida = str_replace("<!-- #&SCRIPT_FOOTER&# -->", "", $saida);
        }*/

        return $saida;
    }

    function verificaPermissao()
    {
        return $this->VerificaPermicao();
    }

    function VerificaPermicao()
    {
        if ($this->processoAp) {
            $permite = true;

            if (!is_array($this->processoAp)) {
                return true;
            }

            foreach ($this->processoAp as $processo) {
                if (!$this->VerificaPermicaoNumerico($processo)) {
                    $permite = false;
                } else {
                    $this->processoAp = $processo;
                    $permite = true;
                    break;
                }
            }

            if (!$permite) {
                header("location: index.php?negado=1&err=1");
                die("Acesso negado para este usu&acute;rio");
            }
        } else {
            if (!$this->VerificaPermicaoNumerico($this->processoAp)) {
                header("location: index.php?negado=1&err=2");
                die("Acesso negado para este usu&acute;rio");
            }
        }

        return TRUE;
    }

    function VerificaPermicaoNumerico($processo_ap)
    {
        if (is_numeric($processo_ap)) {
            $sempermissao = TRUE;

            if ($processo_ap == 0) {
                $this->prog_alert .= "Processo AP == 0!";
            }

            if ($processo_ap != 0) {
                $this->db()->Consulta("SELECT 1 FROM pmieducar.menu_tipo_usuario mtu
                                INNER JOIN pmieducar.tipo_usuario tu ON mtu.ref_cod_tipo_usuario = tu.cod_tipo_usuario
                                INNER JOIN pmieducar.usuario u ON tu.cod_tipo_usuario = u.ref_cod_tipo_usuario
                                WHERE mtu.ref_cod_menu_submenu = 0 AND u.cod_usuario = {$this->currentUserId()}");
                if ($this->db()->ProximoRegistro()) {
                    list($aui) = $this->db()->Tupla();
                    $sempermissao = FALSE;
                }

                // @todo A primeira consulta OK, verifica de forma simples de tem
                //       permissão de acesso ao processo. Já a segunda, não existe
                //       sentido para nivel = 2 já que processoAp pode ser de níveis
                //       maiores que 2.
                $this->db()->Consulta("SELECT 1 FROM pmieducar.menu_tipo_usuario mtu
                                INNER JOIN pmieducar.tipo_usuario tu ON mtu.ref_cod_tipo_usuario = tu.cod_tipo_usuario
                                INNER JOIN pmieducar.usuario u ON tu.cod_tipo_usuario = u.ref_cod_tipo_usuario
                                WHERE (mtu.ref_cod_menu_submenu = {$processo_ap} AND u.cod_usuario = {$this->currentUserId()})
                                OR (SELECT true FROM menu_submenu WHERE cod_menu_submenu = {$processo_ap} AND nivel = 2)
                                LIMIT 1");
                if ($this->db()->ProximoRegistro()) {
                    list($aui) = $this->db()->Tupla();
                    $sempermissao = FALSE;
                }

                if ($sempermissao) {
                    $ip = empty($_SERVER['REMOTE_ADDR']) ? "NULL" : $_SERVER['REMOTE_ADDR'];
                    $ip_de_rede = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? "NULL" : $_SERVER['HTTP_X_FORWARDED_FOR'];
                    $pagina = $_SERVER["PHP_SELF"];
                    $posts = "";
                    $gets = "";
                    $sessions = "";

                    foreach ($_POST as $key => $val) {
                        $posts .= " - $key: $val\n";
                    }

                    foreach ($_GET as $key => $val) {
                        $gets .= " - $key: $val\n";
                    }

                    foreach ($_SESSION as $key => $val) {
                        $sessions .= " - $key: $val\n";
                    }

                    $variaveis = "POST\n{$posts}GET\n{$gets}SESSION\n{$sessions}";
                    $variaveis = Portabilis_String_Utils::toLatin1($variaveis, array('escape' => true));

                    if ($this->currentUserId()) {
                        $this->db()->Consulta("INSERT INTO intranet_segur_permissao_negada (ref_ref_cod_pessoa_fj, ip_externo, ip_interno, data_hora, pagina, variaveis) VALUES('{$this->currentUserId()}', '$ip', '$ip_de_rede', NOW(), '$pagina', '$variaveis')");
                    } else {
                        $this->db()->Consulta("INSERT INTO intranet_segur_permissao_negada (ref_ref_cod_pessoa_fj, ip_externo, ip_interno, data_hora, pagina, variaveis) VALUES(NULL, '$ip', '$ip_de_rede', NOW(), '$pagina', '$variaveis')");
                    }

                    return FALSE;
                }
            }
            return TRUE;
        }
    }

    /**
     * @see Core_Page_Controller_Abstract#getAppendedOutput()
     * @see Core_Page_Controller_Abstract#getPrependedOutput()
     */
    function MakeBody()
    {
        $corpo = '';
        $scripts = '';
        $templateForm = array();

        foreach ($this->clsForm as $form) {
            if (method_exists($form, 'getTemplate')) {
                $corpo = $form->RenderHTML();
                $this->template = $form->getTemplate();
            } else {
                $this->template = '@pages/base';
                $corpo = array(
                    'dados' => array(
                        'html' => $form->RenderHTML()
                    )
                );
            }

            $corpo['instancia'] = $form;

            // Prepend output.
            if (method_exists($form, 'getPrependedOutput')) {
                $scripts = $form->getPrependedOutput() . $scripts;
            }

            // Append output.
            if (method_exists($form, 'getAppendedOutput')) {
                $scripts = $scripts . $form->getAppendedOutput();
            }

            if (!isset($form->prog_alert)) {
                continue;
            }

            if (is_string($form->prog_alert) && $form->prog_alert) {
                $this->prog_alert .= $form->prog_alert;
            }
        }

        $saida = $corpo;

        // Pega o endereço IP do host, primeiro com HTTP_X_FORWARDED_FOR (para pegar o IP real
        // caso o host esteja atrás de um proxy)
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
            // No caso de múltiplos IPs, pega o último da lista
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip_maquina = trim(array_pop($ip));
        } else {
            $ip_maquina = $_SERVER['REMOTE_ADDR'];
        }

        $sql = "UPDATE funcionario SET ip_logado = '$ip_maquina' , data_login = NOW() WHERE ref_cod_pessoa_fj = {$this->currentUserId()}";
        $this->db()->Consulta($sql);

        $saida .= "<script type=\"text/javascript\" src=\"/intranet/scripts/select2/select2.full.min.js\"></script>";
        $saida .= "<script type=\"text/javascript\" src=\"/intranet/scripts/select2/pt-BR.js\"></script>";
        $saida .= "<link type=\"text/css\" rel=\"stylesheet\" href=\"/intranet/scripts/select2/select2.min.css\" />";

        $retorno = array(
            'render_menu' => $this->renderMenu,
            'render_menu_suspenso' => $this->renderMenuSuspenso,
            'render_banner' => $this->renderBanner,
            'scripts' => $scripts,
            'menu' => $menu,
            'content' => $corpo,
            'user' => array(
                'name' => $nomePessoa,
                'email' => $email,
                'foto' => $foto,
            )
        );

        return $retorno;
    }

    function Formular()
    {
        return FALSE;
    }

    /**
     * @todo Verificar se funciona.
     */
    function CadastraAcesso()
    {
        @session_start();
        if (@$_SESSION['marcado'] != "private") {
            if (!$this->convidado) {
                $ip = empty($_SERVER['REMOTE_ADDR']) ? "NULL" : $_SERVER['REMOTE_ADDR'];
                $ip_de_rede = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? "NULL" : $_SERVER['HTTP_X_FORWARDED_FOR'];
                $id_pessoa = $_SESSION['id_pessoa'];

                $logAcesso = new clsLogAcesso(FALSE, $ip, $ip_de_rede, $id_pessoa);
                $logAcesso->cadastra();

                $_SESSION['marcado'] = "private";
            }
        }
        session_write_close();
    }

    function MakeAll()
    {
        try {
            $cronometro = new clsCronometro();
            $cronometro->marca('inicio');
            $liberado = TRUE;

            if ($this->convidado) {
                @session_start();
                $_SESSION['convidado'] = TRUE;
                $_SESSION['id_pessoa'] = '0';
                session_write_close();
            }

            $controlador = new clsControlador();

            if ($controlador->Logado() && $liberado || $this->convidado) {
                $this->mostraSupenso();

                $this->Formular();
                $this->VerificaPermicao();
                $this->CadastraAcesso();
                $saida_geral = '';

                app(TopMenu::class)->current($this->processoAp,  request()->getRequestUri());
                View::share('title', $this->titulo);

                $saida_head = $this->MakeHeadHtml();

                $saida_body = $this->MakeBody();

            } elseif ((empty($_POST['login'])) || (empty($_POST['senha'])) && $liberado) {
                $force = !empty($_GET['force']) ? true : false;

                if (!$force) {
                    $this->mostraSupenso();
                }

                $saida_head = $this->MakeHeadHtml();
                $controlador->Logar(false);
                $saida_foot = $this->MakeFootHtml();
            } else {
                $controlador->Logar(true);
                $referer = $_SERVER['HTTP_REFERER'];

                header("Location: " . $referer, true, 302);
                die();
            }

            $view = 'legacy.body';

            if (!$this->renderMenu || !$this->renderMenuSuspenso) {
                $view = 'legacy.blank';
            }

            echo view($view, ['body' => $saida_geral])->render();

        } catch (Exception $e) {

            if ($GLOBALS['coreExt']['Config']->modules->error->track) {
                $tracker = TrackerFactory::getTracker($GLOBALS['coreExt']['Config']->modules->error->tracker_name);
                $tracker->notify($e);
            }

            if (config('app.debug')) {
                throw new \Exception($e->getMessage(), 0, $e);
            }

            $lastError = error_get_last();

            @session_start();
            $_SESSION['last_error_message'] = $e->getMessage();
            $_SESSION['last_php_error_message'] = $lastError['message'];
            $_SESSION['last_php_error_line'] = $lastError['line'];
            $_SESSION['last_php_error_file'] = $lastError['file'];
            @session_write_close();

            error_log("Erro inesperado (pego em clsBase): " . $e->getMessage());

            die("<script>document.location.href = '/module/Error/unexpected';</script>");
        }
    }

    function setAlertaProgramacao($string)
    {
        if (is_string($string) && $string) {
            $this->prog_alert = $string;
        }
    }

    protected function checkUserExpirations()
    {
        $user = Portabilis_Utils_User::load('current_user');
        $uri = $_SERVER['REQUEST_URI'];
        $forcePasswordUpdate = $GLOBALS['coreExt']['Config']->app->user_accounts->force_password_update == true;

        if ($user['expired_account'] || $user['proibido'] != '0' || $user['ativo'] != '1')
            header("Location: /intranet/logof.php");

        elseif ($user['expired_password'] && $forcePasswordUpdate && $uri != '/module/Usuario/AlterarSenha')
            header("Location: /module/Usuario/AlterarSenha");
    }

    protected function db()
    {
        return Portabilis_Utils_Database::db();
    }

    protected function currentUserId()
    {
        return Portabilis_Utils_User::currentUserId();
    }
}
