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

class App_Model_VivenciaProfissionalSituacao extends CoreExt_Enum
{
	const EVADIDO			= 0;
	const DESISTENTE		= 1;
	const DESLIGADO			= 2;
	const APTO				= 3;
	const EM_CUMPRIMENTO	= 4;
	const CONCLUIDO			= 5;
	const INSERIDO			= 6;

	protected $_data = array(
		''							=> 'Informe a situação desta entrevista',
		self::EVADIDO				=> 'Evadido',
		self::DESISTENTE			=> 'Desistente',
		self::DESLIGADO				=> 'Desligado',
		self::APTO					=> 'Apto a VPS',
		self::EM_CUMPRIMENTO		=> 'Em cumprimento',
		self::CONCLUIDO				=> 'Concluído (Avaliado)',
		self::INSERIDO				=> 'Inserido'
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
