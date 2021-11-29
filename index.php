<?php

const response = '{
	"tournamentTitle": "xxx xxxxxx",
	"organizerInfo": {
		"name": "xxxxxx"
	},
	"fields": [
		{
			"key": "profileImage",
			"label": "Profile Image"
		},
		{
			"key": "name",
			"label": "Name"
		},
		{
			"key": "placePts",
			"label": "Place Pts"
		},
		{
			"key": "killPts",
			"label": "Kill Pts"
		},
		{
			"key": "totalPts",
			"label": "Total Pts"
		}
	],
	"results": [
		{
			"name": "abc",
			"profileImage":
				"https://firebasestorage.googleapis.com/v0/b/playbookx-project-ce143.appspot.com/o/assets%2Fimages%2Fusers%2FcCImkBCN1IhRwFEpFx2I5q5Mnu42%2Fprofile%2Fuser_profile.jpg?alt=media&token=5c9aee34-c038-448a-a307-c4971649ddca",
			"placePts": 10,
			"killPts": 2,
			"totalPts": 12
		},
		{
			"name": "ghj",
			"profileImage":
				"https://firebasestorage.googleapis.com/v0/b/playbookx-project-ce143.appspot.com/o/assets%2Fimages%2Fusers%2FcCImkBCN1IhRwFEpFx2I5q5Mnu42%2Fprofile%2Fuser_profile.jpg?alt=media&token=5c9aee34-c038-448a-a307-c4971649ddca",
			"placePts": 5,
			"killPts": 8,
			"totalPts": 13
		},
		{
			"name": "xyz",
			"profileImage":
				"https://firebasestorage.googleapis.com/v0/b/playbookx-project-ce143.appspot.com/o/assets%2Fimages%2Fusers%2FcCImkBCN1IhRwFEpFx2I5q5Mnu42%2Fprofile%2Fuser_profile.jpg?alt=media&token=5c9aee34-c038-448a-a307-c4971649ddca",
			"placePts": 20,
			"killPts": 5,
			"totalPts": 25
		},
		{
			"name": "wsx",
			"profileImage":
				"https://firebasestorage.googleapis.com/v0/b/playbookx-project-ce143.appspot.com/o/assets%2Fimages%2Fusers%2FcCImkBCN1IhRwFEpFx2I5q5Mnu42%2Fprofile%2Fuser_profile.jpg?alt=media&token=5c9aee34-c038-448a-a307-c4971649ddca",
			"placePts": 2,
			"killPts": 0,
			"totalPts": 2
		}
	]
}';


class ImageAttribute{


   
public function getJson ()
    {

       $ImgAttributJson= response;

       return $ImgAttributJson;
    }



 public static function JsonDeserialize($ImgAtrjson)
    {
        $arr = json_decode($ImgAtrjson);

        return $arr;
    }

public function gettournamentTitle($ImgAtrjson)
    {
 
        return $ImgAtrjson->{'tournamentTitle'};

     }

public function getorganizerInfo($ImgAtrjson)
    {
 
        return $ImgAtrjson->{'organizerInfo'};

     }

public function getfields($ImgAtrjson)
    {
 
        return $ImgAtrjson->{'fields'};

     }
public function getresults($ImgAtrjson)
    {
 
        return $ImgAtrjson->{'results'};

     }

public function sortresults($ImgAtrArray)
    {
 
       usort($ImgAtrArray, function($a, $b) { //Sort the array using a user defined function
          return $a->totalPts > $b->totalPts ? -1 : 1; //Compare the scores
           });    

           return $ImgAtrArray;

     }

public function exportImg($html, $style){



$html = <<<EOD
$html
EOD;



$google_fonts = "Roboto";

$data = array('html'=>$html,
              'css'=>$css,
              'google_fonts'=>$google_fonts);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://hcti.io/v1/image");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

curl_setopt($ch, CURLOPT_POST, 1);
// Retrieve your user_id and api_key from https://htmlcsstoimage.com/dashboard
curl_setopt($ch, CURLOPT_USERPWD, "3ee0a157-a60b-4053-85e1-0e80a30c4ec2" . ":" . "9945c5ce-da79-4c71-a21e-5c3db8d2fdb3");

$headers = array();
$headers[] = "Content-Type: application/x-www-form-urlencoded";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
  echo 'Error:' . curl_error($ch);
}
curl_close ($ch);
$res = json_decode($result,true);
$remoteURL = $res['url'].".png";

// Force download
header("Content-type: application/x-file-to-save"); 
header("Content-Disposition: attachment; filename=".basename($remoteURL));
ob_end_clean();
readfile($remoteURL);
exit();



}

public function printJson(){

$ImgAttr = self::getJson();

$ImgData = self::JsonDeserialize($ImgAttr);

$tournamentTitle=self::gettournamentTitle($ImgData);
$style2= <<<EOD

<style>
table {
font-family: arial, sans-serif;
border-collapse: collapse;
width: 60%;
margin-left: 39%;
font-size: 14px;
letter-spacing: 2px;
}

td, th {
border: 1px solid #dddddd;
text-align: left;
padding: 4px;
}
</style>
EOD;

$html1=<<<EOD
<style>
table {
font-family: arial, sans-serif;
border-collapse: collapse;
width: 60%;
margin-left: 39%;
font-size: 14px;
letter-spacing: 2px;
}

td, th {
border: 1px solid #dddddd;
text-align: left;
padding: 4px;
}
</style>
<div style="width: 1080px;height: 608px;background-image: url('https://4kwallpapers.com/images/wallpapers/pubg-playerunknowns-battlegrounds-level-3-helmet-yellow-3840x2160-2630.jpg');background-size: contain;">
<div style="max-width: 1080px;text-transform: capitalize;font-size: 33px;font-family: fantasy;letter-spacing: 8px;text-align: right;">
<h1 style="margin-right: 160px;margin-left: 200px;margin-top: 0%;margin-bottom: 1%;">$tournamentTitle</h1>
</div>
<table>
<tr>
<th></th>
EOD;

$field=self::getfields($ImgData);
$countloop=0;
foreach ($field as $value) {
if($countloop==0)
$html1.=<<<EOD
<th style="border-right: unset; padding-right: 0px;"></th>
EOD;
else if($countloop==1)
$html1.= <<<EOD
<th style="border-left: unset;padding-left: 0px;">$value->label</th>

EOD;
else
$html1.= <<<EOD

<th>$value->label</th>

EOD;
$countloop++;
}
$html1.= <<<EOD

</tr>
EOD;


$results=self::getresults($ImgData);
$results=self::sortresults($results);
$countloopforresult=1;
foreach ($results as $value) {
$html1.= <<<EOD

<tr>
<td>$countloopforresult</td>
<td style="border-right: unset;padding-right: 0px;">
<img src='$value->profileImage' height=40 width=40></img>
</td>
<td style="border-left: unset;padding-left: 0px;">$value->name</td>
<td>$value->placePts</td>
<td>$value->killPts</td>
<td>$value->totalPts</td></tr>

EOD;

$countloopforresult++;
}


$html1.= <<<EOD

</table> </div>

EOD;
echo $style2;
echo $html1;
self::exportImg($html1,$style2);
}


} 



//ImageAttribute::printJson();


 if (isset($_GET['export'])) {
   ImageAttribute::printJson();
  }
?>

Hello there!
<a href='index.php?export=true'>Click me to export Image</a>
