<?php
require('SplClassLoader.php');
$classLoader = new SplClassLoader('PodioStats', '.');
$classLoader->register();
$maildirParser = new PodioStats\MaildirParser();
?>
<!DOCTYPE html> 
<html lang="sv">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Podiostatistics for Christian Sipola</title>
    <script src="js/jquery/jquery.1.7.2.min.js"></script>
    <script src="js/jquery-plugins/jquery.dataTables.js"></script>
    <script src="js/Chart.js"></script>
    <link href="css/demo_table.css" rel="stylesheet">
</head>
<body>
    <h1>Podiostatistics for Christian Sipola</h1>
    <canvas id="myChart" width="1200" height="400"></canvas>
    <div style="width:400px">
    <table id="datatable" style="width:100%">
    <thead>
        <tr>
        <th>Date</th>
        <th>Day</th>
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
            <?=$day->getDate()->format('y-m-d') ?>
            </td>
            <td>
            <?=$day->getDate()->format('D') ?>
            </td>
            <td>
            <?=$day->getNotices() ?>
            </td>
            <td>
            <?=$day->getMentions() ?>
            </td>
            <td>
            <?=$day->getPercentMentions() ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
    </table>
    </div>
    
    
    <div>Not readable:</div>
    <?=implode(', ', $maildirParser->getNotReadable()) ?>
    <script type="text/javascript">
    
    var data = <?=\PodioStats\Day::getArrayAsLineChartJson($maildirParser->getData()) ?>;
	var options = { datasetFill : true};
    //Get the context of the canvas element we want to select
    var ctx = document.getElementById("myChart").getContext("2d");
    var myNewChart = new Chart(ctx).Line(data,options);
    </script>
    
    <script type="text/javascript">
    jQuery(document).ready(function(){
					//.columnFilter() on oTable is column filter
           var oTable = jQuery('#datatable').dataTable( {
	 					//C = show hide columns
	 					//i = paginate info
	 					//p = paginate select
	 					//l = number of rows per page
	 					//f = search
	 					//r = ?
	 					//t = ?
	 					//T = tableTools
	 					//<"clear"> = ?
						"sDom": 'CipflT<"clear">t',
						 "bPaginate": false,
						//"aaSorting": [[ 0, 'asc' ]]
					} )
	});
 </script>
</body>
</html>