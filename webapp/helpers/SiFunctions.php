<?php

/**
 * Esta clase contiene funciones internas de uso comun
 */

class siFunctionsException extends Exception {
    
}

class SiFunctions extends Exception{

    /**
     * Retorna un nombre aleatrorio de archivo concatenado con la extension pasada como parametro.
     * @param $extensionName ExtensiÃ³n del archivo sin punto, ej: doc, ppt, pdf
     * @return String

     *  @author r.a.p (02/08/2009)
     */
    public static function randFileName($extensionName, $fileName = '') {
        mt_srand();
        if ($fileName != '') {
            return self::formatUrl($fileName) . '_' . mt_rand(10, 99) . '.' . strtolower($extensionName);
        } else {
            return date('YmdHis') . '_' . mt_rand(10, 99) . '.' . strtolower($extensionName);
        }
    }

    /**
     * Cambia la fecha del formato Español(08/07/2009) al formato de BD(2009-07-08)
     * @param $date Fecha en formato "08/07/2009"
     * @return String

     */
    public static function date2db($date, $form = "-") {
        //Si la fecha viene de la forma 24-11-2009 entonces reemplaza los - por /
        $date = str_replace($form, '/', $date);

        if (strpos($date, "/")) {
            $fec = explode("/", $date);
            return $fec[2] . "-" . $fec[1] . "-" . $fec[0];
        }else
            return false;
    }

    /**
     * Cambia la fecha del formato Español(08/07/2009) al formato de BD(2009-07-08)
     * @param $date Fecha en formato "08/07/2009"
     * @return String
     *
     */
    public static function db2date($date, $form = "-") {
        //Si la fecha viene de la forma 24-11-2009 entonces reemplaza los - por /
        $date = str_replace($form, '-', $date);

        if (strpos($date, "-")) {
            $fec = explode("-", $date);
            return $fec[2] . "/" . $fec[1] . "/" . $fec[0];
        }else
            return false;
    }

    /**
     * Convierte un arreglo en una cadena separada con <br>
     * @param $data es el array() que se va a convertir en texto
     * @return String    
     */
    public static function array2text($data) {
        $text = '';
        foreach ($data as $row)
            $text .= '-' . $row[0] . '<br>';

        return $text;
    }

    /**
     * Trunca un texto largo para cuando se necesita solo una parte
     * @param $string Texto que va a ser truncado
     * @param $length Este determina para cuantos caracteres truncar.
     * @param $etc Este es el texto para adicionar si el truncamiento ocurre. La longitud NO se incluye para la logitud del truncamiento
     * @param $break_words Este determina cuando truncar o no o al final de una palabra(false), o un caracter exacto(true).
     * @param $middle Este determina cuando ocurre el truncamiento al final de la cadena(false), o en el centro de la cadena(true). Nota cuando este es true, entonces la palabra limite es ignorada.
     * @return String

     */
    public static function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
        if ($length == 0)
            return '';

        if (strlen($string) > $length) {
            $length -= strlen($etc);
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
            }
            if (!$middle) {
                return substr($string, 0, $length) . $etc;
            } else {
                return substr($string, 0, $length / 2) . $etc . substr($string, -$length / 2);
            }
        } else {
            return $string;
        }
    }

    /**
     * Le da formato a un texto que va a ser utilizado en url semantica.
     * @param $str es la cadena que queremos utilizar en la url
     * @return String

     */
    public static function formatUrl($str) {
        if (@preg_match('/.+/u', $str))
            $str = utf8_decode($str);

        $str = htmlentities($str);
        $str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/', '$1', $str);
        $str = html_entity_decode($str);
        $str = strtolower($str);
        $str = str_replace("Ã§", "c", $str);
        $str = preg_replace('@[ =()/\'\"\:\+\!\â€œ\â€�\â€˜\â€™\Â¡\Â¿\?\Âº\,\;\$\&\#\%\Â´\Â·\.\@\Â«\Â»]+@', '-', trim($str));
        $str = preg_replace('@[\W]+@', '-', $str);
        $str = preg_replace('@[-]*[^A-Za-z0-9._,]@', '-', $str);
        $str = preg_replace('@^[-]@', '', $str);
        $str = preg_replace('@([-])$@', '', $str);

        $str = strtolower($str);
        if (empty($str))
            $str = 'none';

        return $str;
    }

    /**
     * Retorna el nombre del navegador que el usuario esta usando para acceder a la Web.
     * @return String

     */
    public static function getBrowser() {
        $httpUserAgent = strtolower($_SERVER["HTTP_USER_AGENT"]);
        if (substr_count($httpUserAgent, 'msie 7.0'))//Internet explorer
            return 'IE7';

        elseif (substr_count($httpUserAgent, 'msie 8.0'))//Internet explorer
            return 'IE8';

        elseif (substr_count($httpUserAgent, 'msie 9.0'))//Internet explorer
            return 'IE9';
        elseif (substr_count($httpUserAgent, 'msie'))//Internet explorer
            return 'IE';
        elseif (substr_count($httpUserAgent, 'chrome'))
            return 'Chrome';
        elseif (substr_count($httpUserAgent, 'firefox'))
            return 'Firefox';
        elseif (substr_count($httpUserAgent, 'safari'))
            return 'Safari';
        else
            return 'Otro';
    }

    public static function lastDay($month, $year) {
        return strftime("%d", mktime(0, 0, 0, $month + 1, 0, $year));
    }

    public static function date_diff($d1, $d2) {
        /* compares two timestamps and returns array with differencies (year, month, day, hour, minute, second)
         */
        //check higher timestamp and switch if neccessary
        if ($d1 < $d2) {
            $temp = $d2;
            $d2 = $d1;
            $d1 = $temp;
        } else {
            $temp = $d1; //temp can be used for day count if required
        }
        $d1 = date_parse(date("Y-m-d H:i:s", $d1));
        $d2 = date_parse(date("Y-m-d H:i:s", $d2));
        //seconds
        if ($d1['second'] >= $d2['second']) {
            $diff['second'] = $d1['second'] - $d2['second'];
        } else {
            $d1['minute']--;
            $diff['second'] = 60 - $d2['second'] + $d1['second'];
        }
        //minutes
        if ($d1['minute'] >= $d2['minute']) {
            $diff['minute'] = $d1['minute'] - $d2['minute'];
        } else {
            $d1['hour']--;
            $diff['minute'] = 60 - $d2['minute'] + $d1['minute'];
        }
        //hours
        if ($d1['hour'] >= $d2['hour']) {
            $diff['hour'] = $d1['hour'] - $d2['hour'];
        } else {
            $d1['day']--;
            $diff['hour'] = 24 - $d2['hour'] + $d1['hour'];
        }
        //days
        if ($d1['day'] >= $d2['day']) {
            $diff['day'] = $d1['day'] - $d2['day'];
        } else {
            $d1['month']--;
            $diff['day'] = date("t", $temp) - $d2['day'] + $d1['day'];
        }
        //months
        if ($d1['month'] >= $d2['month']) {
            $diff['month'] = $d1['month'] - $d2['month'];
        } else {
            $d1['year']--;
            $diff['month'] = 12 - $d2['month'] + $d1['month'];
        }
        //years
        $diff['year'] = $d1['year'] - $d2['year'];
        return $diff;
    }
    
    
     public static function  time_diff($dt1, $dt2) {
        $y1 = substr($dt1, 0, 4);
        $m1 = substr($dt1, 5, 2);
        $d1 = substr($dt1, 8, 2);
        $h1 = substr($dt1, 11, 2);
        $i1 = substr($dt1, 14, 2);
        $s1 = substr($dt1, 17, 2);

        $y2 = substr($dt2, 0, 4);
        $m2 = substr($dt2, 5, 2);
        $d2 = substr($dt2, 8, 2);
        $h2 = substr($dt2, 11, 2);
        $i2 = substr($dt2, 14, 2);
        $s2 = substr($dt2, 17, 2);

        $r1 = date('U', mktime($h1, $i1, $s1, $m1, $d1, $y1));
        $r2 = date('U', mktime($h2, $i2, $s2, $m2, $d2, $y2));
        return ($r1 - $r2);
    }

    /**
     * Crea un resize de una imagen
     *
     * @param integer $width Ancho.
     * @param integer $height Alto.
     * @param string $path ruta donde se guarda la iamgen y resizes
     * @param string $name Nombre del archivo.
     * @author randrade
     */
    public static function createResize($width, $height, $path, $name) {
        Base::import('application.helpers.Thumbnail');
        $thumbnail = new Thumbnail($path . 'original/' . $name);
        $dimensions = array();
        $dimensions = $thumbnail->currentDimensions;

        if ($dimensions['width'] >= $dimensions['height'])
            $thumbnail->resize($width, 0);
        else
            $thumbnail->resize(0, $height);

        $thumbnail->save($path . $width . 'x' . $height . '/' . $name);
        $thumbnail->destruct();
    }

    public static function listar_ficheros($tipos, $carpeta) {

        $arrFiles = array();
        $arrFilesNames = array();

        //Comprobamos que la carpeta existe  
        if (is_dir($carpeta)) {
            //Escaneamos la carpeta usando scandir  
            $scanarray = scandir($carpeta);
            for ($i = 0; $i < count($scanarray); $i++) {
                //Eliminamos  "." and ".." del listado de ficheros  
                if ($scanarray[$i] != "." && $scanarray[$i] != "..") {
                    //No mostramos los subdirectorios  
                    if (is_file($carpeta . "/" . $scanarray[$i])) {
                        //Verificamos que la extension se encuentre en $tipos  
                        $thepath = pathinfo($carpeta . "/" . $scanarray[$i]);
                        if (in_array($thepath['extension'], $tipos)) {
                            $arrFiles[] = $scanarray[$i];
                            $arrFilesNames[] = $thepath['filename'];
                        }
                    }
                }
            }
            return array($arrFiles, $arrFilesNames);
        } else {
              throw new siFunctionsException("la ruta $carpeta. no existe");
            
        }
    }

}
