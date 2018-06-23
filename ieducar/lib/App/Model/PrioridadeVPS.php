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
	const SEM_ENTREVISTA_0			= 0;
	const TEMPORARIO				= 1;
	const DESISTIU_VPS				= 2;
	const FALTOU_ENTREVISTA			= 3;
	const RECUSOU_VAGA				= 4;
	const ENCAMINHADO				= 5;
	const NAO_ENCAMINHADO			= 6;

	protected $_data = array(
		self::SEM_ENTREVISTA_0		=> '0 - Não enviar entrevistas',
		self::TEMPORARIO			=> '1 - Final da fila temporário',
		self::DESISTIU_VPS			=> '1 - Desitiu da VPS',
		self::FALTOU_ENTREVISTA		=> '2 - Faltou Entrevista',
		self::RECUSOU_VAGA			=> '3 - Recusou Vaga',
		self::ENCAMINHADO			=> '4 - Já foi encaminhado a entrevistas',
		self::NAO_ENCAMINHADO		=> '5 - Ainda não encaminhado',
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
