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
		self::INVALIDO				=> 'Série',
		self::SERIE_EF5				=> '5ª série',
		self::SERIE_EF6				=> '6ª série',
		self::SERIE_EF7				=> '7ª série',
		self::SERIE_EF8				=> '8ª série',
		self::SERIE_EF9				=> '9ª série',
		self::SERIE_EM1				=> '1º ano Ensino Médio',
		self::SERIE_EM2				=> '2º ano Ensino Médio',
		self::SERIE_EM3				=> '3º ano Ensino Médio',
		self::SERIE_EGRESSO			=> 'Egresso',
		self::SERIE_EJA				=> 'EJA',
		self::SERIE_CEBEJA			=> 'CEBEJA'
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
