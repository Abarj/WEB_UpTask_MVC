<div class="contenedor crear">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crea tu cuenta en UpTask</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/crear">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="texto" id="nombre" placeholder="Nombre" name="nombre" value="<?php echo $usuario->nombre; ?>">
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Email" name="email" value="<?php echo $usuario->email; ?>">
            </div>

            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" placeholder="Contraseña" name="password">
            </div>

            <div class="campo">
                <label for="password2">Repetir contraseña</label>
                <input type="password" id="password2" placeholder="Repite tu contraseña" name="password2">
            </div>

            <input type="submit" class="boton" value="Crear Cuenta">
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
            <a href="/olvide">¿Olvidaste tu contraseña?</a>
        </div>
    </div> <!--.contenedor-sm-->
</div>