<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Vlad Popov">
    <title>Physgeo MeteoStation</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/pricing/">
	<link rel="icon" sizes="16x16 32x32 57x57 64x64 120x120 144x144" href="./favicon.png">

    

    <!-- Bootstrap core CSS -->
<link href="../css/bootstrap.min.css" rel="stylesheet">
<link href="css/anychart-ui.min.css" rel="stylesheet" type="text/css">
<link href="css/anychart-font.min.css" rel="stylesheet" type="text/css">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
	<style>
#container {
  width: 50%;
  height: 50%;
  margin: auto;
  padding: 0;
}</style>

    
    <!-- Custom styles for this template -->
    <link href="pricing.css" rel="stylesheet">
<?php
// composer autoload
require_once __DIR__ . '\vendor\autoload.php';
// attach class	
use MongoDB\Client as Mongo;
// params for query database
$filter = [];
$options = ['sort' => ['_id' => -1]]; // descending sort for last element
// connect to database
$mongo = new Mongo("mongodb://127.0.0.1:7443");
$collection = $mongo->meteo->data;
$bson = $collection->findOne($filter,$options);
// lets convert mongodb bson to json
$json = $bson->jsonSerialize();
// get all values to variables
$timestamp = $json->DateTime;
$timestampUTC = $json->DateTimeUTC;
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
$globalradavg = $json->globalrad_avg;
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
$groundtemp_2_4 = $json->ground_temp_2_4;

if ($precipitationtype == 0) { // Конвертируем код типа осадков на украинском
    $precipitation = "Немає";
} elseif ($precipitationtype == 40) {
    $precipitation = "Не визначено";
} elseif ($precipitationtype == 60) {
    $precipitation = "Дощ";
} elseif ($precipitationtype == 70) {
    $precipitation = "Сніг";
} elseif ($precipitationtype == 67) {
    $precipitation = "Крижаний дощ";
} elseif ($precipitationtype == 69) {
    $precipitation = "Крупа";
} elseif ($precipitationtype == 90) {
    $precipitation = "Град";
}

?>
<div id="dom-target1" style="display: none;">
<?php
  echo htmlspecialchars($winddirectionact); // Передаём значения ветра в DOM
?>
</div>
<div id="dom-target2" style="display: none;">
<?php
  echo htmlspecialchars($windspeedact); // Передаём значения ветра в DOM
?>
</div>
<div id="dom-target3" style="display: none;">
<?php
  echo htmlspecialchars($windrose); // Передаём значения ветра в DOM
?>
</div>
<script src="js/anychart-base.min.js"></script>
  <script src="js/anychart-ui.min.js"></script>
  <script src="js/anychart-exports.min.js"></script>
  <script src="js/anychart-circular-gauge.min.js"></script>
  <script type="text/javascript">anychart.onDocumentReady(function () {
  var gauge = anychart.gauges.circular();
  var div = document.getElementById("dom-target1");
  var dir = div.textContent;
  var div = document.getElementById("dom-target2");
  var speed = div.textContent;
  var div = document.getElementById("dom-target3");
  var rose = div.textContent;
  
  gauge
    .fill('#fff')
    .stroke(null)
    .padding(0)
    .margin(30)
    .startAngle(0)
    .sweepAngle(360);

  gauge
    .axis()
    .labels()
    .padding(3)
    .position('outside')
    .format(function () {
          if (this.value === 0) return 'N';
		  if (this.value === 22.5) return 'NNE';
		  if (this.value === 45) return 'NE';
		  if (this.value === 67.5) return 'ENE';
          if (this.value === 90) return 'E';
		  if (this.value === 112.5) return 'ESE';
		  if (this.value === 135) return 'SE';
		  if (this.value === 157.5) return 'SSE';
          if (this.value === 180) return 'S';
		  if (this.value === 202.5) return 'SSW';
		  if (this.value === 225) return 'SW';
		  if (this.value === 247.5) return 'WSW';
          if (this.value === 270) return 'W';
		  if (this.value === 292.5) return 'WNW';
		  if (this.value === 315) return 'NW';
		  if (this.value === 337.5) return 'NNW';
          return this.value;
        });

  gauge.data([dir, speed]);

  gauge
    .axis()
    .scale()
    .minimum(0)
    .maximum(360)
    .ticks({ interval: 22.5 })
    .minorTicks({ interval: 10 });

  gauge
    .axis()
    .fill('#7c868e')
    .startAngle(0)
    .sweepAngle(360)
    .width(1)
    .ticks({
      type: 'line',
      fill: '#7c868e',
      length: 4,
      position: 'outside'
    });

  gauge
    .axis(1)
    .fill('#7c868e')
    .startAngle(270)
    .radius(40)
    .sweepAngle(180)
    .width(1)
    .ticks({
      type: 'line',
      fill: '#7c868e',
      length: 4,
      position: 'outside'
    });

  gauge
    .axis(1)
    .labels()
    .padding(3)
    .position('outside')
    .format('{%Value} m/s');

  gauge
    .axis(1)
    .scale()
    .minimum(0)
    .maximum(20)
    .ticks({ interval: 5 })
    .minorTicks({ interval: 1 });

  gauge.title().padding(0).margin([0, 0, 10, 0]);

  gauge
    .marker()
    .fill('#cd181f')
	.type('triangle-down')
    .stroke(null)
    .size('15%')
    .zIndex(120)
    .radius('105%');

  gauge
    .needle()
    .fill('#1976d2')
    .stroke(null)
    .axisIndex(1)
    .startRadius('6%')
    .endRadius('38%')
    .startWidth('2%')
    .middleWidth(null)
    .endWidth('0');

  gauge.cap().radius('4%').fill('#1976d2').enabled(true).stroke(null);

  var bigTooltipTitleSettings = {
    fontFamily: '\'Verdana\', Helvetica, Arial, sans-serif',
    fontWeight: 'normal',
    fontSize: '12px',
    hAlign: 'left',
    fontColor: '#212121'
  };

  gauge
    .label()
    .text(
      '<span style="color: #64B5F6; font-size: 13px">Напрям вітру: </span>' +
      '<span style="color: #5AA3DD; font-size: 15px">' +
      rose +
      '</span><br>' +
      '<span style="color: #1976d2; font-size: 13px">Швидкість:</span> ' +
      '<span style="color: #166ABD; font-size: 15px">' +
      speed +
      ' м/с</span>'
    )
    .useHtml(true)
    .textSettings(bigTooltipTitleSettings);
  gauge
    .label()
    .hAlign('center')
    .anchor('center-top')
    .offsetY(-20)
    .padding(15, 20)
    .background({
      fill: '#fff',
      stroke: {
        thickness: 1,
        color: '#E0F0FD'
      }
    });

  // set container id for the chart
  gauge.container('container');

  // initiate chart drawing
  gauge.draw();
});</script>
  </head>
  <body>
    
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="check" viewBox="0 0 16 16">
    <title>Check</title>
    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
  </symbol>
</svg>

<div class="container py-3">
  <header>
    <div class="d-flex flex-column flex-md-row align-items-center pb-3 mb-4 border-bottom">
      <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
        <span class="fs-4">Physgeo MeteoStation</span>
      </a>

      <nav class="d-inline-flex mt-2 mt-md-0 ms-md-auto">
        <a class="me-3 py-2 text-dark text-decoration-none" href="https://physgeo.univer.kharkov.ua/">Кафедра фізичної географії та картографії</a>
      </nav>
    </div>

    <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
      <h1 class="display-4 fw-normal">ННГБ "Гайдари"</h1>
      <p class="fs-5 text-muted">Оновлення метеоінформації на сайті відбувається кожні 10 хвилин.</p>
	  <p class="fs-5 text-muted">Поточний локальний час вимірів: <b>
	  <?php
	  echo $timestamp;
	  ?>
	  </b></p>
	  <p class="fs-5 text-muted">UTC: <b>
	  <?php
	  echo $timestampUTC;
	  ?>
	  </b></p>
    </div>
  </header>

  <main>
    <div class="row row-cols-1 row-cols-md-4 mb-4 text-center">
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Температура повітря</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $temperature;
			?>
			<small class="text-muted fw-light">°C</small></h1>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Точка<br>роси</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $dewpoint;
			?>
			<small class="text-muted fw-light">°C</small></h1>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Атмосферний тиск</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $absairpressure;
			?><small class="text-muted fw-light"> <small><small>hPa</small></small></small></h1>
          </div>
        </div>
      </div>
	  <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Щільність повітря</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $airdensity;
			?>
			<small class="text-muted fw-light"><small><small>kg/m³</small></small></small></h1>
          </div>
        </div>
      </div>
    </div>
    <div class="row row-cols-1 row-cols-md-4 mb-4 text-center">
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3 text-white bg-primary border-primary">
            <h4 class="my-0 fw-normal">Відносна вологість</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $relativehumidity;
			?>
			<small class="text-muted fw-light">%</small></h1>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3 text-white bg-primary border-primary">
            <h4 class="my-0 fw-normal">Абсолютна вологість</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $absolutehumidity;
			?>
			<small class="text-muted fw-light">g/m³</small></h1>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3 text-white bg-primary border-primary">
            <h4 class="my-0 fw-normal">Коефіцієнт змішування</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $humiditymixratio;
			?><small class="text-muted fw-light"> g/kg</small></h1>
          </div>
        </div>
      </div>
	  <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3 text-white bg-primary border-primary">
            <h4 class="my-0 fw-normal">Питома ентальпія</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $enthalpy;
			?><small class="text-muted fw-light"> <small><small>kJ/kg</small></small></small></h1>
          </div>
        </div>
      </div>
    </div>
	<div class="row row-cols-1 row-cols-md-4 mb-4 text-center">
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Швидкість вітру</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $windspeedact;
			?>
			<small class="text-muted fw-light"> m/s</small></h1>
			<p align="center">Поточна</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Швидкість вітру</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $windspeedavg;
			?>
			<small class="text-muted fw-light"> m/s</small></h1>
			<p align="center">Середня (10 хв)</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Швидкість вітру</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $windspeedmin;
			?><small class="text-muted fw-light"> m/s</small></h1>
			<p align="center">Мінімальна (10 хв)</p>
          </div>
        </div>
      </div>
	  <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Швидкість вітру</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $windspeedmax;
			?><small class="text-muted fw-light"> m/s</small></h1>
			<p align="center">Максимальна (10 хв)</p>
          </div>
        </div>
      </div>
    </div>
	<div class="row row-cols-1 row-cols-md-4 mb-4 text-center">
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Азимут вітру</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $winddirectionact;
			?>
			<small class="text-muted fw-light">°</small></h1>
			<p align="center">Поточний</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Азимут вітру</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $winddirectionmin;
			?>
			<small class="text-muted fw-light">°</small></h1>
			<p align="center">Мінімальний (10 хв)</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Азимут вітру</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $winddirectionmax;
			?><small class="text-muted fw-light">°</small></h1>
			<p align="center">Максимальний (10 хв)</p>
          </div>
        </div>
      </div>
	  <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Напрям вітру</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $windrose;
			?><small class="text-muted fw-light"></small></h1>
			<p align="center">За 16 променями</p>
          </div>
        </div>
      </div>
    </div>
	
	<h2 class="display-6 text-center mb-4">Вітер</h2>
	
	<div id="container"></div>

    <h2 class="display-6 text-center mb-4">Ґрунтові термометри</h2>

    <div class="table-responsive">
      <table class="table text-center">
        <thead>
          <tr>
			<th style="width: 16%;">Поверхня</th>
            <th style="width: 16%;">0,2 м</th>
            <th style="width: 16%;">0,4 м</th>
            <th style="width: 16%;">0,8 м</th>
            <th style="width: 16%;">1,2 м</th>
            <th style="width: 16%;">2,4 м</th>
          </tr>
        </thead>
        <tbody>
          <tr>
			<td><big><?php echo $groundtempsurf." °C"; ?></big></td>
            <td><big><?php echo $groundtemp_0_2." °C"; ?></big></td>
            <td><big><?php echo $groundtemp_0_4." °C"; ?></big></td>
			<td><big><?php echo $groundtemp_0_8." °C"; ?></big></td>
			<td><big><?php echo $groundtemp_1_2." °C"; ?></big></td>
			<td><big><?php echo $groundtemp_2_4." °C"; ?></big></td>
          </tr>
        </tbody>
      </table>
    </div>
	<div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3 text-white bg-primary border-primary">
            <h4 class="my-0 fw-normal">Кількість опадів</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $precipitationquantityabs;
			?>
			<small class="text-muted fw-light"> мм</small></h1>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3 text-white bg-primary border-primary">
            <h4 class="my-0 fw-normal">Тип опадів</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $precipitation;
			?>
			<small class="text-muted fw-light"></small></h1>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm border-primary">
          <div class="card-header py-3 text-white bg-primary border-primary">
            <h4 class="my-0 fw-normal">Інтенсивність опадів</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $precipitationintensity;
			?><small class="text-muted fw-light"> мм/год</small></h1>
          </div>
        </div>
      </div>
    </div>
    <div class="row row-cols-1 row-cols-md-4 mb-4 text-center">
	  <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Сонячна радіація</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $globalradact;
			?>
			<small class="text-muted fw-light"> W/m²</small></h1>
			<p align="center">Поточна</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Сонячна радіація</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $globalradavg;
			?>
			<small class="text-muted fw-light"> W/m²</small></h1>
			<p align="center">Середня (10 хв)</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Сонячна радіація</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $globalradmax;
			?>
			<small class="text-muted fw-light"> W/m²</small></h1>
			<p align="center">Максимальна (10 хв)</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h4 class="my-0 fw-normal">Сонячна радіація</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">
			<?php
			echo $globalradmin;
			?>
			<small class="text-muted fw-light"> W/m²</small></h1>
			<p align="center">Мінімальнa (10 хв)</p>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="pt-4 my-md-5 pt-md-5 border-top">
    <div class="row">
      <div class="col-12 col-md">
        <small class="d-block mb-3 text-muted">&copy; 2021 Vladyslav Popov</small>
      </div>
    </div>
  </footer>
</div>


    
  </body>
</html>