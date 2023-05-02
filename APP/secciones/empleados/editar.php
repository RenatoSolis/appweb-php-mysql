<?php
include("../../bd.php");//nos ubicamos en el archivo bd

//recepcion del id para consultar los registros
if(isset($_GET['txtID'])){//envio de parametros a traves de la url en el metodo GET
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("SELECT * FROM tbl_empleados WHERE id=:id");
    $sentencia->bindParam(":id",$txtID);//bindParam lo que hace es escribir en la sentencia $nombredelpuesto y ejecutar
    $sentencia->execute();
    
    //llevar dicho registro que se selecciono arriba
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $nombredelpuesto=$registro["nombredelpuesto"];//asignamos los valores para mostrar en el formulario

    $primernombre=$registro["primernombre"];
    $segundonombre=$registro["segundonombre"];
    $primerapellido=$registro["primerapellido"];
    $segundoapellido=$registro["segundoapellido"];

    $foto=$registro["foto"];
    $cv=$registro["cv"];

    $idpuesto=$registro["idpuesto"];
    $fechadeingreso=$registro["fechadeingreso"];

    $sentencia=$conexion->prepare("SELECT * from `tbl_puestos`");
    $sentencia->execute();//ejecuta la instruccion select para que se muestren los registros
    $lista_tbl_puestos=$sentencia->fetchAll(PDO::FETCH_ASSOC);//esta instruccion consulta los registros de la tabla puestos
}
if($_POST){
    //tenemos recepcionados todos los datos a actualizar en caso de que exista
    $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
    $primernombre=(isset($_POST["primernombre"])?$_POST["primernombre"]:"");
    $segundonombre=(isset($_POST["segundonombre"])?$_POST["segundonombre"]:"");
    $primerapellido=(isset($_POST["primerapellido"])?$_POST["primerapellido"]:"");
    $segundoapellido=(isset($_POST["segundoapellido"])?$_POST["segundoapellido"]:"");
    $idpuesto=(isset($_POST["idpuesto"])?$_POST["idpuesto"]:"");
    $fechadeingreso=(isset($_POST["fechadeingreso"])?$_POST["fechadeingreso"]:"");

   $sentencia=$conexion->prepare("
   UPDATE tbl_empleados 
   SET
    primernombre=:primernombre,
    segundonombre=:segundonombre,
    primerapellido=:primerapellido,
    segundoapellido=:segundoapellido,
    idpuesto=:idpuesto,
    fechadeingreso=:fechadeingreso
   WHERE id=:id");


    $sentencia->bindParam(":primernombre",$primernombre);
    $sentencia->bindParam(":segundonombre",$segundonombre);
    $sentencia->bindParam(":primerapellido",$primerapellido);
    $sentencia->bindParam(":segundoapellido",$segundoapellido); 
    $sentencia->bindParam(":idpuesto",$idpuesto);
    $sentencia->bindParam(":fechadeingreso",$fechadeingreso);
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    //buscamos el elemento foto
    $foto=(isset($_FILES["foto"]['name'])?$_FILES["foto"]['name']:"");
    $fecha=new DateTime();//obtener el tiempo
    //si tiene informacion la fotografia entonces vamos a utilizar la fecha de la foto,el tiempo y le va a concatenar un valor sino lo deja vacio
    $nombreArchivo_foto=($foto!='')?$fecha->getTimestamp()."_".$_FILES["foto"]['name']:"";
    //un archivo temporal 
    $tmp_foto=$_FILES["foto"]['tmp_name'];
    //usamos el archivo temporal para mover ese archivo a un nuevo destino $nombreArchivo_foto
    if($tmp_foto!=''){
        move_uploaded_file($tmp_foto,"./".$nombreArchivo_foto);//cargamos la foto
        
        //hacer una consulta para buscar la foto vieja o anterior
        $sentencia=$conexion->prepare("SELECT foto from `tbl_empleados`WHERE id=:id");
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();//ejecuta la instruccion select para que se muestren los registros
        $registro_recuperado=$sentencia->fetch(PDO::FETCH_LAZY);//esta instruccion consulta los registros de la tabla puestos y recupera un registro

        //identificar si existe la foto y borrarlo
        if(isset($registro_recuperado["foto"]) && $registro_recuperado["foto"]!=""){
            if(file_exists("./".$registro_recuperado["foto"])){
                unlink("./".$registro_recuperado["foto"]);//borra el registro
            }
        }

        //y despues se actualiza en la base de datos conesa foto nueva
        $sentencia=$conexion->prepare("UPDATE tbl_empleados SET foto=:foto WHERE id=:id");
        $sentencia->bindParam(":foto",$nombreArchivo_foto);
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();
    }
    

    $cv=(isset($_FILES["cv"]['name'])?$_FILES["cv"]['name']:"");

    //buscamos un nombre temporal que nos permita borrar
    $nombreArchivo_cv=($cv!='')?$fecha->getTimestamp()."_".$_FILES["cv"]['name']:"";
    $tmp_cv=$_FILES["cv"]['tmp_name'];
    if($tmp_cv!=''){
        move_uploaded_file($tmp_cv,"./".$nombreArchivo_cv);
        
        //SELECT-Buscar el archivo seleccionado con el empleado
        $sentencia=$conexion->prepare("SELECT cv from `tbl_empleados`WHERE id=:id");
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();//ejecuta la instruccion select para que se muestren los registros
        $registro_recuperado=$sentencia->fetch(PDO::FETCH_LAZY);//esta instruccion consulta los registros de la tabla puestos y recupera un registro

        if(isset($registro_recuperado["cv"]) && $registro_recuperado["cv"]!=""){
            if(file_exists("./".$registro_recuperado["cv"])){
                unlink("./".$registro_recuperado["cv"]);
            }
        }

        $sentencia=$conexion->prepare("UPDATE tbl_empleados SET cv=:cv WHERE id=:id");
        $sentencia->bindParam(":cv",$nombreArchivo_cv);
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();

    }
    $mensaje="Registro Actualizado";
    header("Location:index.php?mensaje=".$mensaje);

}

?>

<?php include("../../templates/header.php"); ?>

</br>
<div class="card">
    <div class="card-header">
        Datos del Empleado
    </div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data"><!--enctype="multipart/formdata" permite adjuntar el archivo-->

            <div class="mb-3">
                <label for="txtID" class="form-label">ID:</label>
                <input type="text"
                    value="<?php echo $txtID;?>"
            class="form-control" readonly name="txtID" id="txtID" aria-describedby="helpId" placeholder="">
          
            </div>

            <div class="mb-3">
              <label for="primernombre" class="form-label">Primer Nombre</label>
              <input type="text"
              value="<?php echo $primernombre;?>"
                class="form-control" name="primernombre" id="primernombre" aria-describedby="helpId" placeholder="Primer nombre">
            </div>

            <div class="mb-3">
            <label for="segundonombre" class="form-label">Segundo Nombre</label>
                <input type="text"
                value="<?php echo $segundonombre;?>"
                 class="form-control" name="segundonombre" id="segundonombre" aria-describedby="helpId" placeholder="Segundo Nombre">
            </div>

            <div class="mb-3">
            <label for="primerapellido" class="form-label">Primer apellido</label>
                <input type="text"
                value="<?php echo $primerapellido;?>"
                 class="form-control" name="primerapellido" id="primerapellido" aria-describedby="helpId" placeholder="Primer apellido">
            </div>

            <div class="mb-3">
            <label for="segundoapellido" class="form-label">Segundo apellido</label>
                <input type="text"
                value="<?php echo $segundoapellido;?>"
                 class="form-control" name="segundoapellido" id="segundoapellido" aria-describedby="helpId" placeholder="Segundo apellido">
            </div>

            <div class="mb-3">
            <label for="foto" class="form-label">Foto</label>
                </br>
                
                <img width="100" 
                    src="<?php echo $foto;?>"
                    class=" rounded" alt="" />
                    </br></br>

                <input type="file"
                 class="form-control" name="foto" id="foto" aria-describedby="helpId" placeholder="Foto">
            </div>

            <div class="mb-3">
            <label for="cv" class="form-label">CV(PDF)</label>
                </br>
                <a href="<?php echo $cv;?>"><?php echo $cv;?></a>
                <input type="file"
                 class="form-control" name="cv" id="cv" aria-describedby="fileHelpId" placeholder="CV">
            </div>

            <div class="mb-3">
                <label for="idpuesto" class="form-label">Puesto</label>

                <select class="form-select form-select-sm" name="idpuesto" id="idpuesto">
                    <?php foreach($lista_tbl_puestos as $registro){?>    
                        <option <?php echo($idpuesto==$registro['id'])?"selected":""; ?> value="<?php echo $registro['id']?>">
                            <?php echo $registro['nombredelpuesto']?>
                        </option>
                    <?php }?>
                </select>
            </div>

            <div class="mb-3">
                <label for="fechaingreso" class="form-label">Fecha de ingreso</label>
                <input type="date"
                value="<?php echo $fechadeingreso;?>"
                class="form-control" name="fechadeingreso" id="fechadeingreso" aria-describedby="emailHelpId" placeholder="Fecha de ingreso">
            </div>
            
            <button type="submit" class="btn btn-success">Actualizar Registro</button>
            <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

        </form>
    </div>
    <div class="card-footer text-muted">
    </div>
</div>

<?php include("../../templates/footer.php"); ?>