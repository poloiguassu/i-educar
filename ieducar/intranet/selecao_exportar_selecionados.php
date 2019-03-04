<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/clsPmieducarInscrito.inc.php';

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('Nome', 'Turno', 'Data Nascimento', 'Idade', 'Telefone', 'Bairro', 'Avaliação Etapa 1', 'Avalia��o Etapa 2'), ";");

$objPessoa = new clsPmieducarInscrito();

$pessoas = $objPessoa->lista(null, 3);

/*foreach($objPessoa->lista(3) as $pessoa)
{
    array_push($pessoas, $pessoa);
}*/

$turno_campo = array(
    '0' => 'Não definido',
    '1' => 'Manhã',
    '2' => 'Tarde',
    '3' => 'Noite',
);

$avaliacao = array(
    '1' => 'Não Adequado',
    '2' => 'Parcialmente Adequado',
    '3' => 'Adequado'
);

function sortByName($a, $b)
{
    return $a['nome'] > $b['nome'];
}

if($pessoas)
{
    foreach($pessoas as $key => $pessoa)
    {
        $pessoas[$key]['nome'] = strtoupper($pessoa['nome']);
    }

    //uasort($pessoas, 'sortByName');

    foreach ($pessoas as $pessoa)
    {

        $data = $pessoa['data_nasc'];

        list($ano, $mes, $dia) = explode('-', $data);

        $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);

        $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);

        if($pessoa['egresso'] > 0)
        {
            $turno = "Egresso " . $pessoa['egresso'];
        } else {
            $turno = $turno_campo[$pessoa['turno']];
        }

        $telefone = "";
        if($pessoa['telefone_1'])
        {
            $telefone = "({$pessoa['ddd_telefone_1']}) {$pessoa['telefone_1']}";
        } else {
            $telefone = "({$pessoa['ddd_telefone_2']}) {$pessoa['telefone_2']}";
        }

        fputcsv($output, array (
            $pessoa['nome'],
            $turno,
            $data,
            $idade,
            $telefone,
            $pessoa['bairro'],
            $avaliacao[$pessoa['etapa_1']],
            $avaliacao[$pessoa['etapa_2']]
        ), ";");
    }
}
