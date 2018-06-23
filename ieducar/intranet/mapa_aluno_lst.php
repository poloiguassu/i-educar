<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja?								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P?blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja?			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  ?  software livre, voc? pode redistribu?-lo e/ou	 *
	*	modific?-lo sob os termos da Licen?a P?blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers?o 2 da	 *
	*	Licen?a   como  (a  seu  crit?rio)  qualquer  vers?o  mais  nova.	 *
	*																		 *
	*	Este programa  ? distribu?do na expectativa de ser ?til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl?cita de COMERCIALI-	 *
	*	ZA??O  ou  de ADEQUA??O A QUALQUER PROP?SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen?a  P?blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc?  deve  ter  recebido uma c?pia da Licen?a P?blica Geral GNU	 *
	*	junto  com  este  programa. Se n?o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once 'include/clsListagem.inc.php';
require_once ("include/clsBanco.inc.php");
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Messenger.php';
$loader = require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar" );
		$this->processoAp = "624";
		$this->renderMenuSuspenso = false;
		$this->addEstilo('estilo-mapa');
	}
}

class indice extends clsListagem
{
	function RenderHTML()
	{
		return "<div id='mapa' style='height: 800px; width: 100%'></div>";
	}

	public function getAllStudents()
	{
		$alunoGateway = new clsPmieducarAluno();
		$enderecosSemCoordenadas = $alunoGateway->getEnderecosComCoordenadasVazias();

		if (!$enderecosSemCoordenadas) {
			return false;
		}

		foreach ($enderecosSemCoordenadas as $endereco) {
			$geocoder = new \Geocoder\Geocoder();
			$adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();
			
			$geocoder->registerProviders(array(
		    	new \Geocoder\Provider\GoogleMapsProvider($adapter)
			));

			try {
			    $geotools = new \League\Geotools\Geotools();
			    $result = $geotools->batch($geocoder)
			    				   ->geocode(array($endereco['logradouro'] . ', ' . $endereco['numero'] . ' ' . $endereco['cidade']))
			    				   ->parallel();
			} catch (\Exception $e) {
			    die($e->getMessage());
			}

			if (empty($result[0]['latitude']) && empty($result[0]['longitude'])) {
				continue;
			}

			$updateResult = $alunoGateway->updateCoordenadas($endereco['idpes'], $result[0]);
		}

		$enderecosComCoordenadas = $alunoGateway->getEnderecosComCoordenadasPreenchidas();
		return $enderecosComCoordenadas;
	}

	public function getAllStudentsWithCoordinates()
	{
		$alunoGateway = new clsPmieducarAluno();
		$enderecosComCoordenadas = $alunoGateway->getEnderecosComCoordenadasPreenchidas();

		$filePath = dirname(__DIR__) . '/map/pontos.json';
		$handle = fopen($filePath, "w");
		fwrite($handle, json_encode($enderecosComCoordenadas));
		fclose($handle);

		return json_encode($enderecosComCoordenadas);
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$students = $miolo->getAllStudentsWithCoordinates();

$pagina->addForm($miolo);
$pagina->MakeAll();
?>


<script src="https://code.jquery.com/jquery-3.0.0.min.js" integrity="sha256-JmvOoLtYsmqlsWxa7mDSLMwa6dZ9rrIdtrrVYRnDRH0=" crossorigin="anonymous"></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js"></script>
<script type="text/javascript" src="../map/infobox.js"></script>
<script type="text/javascript" src="../map/markerclustererplus/src/markerclusterer.js"></script>
<script type="text/javascript">
	var map;
	var idInfoBoxAberto;
	var infoBox = [];
	var markers = [];

	function initialize() {	
		var latlng = new google.maps.LatLng(-18.8800397, -47.05878999999999);
		
	    var options = {
	        zoom: 5,
			center: latlng,
	        mapTypeId: google.maps.MapTypeId.ROADMAP
	    };

	    map = new google.maps.Map(document.getElementById("mapa"), options);
	}

	initialize();

	function abrirInfoBox(id, marker) {
		if (typeof(idInfoBoxAberto) == 'number' && typeof(infoBox[idInfoBoxAberto]) == 'object') {
			infoBox[idInfoBoxAberto].close();
		}

		infoBox[id].open(map, marker);
		idInfoBoxAberto = id;
	}

	function carregarPontos() {
		
		$.getJSON('../map/pontos.json', function(pontos) {
			
			var latlngbounds = new google.maps.LatLngBounds();
			
			$.each(pontos, function(index, ponto) {
				
				var marker = new google.maps.Marker({
					position: new google.maps.LatLng(ponto.lat, ponto.long),
					title: "Meu ponto personalizado! :-D",
					icon: 'https://raw.githubusercontent.com/rodolfoprr/GoogleMapsAPIv3MapaPersonalizado/master/img/marcador.png'
				});
				
				var myOptions = {
					content: "<p>" + ponto.logradouro + ", " + ponto.numero + "</p>",
					pixelOffset: new google.maps.Size(-150, 0)
	        	};

				infoBox[ponto.idpes] = new InfoBox(myOptions);
				infoBox[ponto.idpes].marker = marker;
				
				infoBox[ponto.idpes].listener = google.maps.event.addListener(marker, 'click', function (e) {
					abrirInfoBox(ponto.idpes, marker);
				});
				
				markers.push(marker);
				
				latlngbounds.extend(marker.position);
				
			});
			
			var markerCluster = new MarkerClusterer(map, markers);
			
			map.fitBounds(latlngbounds);
			
		});
		
	}

	carregarPontos();
</script>
