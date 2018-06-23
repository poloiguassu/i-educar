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

class App_Model_MESES extends CoreExt_Enum
{
	const INVALIDO			= 0;
	const JANEIRO			= 1;
	const FEVEREIRO 		= 2;
	const MAR�O				= 3;
	const ABRIL				= 4;
	const MAIO				= 5;
	const JUNHO				= 6;
	const JULHO				= 7;
	const AGOSTO			= 8;
	const SETEMBRO			= 9;
	const OUTUBRO			= 10;
	const NOVEMBRO			= 11;
	const DEZEMBRO			= 12;

	protected $_data = array(
		self::INVALIDO			=> 'Selecione um m�s',
		self::JANEIRO			=> 'Janeiro',
		self::FEVEREIRO 		=> 'Fevereiro',
		self::MAR�O				=> 'Mar�o',
		self::ABRIL				=> 'Abril',
		self::MAIO				=> 'Maio',
		self::JUNHO				=> 'Junho',
		self::JULHO				=> 'Julho',
		self::AGOSTO			=> 'Agosto',
		self::SETEMBRO			=> 'Setembro',
		self::OUTUBRO			=> 'Outubro',
		self::NOVEMBRO			=> 'Novembro',
		self::DEZEMBRO			=> 'Dezembro'
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
