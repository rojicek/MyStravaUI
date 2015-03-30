<!DOCTYPE html>
<html> 
<head> 

<title>Eddington number</title>
<meta charset="UTF-8">

<link rel="stylesheet" type="text/css" href="ed.css">



    </head>

<body>
<?php


include 'enfce.php';


session_start();
$mileCoef = 1.609344;
$token =  $_SESSION['stravatoken'];
//echo "token: " .    $token . "<br>";

if (is_null($token)) 
{
       echo "<p>To learn your <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> &nbsp; <a href=\"auth.php\"><img src=\"LogInWithStrava.png\" width=\"159\" height=\"31\" align=\"middle\" alt=\"Log in with Strava\"></a></p>";          
}   
 
 else
 {//login
//jsem prihlaseny, muzu nacist aktivity 
 
 
 $url = "https://www.strava.com/api/v3/athlete?access_token=" . $token;
 $result = file_get_contents($url);
 $athlete = json_decode ($result, true);


 
 //get rides
 
 $page = 1;
 
 while (true)
 {
 $url = "https://www.strava.com/api/v3/activities?per_page=200&page=".$page."&access_token=" . $token;
 $result = file_get_contents($url);
 $rides = json_decode ($result, true);
 
//$dnesek = strtotime(date("Y-m-d"));
$predrokem = strtotime(date("Y-m-d", strtotime(date("Y-m-d") . " - 1 year")));
 
 if (empty($rides))
 {
  break; //zadne dalsi jizdy
 }
 

 //create array fof lifes (all live / last year)
 //metric and statute -> 4 arrays
 //can be more pages (max 200 rides per page by Strava)
  $ix = 0;
  $ixLife = 0;
 foreach ($rides as $oneride)
  {
     
       $rideDate =  strtotime($oneride["start_date_local"]);
       
       //last year
       if (($rideDate >= $predrokem )   && (strtolower($oneride["type"]) == 'ride'))
       {
        $distances[$ix] =  floor($oneride["distance"]/1000);   
        $distancesStatute[$ix] = floor($oneride["distance"] / $mileCoef / 1000);     
        $ix = $ix + 1;
      } 
      
      //all time
      if ((strtolower($oneride["type"]) == 'ride'))
       {                 
        $distancesLife[$ixLife] =  floor($oneride["distance"]/1000);
        $distancesLifeStatute[$ixLife] = floor($oneride["distance"] / $mileCoef / 1000);       
        $ixLife = $ixLife + 1;
      } 
      
  }
  
  $page=$page + 1;
}
 
 
  $edn  = GetEN($distances);
  $ednStatute  = GetEN($distancesStatute);
  $ednLife  = GetEN($distancesLife);
  $ednLifeStatute  = GetEN($distancesLifeStatute);
 
 
 
 
 
 echo  "Your last year metric <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $edn . "</b>. <br>";
 echo "<i>That means you rode " . $edn . "km or more at least " . $edn . " times in last year as of today.</i><p>"  ;

  echo  "Your last year real <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $ednStatute . "</b>. <br>";
 echo "<i>That means you rode " . $ednStatute . " miles or more at least " . $ednStatute . " times in last year as of today.</i><p>"  ;


 echo  "Your lifetime metric <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $ednLife . "</b>. <br>";
 echo "<i>That means you rode " . $ednLife . "km or more at least " . $ednLife . " times since your started recording on Strava.</i><p>"  ;
 
  echo  "Your lifetime real <a href=\"http://triathlete-europe.competitor.com/2011/04/18/measuring-bike-miles-eddington-number\">Eddington number</a> is <b>". $ednLifeStatute . "</b>. <br>";
 echo "<i>That means you rode " . $ednLifeStatute . " miles or more at least " . $ednLifeStatute . " times since your started recording on Strava.</i><p>"  ;
 
 
   }//konec if autorizace
?>
          </body>
