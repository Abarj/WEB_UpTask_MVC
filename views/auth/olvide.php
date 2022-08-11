<div class="contenedor olvide">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>
    
    <div class="contenedor-sm">
        <p class="descripcion-pagina">Restablece tu contraseña de UpTask</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/olvide">
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Email" name="email">
            </div>

            <input type="submit" class="boton" value="Enviar Email">
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
            <a href="/crear">¿Aún no tienes una cuenta? Crear una</a>
        </div>
    </div> <!--.contenedor-sm-->
</div>