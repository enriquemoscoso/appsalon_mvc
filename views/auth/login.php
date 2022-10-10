<h1 class="nombre-pagina" >Login</h1>
<p class="descripcion-pagina" >Inicia Sesion Con Tus Datos</p>

<?php 
    include_once __DIR__ ."/../templates/alertas.php";
?>


<form method="POST"  action="/"  class="formulario" >
    <div class="campo">
        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="Tu Email"    
            
        />
    </div>

    <div class="campo">
        <label for="password">Password</label>
        <input 
            type="password"
            id="password"
            name="password"
            placeholder="Tu Password"
            
        >
    </div>

    <input type="submit" class="boton" value="Iniciar Sesión">
</form>

<div class="acciones">
    <a href="/crear-cuenta">¿Aun no tienes una cuenta? Crear Una</a>
    <a href="/olvide">¿Olvidaste tu Password?</a>
</div>