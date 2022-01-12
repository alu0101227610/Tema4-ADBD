<?php
$enlace = mysqli_connect("localhost", "basededatos", "passwordbd1234", "mydb");
if (!$enlace) {
    echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
    echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
    echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

if (isset($_POST['nueva_entrada'])) {
    switch($_POST['tabla']) {
        case 'Producto':
            $query = "INSERT INTO mydb.Producto (codigo, borrado, nombre, familia, descripcion, stock, dimensionX, dimensionY, dimensionZ, peso, PVP) VALUES (
                '".$_POST['codigo']."',
                ".$_POST['borrado'].",
                '".$_POST['nombre']."',
                '".$_POST['familia']."',
                '".$_POST['descripcion']."',
                ".$_POST['stock'].",
                ".$_POST['dimensionX'].",
                ".$_POST['dimensionY'].",
                ".$_POST['dimensionZ'].",
                ".$_POST['peso'].",
                ".$_POST['PVP'].");";
            break;
        case 'Compra':
            $query = "SELECT stock FROM mydb.Producto WHERE codigo='".$_POST['producto_codigo']."' AND borrado=false;";
            $data = mysqli_query($enlace, $query);
            $stock = intval(mysqli_fetch_row($data)[0]);
            if (intval($_POST['cantidad']) <= $stock && $stock > 0) {
                $newstock = intval($stock-intval($_POST['cantidad']));
                mysqli_begin_transaction($enlace, MYSQLI_TRANS_START_READ_WRITE);
                $query = "INSERT INTO mydb.Compra (cliente_dni, borrado, producto_codigo, fecha, cantidad) VALUES (
                    '".$_POST['cliente_dni']."',
                    false,
                    '".$_POST['producto_codigo']."',
                    '".$_POST['fecha']."',
                    ".$_POST['cantidad'].");";
                    mysqli_query($enlace,$query);
                $query = "UPDATE mydb.Producto SET stock=".$newstock." WHERE codigo='".$_POST['producto_codigo']."';";
                mysqli_query($enlace,$query);
                mysqli_commit($enlace);
                $query = "";
            } else {
                $query = "";
            }
            break;
        case 'Cliente':
            $query = "INSERT INTO mydb.Cliente (dni, borrado, nombre, apellidos, email, telefono, codigo_postal, direccion_postal) VALUES (
                '".$_POST['dni']."',
                ".$_POST['borrado'].",
                '".$_POST['nombre']."',
                '".$_POST['apellidos']."',
                '".$_POST['email']."',
                '".$_POST['telefono']."',
                '".$_POST['codigo_postal']."',
                '".$_POST['direccion_postal']."');";
            break;
        default:
            $query = "";
            break;
    }
} elseif (isset($_POST['editar'])) {
    switch($_POST['tabla']) {
        case 'Producto':
            $query = "UPDATE mydb.Producto SET 
            borrado=".$_POST['borrado'].",
            nombre='".$_POST['nombre']."',
            familia='".$_POST['familia']."',
            descripcion='".$_POST['descripcion']."',
            stock=".$_POST['stock'].",
            dimensionX=".$_POST['dimensionX'].",
            dimensionY=".$_POST['dimensionY'].",
            dimensionZ=".$_POST['dimensionZ'].",
            peso=".$_POST['peso'].",
            PVP=".$_POST['PVP']." WHERE codigo='".$_POST['codigo']."';";
            break;
        case 'Compra':
            $query = "SELECT stock FROM mydb.Producto WHERE codigo='".$_POST['producto_codigo']."' AND borrado=false;";
            $data = mysqli_query($enlace, $query);
            $stock = intval(mysqli_fetch_array($data)[0]);
            $query = "SELECT cantidad FROM mydb.Compra WHERE codigo='".$_POST['codigo']."';";
            $data = mysqli_query($enlace, $query);
            $previous = intval(mysqli_fetch_array($data)[0]);
            if ($previous > intval($_POST['cantidad'])) {
                $newstock = intval($stock+($previous-intval($_POST['cantidad'])));
                mysqli_begin_transaction($enlace, MYSQLI_TRANS_START_READ_WRITE);
                $query = "UPDATE mydb.Compra SET
                cliente_dni='".$_POST['cliente_dni']."',
                producto_codigo='".$_POST['producto_codigo']."',
                fecha='".$_POST['fecha']."',
                cantidad=".$_POST['cantidad']." WHERE codigo='".$_POST['codigo']."';";
                mysqli_query($enlace,$query);
                $query = "UPDATE mydb.Producto SET stock=".$newstock." WHERE codigo='".$_POST['producto_codigo']."';";
                mysqli_query($enlace,$query);
                mysqli_commit($enlace);
                $query = "";
            } else if($stock >= (intval($_POST['cantidad'])-$previous)) {
                $newstock = intval($stock-(intval($_POST['cantidad'])-$previous));
                mysqli_begin_transaction($enlace, MYSQLI_TRANS_START_READ_WRITE);
                $query = "UPDATE mydb.Compra SET
                cliente_dni='".$_POST['cliente_dni']."',
                producto_codigo='".$_POST['producto_codigo']."',
                fecha='".$_POST['fecha']."',
                cantidad=".$_POST['cantidad']." WHERE codigo='".$_POST['codigo']."';";
                mysqli_query($enlace,$query);
                $query= "UPDATE mydb.Producto SET stock=".$newstock." WHERE codigo='".$_POST['producto_codigo']."';";
                mysqli_query($enlace,$query);
                mysqli_commit($enlace);
                $query = "";
            } else {
                $query = "";
            }
            break;
        case 'Cliente':
            $query = "UPDATE mydb.Cliente SET
            borrado=".$_POST['borrado'].",
            nombre='".$_POST['nombre']."',
            apellidos='".$_POST['apellidos']."',
            email='".$_POST['email']."',
            telefono='".$_POST['telefono']."',
            codigo_postal='".$_POST['codigo_postal']."',
            direccion_postal='".$_POST['direccion_postal']."' WHERE dni='".$_POST['dni']."';";
            break;
        default:
            $query = "";
            break;
    }
} elseif (isset($_POST['borrar'])) {
    switch($_POST['tabla']) {
        case 'Producto':
            $query = "UPDATE mydb.Producto SET borrado=true WHERE codigo='".$_POST['id']."';";
            break;
        case 'Compra':
            mysqli_begin_transaction($enlace, MYSQLI_TRANS_START_READ_WRITE);
            $query = "SELECT stock FROM mydb.Producto WHERE codigo='".$_POST['producto_codigo']."' AND borrado=false;";
            $data = mysqli_query($enlace,$query);
            $stock = intval(mysqli_fetch_array($data)[0]);
            $query = "SELECT cantidad FROM mydb.Compra WHERE codigo='".$_POST['id']."' AND borrado=false;";
            $data = mysqli_query($enlace,$query);
            $currentstock = intval(mysqli_fetch_array($data)[0]);
            $newstock = intval($stock+$currentstock);
            $query = "UPDATE mydb.Producto SET stock=".$newstock." WHERE codigo='".$_POST['producto_codigo']."' AND borrado=false;";
            mysqli_query($enlace,$query);
            $query = "UPDATE mydb.Compra SET borrado=true WHERE codigo=".$_POST['id'].";";
            mysqli_commit($enlace);
            break;
        case 'Cliente':
            $query = "UPDATE mydb.Cliente SET borrado=true WHERE dni='".$_POST['id']."';";
            break;
        default:
        $query = "";
        break;
    }
}
if (isset($query)) {
    if ($query != "") {
        mysqli_query($enlace, $query);
    } else {
        //TODO añadir error
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>Base de datos</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="">
<style>
#rows {
    margin: 10 10 10 10;
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%
}
#rows td, #rows th {
    text-align: center; 
    vertical-align: middle;
    border: 1px solid #ddd;
    padding: 2px;
    border: 1px solid black;
}
#rows tr:nth-child(even){
    background-color: #f2f2f2;
}
#rows tr:hover {
    background-color: #ebcfcf;
}
#rows th {
    padding-top: 8px;
    padding-bottom: 8px;
    text-align: center;
    background-color: #bb9cfd;
    color: white;
    cursor: pointer;
}
</style>
<body>

<div>
    <h1>Desarollo de Aplicaciones para Bases de Datos</h1>
    <h2>Con php, css y javascript</h2>
    <h3>Antonella Sofia Garcia Alvarez</h3>
</div>
<div>
    <form action="index.php" method="post">
        <input type="submit" name="Cliente" value="Gestión de clientes">
        <input type="submit" name="Producto" value="Gestión de productos">
        <input type="submit" name="Compra" value="Gestión de compras">
    </form>
</div>

<div>
    <?php

    //Comprobamos si se ha pulsado algún botón
    if (isset($_POST['Producto'])) {
        $query = "SELECT codigo,
        nombre,
        familia,
        descripcion,
        stock,
        dimensionX,
        dimensionY,
        dimensionZ,
        peso,
        PVP FROM Producto WHERE borrado=false";
        $tabla = "Producto";
    } elseif (isset($_POST['Compra'])) {
        $query = "SELECT codigo,
        cliente_dni,
        producto_codigo,
        fecha,
        cantidad FROM Compra WHERE borrado=false";
        $tabla = "Compra";
    } else {
        $query = "SELECT dni,
        nombre,
        apellidos,
        email,
        telefono,
        codigo_postal,
        direccion_postal FROM Cliente WHERE borrado=false";
        $tabla = "Cliente";
    }
    //Hacemos la consulta
    $resultado = mysqli_query($enlace, $query);
    //Iteramos por cada fila
    ?>
    <table id="rows">
    <?php switch($tabla) {
        case 'Producto':?>
            <tr>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Familia</th>
                <th>Descripcion</th>
                <th>Stock</th>
                <th>Dimension X</th>
                <th>Dimension Y</th>
                <th>Dimension Z</th>
                <th>Peso</th>
                <th>Pvp</th>
            </tr>
            <?php break;

        case 'Cliente':?>
            <tr>
                <th>DNi</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Email</th>
                <th>Telefono</th>
                <th>Codigo Postal</th>
                <th>Direccion Postal</th>
            </tr>
            <?php break;

        case 'Compra':?>
            <tr>
                <th>Codigo de Compra</th>
                <th>Dni</th>
                <th>Codigo de Producto</th>
                <th>Fecha</th>
                <th>Cantidad</th>
            </tr>
            <?php break;
    }

    while ($fila = mysqli_fetch_array($resultado, MYSQLI_NUM)) { ?>
                <tr>
                <?php foreach($fila as $item) { ?>
                    <td> <?php echo $item; ?> </td>
                <?php } ?>
                    <form action="index.php" method="post">
                        <td> <input type="submit" name="borrar" value="Borrar"> </td>
                        <input type="hidden" name="id" value="<?= $fila[0] ?>">
                        <?php if ($tabla == 'Compra') { ?>
                            <input type="hidden" name="producto_codigo" value="<?= $fila[2] ?>">
                        <?php } ?>
                        <input name="tabla" type="hidden" value="<?= $tabla ?>">
                        <input name="<?= $tabla ?>" type="hidden">
                    </form>
                    <form action="row.php" method="post">
                        <td> <input type="submit" name="editar" value="Editar"> </td>

                        <input type="hidden" name="id" value="<?= $fila[0] ?>">
                        <input name="tabla" type="hidden" value="<?= $tabla ?>">
                        <input name="<?= $tabla ?>" type="hidden">
                    </form>
                </tr>
            
            <?php } ?>

    </table>
        <form action="row.php" method="post">
            <input name="tabla" type="hidden" value="<?= $tabla ?>">
            <input type="submit" name="add" value="Añadir registro">
        </form>
</div>

</body>
<script src="order.js"></script>
</html> 

<?php
mysqli_close($enlace);
?>
