
<?php

if($data_ini == $data_fin) {
$datas = "LIKE '".$data_ini."%'";	
}	

else {
$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";	
}

if(isset($_REQUEST['limite'])) {
	$limit = $_REQUEST['limite'];	
}	
else {
	$limite = 25;
}

$sql_grp = "
SELECT gi.id AS id, gi.name AS name, count(gt.id) AS conta
FROM glpi_tickets gt, glpi_". strtolower($type)."s gi
WHERE gt.itemtype = '".$type."'
AND gt.items_id = gi.id
AND gt.is_deleted = 0
AND gt.date ".$datas."
GROUP BY gi.name
ORDER BY conta DESC
LIMIT ".$limite." 
";

$query_grp = $DB->query($sql_grp);

if($DB->fetch_assoc($query_grp) != 0) {

echo "
<script type='text/javascript'>

$(function () {
	var categoryLinks = {
";	
	
$DB->data_seek($query_grp, 0) ;  
while ($grupo = $DB->fetch_assoc($query_grp)) {

echo "	
        '". $grupo['name']."': '".$CFG_GLPI["url_base"]."/front/".$type.".form.php?id=".$grupo['id']."',       
    ";	
}

echo "	};	
        $('#graf1').highcharts({
            chart: {
                type: 'bar',
                height: 800
            },
            title: {
                text: '". __(ucfirst($type)) ."'
            },
            subtitle: {
                text: ''
            },
            xAxis: { 
            categories: [ ";

$DB->data_seek($query_grp, 0) ;  
while ($grupo = $DB->fetch_assoc($query_grp)) {

echo "'". $grupo['name']."',";
}              

echo "    ],
                title: {
                    text: null
                },
                labels: {
                	style: {
                        fontSize: '12px',
                        fontFamily: 'Verdana, sans-serif'
                    },
                  formatter: function() {
                    return '<a href=\"'+ categoryLinks[this.value] +'\" target=\"_blank\" style=\"color:#606060;\">'+this.value +'</a>';
                		},
                		useHTML: true
                	}
            },
            yAxis: {
                min: 0,
                title: {
                    text: '',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {
                valueSuffix: ''
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true                                                
                    },
                     borderWidth: 1,
                	borderColor: 'white',
                	shadow:true,           
                	showInLegend: false
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -40,
                y: 100,
                floating: true,
                borderWidth: 1,
                backgroundColor: '#FFFFFF',
                shadow: true,
                enabled: false
            },
            credits: {
                enabled: false
            },
			 events:{
              click: function (event) {
                  alert(event.point.name);
                  // add your redirect code and u can get data using event.point
              }
          },
            series: [{            	
            	 dataLabels: {
            	 	color: '#000099'
            	 	},
                name: '". __('Tickets','dashboard')."',
                data: [  
";
             
//zerar rows para segundo while

$DB->data_seek($query_grp, 0) ;  
             
while ($grupo = $DB->fetch_assoc($query_grp)) 
{
	echo $grupo['conta'].",";
}    

echo "]
            }]
        });
    });

</script>
";
		}
		?>
