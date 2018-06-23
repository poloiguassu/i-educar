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

class App_Model_EntrevistaResultado extends CoreExt_Enum
{
	const EM_ANDAMENTO			= 1;
	const ABANDONO				= 2;
	const REPROVADO				= 3;
	const APROVADO_ABANDONO		= 4;
	const APROVADO_ETAPA		= 5;
	const APROVADO_EXTRA		= 6;
	const APROVADO_ESTAGIO		= 7;
	const APROVADO_CONTRATADO	= 8;

	protected $_data = array(
		''							=> 'Informe a situa��o desta entrevista',
		self::EM_ANDAMENTO			=> 'Aguardando entrevista',
		self::ABANDONO				=> 'N�o compareceu',
		self::REPROVADO				=> 'N�o contratado',
		self::APROVADO_ABANDONO		=> 'Aprovado mais abandonou',
		self::APROVADO_ETAPA		=> 'Avan�ou pr�xima etapa',
		self::APROVADO_EXTRA		=> 'Extra',
		self::APROVADO_ESTAGIO		=> 'Est�gio',
		self::APROVADO_CONTRATADO	=> 'Aprovado'
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
