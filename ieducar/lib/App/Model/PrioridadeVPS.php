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

class App_Model_PrioridadeVPS extends CoreExt_Enum
{
	const SEM_ENTREVISTA_0	= 0;
	const MUITO_BAIXA_1		= 1;
	const BAIXA_2			= 2;
	const BAIXA_3			= 3;
	const BAIXA_4			= 4;
	const MEDIA_5			= 5;
	const MEDIA_6			= 6;
	const MEDIA_7			= 7;
	const ALTA_8			= 8;
	const ALTA_9			= 9;
	const MUITO_ALTA_10		= 10;

	protected $_data = array(
		self::SEM_ENTREVISTA_0		=> '0 - Não enviar entrevistas',
		self::MUITO_BAIXA_1			=> '1 - Muito Baixa',
		self::BAIXA_2				=> '2 - Baixa',
		self::BAIXA_3				=> '3 - Baixa',
		self::BAIXA_4				=> '4 - Baixa',
		self::MEDIA_5				=> '5 - Média',
		self::MEDIA_6				=> '6 - Média',
		self::MEDIA_7				=> '7 - Média',
		self::ALTA_8				=> '8 - Alta',
		self::ALTA_9				=> '9 - Alta (Participou poucas entrevistas)',
		self::MUITO_ALTA_10			=> '10 - Alta (Não foi enviado entrevista)'
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
