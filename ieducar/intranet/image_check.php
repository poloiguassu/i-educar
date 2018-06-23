<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu�do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl�cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Api
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o ?
 * @version     $Id$
 */

class PictureController {

    var $imageFile;
    var $errorMessage;
    var $maxWidth;
    var $maxHeight;
    var $maxSize;
    var $suportedExtensions;
    var $imageName;

    function __construct($imageFile, $maxWidth = NULL, $maxHeight = NULL, $maxSize = NULL,
                             $suportedExtensions = NULL){

        
       $this->imageFile = $imageFile;
       

        if ($maxWidth!=null)
            $this->maxWidth = $maxWidth;
        else
            $this->maxWidth = 500;

        if ($maxHeight!=null)
            $this->maxHeight = $maxHeight;
        else
            $this->maxHeight = 500;

        if ($maxSize!=null)
            $this->maxSize = $maxSize;
        else
            $this->maxSize = 4096*1024;

        if ($suportedExtensions != null)
            $this->suportedExtensions = $suportedExtensions;
        else
            $this->suportedExtensions = array('jpeg','jpg','png');
    }

    /**
    * Envia imagem caso seja v�lida e retorna caminho
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    * @return String
    */
	function sendPicture($imageName){

		$this->imageName = $imageName;
		$tmp = $this->imageFile["tmp_name"];
		$tmp_extension = $this->getExtension($this->imageFile["name"]);

		$actual_image_name = './arquivos/fotosPessoa/' . $imageName . '.' . $tmp_extension; 
		if(move_uploaded_file($tmp, $actual_image_name))
		{
			return $actual_image_name;
		}
		else{
			echo "<script type='text/javascript'>
			alert('".$actual_image_name." foi criada com sucesso..');
			</script>";
			$this->errorMessage = "Ocorreu um erro no servidor ao enviar foto. Tente novamente.";
			return '';
		}
	}

    /**
    * Verifica se a imagem � v�lida
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    * @return boolean
    */
    function validatePicture(){

        $msg='';

        $name = $this->imageFile["name"];
        $size = $this->imageFile["size"];
        $ext = $this->getExtension($name);


        if(strlen($name) > 0)
        {
            // File format validation
            if(in_array($ext,$this->suportedExtensions))
            {
                // File size validation
                if($size < $this->maxSize){
                    return true;   
                }
                else{
                    $this->errorMessage = "N�o � permitido fotos com mais de 4Mb.";
                    return false;
                }
            }
            else{
                $this->errorMessage = "O cadastro n&atilde;o pode ser realizado, a foto possui um formato diferente daqueles permitidos.";
                return false;
            }
        }
        else{
            $this->errorMessage = "Selecione uma imagem."; 
            return false;
        }
        $this->errorMessage = "Imagem inv�lida."; 
        return false;
    }
    /**
    * Retorna a mensagem de erro
    *
    * @author Lucas Schmoeller da Silva - lucas@portabilis.com
    * @return String
    */
    function getErrorMessage(){
        return $this->errorMessage;
    }


    function getExtension($name) 
    {
        $i = strrpos($name,".");
        if (!$i)
          return "";
        $l = strlen($name) - $i;
        $ext = substr($name,$i+1,$l);

        return strtolower($ext);
    }
}

?>
