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

class App_Model_SerieEstudo extends CoreExt_Enum
{
	const INVALIDO				= 0;
	const SERIE_EF5				= 1;
	const SERIE_EF6				= 2;
	const SERIE_EF7				= 3;
	const SERIE_EF8				= 4;
	const SERIE_EF9				= 5;
	const SERIE_EM1				= 6;
	const SERIE_EM2				= 7;
	const SERIE_EM3				= 8;
	const SERIE_EGRESSO			= 9;
	const SERIE_EJA				= 10;
	const SERIE_CEBEJA			= 11;

	protected $_data = array(
		self::INVALIDO				=> 'S�rie',
		self::SERIE_EF5				=> '5� s�rie',
		self::SERIE_EF6				=> '6� s�rie',
		self::SERIE_EF7				=> '7� s�rie',
		self::SERIE_EF8				=> '8� s�rie',
		self::SERIE_EF9				=> '9� s�rie',
		self::SERIE_EM1				=> '1� ano Ensino M�dio',
		self::SERIE_EM2				=> '2� ano Ensino M�dio',
		self::SERIE_EM3				=> '3� ano Ensino M�dio',
		self::SERIE_EGRESSO			=> 'Egresso',
		self::SERIE_EJA				=> 'EJA',
		self::SERIE_CEBEJA			=> 'CEBEJA'
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
