<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Geocoder\Exception\Exception;
use Geocoder\Formatter\StringFormatter;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\ProviderAggregator;
use Geocoder\Query\GeocodeQuery;
use Http\Client\Curl\Client;

/**
 * Geocoder component
 */
class GeocoderComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function getCoordinates(string $locality){
        $response = [
            'status' => 'OK',
            'code' => 200,
            'message' => "Dati recuperati correttamente",
            'results' => []
        ];
        $options = [
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_SSL_VERIFYPEER => false,
        ];
        $formatter = new StringFormatter();
        $adapter  = new Client(null, null, $options);
        $geocoder = new ProviderAggregator();
        $userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 11_2_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36
";
        $provider = Nominatim::withOpenStreetMapServer($adapter, $userAgent);
        try {
            $locations = $geocoder
                ->registerProvider($provider)
                ->using('nominatim')
                ->geocodeQuery(GeocodeQuery::create($locality)
                    ->withLocale('it')
                    ->withData("countrycodes", 'IT')
                    ->withLimit(10));
            foreach ($locations as $location) {
                //if(in_array(strtolower($location->getType()),['administrative'])){
                    $desc = $location->getDisplayName();
                    $coordinates = $location->getCoordinates();
                    $response['results'][] = [
                        'geometry' => ['location' => ['lat' => $coordinates->getLatitude(),'lng' => $coordinates->getLongitude()]],
                        'formatted_address' => $desc
                    ];
                //}
            }
        } catch (Exception $e) {
            $response['code'] = $e->getCode();
            $response['status'] = "KO";
            $response['message'] = $e->getMessage();
        } finally {
            return $response;
        }
    }
}
