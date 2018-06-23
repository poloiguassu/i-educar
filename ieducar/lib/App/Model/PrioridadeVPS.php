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

class App_Model_PrioridadeVPS extends CoreExt_Enum
{
	const SEM_ENTREVISTA_0			= 0;
	const TEMPORARIO				= 1;
	const DESISTIU_VPS				= 2;
	const FALTOU_ENTREVISTA			= 3;
	const RECUSOU_VAGA				= 4;
	const ENCAMINHADO				= 5;
	const NAO_ENCAMINHADO			= 6;

	protected $_data = array(
		self::SEM_ENTREVISTA_0		=> '0 - N�o enviar entrevistas',
		self::TEMPORARIO			=> '1 - Final da fila tempor�rio',
		self::DESISTIU_VPS			=> '1 - Desitiu da VPS',
		self::FALTOU_ENTREVISTA		=> '2 - Faltou Entrevista',
		self::RECUSOU_VAGA			=> '3 - Recusou Vaga',
		self::ENCAMINHADO			=> '4 - J� foi encaminhado a entrevistas',
		self::NAO_ENCAMINHADO		=> '5 - Ainda n�o encaminhado',
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
