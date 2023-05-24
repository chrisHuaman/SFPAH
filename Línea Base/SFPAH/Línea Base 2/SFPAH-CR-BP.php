<?php
session_start();

require_once("clase.php");

$usar_db = new DBControl();

if(!empty($_GET["accion"])) 
{
switch($_GET["accion"]) 
{
	case "agregar":
		if(!empty($_POST["txtcantidad"])) 
		{
			$codproducto = $usar_db->vaiQuery("SELECT * FROM productos WHERE cod='" . $_GET["cod"] . "'");
			$items_array = array($codproducto[0]["cod"]=>array(
			'vai_nom'		=>$codproducto[0]["nom"], 
			'vai_cod'		=>$codproducto[0]["cod"], 
			'txtcantidad'	=>$_POST["txtcantidad"], 
			'vai_pre'		=>$codproducto[0]["pre"], 
			'vai_img'		=>$codproducto[0]["img"]
			));
			
			if(!empty($_SESSION["items_carrito"])) 
			{
				if(in_array($codproducto[0]["cod"],
				array_keys($_SESSION["items_carrito"]))) 
				{
					foreach($_SESSION["items_carrito"] as $i => $j) 
					{
							if($codproducto[0]["cod"] == $i) 
							{
								if(empty($_SESSION["items_carrito"][$i]["txtcantidad"])) 
								{
									$_SESSION["items_carrito"][$i]["txtcantidad"] = 0;
								}
								$_SESSION["items_carrito"][$i]["txtcantidad"] += $_POST["txtcantidad"];
							}
					}
				} else 
				{
					$_SESSION["items_carrito"] = array_merge($_SESSION["items_carrito"],$items_array);
				}
			} 
			else 
			{
				$_SESSION["items_carrito"] = $items_array;
			}
		}
	break;
	case "eliminar":
		if(!empty($_SESSION["items_carrito"])) 
		{
			foreach($_SESSION["items_carrito"] as $i => $j) 
			{
				if($_GET["eliminarcode"] == $i)
				{
					unset($_SESSION["items_carrito"][$i]);	
				}			
				if(empty($_SESSION["items_carrito"]))
				{
					unset($_SESSION["items_carrito"]);
				}
			}
		}
	break;
	case "vacio":
		unset($_SESSION["items_carrito"]);
	break;	
	case "pagar":
	echo "<script> alert('Buscando tiendas con el menor precio...');window.location= 'Resultado.html' </script>";
		unset($_SESSION["items_carrito"]);
	
	break;	
}
}
?>
<html>
<meta charset="UTF-8">
<head>
<title>SFPAH</title>

<link href="css/prodstyle.css" rel="stylesheet" />
<link href="css/style.css" rel="stylesheet" />


</head>
<body>
<div align="center"><h1 style="color: white;">Selección de productos</h1>



</div>

<div align="center">


<form actions="" method="get">
	<input type="text" name="busqueda"> <br>
	<input type="submit" name="enviar" value="Buscar">

</form>

<br>

</div>

<?php

if(isset($_GET['enviar'])){

	$busqueda = $_GET['busqueda'];
	$consulta = $usar_db->vaiquery("SELECT * FROM productos WHERE nom LIKE '%$busqueda%'"); 


	foreach($consulta as $i=>$k)
		{
	?>
		<div class="contenedor_productos">
			<form method="POST" action="prodslct.php?accion=agregar&cod=
			<?php echo $consulta[$i]["cod"]; ?>">
			<div><img src="<?php echo $consulta[$i]["img"]; ?>"></div>
			<div>
			<div style="padding-top:20px;font-size:18px;"><?php echo $consulta[$i]["nom"]; ?></div>
			<div style="padding-top:10px;font-size:20px;"><?php echo "S/.".$consulta[$i]["pre"]; ?></div>
			<div><input type="text" name="txtcantidad" value="1" size="2" /><input type="submit" value="Agregar" />
			</div>
			</div>
			</form>
		</div>
	<?php
		}
	
}

?>

<?php
if(isset($_SESSION["items_carrito"]))
{
    $totcantidad = 0;
    $totprecio = 0;
?>	

<table>
<tr>
<th style="width:30%">Ítem</th>
<th style="width:10%">Código</th>
<th style="width:10%">Cantidad</th>
<th style="width:10%">Precio recomendado</th>
<th style="width:10%">Precio x cantidad</th>
<th style="width:10%"><a href="prodslct.php?accion=vacio">Limpiar</a></th>
</tr>	
<?php		
    foreach ($_SESSION["items_carrito"] as $item){
        $item_price = $item["txtcantidad"]*$item["vai_pre"];
		?>
				<tr>
				<td><img src="<?php echo $item["vai_img"]; ?>" class="imagen_peque" /><?php echo $item["vai_nom"]; ?></td>
				<td><?php echo $item["vai_cod"]; ?></td>
				<td><?php echo $item["txtcantidad"]; ?></td>
				<td><?php echo "S/. ".$item["vai_pre"]; ?></td>
				<td><?php echo "S/. ". number_format($item_price,2); ?></td>
				<td><a href="prodslct.php?accion=eliminar&eliminarcode=<?php echo $item["vai_cod"]; ?>">Quitar</a></td>
				</tr>
				<?php
				$totcantidad += $item["txtcantidad"];
				$totprecio += ($item["vai_pre"]*$item["txtcantidad"]);
		}
		?>

<tr style="background-color:#f3f3f3">
<td colspan="2"><b>Monto aproximado: </b></td>
<td><b><?php echo $totcantidad; ?></b></td>
<td colspan="2"><strong><?php echo "S/. ".number_format($totprecio, 2); ?></strong></td>
<td><a href="prodslct.php?accion=pagar">Buscar en tienda</a></td>
</tr>

</table>		
  <?php
} else {
?>
<div align="center"><h3 style="color: yellow;">Su lista está vacía!</h3></div>

<?php 
}
?>
</div>

<div>

<div class="contenedor_general">
	<?php
	/*VaidrollTeam*/
	$productos_array = $usar_db->vaiquery("SELECT * FROM productos ORDER BY id ASC");
	if (!empty($productos_array)) 
	{ 
		foreach($productos_array as $i=>$k)
		{
	?>
		<div class="contenedor_productos">
			<form method="POST" action="prodslct.php?accion=agregar&cod=
			<?php echo $productos_array[$i]["cod"]; ?>">
			<div><img src="<?php echo $productos_array[$i]["img"]; ?>"></div>
			<div>
			<div style="padding-top:20px;font-size:18px;"><?php echo $productos_array[$i]["nom"]; ?></div>
			<div style="padding-top:10px;font-size:20px;"><?php echo "S/.".$productos_array[$i]["pre"]; ?></div>
			<div><input type="text" name="txtcantidad" value="1" size="2" /><input type="submit" value="Agregar" />
			</div>
			</div>
			</form>
		</div>
	<?php
		}
	}
	?>
</div>



</body>
</html>