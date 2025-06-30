<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Property;
use App\Models\RealEstateCompany;

class MigrateController extends Controller
{
    public function migrate_real_estate_companies()
    {
        $json = '[{"field_company_name":"ENGEL AND VOLKERS","title":"ENGEL AND VOLKERS","nid":"15024"},{"field_company_name":"TOURNESOL (MARC BAISSUS)","title":"TOURNESOL (MARC BAISSUS)","nid":"13369"},{"field_company_name":"JOE BOAN","title":"JOE BOAN","nid":"13316"},{"field_company_name":"C AND C (Christelle and Chan)","title":"C AND C (Christelle and Chan)","nid":"13148"},{"field_company_name":"CR PARADISE (Allen Lungo)","title":"CR PARADISE (Allen Lungo)","nid":"13146"},{"field_company_name":"PALM BEACH CRRE","title":"PALM BEACH CRRE","nid":"13145"},{"field_company_name":"EPIC PROPERTIES","title":"EPIC PROPERTIES","nid":"13144"},{"field_company_name":"DALTON GROUP","title":"DALTON GROUP","nid":"13143"},{"field_company_name":"PALMYRA PROP.","title":"PALMYRA PROP.","nid":"12804"},{"field_company_name":"CENTURY 21 FLAMINGO","title":"CENTURY 21 FLAMINGO","nid":"12330"},{"field_company_name":"2CR REAL ESTATE","title":"2CR REAL ESTATE","nid":"12059"},{"field_company_name":"CWB Pacific Realty","title":"CWB PACIFIC REALTY","nid":"12010"},{"field_company_name":"NATIVU","title":"NATIVU","nid":"11559"},{"field_company_name":"HIGH GRADE REAL ESTATE","title":"HIGH GRADE REAL ESTATE","nid":"11380"},{"field_company_name":"CENTURY 21 (MANY OFFICES)","title":"CENTURY 21 PURA VIDA","nid":"11234"},{"field_company_name":"LAT 10 REALTY","title":"LAT 10 REALTY","nid":"11140"},{"field_company_name":"HIDEAWAY CR","title":"HIDEAWAY CR","nid":"11139"},{"field_company_name":"SENDEROS","title":"SENDEROS","nid":"11046"},{"field_company_name":"TROPICAL HOMES","title":"TROPICAL HOMES","nid":"11045"},{"field_company_name":"GUANACASTE LUXURY PROP. ","title":"GUANACASTE LUXURY PROP. ","nid":"10903"},{"field_company_name":"Coldwell Banker Flamingo","title":"CWB FLAMINGO","nid":"10821"},{"field_company_name":"VAQUERA PROPERTIES","title":"VAQUERA PROPERTIES","nid":"10809"},{"field_company_name":"FLAMINGO PROPERTY","title":"FLAMINGO PROPERTY","nid":"10632"},{"field_company_name":"BRUCE MCKILLAN","title":"BRUCE MCKILLAN","nid":"10345"},{"field_company_name":"LANGOSTA REALTY","title":"LANGOSTA REALTY","nid":"10344"},{"field_company_name":"OTHERS","title":"OTHERS","nid":"10343"},{"field_company_name":"ESMERALDA","title":"ESMERALDA","nid":"10334"},{"field_company_name":"HIDDEN","title":"HIDDEN","nid":"862"},{"field_company_name":"TRADE WINDS","title":"TRADE WINDS","nid":"160"},{"field_company_name":"SURFSIDE PR.","title":"SURFSIDE PR.","nid":"158"},{"field_company_name":"SUMMER C.","title":"SUMMER C.","nid":"157"},{"field_company_name":"SOTHEBYS","title":"SOTHEBYS","nid":"156"},{"field_company_name":"SOL REALTY","title":"SOL REALTY","nid":"155"},{"field_company_name":"SABATO","title":"SABATO","nid":"154"},{"field_company_name":"RPM","title":"RPM","nid":"153"},{"field_company_name":"REMAX","title":"REMAX","nid":"152"},{"field_company_name":"CHRISTIES","title":"CHRISTIES","nid":"151"},{"field_company_name":"PICR","title":"PICR","nid":"150"},{"field_company_name":"PALMS","title":"PALMS","nid":"149"},{"field_company_name":"OVERSEAS","title":"OVERSEAS","nid":"148"},{"field_company_name":"MARCUS","title":"MARCUS","nid":"147"},{"field_company_name":"NEST PROPERTIES","title":"NEST PROPERTIES","nid":"146"},{"field_company_name":"KW","title":"KW","nid":"145"},{"field_company_name":"KRAIN","title":"KRAIN","nid":"144"},{"field_company_name":"JOFFROY","title":"JOFFROY","nid":"143"},{"field_company_name":"IMMO","title":"IMMO","nid":"142"},{"field_company_name":"HACIENDA","title":"HACIENDA","nid":"140"},{"field_company_name":"GREG K.","title":"GREG K.","nid":"138"},{"field_company_name":"GRANDE RE","title":"GRANDE RE","nid":"137"},{"field_company_name":"GAB ARAYA","title":"GAB ARAYA","nid":"136"},{"field_company_name":"FIRST CHOICE","title":"FIRST CHOICE","nid":"135"},{"field_company_name":"CONCHAL","title":"CONCHAL","nid":"132"},{"field_company_name":"COMMERCIAL CR","title":"COMMERCIAL CR","nid":"131"},{"field_company_name":"COASTAL","title":"COASTAL","nid":"130"},{"field_company_name":"BUSS","title":"BUSS","nid":"128"},{"field_company_name":"AMIGO","title":"AMIGO","nid":"127"},{"field_company_name":"ALVARO","title":"ALVARO","nid":"126"},{"field_company_name":"ALONSO","title":"ALONSO","nid":"125"},{"field_company_name":"ALLIANCE","title":"ALLIANCE","nid":"124"},{"field_company_name":"ABC Real Estate","title":"ABC","nid":"123"},{"field_company_name":"Tamarindo Real Estate","title":"TAMARINDO REAL ESTATE","nid":"112"},{"field_company_name":"Coldwell Banker Tamarindo","title":"CWB TAMARINDO","nid":"111"},{"field_company_name":"Blue Water Properties","title":"BWP","nid":"110"},{"field_company_name":"Flamingo Beach Realty","title":"FLAMINGO BEACH REALTY","nid":"109"}]';
        $data = json_decode($json);

        DB::table('real_estate_companies')->truncate();

        // Recorrer los datos
        foreach ($data as $item)
        {

            RealEstateCompany::create(['nid' => $item->nid, 'title' => $item->title, 'company_name' => $item->field_company_name]);

        }

    }

    public function migrate_real_estate_properties()
    {
        DB::table('properties')->truncate();


        // URL de tu API (puedes cambiarla por la que necesites)
        $url = 'https://mymls-cr.com/api/v1/json-properties-list';

        // Obtener datos desde la API externa
        $response = Http::get($url);

        // Verificar si fue exitoso
        if ($response->successful())
        {
            $data = $response->json();
            dd($data);
            // Recorrer los datos
            foreach ($data as $item)
            {

                dump('Item:', $item);



                Property::create([
                    'nid'                          => (isset($item['nid']) && !empty($item['nid'])) ? $item['nid'] : null,
                    'property_title'               => (isset($item['title']) && !empty($item['title'])) ? $item['title'] : null,
                    'property_added_date'          => (isset($item['field_property_added_date']) && !empty($item['field_property_added_date'])) ? $item['field_property_added_date'] : null,
                    'property_bathrooms'           => (isset($item['field_property_bathrooms']) && !empty($item['field_property_bathrooms'])) ? $item['field_property_bathrooms'] : 0,
                    'property_bathrooms_inner'     => (isset($item['field_property_bathrooms_inner']) && !empty($item['field_property_bathrooms_inner'])) ? $item['field_property_bathrooms_inner'] : 0,
                    'property_bedrooms'            => (isset($item['field_property_bedrooms']) && !empty($item['field_property_bedrooms'])) ? $item['field_property_bedrooms'] : 0,
                    'property_body'                => (isset($item['body']) && !empty($item['body'])) ? $item['body'] : null,


                    /*//DMR  'building_size'                => (isset($item['field_property_building_size']) && !empty($item['field_property_building_size'])) ? $item['field_property_building_size'] : null,
                    //DMR  'building_size_m2'             => (isset($item['field_property_building_size_m2']) && !empty($item['field_property_building_size_m2'])) ? $item['field_property_building_size_m2'] : null,
                    'geolocation'                  => (isset($item['field_property_geolocation']) && !empty($item['field_property_geolocation'])) ? $item['field_property_geolocation'] : null,
                    'hoa_fee'                      => (isset($item['field_property_hoa_fee']) && !empty($item['field_property_hoa_fee'])) ? $item['field_property_hoa_fee'] : null,
                    'listing_competito'            => (isset($item['field_property_listing_competito']) && !empty($item['field_property_listing_competito'])) ? $item['field_property_listing_competito'] : null,
                    //DMR   'lot_size'                     => (isset($item['field_property_lot_size']) && !empty($item['field_property_lot_size'])) ? $item['field_property_lot_size'] : null,
                    //DMR    'lot_size_m2'                  => (isset($item['field_property_lot_size_m2']) && !empty($item['field_property_lot_size_m2'])) ? $item['field_property_lot_size_m2'] : null,
                    'no_of_floors'                 => (isset($item['field_property_no_of_floors']) && !empty($item['field_property_no_of_floors'])) ? $item['field_property_no_of_floors'] : 0,
                    'notes_to_agents'              => (isset($item['field_property_notes_to_agents']) && !empty($item['field_property_notes_to_agents'])) ? $item['field_property_notes_to_agents'] : null,
                    'on_floor_no'                  => (isset($item['field_property_on_floor_no']) && !empty($item['field_property_on_floor_no'])) ? $item['field_property_on_floor_no'] : 0,
                    'osnid'                        => (isset($item['field_property_osnid']) && !empty($item['field_property_osnid'])) ? $item['field_property_osnid'] : null,
                    //DMR    'price'                        => (isset($item['field_property_price']) && !empty($item['field_property_price'])) ? $item['field_property_price'] : 0,
                    'features'                     => (isset($item['field_property_features']) && !empty($item['field_property_features'])) ? $item['field_property_features'] : null,
                    'location'                     => (isset($item['field_property_location']) && !empty($item['field_property_location'])) ? $item['field_property_location'] : null,
                    'photos'                       => (isset($item['field_property_photos']) && !empty($item['field_property_photos'])) ? $item['field_property_photos'] : null,
                    'status'                       => (isset($item['field_property_status']) && !empty($item['field_property_status'])) ? $item['field_property_status'] : null,
                    'type'                         => (isset($item['field_property_type']) && !empty($item['field_property_type'])) ? $item['field_property_type'] : null,
                    'geolocation_proximity'        => (isset($item['field_property_geolocation_proximity']) && !empty($item['field_property_geolocation_proximity'])) ? $item['field_property_geolocation_proximity'] : null,
                    'geolocation_proximity_form'   => (isset($item['field_property_geolocation_proximity_form']) && !empty($item['field_property_geolocation_proximity_form'])) ? $item['field_property_geolocation_proximity_form'] : null,
                    'sold_reference'               => (isset($item['field_property_sold_reference']) && !empty($item['field_property_sold_reference'])) ? $item['field_property_sold_reference'] : null,
                    'video'                        => (isset($item['field_video']) && !empty($item['field_video'])) ? $item['field_video'] : null,
                    */

                    /*
                    'published',
                    'building_size',
                    'building_size_unit',
                    'building_size_m2',
                    'lot_size',
                    'lot_size_unit',
                    'lot_size_m2',
                    'property_description',

                    'property_location_beach',
                    'property_location_city',
                    'property_location_latitude',
                    'property_location_longitude',

                    'notes_to_agents',
                    'listing_company',
                    'listing_agent',
                    'company_name',
                    'property_link',
                    'list_price',
                    'listing_notes', */





                ]);





                // O retornar algo para confirmar
                // echo $item['nombre']; // ajusta según tus datos
            }




            return response()->json(['status' => 'success', 'total' => count($data)]);
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => 'No se pudo obtener el JSON'], 500);
        }




        dd("a");






    }


    public function start_migration()
    {

        $this->migrate_real_estate_companies();
        $this->migrate_real_estate_properties();





    }
}
