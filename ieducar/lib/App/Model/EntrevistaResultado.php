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
		''							=> 'Informe a situação desta entrevista',
		self::EM_ANDAMENTO			=> 'Aguardando entrevista',
		self::ABANDONO				=> 'Não compareceu',
		self::REPROVADO				=> 'Não contratado',
		self::APROVADO_ABANDONO		=> 'Aprovado mais abandonou',
		self::APROVADO_ETAPA		=> 'Avançou próxima etapa',
		self::APROVADO_EXTRA		=> 'Extra',
		self::APROVADO_ESTAGIO		=> 'Estágio',
		self::APROVADO_CONTRATADO	=> 'Aprovado'
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
