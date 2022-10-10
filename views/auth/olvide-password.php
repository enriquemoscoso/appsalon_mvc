<h1 class="nombre-pagina" >Olvide Password</h1>
<p class="descripcion-pagina" >Restablece Tu Password escribiendo tu email</p>

<?php 
    include_once __DIR__ ."/../templates/alertas.php";
?>

<form  class="formulario" method="POST" action="/olvide">
    <div class="campo">
        <label for="email" >Email</label>
        <input 
            type="email" 
            id="email" 
            name="email"
            placeholder="Tu Email" 
            
        />        
    </div>    

    <input type="submit" class="boton" value="Enviar Instrucciones">
</form>

<div class="acciones">
    <a href="/">¿Tenes Una Cuenta? Inicia Sesion</a>
    <a href="/crear-cuenta">¿Aun No tienes una Cuenta? Crear Una</a>
</div>
