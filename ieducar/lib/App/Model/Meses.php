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

class App_Model_MESES extends CoreExt_Enum
{
	const INVALIDO			= 0;
	const JANEIRO			= 1;
	const FEVEREIRO 		= 2;
	const MARÇO				= 3;
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
		self::INVALIDO			=> 'Selecione um mês',
		self::JANEIRO			=> 'Janeiro',
		self::FEVEREIRO 		=> 'Fevereiro',
		self::MARÇO				=> 'Março',
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
