<?php

define('GLPI_ROOT', '../../../..');
include (GLPI_ROOT . "/inc/includes.php");
include (GLPI_ROOT . "/config/config.php");

Session::checkLoginUser();
Session::checkRight("profile", "r");

global $DB;
   
    switch (date("m")) {
    case "01": $mes = __('January','dashboard'); break;
    case "02": $mes = __('February','dashboard'); break;
    case "03": $mes = __('March','dashboard'); break;
    case "04": $mes = __('April','dashboard'); break;
    case "05": $mes = __('May','dashboard'); break;
    case "06": $mes = __('June','dashboard'); break;
    case "07": $mes = __('July','dashboard'); break;
    case "08": $mes = __('August','dashboard'); break;
    case "09": $mes = __('September','dashboard'); break;
    case "10": $mes = __('October','dashboard'); break;
    case "11": $mes = __('November','dashboard'); break;
    case "12": $mes = __('December','dashboard'); break;
    }

?>

<html> 
<head>
<title>GLPI - <?php echo __('Charts','dashboard'). " " . __('by Entity','dashboard'); ?></title>
<!-- <base href= "<?php $_SERVER['SERVER_NAME'] ?>" > -->
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="content-language" content="en-us" />
<!--  <meta http-equiv="refresh" content= "120"/> -->

<link rel="icon" href="../img/dash.ico" type="image/x-icon" />
<link rel="shortcut icon" href="../img/dash.ico" type="image/x-icon" />
<link href="../css/styles.css" rel="stylesheet" type="text/css" />
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="../css/bootstrap-responsive.css" rel="stylesheet" type="text/css" />
<link href="../css/font-awesome.css" type="text/css" rel="stylesheet" />

<script type="text/javascript" src="../js/jquery.min.js"></script> 
<link href="../inc/chosen/chosen.css" rel="stylesheet" type="text/css">
<script src="../inc/chosen/chosen.jquery.js" type="text/javascript" language="javascript"></script>  

<script src="../js/highcharts.js"></script>
<script src="../js/modules/exporting.js"></script>
<script src="../js/modules/no-data-to-display.js"></script>
<script src="../js/themes/grid-light.js"></script>

<script src="../js/bootstrap-datepicker.js"></script>
<link href="../css/datepicker.css" rel="stylesheet" type="text/css">
<link href="../less/datepicker.less" rel="stylesheet" type="text/css">

</head>
<body>

<?php

global $DB;

if(!empty($_POST['submit']))
{	
	$data_ini =  $_POST['date1'];	
	$data_fin = $_POST['date2'];
}

else {
	$data_ini = date("Y-m-01");
	$data_fin = date("Y-m-d");
} 

$ano = date("Y");
$month = date("Y-m");
$datahoje = date("Y-m-d");

//seleciona entidade

$sql_ent = "
SELECT id, name
FROM `glpi_entities`
ORDER BY `name` ASC
";

$result_ent = $DB->query($sql_ent);
$ent = $DB->fetch_assoc($result_ent);


// lista de entidades

function dropdown( $name, array $options, $selected=null )
{
    /*** begin the select ***/
    $dropdown = '<select class="chosen-select" tabindex="-1" style="width: 300px; height: 27px;" autofocus onChange="javascript: document.form1.submit.focus()" name="'.$name.'" id="'.$name.'">'."\n";

    $selected = $selected;
    /*** loop over the options ***/
    foreach( $options as $key=>$option )
    {
        /*** assign a selected value ***/
        $select = $selected==$key ? ' selected' : null;

        /*** add each option to the dropdown ***/
        $dropdown .= '<option value="'.$key.'"'.$select.'>'.$option.'</option>'."\n";
    }

    /*** close the select ***/
    $dropdown .= '</select>'."\n";

    /*** and return the completed dropdown ***/
    return $dropdown;
}


$res_ent = $DB->query($sql_ent);
$arr_ent = array();
$arr_ent[0] = "-- ". __('Select a entity','dashboard') . " --" ;

$DB->data_seek($result_ent, 0);

while ($row_result = $DB->fetch_assoc($result_ent))		
	{ 
	$v_row_result = $row_result['id'];
	$arr_ent[$v_row_result] = $row_result['name'] ;			
	} 	 

$name = 'sel_ent';
$options = $arr_ent;
$selected = "0";

?>

<div id='content' >
<div id='container-fluid' style="margin: 0px 8% 0px 8%;"> 

<div id="pad-wrapper" >

<div id="charts" class="row-fluid chart"> 
<div id="head" class="row-fluid">

	<a href="../index.php"><i class="fa fa-home" style="font-size:14pt; margin-left:25px;"></i><span></span></a>

	<div id="titulo_graf" >
	
	  <?php echo __('Tickets','dashboard') ." ". __('by Entity','dashboard'); ?> 
	<span style="color:#8b1a1a; font-size:35pt; font-weight:bold;"> </span> </div>

<div id="datas-tec" class="span12 row-fluid" > 
<form id="form1" name="form1" class="form2" method="post" action="?date1=<?php echo $data_ini ?>&date2=<?php echo $data_fin ?>&con=1" onsubmit="datai();dataf();"> 
<table border="0" cellspacing="0" cellpadding="1" bgcolor="#efefef">

<tr>
<td>
<?php 
echo'
<table style="margin-top:6px;" ><tr><td>
    <div class="input-append date" id="dp1" data-date="'.$data_ini.'" data-date-format="yyyy-mm-dd">
    <input class="span9" size="16" type="text" name="date1" value="'.$data_ini.'">
    <span class="add-on"><i class="icon-th"></i></span>
    </div>
</td><td>
   <div class="input-append date" id="dp2" data-date="'.$data_fin.'" data-date-format="yyyy-mm-dd">
    <input class="span9" size="16" type="text" name="date2" value="'.$data_fin.'">
    <span class="add-on"><i class="icon-th"></i></span>
    </div>
    </tr></td>
    </table>
    ';
?>

<script language="Javascript">

$('#dp1').datepicker('update');
$('#dp2').datepicker('update');

</script>
</td>

<td style="margin-top:2px;">
<?php

echo dropdown( $name, $options, $selected );

//Dropdown::showFromArray( $name, $options, $selected );

?>
</td>
</tr>
<tr><td height="15px"></td></tr>
<tr>
<td colspan="2" align="center" style="">
<button class="btn btn-primary btn-small" type="submit" name="submit" value="Atualizar" ><i class="icon-white icon-search"></i>&nbsp; <?php echo __('Consult','dashboard'); ?></button>
<button class="btn btn-primary btn-small" type="button" name="Limpar" value="Limpar" onclick="location.href='graf_entidade.php'" > <i class="icon-white icon-trash"></i>&nbsp; <?php echo __('Clean','dashboard'); ?> </button></td>
</td>
</tr>
	
	</table>
<?php Html::closeForm(); ?>
<!-- </form> -->
</div>
</div>
<!-- DIV's -->

<script type="text/javascript" >
$('.chosen-select').chosen();
</script>

<?php

$con = $_GET['con'];

if($con == "1") {
		

if(!isset($_POST['date1']))
{	
	$data_ini2 = $_GET['date1'];	
	$data_fin2 = $_GET['date2'];
}

else {	
	$data_ini2 = $_POST['date1'];	
	$data_fin2 = $_POST['date2'];	
}  

if(!isset($_POST["sel_ent"])) {
	$id_ent = $_GET["sel_ent"];	
}

else {
	$id_ent = $_POST["sel_ent"];
}

if($id_ent == " ") {
	echo '<script language="javascript"> alert(" ' . __('Select a entity','dashboard') . ' "); </script>';
	echo '<script language="javascript"> location.href="graf_entidade.php"; </script>';
}

if($data_ini == $data_fin) {
	$datas = "LIKE '".$data_ini."%'";	
}	

else {
	$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";	
}

// nome da entidade

$sql_nm = "
SELECT id, name
FROM `glpi_entities`
WHERE id = ".$id_ent."
";

$result_nm = $DB->query($sql_nm);
$ent_name = $DB->fetch_assoc($result_nm);


//quant chamados

$query2 = "
SELECT COUNT(glpi_tickets.id) as total
FROM glpi_tickets
WHERE glpi_tickets.date ".$datas."
AND glpi_tickets.is_deleted = 0     
AND glpi_tickets.entities_id = ".$id_ent."    
";

$result2 = $DB->query($query2) or die('erro');
$total = $DB->fetch_assoc($result2);


echo '<div id="entidade" class="span12 row-fluid" style="margin-top:25px;">';
echo $ent_name['name']." - <span style = 'color:#000;'> ".$total['total']." ".__('Tickets','dashboard')."</span>
</div>";

 ?>

<div id="graf_linhas" class="span12" style="height: 450px; margin-top: 25px; margin-left: -5px;">
	<?php include ("./inc/graflinhas_ent.inc.php"); ?>
</div>


<div id="graf2" class="span6" >
	<?php include ("./inc/grafpie_stat_ent.inc.php"); ?>
</div>


<div id="graf4" class="span6" >
	<?php include ("./inc/grafcat_ent.inc.php");  ?>
</div>

<?php 
include ("./inc/grafbar_ent.inc.php");

}
?>

</div>

</div>

</div>
</div>
</div>
</body>
</html>
