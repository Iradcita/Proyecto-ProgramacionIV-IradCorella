<?php

/*
   Servicio encargado de subir y borrar las imagenes del sistema.

   Lo usan los modulos que guardan fotos: destinos, hoteles, actividades
   y la fotografia del perfil del usuario.

   Se hizo un servicio aparte para no repetir el mismo codigo cuatro veces.
   Si algun dia hay que cambiar el tamano maximo o permitir otro formato,
   se cambia aqui una sola vez y aplica para todo el sistema.

   Las imagenes se guardan dentro de public/uploads/<carpeta>/ y en la base
   de datos solo se guarda la RUTA (por ejemplo "uploads/destinos/foto.jpg"),
   nunca la imagen completa. Asi la base de datos se mantiene liviana.
*/
class ImagenService
{
    // Tamano maximo permitido: 2 MB (el numero esta en bytes).
    const TAMANO_MAXIMO = 2097152;

    // Solo se aceptan estos tipos de imagen. A la izquierda va el tipo real
    // del archivo y a la derecha la extension con la que se va a guardar.
    private static function formatosPermitidos()
    {
        return array(
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        );
    }

    /*
       Guarda una imagen que el usuario subio por un formulario.

       $archivo  = el elemento de $_FILES, por ejemplo $_FILES['imagen']
       $carpeta  = subcarpeta destino: "destinos", "hoteles", "actividades"...
       $prefijo  = texto con el que empieza el nombre del archivo

       Devuelve la ruta relativa para guardar en la base de datos,
       por ejemplo: uploads/destinos/destino_1739822_.jpg

       Si algo sale mal lanza una excepcion con un mensaje claro,
       que el controlador atrapa y le muestra al usuario.
    */
    public static function guardar($archivo, $carpeta, $prefijo = 'img')
    {
        // 1. Revisar que la subida no haya fallado.
        if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('No se pudo subir la imagen. Intenta de nuevo.');
        }

        // 2. Revisar el peso del archivo.
        if ($archivo['size'] > self::TAMANO_MAXIMO) {
            throw new Exception('La imagen no debe pesar mas de 2 MB.');
        }

        // 3. Revisar que de verdad sea una imagen.
        //    No basta con ver la extension del nombre, porque alguien podria
        //    cambiarle el nombre a un archivo peligroso. Por eso se revisa
        //    el tipo real del contenido.
        $tipoReal = self::obtenerTipoReal($archivo['tmp_name']);
        $permitidos = self::formatosPermitidos();

        if (!isset($permitidos[$tipoReal])) {
            throw new Exception('Solo se permiten imagenes JPG, PNG o WEBP.');
        }

        // 4. Crear la carpeta destino si todavia no existe.
        $directorio = BASE_PATH . '/public/uploads/' . $carpeta;

        if (!is_dir($directorio)) {
            mkdir($directorio, 0775, true);
        }

        // 5. Armar un nombre unico usando la hora actual y un numero al azar,
        //    para que dos imagenes nunca se sobrescriban entre si.
        $nombreArchivo = $prefijo . '_' . time() . '_' . rand(100, 999) . '.' . $permitidos[$tipoReal];
        $rutaCompleta = $directorio . '/' . $nombreArchivo;

        // 6. Mover el archivo desde la carpeta temporal de PHP a su lugar final.
        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            throw new Exception('No se pudo guardar la imagen en el servidor.');
        }

        // 7. Devolver la ruta que se va a guardar en la base de datos.
        return 'uploads/' . $carpeta . '/' . $nombreArchivo;
    }

    /*
       Borra del disco una imagen que ya no se necesita.

       Por seguridad solo borra archivos que esten dentro de la carpeta
       uploads. Asi, aunque en la base de datos hubiera una ruta rara,
       nunca se podria borrar un archivo del sistema.
    */
    public static function eliminar($rutaRelativa)
    {
        if (empty($rutaRelativa)) {
            return;
        }

        // Si no empieza con "uploads/" no es un archivo nuestro
        // (puede ser una direccion de internet), entonces no se toca.
        if (strpos($rutaRelativa, 'uploads/') !== 0) {
            return;
        }

        $ruta = BASE_PATH . '/public/' . $rutaRelativa;

        if (is_file($ruta)) {
            unlink($ruta);
        }
    }

    /*
       Indica si el formulario venia con un archivo seleccionado.
       Sirve para saber si el usuario quiere cambiar la imagen o
       si prefiere dejar la que ya tenia.
    */
    public static function seSubioArchivo($archivo)
    {
        return isset($archivo)
            && isset($archivo['error'])
            && $archivo['error'] !== UPLOAD_ERR_NO_FILE
            && !empty($archivo['name']);
    }

    /*
       Averigua el tipo real del archivo revisando su contenido.
       finfo es la forma recomendada; si el servidor no la tiene,
       se usa mime_content_type como respaldo.
    */
    private static function obtenerTipoReal($rutaTemporal)
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $tipo = finfo_file($finfo, $rutaTemporal);
            finfo_close($finfo);

            return $tipo;
        }

        if (function_exists('mime_content_type')) {
            return mime_content_type($rutaTemporal);
        }

        return '';
    }
}
