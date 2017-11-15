<?php


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																		 *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com							 *
*																		 *
*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
*																		 *
*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
*	junto  com  este  programa. Se não, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

require_once 'CoreExt/Enum.php';

// HACK: Excluir essa classe e fazer tudo através de TWIG
class App_Model_PrioridadeVPSHTML extends CoreExt_Enum
{
	const SEM_ENTREVISTA_0			= 0;
	const TEMPORARIO				= 1;
	const DESISTIU_VPS				= 2;
	const FALTOU_ENTREVISTA			= 3;
	const RECUSOU_VAGA				= 4;
	const ENCAMINHADO				= 5;
	const NAO_ENCAMINHADO			= 6;

	protected $_data = array(
		self::SEM_ENTREVISTA_0		=> '<span class="badge badge badge-secondary" data-toggle="popover" title="Motivo" data-content="Este aluno não completou o processo de fomação" >0 - Não enviar entrevistas</span>',
		self::TEMPORARIO			=> '<span class="badge badge badge-dark" data-toggle="popover" title="Motivo" data-content="Está doente, só poderá cumprir vps em janeiro" >1 - Final da fila temporário</span>',
		self::DESISTIU_VPS			=> '<span class="badge badge badge-danger" data-toggle="popover" title="Motivo" data-content="Desistiu da VPS em andamento" >1 - Desitiu da VPS</span>',
		self::FALTOU_ENTREVISTA		=> '<span class="badge badge badge-warning" data-toggle="popover" title="Motivo" data-content="Faltou na entrevista." >2 - Faltou Entrevista</span>',
		self::RECUSOU_VAGA			=> '<span class="badge badge badge-info" data-toggle="popover" title="Motivo" data-content="Recusou vaga tal" >3 - Recusou Vaga</span>',
		self::ENCAMINHADO			=> '<span class="badge badge badge-primary" data-toggle="popover" title="Motivo" data-content="Já foi encaminhado a para entrevistas" >4 - Já foi encaminhado</span>',
		self::NAO_ENCAMINHADO		=> '<span class="badge badge badge-success" data-toggle="popover" title="Motivo" data-content="Ainda não enviado a nenhuma entrevista." >5 - Nenhum encaminhamento</span>',
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
