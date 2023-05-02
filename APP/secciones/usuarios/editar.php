<?php

include("../../bd.php");//nos ubicamos en el archivo bd

//recepcion del id para consultar los registros
if(isset($_GET['txtID'])){//envio de parametros a traves de la url en el metodo GET
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("SELECT * FROM tbl_usuarios WHERE id=:id");
    $sentencia->bindParam(":id",$txtID);//bindParam lo que hace es escribir en la sentencia $nombredelpuesto y ejecutar
    $sentencia->execute();
    
    //llevar dicho registro que se selecciono arriba
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $usuario=$registro["usuario"];//asignamos los valores para mostrar en el formulario
    $password=$registro["password"];
    $correo=$registro["correo"];
}
if($_POST){
  //print_r($_POST);
  //recolectamos los datos del metodo POST
  $txtID=(isset($_POST["txtID"])?$_POST["txtID"]:"");
  $usuario=(isset($_POST["usuario"])?$_POST["usuario"]:"");//validamos que esita informnacion enviada,si existe se vaa asignar sino exite va a ponerlo en blanco
  $password=(isset($_POST["password"])?$_POST["password"]:"");
  $correo=(isset($_POST["correo"])?$_POST["correo"]:"");
  
  //Preparar la insercion de los datos
  $sentencia=$conexion->prepare("UPDATE tbl_usuarios SET
                usuario=:usuario,
                password=:password,
                correo=:correo
                where id =:id");

  //Asignando los valores que vienen del metodo POST (los que vienen del formulario)
  $sentencia->bindParam(":usuario",$usuario);//bindParam lo que hace es escribir en la sentencia $nombredelpuesto y ejecutar
  $sentencia->bindParam(":password",$password);
  $sentencia->bindParam(":correo",$correo);
  $sentencia->bindParam(":id",$txtID);
  $sentencia->execute();
  $mensaje="Registro Actualizado";
    header("Location:index.php?mensaje=".$mensaje);
}

?>

<?php include("../../templates/header.php"); ?>



</br>
<div class="card">
    <div class="card-header">
        Datos del Usuario
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
          <label for="usuario" class="form-label">Nombre del usuario:</label>
          <input type="text"
            value="<?php echo $usuario;?>"
            class="form-control" name="usuario" id="usuario" aria-describedby="helpId" placeholder="Nombre del Usuario"> 
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password"
            value="<?php echo $password;?>"
            class="form-control" name="password" id="password" aria-describedby="helpId" placeholder="Escriba su contraseña">
        </div>

        <div class="mb-3">
          <label for="correo" class="form-label">Correo</label>
          <input type="email"
            value="<?php echo $correo;?>"
            class="form-control" name="correo" id="correo" aria-describedby="helpId" placeholder="Escriba su correo">
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
       <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>
    </form>
    </div>
    <div class="card-footer text-muted">

    </div>
</div>

<?php include("../../templates/footer.php"); ?>