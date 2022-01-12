<?php
$enlace = mysqli_connect("localhost", "basededatos", "passwordbd1234", "mydb");
if (!$enlace) {
    echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
    echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
    echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>Base de datos</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="">
<body>

<div>
    <h1>Desarollo de Aplicaciones para Bases de Datos</h1>
    <h2>Con php, css y javascript</h2>
    <h3>Antonella Sofia Garcia Alvarez</h3>
    <?php 
    switch($_POST['tabla']) {
        case 'Producto':
            $tabla = "Producto";
            $id = 'codigo';
            break;
        case 'Compra':
            $tabla = "Compra";
            $id = 'codigo';
            break;
        case 'Cliente':
            $tabla = "Cliente";
            $id = 'dni';
            break;
        default:
            $tabla = "Cliente";
            $id = 'dni';
            break;
    }
    $query = "SHOW columns FROM ".$tabla.";";
    //Hacemos la consulta
    $resultado = mysqli_query($enlace, $query);
    if (isset($_POST['editar'])) {
        $dataquery = "SELECT * FROM ".$tabla." WHERE ".$id."='".$_POST['id']."';";
        $dataresult = mysqli_query($enlace,$dataquery);
        $data = mysqli_fetch_array($dataresult);
        $action = "editar";
        $actionvalue= "Editar entrada";
    } else {
        $action = "nueva_entrada";
        $actionvalue = "Añadir registro";
    } ?>
    <form action="index.php" method="post">
    <table id="rows">
    <?php while($columnas = mysqli_fetch_array($resultado)) { ?>
                <tr>
                    <td> <?php echo $columnas['Field'] ?> </td>
                    <td>
                        <?php if ($columnas['Field'] == "fecha") {
                            ?><input type="date" name="fecha" step="1" value="<?php echo date("Y-m-d");?>"> <?php
                        } elseif ($columnas['Field'] == "codigo" && $tabla == "Compra" && isset($_POST['add'])) {
                            $lastidquery = "SELECT codigo FROM Compra ORDER BY codigo DESC LIMIT 1;";
                            $lastidresult = mysqli_query($enlace,$lastidquery);
                            $lastid = mysqli_fetch_row($lastidresult)[0];
                            ?><input type="text" name="<?= $columnas['Field'] ?>" value="<?= (intval($lastid)+1) ?>" disabled> <?php
                        } elseif ($columnas['Field'] == "borrado") {
                            ?> <input type="text" name="borrado" value="false"> <?php
                        } else {
                            ?> <input type="text" name="<?= $columnas['Field'] ?>" value="<?= $data[$columnas['Field']] ?>"> <?php
                        } ?>
                    </td>
                </tr>
    <?php } ?>
    </table>
    <input type="submit" name="<?= $action ?>" value="<?= $actionvalue ?>">
    <input type="hidden" name="tabla" value=<?= $tabla ?>>
    <input type="hidden" name="<?= $tabla ?>">
    <input type="submit" name="cancelar" value="Cancelar">
    </form>
</div>
</body>
</html> 

<?php
mysqli_close($enlace);
?>