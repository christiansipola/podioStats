<html>
<body>
<?php

if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'vps.kaustik.com'){
    $filePathDir = '/home/zippo/Maildir/.Podio/cur';
} else{
    $filePathDir = 'tmp/.Podio/cur/';
}
$dir = scandir($filePathDir);
$show = array();

$data = array();
foreach($dir as $file){
    $arr = file($filePathDir.$file);
    if(empty($arr)){
        continue;
    }
    $dateFound = false;
    $isMention = false;
    foreach($arr as $row){
        
       
        if(substr($row, 0,5) == 'Date:'){
            $dateFound = true;
            $timeString = substr($row, 6);
            $date = strtotime($timeString);
            $ymd = date('Y-m-d',$date);
            $day['date'] = $ymd;
        }
        
        if(substr($row, 0,8) == 'Subject:' && stristr($row, 'mention')){
            $isMention = true;
        }
        
        
    }
    if(!$dateFound){
        echo "no date found in $file <br />";
    } else {
        if(!isset($data[$ymd])){
            $data[$ymd] = array(
            	'mentions' => 0,
                'notices'  => 0,
            ); 
        }
        $data[$ymd]['notices']++;
        if($isMention){
            $data[$ymd]['mentions']++;
        }
    }
}

?>
<h1>Podiostatistik f&ouml;r Christian Sipola</h1>
<table border="1px">
<thead>
    <tr>
    <th>Datum</th>
    <th>Notices</th>
    <th>Mentions</th>
    </tr>
</thead>
<tbody>
<?php foreach($data as $ymd => $d){ ?>
    <tr>
        <td>
        <?=$ymd ?>
        </td>
        <td>
        <?=$d['notices'] ?>
        </td>
        <td>
        <?=$d['mentions'] ?>
        </td>
    </tr>
<?php } ?>
</tbody>
</table>
</body>
</html>