<?php
require('SplClassLoader.php');
$classLoader = new SplClassLoader('PodioStats', '.');
$classLoader->register();
$maildirParser = new PodioStats\MaildirParser();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="sv">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Podiostatistics for Christian Sipola</title>
    <script src="js/Chart.js"></script>
</head>
<body>
    <h1>Podiostatistics for Christian Sipola</h1>
    <canvas id="myChart" width="800" height="400"></canvas>
    <table border="1px">
    <thead>
        <tr>
        <th>Day</th>
        <th>Date</th>
        <th>Notices</th>
        <th>Mentions</th>
        <th>Mentions %</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    /* @var $day PodioStats\Day */  
    foreach($maildirParser->getData() as $ymd => $day){ 
    ?>
        <tr>
            <td>
            <?=$day->getDate()->format('D') ?>
            </td>
            <td>
            <?=$day->getDate()->format('y-m-d') ?>
            </td>
            <td>
            <?=$day->getNotices() ?>
            </td>
            <td>
            <?=$day->getMentions() ?>
            </td>
            <td>
            <?=$day->getPercentMentions() ?> %
            </td>
        </tr>
    <?php } ?>
    </tbody>
    </table>
    
    
    
    <div>Not readable:</div>
    <?=implode(', ', $maildirParser->getNotReadable()) ?>
    <script type="text/javascript">
    
    var data = <?=\PodioStats\Day::getArrayAsLineChartJson($maildirParser->getData()) ?>;
	var options = { datasetFill : true};
    //Get the context of the canvas element we want to select
    var ctx = document.getElementById("myChart").getContext("2d");
    var myNewChart = new Chart(ctx).Line(data,options);
    </script>
</body>
</html>