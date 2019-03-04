<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'intranet/include/pmieducar/clsPmieducarVPSIdioma.inc.php';

class IdiomaController extends ApiCoreController
{
    // search options
    protected function searchOptions()
    {
        return ['namespace' => 'pmieducar', 'labelAttr' => 'nm_idioma', 'idAttr' => 'cod_vps_idioma'];
    }

    protected function formatResourceValue($resource)
    {
        return $this->toUtf8($resource['name'], ['transform' => true]);
    }

    protected function getIdioma()
    {
        $obj = new clsPmieducarVPSIdioma();
        $arrayIdiomas;

        foreach ($obj->listaIdiomasEntrevista($this->getRequest()->id) as $reg) {
            $arrayIdiomas[] = $reg['ref_cod_vps_idioma'];
        }

        return ['idiomas' => $arrayIdiomas];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'idioma-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'idioma')) {
            $this->appendResponse($this->getIdioma());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
