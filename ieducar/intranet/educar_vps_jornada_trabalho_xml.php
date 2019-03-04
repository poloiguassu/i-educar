<?php
header('Content-type: text/xml');

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"colecoes\">\n";

if (is_numeric($_GET['inst'])) {
    $db = new clsBanco();
    $db->Consulta(
        "SELECT
            cod_vps_jornada_trabalho,
            nm_jornada_trabalho
        FROM
            pmieducar.vps_jornada_trabalho
        WHERE
            ativo = 1
            AND ref_cod_instituicao = '{$_GET['inst']}'
        ORDER BY
            nm_jornada_trabalho ASC"
    );

    if ($db->numLinhas()) {
        while ($db->ProximoRegistro()) {
            list($cod, $nome) = $db->Tupla();
            $nome = str_replace('&', 'e', $nome);
            echo "	<vps_jornada_trabalho cod_jornada_trabalho=\"{$cod}\" >{$nome}</vps_jornada_trabalho>\n";
        }
    }
}
echo '</query>';
