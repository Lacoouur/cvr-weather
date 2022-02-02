<?php
   function cvrapi($country, $vat) {
    // Strip all other characters than numbers
    $vat = preg_replace('/[^0-9]/', '', $vat);
  
    if(empty($vat)) {
      return('Intet CVR-nummer');
    } else {
      // cURL open connection
      $chCVR = curl_init();
      // cURL options
      curl_setopt($chCVR, CURLOPT_URL, 'http://cvrapi.dk/api?country=' . $country . '&search=' . $vat );
      curl_setopt($chCVR, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($chCVR, CURLOPT_USERAGENT, 'CVRAPI - CVR Projekt - Christian la Cour - +45 20928074');
      // Parse result
      $result = curl_exec($chCVR);
      $decoded_result = json_decode($result,true);
      // Checks if the api is working
      if (!empty($decoded_result['error'])) { 
        echo "error in API";
        return 'Fejl'; 
      }
      // Decode result and make city variable
      $city = $decoded_result['city'];
      if(empty($city)) {
        return'no city found?';
      } 
      else {
        $ch = curl_init();
        // cURL options
        curl_setopt($ch, CURLOPT_URL, 'http://vejr.eu/api.php?location='. urlencode($city) .'&degree=C');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'CVRAPI - CVR Projekt - Christian la Cour - +45 20928074');
        // Parse result
        $weatherresult = curl_exec($ch);
        //close connections
        curl_close($ch);
        curl_close($chCVR);
        return json_decode($weatherresult, true);
      }
    }
  }
?>
<!doctype html>
<html lang="en">
  <link href="styling/StyleSheet.css" rel="stylesheet">
  <head >
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>CVR Weather Search</title>
  </head>
  <body>
    <div class="container">
        <h1>CVR Weather Search</h1>
        <form action="" method="GET">
            <p><label for="CVR">Input CVR</label></p>
            <p><input type="text" id="vat"name="vat"placeholder="87654321"></p>
            <p><label for="country">Choose Country:</label>
            <select name="country" id="country">
                <option value="dk">dk</option>
                <option value="no">no</option>
            </select>
            <button type="search" name="search" style="height:30px; width:80px;" >Search</button>
            <?php 
              $vat = $_GET['vat']; $country = $_GET['country'];
              $weather = cvrapi($country,$vat);
            ?>
        </form>
    </div>
    <div class="weather">
      <table class="table">
      <tr>
          <th>
            <?php print "Weather for:" ?>
          </th>
          <th>
            <?php echo " Temperature <br>";?>
          </th>
          <th>
            <?php echo  " Sky description <br>";?>
          </th>
          <th>
            <?php echo  " Humidity <br>";?>
          </th>
          <th>
            <?php echo  " Wind <br>";?>
          </th>
        </tr>
        <tr>
        <th>
        <?php 
        echo $weather['LocationName']." <br> ";
        $currentData = $weather["CurrentData"];
        ?>
        </th>
        <th>
        <?php echo $currentData['temperature']. " degree <br>";?>
        </th>
        <th>
        <?php echo $currentData['skyText']. " <br>";?>
        </th>
        <th>
        <?php echo $currentData['humidity'] . " % humidty <br>";?>
        </th>
        <th>
        <?php echo $currentData['windText']. "<br>";?>
        </th>
        </tr>
      <?php  ?>
      </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </body>
</html>