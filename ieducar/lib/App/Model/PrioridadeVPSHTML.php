<?php


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																		 *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com							 *
*																		 *
*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
*																		 *
*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

require_once 'CoreExt/Enum.php';

// HACK: Excluir essa classe e fazer tudo atrav�s de TWIG
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
		self::SEM_ENTREVISTA_0		=> '<span class="badge badge badge-secondary" data-toggle="popover" title="Motivo" data-content="Este aluno n�o completou o processo de foma��o" >0 - N�o enviar entrevistas</span>',
		self::TEMPORARIO			=> '<span class="badge badge badge-dark" data-toggle="popover" title="Motivo" data-content="Est� doente, s� poder� cumprir vps em janeiro" >1 - Final da fila tempor�rio</span>',
		self::DESISTIU_VPS			=> '<span class="badge badge badge-danger" data-toggle="popover" title="Motivo" data-content="Desistiu da VPS em andamento" >1 - Desitiu da VPS</span>',
		self::FALTOU_ENTREVISTA		=> '<span class="badge badge badge-warning" data-toggle="popover" title="Motivo" data-content="Faltou na entrevista." >2 - Faltou Entrevista</span>',
		self::RECUSOU_VAGA			=> '<span class="badge badge badge-info" data-toggle="popover" title="Motivo" data-content="Recusou vaga tal" >3 - Recusou Vaga</span>',
		self::ENCAMINHADO			=> '<span class="badge badge badge-primary" data-toggle="popover" title="Motivo" data-content="J� foi encaminhado a para entrevistas" >4 - J� foi encaminhado</span>',
		self::NAO_ENCAMINHADO		=> '<span class="badge badge badge-success" data-toggle="popover" title="Motivo" data-content="Ainda n�o enviado a nenhuma entrevista." >5 - Nenhum encaminhamento</span>',
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
