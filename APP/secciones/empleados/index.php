<?php

include("../../bd.php");//nos ubicamos en el archivo bd

//BORRADO-Eliminar
if(isset($_GET['txtID'])){//envio de parametros a traves de la url en el metodo GET
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    //SELECT-Buscar el archivo seleccionado con el empleado
    $sentencia=$conexion->prepare("SELECT foto,cv from `tbl_empleados`WHERE id=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();//ejecuta la instruccion select para que se muestren los registros
    $registro_recuperado=$sentencia->fetch(PDO::FETCH_LAZY);//esta instruccion consulta los registros de la tabla puestos y recupera un registro

    print_r($registro_recuperado);

    //identificar si existe la foto y borrarlo
    if(isset($registro_recuperado["foto"]) && $registro_recuperado["foto"]!=""){
        if(file_exists("./".$registro_recuperado["foto"])){
            unlink("./".$registro_recuperado["foto"]);
        }
    }

    if(isset($registro_recuperado["cv"]) && $registro_recuperado["cv"]!=""){
        if(file_exists("./".$registro_recuperado["cv"])){
            unlink("./".$registro_recuperado["cv"]);
        }
    }
  
    $sentencia=$conexion->prepare("DELETE FROM tbl_empleados WHERE id=:id");
    $sentencia->bindParam(":id",$txtID);//bindParam lo que hace es escribir en la sentencia $nombredelpuesto y ejecutar
    
    $sentencia->execute();
    $mensaje="Registro eliminado";
    header("Location:index.php?mensaje=".$mensaje);
   
}

//SELECT
$sentencia=$conexion->prepare("SELECT *,
(SELECT nombredelpuesto 
FROM tbl_puestos 
WHERE tbl_puestos.id=tbl_empleados.idpuesto limit 1)as puesto
FROM `tbl_empleados`");
$sentencia->execute();//ejecuta la instruccion select para que se muestren los registros
$lista_tbl_empleados=$sentencia->fetchAll(PDO::FETCH_ASSOC);//esta instruccion consulta los registros de la tabla puestos


?>

<?php include("../../templates/header.php"); ?>
</br>

<div class="card">
    <div class="card-header">
       
        <a name="" id="" class="btn btn-primary" 
        href="crear.php" role="button">Agregar Registro</a>
    </div>
    <div class="card-body">
       <div class="table-responsive-sm">
        <table class="table" id="tabla_id">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Foto</th>
                    <th scope="col">CV</th>
                    <th scope="col">Puesto</th>
                    <th scope="col">Fecha de ingreso</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($lista_tbl_empleados as $registro){?>
                <tr class="">
                    <td><?php echo $registro['id']; ?></td>
                    <td scope="row"><?php echo $registro['primernombre']; ?>
                        <?php echo $registro['segundonombre']; ?>
                        <?php echo $registro['primerapellido']; ?>
                        <?php echo $registro['segundoapellido']; ?>
                    </td>
                    <td>
                        <img width="50" src=" <?php echo $registro['foto']; ?>" class="img-fluid rounded" alt="" />
                       
                    </td>
                    <td>
                        <a href="<?php echo $registro['cv']; ?>">
                        <?php echo $registro['cv']; ?>
                        </a>
                    </td>
                    <td><?php echo $registro['puesto']; ?></td>
                    <td><?php echo $registro['fechadeingreso']; ?></td>
                    <td>
                    <a href="carta_recomendacion.php?txtID=<?php echo $registro['id']; ?>" class="btn btn-primary" role="button">Carta</a>
                    <a name="btneditar" id="btneditar" class="btn btn-info" href="editar.php?txtID=<?php echo $registro['id']; ?>" role="button">Editar</a>
                    <a class="btn btn-danger" href="javascript:borrar(<?php echo $registro['id']; ?>);" role="button">Eliminar</a>
                </tr>
            <?php }?>    
            </tbody>
        </table>
       </div>
       
    </div>
</div>

<?php include("../../templates/footer.php"); ?>