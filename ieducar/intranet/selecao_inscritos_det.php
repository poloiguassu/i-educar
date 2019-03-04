<?php

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
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Cadastro
 *
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 *
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo('Processo Seletivo - Jovem');
        $this->processoAp = 43;
        $this->addEstilo('localizacaoSistema');
    }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Cadastro
 *
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 *
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
    public function Gerar()
    {
        $this->titulo = 'Detalhe da Jovem - Processo Seletivo';

        $cod_inscrito = @$_GET['cod_pessoa'];

        echo 'pq? ' . $cod_inscrito;
        $objPessoa = new clsPmieducarInscrito($cod_inscrito);
        $db        = new clsBanco();

        $detalhe = $objPessoa->detalhe();

        $this->addDetalhe(['Nome', $detalhe['nome']]);

        if ($detalhe['cpf']) {
            $this->addDetalhe(['CPF', int2cpf($detalhe['cpf'])]);
        }

        if ($detalhe['rg']) {
            $this->addDetalhe(['RG', $detalhe['rg']]);
        }

        if ($detalhe['data_nasc']) {
            $this->addDetalhe(['Data de Nascimento', dataFromPgToBr($detalhe['data_nasc'])]);
        }

        if ($detalhe['sexo']) {
            $sexo = $detalhe['sexo'] == 'M' ? 'Masculino' : 'Feminino';
            $this->addDetalhe(['Gênero', $sexo]);
        }

        if ($detalhe['nm_responsavel']) {
            $this->addDetalhe(['Nome do Responsável', $detalhe['nm_responsavel']]);
        }

        if ($detalhe['ddd_telefone_1'] && $detalhe['telefone_1']) {
            $telefone = '(' . $detalhe['ddd_telefone_1'] . ') ' . $detalhe['telefone_1'];
            $this->addDetalhe(['Telefone 1', $telefone]);
        }

        if ($detalhe['ddd_telefone_2'] && $detalhe['telefone_2']) {
            $telefone = '(' . $detalhe['ddd_telefone_2'] . ') '  . $detalhe['telefone_2'];
            $this->addDetalhe(['Telefone 2', $telefone]);
        }

        if ($detalhe['ddd_telefone_mov'] && $detalhe['telefone_mov']) {
            $telefone = '(' . $detalhe['ddd_telefone_mov'] . ') '  . $detalhe['telefone_mov'];
            $this->addDetalhe(['Telefone Celular', $telefone]);
        }

        if ($detalhe['email']) {
            $this->addDetalhe(['E-mail', $detalhe['email']]);
        }

        if ($detalhe['indicacao']) {
            $this->addDetalhe(['Como ficou sabendo do projeto?', $detalhe['indicacao']]);
        }

        if ($detalhe['guarda_mirim']) {
            $this->addDetalhe(['Inscrito na Guarda Mirim?', ($detalhe['guarda_mirim'] == 1) ? 'sim' : 'não']);
        }

        $serie = [
            '0' => 'Não definido',
            '1' => '5º série',
            '2' => '6º série',
            '3' => '7º série',
            '4' => '8º série',
            '5' => '9º série',
            '6' => '1º ano Ensino Médio',
            '7' => '2º ano Ensino Médio',
            '8' => '3º ano Ensino Médio',
            '9' => 'Concluido',
            '10' => 'EJA'
        ];

        if ($detalhe['serie']) {
            $this->addDetalhe(['Serie', $serie[$detalhe['serie']]]);
        }

        $turno = [
            '0' => 'Nao definido',
            '1' => 'Manhã',
            '2' => 'Tarde',
            '3' => 'Noite',
        ];

        if ($detalhe['turno']) {
            $this->addDetalhe(['Turno', $turno[$detalhe['turno']]]);
        }

        if ($detalhe['egresso']) {
            $this->addDetalhe(['Ano de Conclusão', $detalhe['egresso']]);
        }

        if ($detalhe['copia_rg']) {
            $this->addDetalhe(['Entregou Cópia do RG?', ($detalhe['copia_rg'] == 1) ? 'sim' : 'não']);
        }

        if ($detalhe['copia_cpf']) {
            $this->addDetalhe(['Entregou Cópia do CPF?', ($detalhe['copia_cpf'] == 1) ? 'sim' : 'não']);
        }

        if ($detalhe['copia_residencia']) {
            $this->addDetalhe(['Entregou Cópia do Comprovante de Residência?', ($detalhe['copia_residencia'] == 1) ? 'sim' : 'não']);
        }

        if ($detalhe['copia_historico']) {
            $this->addDetalhe(['Entregou Histórico ou Declaração de Matrícula?', ($detalhe['copia_historico'] == 1) ? 'sim' : 'não']);
        }

        if ($detalhe['copia_renda']) {
            $this->addDetalhe(['Entregou Comprovaçãoo de Renda Familiar?', ($detalhe['copia_renda'] == 1) ? 'sim' : 'não']);
        }

        $avaliacao = [
            '1' => 'Não Adequado',
            '2' => 'Parcialmente Adequado',
            '3' => 'Adequado'
        ];

        if ($detalhe['etapa_1']) {
            $this->addDetalhe(['Etapa 1', $avaliacao[$detalhe['etapa_1']]]);
        }

        $this->url_novo     = 'selecao_inscritos_cad.php';
        $this->url_editar   = 'selecao_inscritos_cad.php?cod_inscrito=' . $cod_inscrito;
        $this->url_cancelar = 'selecao_inscritos_lst.php';

        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'Início',
            ''                                  => 'Detalhe do Jovem - Processo Seletivo'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }
}

// Instancia objeto de p�gina
$pagina = new clsIndex();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do � p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
