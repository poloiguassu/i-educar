<?php

header('Content-type: text/xml');

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn(
    $xmlns = 'colecoes'
);

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"colecoes\">\n";

if (is_numeric($_GET['esc'])) {
    $db = new clsBanco();
    $db->Consulta(
        "SELECT
            cod_vps_funcao,
            nm_funcao
        FROM
            pmieducar.vps_funcao
        WHERE
            ativo = 1
            AND ref_cod_escola = '{$_GET['esc']}'
        ORDER BY
            nm_funcao ASC"
    );

    if ($db->numLinhas()) {
        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();
            $nome = str_replace('&', 'e', $nome);
            echo "	<vps_funcao cod_funcao=\"{$cod}\" >{$nome}</vps_funcao>\n";
        }
    }
}
echo '</query>';
