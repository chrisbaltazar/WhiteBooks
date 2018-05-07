<?php 
    function insertMail($remit, $mail, $subject, $content, $name = null){
        Illuminate\Support\Facades\DB::connection('intranet')->insert("
            insert into enviacorreos (Fecha_Creacion, Fecha_Programada, Remitente, Correo, Nombre, Asunto, Contenido) values(NOW(), NOW(), ?, ?, ?, ?, ?)", 
            [$remit, $mail, $name, $subject, $content]
        );
    }
   
    function validateTime($time){
        $exp = explode(":", $time);
        if(count($exp) > 1){
            if(is_numeric($exp[0]) && $exp[0] >= 0 && $exp[0] <= 23){
                if(is_numeric($exp[1]) && $exp[1] >= 0 && $exp[1] <= 59){
                    if(!$exp[2] || (is_numeric($exp[2]) && $exp[2] >= 0 && $exp[2] <= 59))
                        return true;
                }
            }
        }
        return false;
    }
    
    function resizeImg($ruta, $nombre, $alto, $ancho, $nombreN, $extension, $calidad){
        $rutaImagenOriginal = $ruta.$nombre;
        if($extension == 'GIF' || $extension == 'gif'){
            $img_original = imagecreatefromgif($rutaImagenOriginal);
        }
        if($extension == 'jpg' || $extension == 'JPG'){
            $img_original = imagecreatefromjpeg($rutaImagenOriginal);
        }
        if($extension == 'png' || $extension == 'PNG'){
            $img_original = imagecreatefrompng($rutaImagenOriginal);
        }
        $max_ancho = $ancho;
        $max_alto = $alto;
        list($ancho,$alto)=getimagesize($rutaImagenOriginal);
        $x_ratio = $max_ancho / $ancho;
        $y_ratio = $max_alto / $alto;
        if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){//Si ancho
            $ancho_final = $ancho;
            $alto_final = $alto;
        } elseif (($x_ratio * $alto) < $max_alto){
            $alto_final = ceil($x_ratio * $alto);
            $ancho_final = $max_ancho;
        } else{
            $ancho_final = ceil($y_ratio * $ancho);
            $alto_final = $max_alto;
        }
        $tmp=imagecreatetruecolor($ancho_final,$alto_final);
        imagecopyresampled($tmp,$img_original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
        imagedestroy($img_original);
        return imagejpeg($tmp,$ruta.$nombreN,$calidad);
    }
    
    function UpperCase($str){
        $search = array("á", "é", "í", "ó", "ú", "ñ");
        $rep = array("Á", "É", "Í", "Ó", "Ú", "Ñ");
        $str = str_replace($search, $rep, $str);
        return strtoupper($str);
    }
    
    function LowerCase($str){
        $search = array("Á", "É", "Í", "Ó", "Ú");
        $rep = array("á", "é", "é", "ó", "ú");
        $str = str_replace($search, $rep, $str);
        return strtolower($str);
    }
    
    function getModule(){
        $pos = strrpos($_SERVER['SCRIPT_NAME'], "/");
        $module = substr($_SERVER['SCRIPT_NAME'], $pos + 1, strlen($_SERVER['SCRIPT_NAME']) - $pos);
        return $module;
    }
    
    function ValidateMail($mail){
        $first = explode("@", $mail);
        $second = explode(".", $first[1]);
        if(count($second) > 1){
            if(strlen($first[0]) == 0)
                return "El correo ingresado parece incorrecto";
        }else
            return "El correo ingresado parece incorrecto";
    }
   
    
    function quitarAcentos($text, $quitspace){
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        $text = strtolower($text);
        $patron = ($quitspace?"/[\, ]+/":"/[\,]+/");
        $patron = array (
                // Espacios, puntos y comas por guion
                 $patron => '_',

                // Vocales
                '/&agrave;/' => 'a',
                '/&egrave;/' => 'e',
                '/&igrave;/' => 'i',
                '/&ograve;/' => 'o',
                '/&ugrave;/' => 'u',

                '/&aacute;/' => 'a',
                '/&eacute;/' => 'e',
                '/&iacute;/' => 'i',
                '/&oacute;/' => 'o',
                '/&uacute;/' => 'u',

                '/&acirc;/' => 'a',
                '/&ecirc;/' => 'e',
                '/&icirc;/' => 'i',
                '/&ocirc;/' => 'o',
                '/&ucirc;/' => 'u',

                '/&atilde;/' => 'a',
                '/&etilde;/' => 'e',
                '/&itilde;/' => 'i',
                '/&otilde;/' => 'o',
                '/&utilde;/' => 'u',

                '/&auml;/' => 'a',
                '/&euml;/' => 'e',
                '/&iuml;/' => 'i',
                '/&ouml;/' => 'o',
                '/&uuml;/' => 'u',

                '/&auml;/' => 'a',
                '/&euml;/' => 'e',
                '/&iuml;/' => 'i',
                '/&ouml;/' => 'o',
                '/&uuml;/' => 'u',

                // Otras letras y caracteres especiales
                '/&aring;/' => 'a',
                '/&ntilde;/' => ($quitspace?'N':'Ñ')

                // Agregar aqui mas caracteres si es necesario

        );
        $text = preg_replace(array_keys($patron),array_values($patron),$text);
        return $text;
    }
    
    function setGrid($grid, $params, $paging = false, $multiline = false){
        $html = $grid . " = new dhtmlXGridObject('" . $grid . "');"
            . $grid . ".setImagePath('js/dhtmlx/imgs/');"
            . $grid . ".enableSmartRendering(true);"
            . $grid . ".setSkin('dhx_skyblue');"
            . $grid . ".setHeader('" . DisplayHeaderGrid($params, "Header") . "',null,[" . styleHeaderGrid(count($params)) . "]);"
            . $grid . ".setInitWidths('" . DisplayHeaderGrid($params, "Width") . "');"
            . $grid . ".attachHeader('" . DisplayHeaderGrid($params, "Attach") . "');"
            . $grid . ".setColAlign('" . DisplayHeaderGrid($params, "Align") . "');"
            . $grid . ".setColSorting('" . DisplayHeaderGrid($params, "Sort") . "');"
            . $grid . ".setColTypes('" . DisplayHeaderGrid($params, "Type") . "');"
            . $grid . ".enableMultiline(" . ($multiline?"true":"false") . ");";
        if($paging){
            $html .=  $grid . ".enablePaging(true,100,null,'pager_" . $grid . "',true,'infopage_" . $grid . "');"
                   . $grid . ".setPagingSkin( 'toolbar', 'dhx_skyblue' );";		 
        }
        $html .= $grid . ".init();"
               . $grid . ".attachEvent('onFilterEnd', function(){ CountGridRows(" . $grid . "); });";
        $footer = "Total,";
        for($i=1; $i<count($params); $i++){
            $cspan = false;
            if($params[$i]['Type'] == "edn")
                $html.= $grid . ".setNumberFormat('0,000.00'," . $i . ");";
            if(isset($params[$i]['Sum'])){
                $footer .= "#stat_total,";
                $cspan = true;
            }elseif(!$cspan){
                $footer .= "#cspan,";
            }else{
                $footer .= ",";
            }
//            $footer .= ($params[$i][Sum] ? "#stat_total" : " ") . ",";
        }
        if(substr_count($footer, "stat_total"))
            $html.= $grid . ".attachFooter('" . substr($footer, 0, -1) . "');";
        echo $html;	
    }

    function styleHeaderGrid($num){
            $style=str_repeat('"text-align:center;font-size:8pt;font-weight:bold;vertical-align:middle;",',$num); 
            $style=substr($style,0,strlen($style)-1);
            return $style;
    }

    function DisplayHeaderGrid($array, $key){
            $val = "";
            for($i=0; $i<count($array)-1; $i++)
                    $val .= ParseFilter($array[$i][$key]) . ",";
            $val .= ParseFilter($array[count($array)-1][$key]);
            return $val;
    }

    function ParseFilter($val){
            switch($val){
                    case "txt": return "#text_filter"; break;
                    case "cmb": return "#select_filter"; break;
                    case "": return ""; break;
                    default: return $val; break;
            }
    }

    function DayOfWeek($year, $month, $day, $format = 'str'){
         $day = date("w",mktime(0, 0, 0, $month, $day, $year));
         $week = array(1 => "Lunes", 
                       2 => "Martes", 
                       3 => "Miércoles", 
                       4 => "Jueves", 
                       5 => "Viernes", 
                       6 => "Sábado", 
                       7 => "Domingo");
         if($format == 'int')
             return $day;                  
         else
             return $week[$day];
    }
    
    function ClearString($str){
//        return str_replace("\"", "'", eregi_replace("[\n|\r|\n\r]", " ", $str));
//        $search = array("[\n]", "[\r]", "[\n\r]");
//        return eregi_replace($search, " ", $str);
        $buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
        return str_ireplace($buscar," ",$str);
    }
    
    function format($str, $total, $char){
        $add = "";
        for($i=0; $i<$total - strlen($str); $i++)
                $add .= $char;
        return $add . $str;
    }
    
    
     
     function BodyMail($subject, $name, $text){
            return utf8_decode("<table style = 'border: 2px outset #084773; border-collapse: collapse; font-size: 9pt; '>
                        <tr>
                            <td style = 'border: 2px outset #084773; padding: 5px; text-align: center; '>
                                <p><b>Secretaría de la Transparencia y Rendición de Cuentas </b></p>
                                <p><b>Gobierno del Estado de Guanajuato</b></p>
                            </td>
                        </tr>
                        <tr>
                            <td width = '700' style = 'border: 2px outset #084773; padding: 5px;'><center>" . $subject . "</center></td>
                        </tr>
                        <tr>
                            <td width = '700' style = 'border: 2px outset #084773; padding: 5px;'>
                                <p><b>Estimado(a): " . $name . "</b></p><p>" . $text . "</p>
                            </td>
                        </tr>
                        <tr>
                            <td style = 'color: red'><center><b>Favor de no responder este mensaje</b></center></td>
                        </tr>
                 </table>");
    }
    
    function Month($m){
        $array = array("01" => "Enero", 
                       "02" => "Febrero", 
                       "03" => "Marzo", 
                       "04" => "Abril", 
                       "05" => "Mayo", 
                       "06" => "Junio", 
                       "07" => "Julio", 
                       "08" => "Agosto", 
                       "09" => "Septiembre", 
                       "10" => "Octubre", 
                       "11" => "Noviembre", 
                       "12" => "Diciembre");
        return $array[$m];
    }
    
    function SimpleDate($datetime){
        $xplode = explode(" ", trim($datetime));
        $date = $xplode[0];
        $time = $xplode[1];
        $array = array(
            "01" => "Ene",
            "02" => "Feb", 
            "03" => "Mar", 
            "04" => "Abr", 
            "05" => "May", 
            "06" => "Jun", 
            "07" => "Jul", 
            "08" => "Ago", 
            "09" => "Sep", 
            "10" => "Oct", 
            "11" => "Nov", 
            "12" => "Dic"
        );
        if($date){
            $exp = explode("-", $date);
            if(is_numeric($exp[1]))
                return trim($exp[2] . "-" . $array[$exp[1]] . "-" . $exp[0] . " " . $time);
            else{
                $key = array_keys($array, $exp[1]);
                return trim($exp[2] . "-" . $key[0] . "-" . $exp[0] . " " . $time);
            }
        }
    }
    
    function lastDayMonth($year, $month){
        return date("d",(mktime(0,0,0,$month+1,1,$year)-1));
    }
    
   