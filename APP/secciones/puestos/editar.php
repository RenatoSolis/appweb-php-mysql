<?php

include("../../bd.php");//nos ubicamos en el archivo bd

//recepcion del id para consultar los registros
if(isset($_GET['txtID'])){//envio de parametros a traves de la url en el metodo GET
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("SELECT * FROM tbl_puestos WHERE id=:id");
    $sentencia->bindParam(":id",$txtID);//bindParam lo que hace es escribir en la sentencia $nombredelpuesto y ejecutar
    $sentencia->execute();
    
    //llevar dicho registro que se selecciono arriba
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $nombredelpuesto=$registro["nombredelpuesto"];//asignamos los valores para mostrar en el formulario

    
}

//se recepcionan los valores
if($_POST){
    print_r($_POST);
    //recolectamos los datos del metodo POST
    $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
    $nombredelpuesto=(isset($_POST["nombredelpuesto"])?$_POST["nombredelpuesto"]:"");//validamos que esita informnacion enviada,si existe se vaa asignar sino exite va a ponerlo en blanco
    //Preparar la insercion de los datos
    $sentencia=$conexion->prepare("UPDATE tbl_puestos SET nombredelpuesto=:nombredelpuesto
                    WHERE id=:id");
    //Asignando los valores que vienen del metodo POST (los que vienen del formulario)
    $sentencia->bindParam(":nombredelpuesto",$nombredelpuesto);//bindParam lo que hace es escribir en la sentencia $nombredelpuesto y ejecutar
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    $mensaje="Registro actualizado";
    header("Location:index.php?mensaje=".$mensaje);
}


?>

<?php include("../../templates/header.php"); ?>


</br>
<div class="card">
    <div class="card-header">
        Puestos
    </div>
    <div class="card-body">
       <form action="" method="post" enctype="multipart/form-data">
        
        <div class="mb-3">
          <label for="txtID" class="form-label">ID:</label>
          <input type="text"
            value="<?php echo $txtID;?>"
            class="form-control" readonly name="txtID" id="txtID" aria-describedby="helpId" placeholder="">
          
        </div>

       <div class="mb-3">
          <label for="nombredelpuesto" class="form-label">Nombre del puesto:</label>
          <input type="text"
          value="<?php echo $nombredelpuesto;?>"
            class="form-control" name="nombredelpuesto" id="nombredelpuesto" aria-describedby="helpId" placeholder="Nombre del Puesto"> 
        </div>
        <button type="submit" class="btn btn-success">Actualizar</button>
       <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>
    </form>
    </div>
    <div class="card-footer text-muted">

    </div>
</div>

<?php include("../../templates/footer.php"); ?>