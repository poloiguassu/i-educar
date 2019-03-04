<?php

header('Content-type: text/xml');

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n
    <query xmlns=\"colecoes\">\n";

if (is_numeric($_GET['esc']) && is_numeric($_GET['idpes'])) {
    $db = new clsBanco();
    $db->Consulta(
        "SELECT
            cod_vps_responsavel_entrevista,
            nm_responsavel
        FROM
            pmieducar.vps_responsavel_entrevista
        WHERE
            ativo = 1
            AND ref_cod_escola = '{$_GET['esc']}'
            AND ref_idpes = '{$_GET['idpes']}'
        ORDER BY
            nm_responsavel ASC"
    );

    if ($db->numLinhas()) {
        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();
            $nome = str_replace('&', 'e', $nome);
            echo "<vps_responsavel_entrevista cod_vps_responsavel_entrevista=\"{$cod}\">{$nome}</vps_responsavel_entrevista>\n";
        }
    }
}
echo '</query>';
