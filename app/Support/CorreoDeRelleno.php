<?php

namespace App\Support;

/**
 * Reconoce los correos que se escriben para rellenar la casilla cuando el
 * paciente no dio uno: "noreporta@gmail.com", "notiene@gmail.com",
 * "sincorreo@huv.com", "0@0.00"...
 *
 * No son el correo de nadie, así que no sirven como usuario de ingreso y
 * tampoco se pueden repartir entre varias personas (users.email es único).
 * Las filas que los traen no se importan.
 *
 * La comparación es EXACTA sobre la parte anterior a la arroba, nunca por
 * prefijo: correos reales como "noralbasanchez18@gmail.com",
 * "noreidalozano@gmail.com" o "sindilopm@hotmail.com" empiezan igual y deben
 * pasar sin problema.
 */
class CorreoDeRelleno
{
    /**
     * Texto antes de la arroba, ya sin separadores, sin los dígitos del final
     * y sin la marca "huv". Así "noreportahuv", "no_reporta" y "notiene12"
     * llegan todos a su forma base.
     */
    const LOCALES = [
        // Negaciones sueltas
        'no', 'nn', 'na', 'xx', 'xxx', 'nd', 'ns', 'ninguno', 'ninguna', 'nulo', 'null',
        // No reporta
        'noreporta', 'noreportan', 'noreporto', 'noreportado', 'nreporta', 'norpeorta', 'noreporte',
        // No registra
        'noregistra', 'noregistran', 'noregistro', 'noregistrado', 'nregistra', 'noresgistra',
        // No tiene / no aporta / no suministra
        'notiene', 'notienen', 'notengo', 'noaporta', 'noaportan',
        'nosuministra', 'nosuministran', 'nosuministro',
        'nodato', 'nodatos', 'nosabe', 'norefiere', 'noinforma',
        // Sin correo
        'sincorreo', 'sinmail', 'sinemail', 'sindato', 'sindatos', 'sininformacion',
        'nocorreo', 'nomail', 'noemail', 'nocuenta',
        // No aplica
        'noaplica', 'naplica', 'noregistraaplica',
        // Marcadores de prueba
        'test', 'prueba', 'ejemplo', 'correo', 'email', 'usuario',
    ];

    /**
     * Coletillas que se le pegan al relleno sin cambiar su sentido:
     * "noreportacorreohuv" sigue siendo "noreporta".
     */
    const SUFIJOS = ['huv', 'hospital', 'cartago', 'correo', 'mail', 'email', 'dato', 'datos'];

    /**
     * Distancia máxima para dar por buena una variante mal escrita
     * ("noreprota", "notienne"). Solo se aplica a palabras largas, donde un
     * error de dos letras no puede confundirse con un nombre real.
     */
    const ERRATAS = 2;
    const LARGO_MINIMO_ERRATAS = 8;

    public static function es($correo): bool
    {
        $correo = mb_strtolower(trim((string) $correo));

        // Sin arroba no es un correo: de eso se encarga la validación de formato.
        if ($correo === '' || substr_count($correo, '@') !== 1) {
            return false;
        }

        [$local, $dominio] = explode('@', $correo, 2);

        if (self::dominioInventado($dominio)) {
            return true;
        }

        // "n@gmail.com", "s@s.com": una sola letra no identifica a nadie.
        // Una letra repetida SÍ puede ser real ("eee@sionkairos.com" son las
        // iniciales de un usuario registrado), así que eso se juzga por el
        // dominio, no por la parte local.
        if (preg_replace('/[^a-z0-9]+/', '', $local) !== '' && mb_strlen(preg_replace('/[^a-z0-9]+/', '', $local)) === 1) {
            return true;
        }

        // Se compara en cada paso de la limpieza: "sincorreo" ya está en la
        // lista y no debe quedar reducido a "sin".
        foreach (self::formas($local) as $forma) {
            if (in_array($forma, self::LOCALES, true) || self::esVarianteMalEscrita($forma)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Formas sucesivas del texto anterior a la arroba, quitando separadores,
     * la numeración con que se desempatan ("notiene12") y las coletillas
     * ("noreportacorreohuv" → "noreportacorreo" → "noreporta").
     *
     * @return list<string>
     */
    public static function formas($local): array
    {
        $nucleo = preg_replace('/[^a-z0-9]+/', '', mb_strtolower((string) $local));
        $nucleo = (string) preg_replace('/\d+$/', '', $nucleo);

        $formas = [$nucleo];

        // Varias coletillas seguidas; el tope corta cualquier caso raro.
        for ($vuelta = 0; $vuelta < 4; $vuelta++) {
            $previo = $nucleo;
            foreach (self::SUFIJOS as $sufijo) {
                if ($nucleo !== $sufijo && substr($nucleo, -strlen($sufijo)) === $sufijo) {
                    $nucleo = (string) preg_replace('/\d+$/', '', substr($nucleo, 0, -strlen($sufijo)));
                    $formas[] = $nucleo;
                    break;
                }
            }
            if ($nucleo === $previo) {
                break;
            }
        }

        return array_values(array_unique(array_filter($formas)));
    }

    /** "noreprota" por "noreporta", "notienne" por "notienen". */
    private static function esVarianteMalEscrita($forma): bool
    {
        if (mb_strlen($forma) < self::LARGO_MINIMO_ERRATAS) {
            return false;
        }

        foreach (self::LOCALES as $base) {
            if (mb_strlen($base) >= self::LARGO_MINIMO_ERRATAS
                && abs(mb_strlen($base) - mb_strlen($forma)) <= self::ERRATAS
                && levenshtein($forma, $base) <= self::ERRATAS) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dominios que no existen y se escriben solo para llenar: "s.s", "0.00",
     * "a.a". Un dominio real termina en al menos dos letras y tiene nombre.
     */
    private static function dominioInventado($dominio): bool
    {
        if (!preg_match('/^([a-z0-9][a-z0-9.-]*)\.([a-z]{2,})$/', $dominio, $partes)) {
            return true;
        }

        // "a.co", "0.com", "aaaa.com": el nombre del dominio necesita algo de
        // sustancia; una sola letra repetida no es un dominio de verdad.
        $nombre = str_replace('.', '', $partes[1]);

        return mb_strlen($nombre) < 2
            || ctype_digit($nombre)
            || (bool) preg_match('/^(.)\1+$/', $nombre);
    }
}
