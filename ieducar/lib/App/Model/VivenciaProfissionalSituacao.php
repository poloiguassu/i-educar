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

class App_Model_VivenciaProfissionalSituacao extends CoreExt_Enum
{
	const EVADIDO			= 1;
	const DESISTENTE		= 2;
	const DESLIGADO			= 3;
	const APTO				= 4;
	const EM_CUMPRIMENTO	= 5;
	const CONCLUIDO			= 6;
	const INSERIDO			= 7;

	protected $_data = array(
		''							=> 'Informe a situa��o desta entrevista',
		self::EVADIDO				=> 'Evadido',
		self::DESISTENTE			=> 'Desistente',
		self::DESLIGADO				=> 'Desligado',
		self::APTO					=> 'Apto a VPS',
		self::EM_CUMPRIMENTO		=> 'Em cumprimento',
		self::CONCLUIDO				=> 'Conclu�do (Avaliado)',
		self::INSERIDO				=> 'Inserido'
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
