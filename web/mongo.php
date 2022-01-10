<?php
// composer autoload
require_once __DIR__ . '\vendor\autoload.php';
// attach class	
use MongoDB\Client as Mongo;
// params for query database
$filter = [];
$options = ['sort' => ['_id' => -1]]; // descending sort for last element
// connect to database
$mongo = new Mongo("mongodb://your_db");
$collection = $mongo->meteo->data;
$bson = $collection->findOne($filter,$options);
// lets convert mongodb bson to json
$json = $bson->jsonSerialize();
// get all values to variables
$timestamp = $json->DateTime;
$temperature = $json->air_temp;
$dewpoint = $json->dew_point;
$relativehumidity = $json->humidity_rel;
$absolutehumidity = $json->humidity_abs;
$humiditymixratio = $json->humidity_mixratio;
$enthalpy = $json->spec_enthalpy;
$airdensity = $json->air_density;
$absairpressure = $json->pressure_abs;
$precipitationquantityabs = $json->precip_abs;
$precipitationtype = $json->precip_code;
$precipitationintensity = $json->precip_intensity;
$precipitation = $json->precip_name;
$globalradact = $json->globalrad_act;
$globalradmin = $json->globalrad_min;
$globalradmax = $json->globalrad_max;
$globalradmavg = $json->globalrad_avg;
$windspeedact = $json->windspeed_act;
$windspeedmin = $json->windspeed_min;
$windspeedmax = $json->windspeed_max;
$windspeedavg = $json->windspeed_avg;
$winddirectionact = $json->winddir_act;
$winddirectionmin = $json->winddir_min;
$winddirectionmax = $json->winddir_max;
$windrose = $json->windrose;
$groundtempsurf = $json->ground_temp_surface;
$groundtemp_0_2 = $json->ground_temp_0_2;
$groundtemp_0_4 = $json->ground_temp_0_4;
$groundtemp_0_8 = $json->ground_temp_0_8;
$groundtemp_1_2 = $json->ground_temp_1_2;
$groundtemp_3_2 = $json->ground_temp_3_2;

?>
