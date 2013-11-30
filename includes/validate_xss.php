<?php

/*
  --------------------------------------------------------------
  PROTECCION A INYECCION DE CODIGO POR XSS (Cross Site Scripting) Y SQL INJECTION 
  --------------------------------------------------------------
 */

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Variables con las expresiones regulares corespondientes a las palabras reservadas a validar
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* --------------------------------------------------------------------------------------------
  DESCRIPCION:
  searchXSS(). Busca codigo malisioso, esta es la funcin que se debe utilizar en cada scrip
  donde deseemos validar cross site.

  PARAMETROS:

  RETORNO:
  boolean - (true -> cdigo malisioso, false -> cdigo limpio)
  ACCESO: public
  AUTOR: Adrian Certuche
  FECHA DE CREACIN: 09/07/2008
  MODIFICACIONES:
  05/09/2007  Se cambi el nombre de una variable por .   Autor: xxxx
  -------------------------------------------------------------------------------------------- */
function searchXSS() {
    if ($_GET)
        if (search_eviltags($_GET)) {
            exorcize();
            return true;
        }

    if ($_POST)
        if (search_eviltags($_POST)) {
            exorcize();
            return true;
        }

    if ($_COOKIE)
        if (search_eviltags($_COOKIE)) {
            exorcize();
            return true;
        }

    return false;
}

/* --------------------------------------------------------------------------------------------
  DESCRIPCION:
  exorcize(). Reinicia  -borra los arreglos globales que puede emplear un atacante.

  PARAMETROS:

  RETORNO:
  void - (Redirecciona a una url expecificada)
  ACCESO: privado
  AUTOR: Adrian Certuche
  FECHA DE CREACIN: 09/07/2008
  MODIFICACIONES:
  05/09/2007  Se cambi el nombre de una variable por .   Autor: xxxx
  -------------------------------------------------------------------------------------------- */

function exorcize() {
    //Se ha encontrado cdigo malisioso y se ha llamado al exorcista para
    //remediar la situacin
    //Vaciar variables
    $_REQUEST = array();
    $_GET = array();
    $_POST = array();
    $_COOKIE = array();
    @session_destroy();
    $_SESSION = array();
}

/* --------------------------------------------------------------------------------------------
  DESCRIPCION:
  is_eviltags($texto). Esta funcin recibe por parametro la cadena de texto que se desea
  evaluar,esta cadena es comparada con un arreglo que contiene posibles palabras
  (expresiones regulares) que indican un ataque cross site.

  PARAMETROS:
  $texto:string - Cadena de texto que se desea evaluar.
  RETORNO:
  boolean - (true -> cdigo malisioso, false -> cdigo limpio)
  GLOBAL REFERENCES:
  $patronXSS:array
  ACCESO: privado
  AUTOR: Adrian Certuche
  FECHA DE CREACIN: 09/07/2008
  MODIFICACIONES:
  05/09/2007  Se cambi el nombre de una variable por .   Autor: xxxx
  -------------------------------------------------------------------------------------------- */

function is_eviltags($texto) {


    $comillaS = "(\%27|\')";
    $espacio = "(\s|\%0A|\%20)";

    $from = "(\%66|f|\%46)(\%72|r|\%52)(\%6F|o|\%4F)(\%6D|m|\%4D)";
    $union = "(\%75|u|\%55)(\%6E|n|\%4E)(\%69|i|\%49)(\%6F|o|\%4F)(\%6E|n|\%4E)";
    $select = "(\%73|s|\%53)(\%65|e|\%45)(\%6C|l|\%4C)(\%65|e|\%45)(\%63|c|\%43)(\%74|t|\%54)";
    $into = "(\%69|i|\%49)(\%6E|n|\%4E)(\%74|t|\%54)(\%6F|o|\%4F)";
    $set = "(\%73|s|\%53)(\%65|e|\%45)(\%74|t|\%54)";
    $insert = "(\%69|i|\%49)(\%6E|n|\%4E)(\%73|s|\%53)(\%65|e|\%45)(\%72|r|\%52)(\%74|t|\%54)";
    $update = "(\%75|u|\%55)(\%70|p|\%50)(\%64|d|\%44)(\%61|a|\%41)(\%74|t|\%54)(\%65|e|\%45)";
    $delete = "(\%64|d|\%44)(\%65|e|\%45)(\%6C|l|\%4C)(\%65|e|\%45)(\%74|t|\%54)(\%65|e|\%45)";
    $drop = "(\%64|d|\%44)(\%72|r|\%52)(\%6F|o|\%4F)(\%70|p|\%50)";
    $create = "(\%63|c|\%43)(\%72|r|\%52)(\%65|e|\%45)(\%61|a|\%41)(\%74|t|\%54)(\%65|e|\%45)";
    $grant = "(\%67|g|\%47)(\%72|r|\%52)(\%61|a|\%41)(\%6E|n|\%4E)(\%74|t|\%54)";
    $revoke = "(\%72|r|\%52)(\%65|e|\%45)(\%76|v|\%56)(\%6F|o|\%4F)(\%6B|k|\%4B)(\%65|e|\%45)";
    $replace = "(\%72|r|\%52)(\%65|e|\%45)(\%70|p|\%50)(\%6C|l|\%4C)(\%61|a|\%41)(\%63|c|\%43)(\%65|e|\%45)";
    $traslate = "(\%74|t|\%54)(\%72|r|\%52)(\%61|a|\%41)(\%73|s|\%53)(\%6C|l|\%4C)(\%61|a|\%41)(\%74|t|\%54)(\%65|e|\%45)";
    $truncate = "(\%74|t|\%54)(\%72|r|\%52)(\%75|u|\%55)(\%6E|n|\%4E)(\%63|c|\%43)(\%61|a|\%41)(\%74|t|\%54)(\%65|e|\%45)";
    $table = "(\%74|t|\%54)(\%61|a|\%41)(\%62|b|\%42)(\%6C|l|\%4C)(\%65|e|\%45)";
    $partition = "(\%70|p|\%50)(\%61|a|\%41)(\%72|r|\%52)((\%74|t|\%54)(\%69|i|\%49)){2}(\%6F|o|\%4F)(\%6E|n|\%4E)";
    $alter = "(\%61|a|\%41)(\%6C|l|\%4C)(\%74|t|\%54)(\%65|e|\%45)(\%72|r|\%52)";
    $like = "(\%6C|l|\%4C)(\%69|i|\%49)(\%6B|k|\%4B)(\%65|e|\%45)";


    $igual = "((=)|\%3D|&\#x0*3D;?|&\#61)";
    $menorQ = "(<|\%3C|&\#x0*3C;?|&\#0*60;?|&lt;?|\\x3C|\\\u003C)";
    $mayorQ = "(>|\%3E|&\#x0*3E;?|&\#0*62;?|&gt;?|\\x3E|\\\u003E)";
    $dosPuntos = "((:)|%3A|&#x3A;|&#58)";

    $img = "(i|\%69|\%49|&\#x0*69;?|&\#x0*49;?|&\#105|&\#73)(m|\%6D|\%4D|&\#x0*6D;?|&\#x0*4D;?|&\#109|&\#77)(g|\%67|\%47|&\#x0*67;?|&\#x0*47;?|&\#103|&\#71)";

    $body = "(b|\%62|\%42|&\#x0*62;?|&\#x0*42;?|&\#98|&\#66)(o|\%6F|\%4F|&\#x0*6F;?|&\#x0*4F;?|&\#111|&\#79)(d|\%64|\%44|&\#x0*64;?|&\#x0*44;?|&\#100|&\#68)(y|\%79|\%59|&\#x0*79;?|&\#x0*59;?|&#121|&\#89)";

    $script = "(s|\%73|\%53|&\#x0*73;?|&\#x0*53;?|&\#115|&\#83)(c|\%63|\%43|&\#x0*63;?|&\#x0*43;?|&\#99|&\#67)(r|\%72|\%52|&\#x0*72;?|&\#x0*52;?|&\#114|&\#82)(i|\%69|\%49|&\#x0*69;?|&\#x0*49;?|&\#105|&\#73)(p|\%70|\%50|&\#x0*70;?|&\#x0*50;?|&\#112|&\#80)(t|\%74|\%54|&\#x0*74;?|&\#x0*54;?|&\#116|&\#84)";

    $iframe = "(i|\%69|\%49|&\#x0*69;?|&\#x0*49;?|&\#105)(f|\%66|\%46|&\#x0*66;?|&\#x0*46;?|&\#102)(r|\%72|\%52|&\#x0*72;?|&\#x0*52;?|&\#114)(a|\%61|\%41|&\#x0*61;?|&\#x0*41;?|&\#97)(m|\%6D|\%4D|&\#x0*6D;?|&\#x0*4D;?|&\#109)(e|\%65|\%45|&\#x0*65;?|&\#x0*45;?|&\#101)";

    $input = "(i|\%69|\%49|&\#x0*69;?|&\#x0*49;?|&\#105|&\#73)(n|\%6E|\%4E|&\#x0*6E;?|&\#x0*4E;?|&\#110|&\#78)(p|\%70|\%50|&\#x0*70;?|&\#x0*50;?|&\#112|&\#80)(u|\%75|\%55|&\#x0*75;?|&\#x0*55;?|&\#117|&\#85)(t|\%74|\%54|&\#x0*74;?|&\#x0*54;?|&\#116|&\#84)";

    $bgsound = "(b|\%62|\%42|&\#x0*62;?|&\#x0*62;?|&\#98|&\#66)(g|\%67|\%47|&\#x0*67;?|&\#x0*47;?|&\#103|&\#71)(s|\%73|\%53|&\#x0*73;?|&\#x0*53;?|&\#115|&\#83)(o|\%6F|\%4F|&\#x0*6F;?|&\#x0*4F;?|&\#111|&\#79)(u|\%75|\%55|&\#x0*75;?|&\#x0*55;?|&\#117|&\#85)(n|\%6E|\%4E|&\#x0*6E;?|&\#x0*4E;?|&\#110|&\#78)(d|\%64|\%44|&\#x0*64;?|&\#x0*44;?|&\#100|&\#68)";

    $layer = "(l|\%6C|\%4C|&\#x0*6C;?|&\#x0*6C;?|&\#108|&\#76)(a|\%61|\%41|&\#x0*61;?|&\#x0*41;?|&\#97|&\#65)(y|\%79|\%59|&\#x0*79;?|&\#x0*59;?|&\#121|&\#89)(e|\%65|\%45|&\#x0*65;?|&\#x0*45;?|&\#101|&\#69)(r|\%72|\%52|&\#x0*72;?|&\#x0*52;?|&\#114|&\#82)";

    $style = "(s|\%73|\%53|&\#x0*73;?|&\#x0*73;?|&\#115|&\#83)(t|\%74|\%54|&\#x0*74;?|&\#x0*54;?|&\#116|&\#84)(y|\%79|\%59|&\#x0*79;?|&\#x0*59;?|&\#121|&\#89)(l|\%6C|\%4C|&\#x0*6C;?|&\#x0*4C;?|&\#108|&\#76)(e|\%65|\%45|&\#x0*65;?|&\#x0*45;?|&\#101|&\#69)";

    $link = "(l|\%6C|\%4C|&\#x0*6C;?|&\#x0*6C;?|&\#108|&\#76)(i|\%69|\%49|&\#x0*69;?|&\#x0*49;?|&\#105|&\#73)(n|\%6E|\%4E|&\#x0*6E;?|&\#x0*4E;?|&\#110|&\#78)(k|\%6B|\%4B|&\#x0*6B;?|&\#x0*4B;?|&\#107|&\#75)";

    $meta = '(m|\%6D|\%4D|&\#x0*6D;?|&\#x0*4D;?|&\#109|&\#77)(e|\%65|\%45|&\#x0*65;?|&\#x0*45;?|&\#101|&\#69)(t|\%74|\%54|&\#x0*74;?|&\#x0*54;?|&\#116|&\#84)(a|\%61|\%41|&\#x0*61;?|&\#x0*41;?|&\#97|&\#65)';

    $br = "(b|\%62|\%42|&\#x0*62;?|&\#x0*42;?|&#98|&#66)(r|\%72|\%52|&\#x0*72;?|&\#x0*52;?|&#114|&#82)";

    $src = "(s|\%73|\%53|&\#x0*73;?|&\#x0*53;?|&\#115|&\#83)(r|\%72|\%52|&\#x0*72;?|&\#x0*52;?|&\#114|&\#82)(c|\%63|\%43|&\#x0*63;?|&\#x0*43;?|&\#99|&\#67)";

    $javascript = "(j|\%6A|\%4A|&\#x0*6A;?|&\#x0*4A;?|&\#106|&\#74)(\r|\n)*(a|\%61|\%41|&\#x0*61;?|&\#x0*41;?|&\#97|&\#65)(\r|\n)*(v|\%76|\%56|&\#x0*76;?|&\#x0*56;?|&\#118|&\#86)(\r|\n)*(a|\%61|\%41|&\#x0*61;?|&\#x0*41;?|&\#97|&\#65)(\r|\n)*(s|\%73|\%53|&\#x0*73;?|&\#x0*53;?|&\#115|&\#83)(\r|\n)*(c|\%63|\%43|&\#x0*63;?|&\#x0*43;?|&\#99|&\#67)(\r|\n)*(r|\%72|\%52|&\#x0*72;?|&\#x0*52;?|&\#114|&\#82)(\r|\n)*(i|\%69|\%49|&\#x0*69;?|&\#x0*49;?|&\#105|&\#73)(\r|\n)*(p|\%70|\%50|&\#x0*70;?|&\#x0*50;?|&\#112|&\#80)(\r|\n)*(t|\%74|\%54|&\#x0*74;?|&\#x0*54;?|&\#116|&\#84)";

    $vbscript = "(v|\%76|\%56|&\#x0*76;?|&\#x0*56;?|&#118|&#86)(b|\%62|\%42|&\#x0*62;?|&\#x0*42;?|&#98|&#66)(s|\%73|\%53|&\#x0*73;?|&\#x0*53;?|&\#115|&\#83)(c|\%63|\%43|&\#x0*63;?|&\#x0*43;?|&\#99|&\#67)(r|\%72|\%52|&\#x0*72;?|&\#x0*52;?|&\#114|&\#82)(i|\%69|\%49|&\#x0*69;?|&\#x0*49;?|&\#105|&\#73)(p|\%70|\%50|&\#x0*70;?|&\#x0*50;?|&\#112|&\#80)(t|\%74|\%54|&\#x0*74;?|&\#x0*54;?|&\#116|&\#84)";

    $applet = "(a|\%61|\%41|&\#x0*61;?|&\#x0*41;?|&\#97|&\#65)(p|\%70|\%50|&\#x0*70;?|&\#x0*50;?|&\#112|&\#80){2}(l|\%6C|\%4C|&\#x0*6C;?|&\#x0*6C;?|&\#108|&\#76)(e|\%65|\%45|&\#x0*65;?|&\#x0*45;?|&\#101|&\#69)(t|\%74|\%54|&\#x0*74;?|&\#x0*54;?|&\#116|&\#84)";


    $form = "(f|\%66|\%46|&\#x0*66;?|&\#x0*46;?|&\#102)(o|\%6F|\%4F|&\#x0*6F;?|&\#x0*4F;?|&\#111|&\#79)(r|\%72|\%52|&\#x0*72;?|&\#x0*52;?|&\#114|&\#82)(m|\%6D|\%4D|&\#x0*6D;?|&\#x0*4D;?|&\#109|&\#77)";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Array q contendra los patrones/vectores XSS
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $patronXSS = array();


//XSS SQL INJECTION
/////////////////////////////////////////////////////////////////////////////////////////////////
// '/**/'
    $patronXSS[] = "{(/|\%2F)(\*|\%2A)(.|\s|\%0A|\%20)*(\*|\%2A)(/|\%2F)}";

//Deteccion ( -- ), ( # ), ( ; )
//$patronXSS[] = '/'.$espacio.'*((\-\-)|(\%2D\%2D)|\%23|\#|\%3B|;)/x';
//$patronXSS[] = '/'.$espacio.'*((\-\-)|(\%2D\%2D))/x';
//Deteccion de un tipico ataque de injeccion SQL ( ' or 1 = 1 )
    $patronXSS[] = '/\w*' . $comillaS . '*' . '(' . $espacio . '|\))(\%6F|o|\%4F)(\%72|r|\%52)' . '(' . $espacio . '|\)).*(' . $igual . '|' . $menorQ . '|' . $mayorQ . '|' . $like . ')/ix';

//SELECT
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '*' . $select . $espacio . '*(\*|\%2A|\w{1,}|\d{0,})' . $espacio . '*' . $from . '/ix';

//INSERT
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '*' . $insert . $espacio . '+' . $into . '/ix';

//UPDATE
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '*' . $update . $espacio . '+.+' . $espacio . '+' . $set . '/ix';

//DELETE
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '+' . $delete . '/ix';

//DROP
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '+' . $drop . '/ix';

//UNION
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '*' . $union . $espacio . '+(\(|%28)*.+/ix';

//CREATE
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '*' . $create . $espacio . '+.+/ix';

//GRANT
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '*' . $grant . $espacio . '+.+/ix';

//REVOKE
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '*' . $revoke . $espacio . '+.+/ix';

//REPLACE
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '*.*' . $espacio . $replace . $espacio . '+\(.*\)/ix';

//TRASLATE
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '*.*' . $espacio . $traslate . $espacio . '+\(.*\)/ix';

//TRUNCATE
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '*.*' . $espacio . $truncate . $espacio . '+(' . $table . '|' . $partition . ')/ix';

//ALTER TABLE
    $patronXSS[] = '/' . $espacio . '*' . $comillaS . '*' . $espacio . '*' . $alter . $espacio . '+' . $table . '/ix';

//Deteccion de ataque de injeccion SQL sobre MS SQL Server
//$patronXSS[] = "/exec(\s|\+)+(s|x)p\w+/ix";
//XSS SCRIPTING
/////////////////////////////////////////////////////////////////////////////////////////////////
//Deteccion Paranoica de XSS ( < ALGO > )
//$patronXSS[] = '/'.$menorQ.'+.*'.$mayorQ.'+/i';
// <algo src= 
    $patronXSS[] = '/' . $menorQ . '.*' . $espacio . '*' . $src . $espacio . '*' . $igual . '/ix';
//
//<IMG
    $patronXSS[] = '/' . $menorQ . $img . $espacio . '*[^ ]*' . $espacio . '*/i';
//
//<BODY algo >
    $patronXSS[] = '/' . $menorQ . $body . $espacio . '*[^ ]*' . $espacio . '*' . $mayorQ . '/i';

//<SCRIPT algo >
    $patronXSS[] = '/' . $menorQ . $script . $espacio . '*(.)*' . $mayorQ . '/i';

//<IFRAME algo
    $patronXSS[] = '/' . $menorQ . $iframe . $espacio . '*[^ ]*/i';
//
//<INPUT algo >
    $patronXSS[] = '/' . $menorQ . $input . $espacio . '*[^ ]*' . $espacio . '*' . $mayorQ . '*/i';

//<BGSOUND algo >
    $patronXSS[] = '/' . $menorQ . $bgsound . $espacio . '*[^ ]*' . $espacio . '*' . $mayorQ . '*/i';

//<BR algo=algo >
    $patronXSS[] = '/' . $menorQ . $br . $espacio . '+(.+)' . $igual . '+' . $espacio . '*(.+)' . $mayorQ . '*/i';

//<LAYER algo >
    $patronXSS[] = '/' . $menorQ . $layer . $espacio . '*[^ ]*' . $espacio . '*' . $mayorQ . '*/i';

//<LINK algo >
    $patronXSS[] = '/' . $menorQ . $link . $espacio . '*[^ ]*' . $espacio . '*' . $mayorQ . '*/i';

//<STYLE algo >
    $patronXSS[] = '/' . $menorQ . $meta . $espacio . '*[^ ]*' . $espacio . '*' . $mayorQ . '*/i';

//Algo JAVASCRIPT: algo
    $patronXSS[] = '/' . $menorQ . '*' . $javascript . $espacio . '*' . $dosPuntos . '/i';

//Algo VBSCRIPT: algo
    $patronXSS[] = '/' . $menorQ . '*' . $vbscript . $espacio . '*' . $dosPuntos . '/i';

//<APPLET
    $patronXSS[] = '/' . $menorQ . $applet . $espacio . '*[^ ]*/i';

//<FORM
    $patronXSS[] = '/' . $menorQ . $form . $espacio . '*[^ ]*/i';

    foreach ($patronXSS as $patron => $pt) {
        if (preg_match($pt, $texto, $coincidencias)) {
             Base::request()->errorHandle(406,"Error de seguridad!! intento de ataque XSS");
            return true;
        }
    }

    return false;
}

/* --------------------------------------------------------------------------------------------
  DESCRIPCION:
  search_eviltags($request). Recorre el arreglo que se le pasa como parametro evaluando
  (is_eviltags) si su contenido contiene cdigo malisioso. Es recursiva si encuentra
  que una de los contenidos del arreglo es otro arreglo.

  PARAMETROS:
  $request:array - $_POST, $_GET, $_COOKIE
  RETORNO:
  boolean (true - cdigo malisioso dentro del arreglo, false - cdigo limpio)
  ACCESO: privado
  AUTOR: Adrian Certuche
  FECHA DE CREACIN: 09/07/2008
  MODIFICACIONES:
  05/09/2007  Se cambi el nombre de una variable por .   Autor: xxxx
  -------------------------------------------------------------------------------------------- */

function search_eviltags($request) {
    $keys_request = array_keys($request);
    $tamArr = count($keys_request);
    $cont = 0;

    while ($cont < $tamArr) {
        $key_request = $keys_request[$cont];

        if (is_array($request[$key_request])) {
            search_eviltags($request[$key_request]); //Recursion
        } else {
            if (is_eviltags($request[$key_request]))
                return true;
        }
        ++$cont;
    }

    return false;
}

/* --------------------------------------------------------------------------------------------
  DESCRIPCION:
  validar_insercion_cabeceras(). Busca si se est intentando enviar alguna cabecera maliciosa por email

  PARAMETROS:

  RETORNO:
  boolean - (true -> cdigo malisioso, false -> cdigo limpio)
  ACCESO: public
  AUTOR: Juan Diego Ocampo
  FECHA DE CREACIN:
  MODIFICACIONES:
  05/09/2007  Se cambi el nombre de una variable por .   Autor: xxxx
  -------------------------------------------------------------------------------------------- */

function validar_insercion_cabeceras($campo) {
    /* --------------------------------------------------------------------------------------------
      Array con las posibles cabeceras a utilizar por un spammer.
      -------------------------------------------------------------------------------------------- */
    $badHeads = array("Content-Type:", "MIME-Version:", "Content-Transfer-Encoding:", "Return-path:", "Subject:", "From:", "Envelope-to:", "To:", "bcc:", "cc:", "<", ">", "%");
    /* --------------------------------------------------------------------------------------------
      Comprobamos que entre los datos no se encuentre alguna de las cadenas del array.
      -------------------------------------------------------------------------------------------- */
    foreach ($badHeads as $valor) {
        if (strpos(strtolower($campo), strtolower($valor)) !== false) {
            return 1;
        }
    }
    return 0;
}