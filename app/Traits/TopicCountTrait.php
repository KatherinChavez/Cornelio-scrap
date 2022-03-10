<?php

namespace App\Traits;

use App\Models\Attachment;
use App\Models\Page;
use App\Models\Post;
use App\Models\Reaction;
use Facebook;
use App\Models\Comment;
use Carbon\Carbon;


trait TopicCountTrait
{
    public function TopicCount($posts){
        $data=[];
        $string='';
        foreach($posts as $post) {
            if($post->type=="share"){
                $getWords = $post->content." ". $post->title;
            }else{
                $getWords= $post->content;
            }
            //función que quita tildes
            $getWords=$this->eliminar_acentos($getWords);

            $array_replace_words = array(
                "📱", "🎁", "💪", "🎄",'🌲', "🎁","✅", "🎅","✨", "👍",'👉🏻', '🤣','😄','🙂','👍', ' 𝗠a𝗦 ', "😈", "🔥","⭐","‼‼‼", ';', '¢', '$','.',
                "❤","😍","📲",'🤩','😱',"https://www.","/","amprensa.com","-", "¿","?",'‼', ",",'.','-','"', '“',"”", ":","¡","!", '|', '#',
                "_", "*","(", ")",'«','»', '%','+','"', "Crhoy.com",'Crhoy', ' .com ',' dar ',' al ', ' la ', ' las ', ' los ',' lo ','🔴',
                ' el ', ' le ',' a ', ' ante ', ' bajo ', ' cabe ' , ' con ', ' contra ', ' de ' , ' desde ' , ' en ',' su ', ' http ', ' youtube ',
                ' muy ', ' entre ', ' hasta ', ' hacia ' , ' para ', ' por ', ' según ', ' segun ', ' sin ', ' so ', ' sobre ', ' fue ', ' elpais ',
                ' tras ', ' durante ', ' mediante ', ' por ', ' pro ', ' versus ', ' via ', ' tras ', ' excepto ',' puedes ', ' costa ', ' rica ',
                ' mediante ', ' aunque ', ' y ', ' e ', ' ni ', ' o ', ' u ', ' pero ', ' sino ', ' mas ', ' que ', ' les ', ' escazu ', ' facebook ',
                ' ellos ', ' tu ', ' nosotros ', ' otras ', ' otra ', ' otros ', ' otro ', ' esta ', ' estan '," ahora ", " nos ", 'Repretel',
                ' estas ', 'estos ', ' esto ', ' es ', ' eso ', ' esas ', ' esos ', ' un ', ' una ', ' uno ', ' unos ', ' puede ', 'amprensa', ' nacional ',
                ' son ', ' tambien ', ' tiene ', ' tienen ', ' tampoco ', ' estaba ', ' estaban ', ' sin ', ' sino ', ' ha ', ' diario ', '  extra ',
                ' aun ', ' aunque ', ' menos ', ' mas ', ' alto ', ' hay ', ' ahi ', ' ah ', ' algo ', ' algunas ', ' ese ', 'Sinart ', ' teleticaradio ',
                ' algunos ', ' todo ', 'todos ', ' todas ', ' toda ', ' puedo ', ' puedas ', ' pueda ', ' tenga ', ' te ', ' me ', 'YonecesitoaSitrajud',
                ' se ', ' a ', ' despues ', ' va ', ' donde ', ' como ', ' cuando ', ' porque ', ' por ', ' que ', ' quien ', 'costarricenses',
                ' quienes ', ' habia ', ' hubo ', ' unir ', ' desde ', ' y ', ' si ', ' no ', ' ideal ', ' mi ', ' san ', ' han ', 'Sputnik',
                ' ja ', ' je ', ' de ', ' del ', ' fue ', ' fueron ', ' segun ', ' ya ', ' tarde ', ' dia ',  ' tenes ', ' parte ', 'NoticiasCRC',
                ' dar ', ' da ', ' cualquier ', ' modo ', ' bueno ', ' buenas ', ' facil ', ' pais ', ' despues ', ' desde ',' m ', 'Teletica',
                ' hasta ', ' total ', ' tal ', ' cual ', ' cualquier ', ' menos ', ' mas ', ' poco ', ' mucbo ', ' muchas ',  'ZFMTeMueve', 'Zeta',
                ' vez ', ' veces ', ' sera ', ' decia ', ' unir ', ' cree ', ' creer ', ' ante ', ' antes ', ' tener ', ' ya ', 'NoticiasTelediario',
                ' como ', ' decia ', ' comienza ', ' tenes ', ' son ', ' ultima ', ' ultimo ', ' ultimas ', ' ultimos ', ' pais ', 'AndresTeMueve',
                ' limite ', ' por ', ' x ', " 𝗘𝗦𝗧𝗔 ", ' MAYORES '," ante "," antes ", " 𝗠𝗔𝗦 "," 𝗬 ", ' definir ', ' ahi ', ' mas ', 'ncrnoticias',
                ' algo ', ' tu ', ' en ', ' estamos ', ' suerte ', ' ideal ', ' noticias ', ' ve ', ' va ', ' sabe ', ' saber ',
                ' comienza ', ' lea ', ' necesidad ', ' bueno ', ' buena ', ' ya ', ' sin ', ' solo ', ' sola ', ' ideal ', ' era ',
                ' eran ', ' total ', ' realizar ', ' tiempos ', ' vieron ', ' deben ', ' pasan ', ' pasar ', ' pasa ', ' fatal ',
                ' ocasiones ', ' dar ', ' permitir ', ' dos ', ' una ', ' tres ', ' falta ', ' fatal ', ' tiempo ', ' poder ',
                ' diferentes ', ' diferente ', ' trajo ', ' algo ', ' dar ', ' en ', ' llenar ', ' sobre ', ' buenas ', ' con ',
                ' TUS ', ' para ', ' 𝗤𝗨𝗜𝗘𝗥𝗘𝗦 ', ' 𝗧𝗜𝗘𝗡𝗘𝗦 ', ' hacer ', ' crecer ', ' En ', ' mayor ', ' este ', ' asi ', ' sus ',
                ' lunes ', ' martes ', ' miercoles ', ' jueves ', ' viernes ', ' sabado ', ' domingo ', ' enero ', ' febrero ', ' marzo ', ' abril', ' mayo ',
                ' junio ', ' julio ', ' agosto ', ' septiembre ', ' octubre ', ' noviembre ', ' diciembre ', 'CostaRica', 'Costarricense', 'Columbia'



            );
            $result_replace = str_ireplace($array_replace_words," ", $getWords);
            $string=$string." ".$result_replace;
        }
        //limpieza de la cadena de texto de saltos de linea
        $string = preg_replace("/[\r\n|\n|\r]+/", "", $string);
        $string = preg_replace("/[0-9]/", "", $string);
        // se convierte la cadena de texto a array cuando encuentra un espacio
        $words= explode(" ",$string);
        //limpieza de array elementos vacíos
        $words = array_diff($words, array("",null));

        //limpieza de array elementos vacíos
        while(array_search("",$words)){
            $index=array_search("",$words);
            unset($words[$index]);
        }
        /*cuenta las veces que se repite una palabra y crea un array
         nuevo donde el index es la palabra y el value las repeticiones
         */
        $count_words=array_count_values($words);
        $array_to_sort=[];
        /*transforma el array anterior a un array con dos keys
          word para las palabras y count para la cantidad de repeticiones
          el array nuevo es en formato $array[n]=>{word,key}
        */
        foreach ($count_words as $key=>$item){
            $array_to_sort[]=array('word'=>$key,'count'=>$item);
        }
        // se crea un array con los valores de count
        $keys = array_column($array_to_sort,'count');
        // se ordena el array de forma descendente
        array_multisort($keys, SORT_DESC, $array_to_sort);
        $range=0;
        $similar=[];
        $result=[];
        foreach ($array_to_sort as $word){
            $range=$word['count'];
            /* se filtran las palabras que tengan un count similar entre
             * el count actual x numero arriba y x numero abajo en este caso x=3
             * y que no sea la palabra actual
            */
            $similar=array_filter($array_to_sort, function ($a) use ($range, $word){
                return ($a['count']>=($range-3) && $a['count']<=($range+3))&& ($a['word']!=$word['word']);
            });
            // encuentra todos los posts que tienen la palabra principal abuscar
            $found=$posts->filter(function ($content) use ($word){
                if (isset($content['content']) && isset($content['title'])){
                    return str_contains($content['content'],$word['word']) || str_contains($content['title'],$word['word']);
                }elseif (isset($content['content'])){
                    return str_contains($content['content'],$word['word']);
                }elseif (isset($content['title'])){
                    return str_contains($content['title'],$word['word']);
                }
            });
            /** Con las palabras similares se va a buscar si se encuentran en el array con publicaciones
             * que contienen la palabra principal, si se llegan a encontrar es porque son temas iguales
             * y se deben de manejar como el mismo tema
             */
            $found_first=$found->first();
            $posts_found=array_map(function ($a) use($found_first){
                if (isset($found_first['content']) && isset($found_first['title'])){
                    if(str_contains($found_first['content'],$a['word']) || str_contains($found_first['title'],$a['word'])){
                        return $a;
                    }
                }elseif (isset($found_first['content'])){
                    if(str_contains($found_first['content'],$a['word'])){
                        return $a;
                    }
                }elseif (isset($found_first['title'])){
                    if(str_contains($found_first['title'],$a['word'])){
                        return $a;
                    }
                }

            },$similar);
            if(count($similar)>1){
                $found_last=$found->last();
                $posts_found=array_map(function ($a) use($found_last){
                    if (isset($found_last['content']) && isset($found_last['title'])){
                        if(str_contains($found_last['content'],$a['word']) || str_contains($found_last['title'],$a['word'])){
                            return $a;
                        }
                    }elseif (isset($found_last['content'])){
                        if(str_contains($found_last['content'],$a['word'])){
                            return $a;
                        }
                    }elseif (isset($found_last['title'])){
                        if(str_contains($found_last['title'],$a['word'])){
                            return $a;
                        }
                    }

                },$similar);
            }
            /** En el array $posts_found se encuentran los temas que son similares al tema principal que se esta buscando
             * Se debe de agrupar los temas relacionados y ver como se van a almacenar para mostrar el reporte
             */
            $array_topic_similar=[];
            /* se limpia el array de valores null, el array_topic_similar contiene únicamente los temas
             * que son similares
             */
            foreach ($posts_found as $item){
                if($item){
                    $array_topic_similar[]=$item;
                }
            }
            $end=0;
            $start=9999999;
            if(count($array_topic_similar)>0){
                if (isset($found_first['content'])){
                    $text=$found_first['content'];
                }elseif (isset($found_first['title'])){
                    $text=$found_first['title'];
                }
                $text = preg_replace("/[\r\n|\n|\r]+/", "", $text);
                $diff=[];
                array_push($array_topic_similar,$word);
                // quitar los temas similares de word
                foreach ($array_to_sort as $item){
                    $flag=true;
                    foreach ($array_topic_similar as $similar_item){
                        if(!in_array($item['word'], $similar_item)){
                            // try {
                            //     $pos = strripos($text, $similar_item['word']);

                            // }catch (\ErrorException $e){
                            //     continue;
                            // }
                            // if($pos>$end){
                            //     $end=$pos;
                            //     $end+=strlen($similar_item['word']);
                            // }elseif ($pos<$start){
                            //     $start=$pos;
                            // }
                            continue;
                        }
                        $flag=false;
                    }
                    ($flag)? $diff[] = $item:null;
                }
                // $x=$end-$start;
                $result[]=array($array_topic_similar);
                $array_to_sort=$diff;
            }
        }
        $result= array_slice($result,0,10);
        return  $result;
    }

    public function TopicWord($posts){
        $data=[];
        $string='';
        foreach($posts as $post) {
            if($post->type=="share"){
                $getWords = $post->content." ". $post->title;
            }else{
                $getWords= $post->comment;
            }
            //función que quita tildes
            $getWords=$this->eliminar_acentos($getWords);

            $array_replace_words = array(
                "📱", "🎁", "💪", "🎄",'🌲', "🎁","✅", "🎅","✨", "👍",'👉🏻', '🤣','😄','🙂','👍', ' 𝗠a𝗦 ', "😈", "🔥","⭐","‼‼‼", ';', '¢', '$','.',
                "❤","😍","📲",'🤩','😱',"https://www.","/","amprensa.com","-", "¿","?",'‼', ",",'.','-','"', '“',"”", ":","¡","!", '|', '#',
                "_", "*","(", ")",'«','»', '%','+','"', "Crhoy.com",'Crhoy', ' .com ',' dar ',' al ', ' la ', ' las ', ' los ',' lo ','🔴',
                ' el ', ' le ',' a ', ' ante ', ' bajo ', ' cabe ' , ' con ', ' contra ', ' de ' , ' desde ' , ' en ',' su ', ' http ', ' youtube ',
                ' muy ', ' entre ', ' hasta ', ' hacia ' , ' para ', ' por ', ' según ', ' segun ', ' sin ', ' so ', ' sobre ', ' fue ', ' elpais ',
                ' tras ', ' durante ', ' mediante ', ' por ', ' pro ', ' versus ', ' via ', ' tras ', ' excepto ',' puedes ', ' costa ', ' rica ',
                ' mediante ', ' aunque ', ' y ', ' e ', ' ni ', ' o ', ' u ', ' pero ', ' sino ', ' mas ', ' que ', ' les ', ' escazu ', ' facebook ',
                ' ellos ', ' tu ', ' nosotros ', ' otras ', ' otra ', ' otros ', ' otro ', ' esta ', ' estan '," ahora ", " nos ", 'Repretel',
                ' estas ', 'estos ', ' esto ', ' es ', ' eso ', ' esas ', ' esos ', ' un ', ' una ', ' uno ', ' unos ', ' puede ', 'amprensa', ' nacional ',
                ' son ', ' tambien ', ' tiene ', ' tienen ', ' tampoco ', ' estaba ', ' estaban ', ' sin ', ' sino ', ' ha ', ' diario ', '  extra ',
                ' aun ', ' aunque ', ' menos ', ' mas ', ' alto ', ' hay ', ' ahi ', ' ah ', ' algo ', ' algunas ', ' ese ', 'Sinart ', ' teleticaradio ',
                ' algunos ', ' todo ', 'todos ', ' todas ', ' toda ', ' puedo ', ' puedas ', ' pueda ', ' tenga ', ' te ', ' me ', 'YonecesitoaSitrajud',
                ' se ', ' a ', ' despues ', ' va ', ' donde ', ' como ', ' cuando ', ' porque ', ' por ', ' que ', ' quien ', 'costarricenses',
                ' quienes ', ' habia ', ' hubo ', ' unir ', ' desde ', ' y ', ' si ', ' no ', ' ideal ', ' mi ', ' san ', ' han ', 'Sputnik',
                ' ja ', ' je ', ' de ', ' del ', ' fue ', ' fueron ', ' segun ', ' ya ', ' tarde ', ' dia ',  ' tenes ', ' parte ', 'NoticiasCRC',
                ' dar ', ' da ', ' cualquier ', ' modo ', ' bueno ', ' buenas ', ' facil ', ' pais ', ' despues ', ' desde ',' m ', 'Teletica',
                ' hasta ', ' total ', ' tal ', ' cual ', ' cualquier ', ' menos ', ' mas ', ' poco ', ' mucbo ', ' muchas ',  'ZFMTeMueve', 'Zeta',
                ' vez ', ' veces ', ' sera ', ' decia ', ' unir ', ' cree ', ' creer ', ' ante ', ' antes ', ' tener ', ' ya ', 'NoticiasTelediario',
                ' como ', ' decia ', ' comienza ', ' tenes ', ' son ', ' ultima ', ' ultimo ', ' ultimas ', ' ultimos ', ' pais ', 'AndresTeMueve',
                ' limite ', ' por ', ' x ', " 𝗘𝗦𝗧𝗔 ", ' MAYORES '," ante "," antes ", " 𝗠𝗔𝗦 "," 𝗬 ", ' definir ', ' ahi ', ' mas ', 'ncrnoticias',
                ' algo ', ' tu ', ' en ', ' estamos ', ' suerte ', ' ideal ', ' noticias ', ' ve ', ' va ', ' sabe ', ' saber ',
                ' comienza ', ' lea ', ' necesidad ', ' bueno ', ' buena ', ' ya ', ' sin ', ' solo ', ' sola ', ' ideal ', ' era ',
                ' eran ', ' total ', ' realizar ', ' tiempos ', ' vieron ', ' deben ', ' pasan ', ' pasar ', ' pasa ', ' fatal ',
                ' ocasiones ', ' dar ', ' permitir ', ' dos ', ' una ', ' tres ', ' falta ', ' fatal ', ' tiempo ', ' poder ',
                ' diferentes ', ' diferente ', ' trajo ', ' algo ', ' dar ', ' en ', ' llenar ', ' sobre ', ' buenas ', ' con ',
                ' TUS ', ' para ', ' 𝗤𝗨𝗜𝗘𝗥𝗘𝗦 ', ' 𝗧𝗜𝗘𝗡𝗘𝗦 ', ' hacer ', ' crecer ', ' En ', ' mayor ', ' este ', ' asi ', ' sus ',
                ' lunes ', ' martes ', ' miercoles ', ' jueves ', ' viernes ', ' sabado ', ' domingo ', ' enero ', ' febrero ', ' marzo ', ' abril', ' mayo ',
                ' junio ', ' julio ', ' agosto ', ' septiembre ', ' octubre ', ' noviembre ', ' diciembre ', 'CostaRica', 'Costarricense', 'Columbia'



            );
            $result_replace = str_ireplace($array_replace_words," ", $getWords);
            $string=$string." ".$result_replace;
        }
        //limpieza de la cadena de texto de saltos de linea
        $string = preg_replace("/[\r\n|\n|\r]+/", "", $string);
        $string = preg_replace("/[0-9]/", "", $string);
        // se convierte la cadena de texto a array cuando encuentra un espacio
        $words= explode(" ",$string);
        //limpieza de array elementos vacíos
        $words = array_diff($words, array("",null));

        //limpieza de array elementos vacíos
        while(array_search("",$words)){
            $index=array_search("",$words);
            unset($words[$index]);
        }
        /*cuenta las veces que se repite una palabra y crea un array
         nuevo donde el index es la palabra y el value las repeticiones
         */
        $count_words=array_count_values($words);
        $array_to_sort=[];
        /*transforma el array anterior a un array con dos keys
          word para las palabras y count para la cantidad de repeticiones
          el array nuevo es en formato $array[n]=>{word,key}
        */
        foreach ($count_words as $key=>$item){
            $array_to_sort[]=array('word'=>$key,'count'=>$item);
        }
        // se crea un array con los valores de count
        $keys = array_column($array_to_sort,'count');
        // se ordena el array de forma descendente
        array_multisort($keys, SORT_DESC, $array_to_sort);
        $range=0;
        $similar=[];
        $result=[];
        foreach ($array_to_sort as $word){
            $range=$word['count'];
            /* se filtran las palabras que tengan un count similar entre
             * el count actual x numero arriba y x numero abajo en este caso x=3
             * y que no sea la palabra actual
            */
            $similar=array_filter($array_to_sort, function ($a) use ($range, $word){
                return ($a['count']>=($range-3) && $a['count']<=($range+3))&& ($a['word']!=$word['word']);
            });
            // encuentra todos los posts que tienen la palabra principal abuscar
            $found=$posts->filter(function ($content) use ($word){
                if (isset($content['comment']) && isset($content['title'])){
                    return str_contains($content['content'],$word['word']) || str_contains($content['title'],$word['word']);
                }else
                    if (isset($content['comment'])){
                    return str_contains($content['comment'],$word['word']);
                }elseif (isset($content['title'])){
                    return str_contains($content['title'],$word['word']);
                }
            });
            /** Con las palabras similares se va a buscar si se encuentran en el array con publicaciones
             * que contienen la palabra principal, si se llegan a encontrar es porque son temas iguales
             * y se deben de manejar como el mismo tema
             */
            $found_first=$found->first();
            $posts_found=array_map(function ($a) use($found_first){
                if (isset($found_first['content']) && isset($found_first['title'])){
                    if(str_contains($found_first['content'],$a['word']) || str_contains($found_first['title'],$a['word'])){
                        return $a;
                    }
                }elseif (isset($found_first['comment'])){
                    if(str_contains($found_first['comment'],$a['word'])){
                        return $a;
                    }
                }elseif (isset($found_first['title'])){
                    if(str_contains($found_first['title'],$a['word'])){
                        return $a;
                    }
                }

            },$similar);
            if(count($similar)>1){
                $found_last=$found->last();
                $posts_found=array_map(function ($a) use($found_last){
                    if (isset($found_last['content']) && isset($found_last['title'])){
                        if(str_contains($found_last['content'],$a['word']) || str_contains($found_last['title'],$a['word'])){
                            return $a;
                        }
                    }elseif (isset($found_last['comment'])){
                        if(str_contains($found_last['comment'],$a['word'])){
                            return $a;
                        }
                    }elseif (isset($found_last['title'])){
                        if(str_contains($found_last['title'],$a['word'])){
                            return $a;
                        }
                    }

                },$similar);
            }
            /** En el array $posts_found se encuentran los temas que son similares al tema principal que se esta buscando
             * Se debe de agrupar los temas relacionados y ver como se van a almacenar para mostrar el reporte
             */
            $array_topic_similar=[];
            /* se limpia el array de valores null, el array_topic_similar contiene únicamente los temas
             * que son similares
             */
            //dd($posts_found);
            foreach ($posts_found as $item){
                if($item){
                    $array_topic_similar[]=$item;
                }
            }
            $end=0;
            $start=9999999;
            //dd($array_topic_similar, $found_first);
            if(count($array_topic_similar)>0){
                if (isset($found_first['comment'])){
                    //dd('sdd');
                    $text=$found_first['comment'];
                }elseif (isset($found_first['title'])){
                    //dd('ddd');
                    $text=$found_first['title'];
                }
                //dd('rdrttr');
                //dd($found_first['comment'], $text);
                $text = preg_replace("/[\r\n|\n|\r]+/", "", $text);
                $diff=[];
                array_push($array_topic_similar,$word);
                // quitar los temas similares de word
                foreach ($array_to_sort as $item){
                    $flag=true;
                    foreach ($array_topic_similar as $similar_item){
                        if(!in_array($item['word'], $similar_item)){
                            // try {
                            //     $pos = strripos($text, $similar_item['word']);

                            // }catch (\ErrorException $e){
                            //     continue;
                            // }
                            // if($pos>$end){
                            //     $end=$pos;
                            //     $end+=strlen($similar_item['word']);
                            // }elseif ($pos<$start){
                            //     $start=$pos;
                            // }
                            continue;
                        }
                        $flag=false;
                    }
                    ($flag)? $diff[] = $item:null;
                }
                // $x=$end-$start;
                $result[]=array($array_topic_similar);
                $array_to_sort=$diff;
            }
            //dd('bajo');
        }
        $result= array_slice($result,0,10);
        return  $result;
    }

    public function TopicCount2($posts){
    $data=[];
    $string='';

    foreach($posts as $post) {
        if($post->type=="share"){
            $getWords = $post->content." ". $post->title;
        }else{
            $getWords= $post->content;
        }
        //función que quita tildes
        $getWords=$this->eliminar_acentos($getWords);

        $array_replace_words = array(
            "¡","!", "amprensa.com","-", "¿","?"," ante "," antes ","Crhoy.com",'Crhoy',"-",'"', '“',"https://www.", ":", " ", "(", ")", ",", ".",
            " tienen ", " tienes ", " tiene ", " es ", " en ", " estamos ", " esto ", " esta ", " estas "," este "," se ", " algo ",
            " la ", " lo ", " el ", " los ", " las ", " al " , ' le ', ":", "(", ")", " de ", " con ", " de ", " com ", " sin ", ' no ', ' que ',
            ' sobre ', ' bajo', " del ", " contra ", " vez ", " veces ", " tu ", " donde ", " no ", " si ", " ser ", " bueno ", " malo ",
            ' y ', ' hablo ', ' Todo ', ' dice ',  ' ve ', '|', ' a ', " su ", " por ", " para "," como ", " cómo ", " y ", " un ", " una ", " más ",
            " mas ",  " pero ", " para ", " se ", " en ", " un ", " tú ", " tenés ", " podés ", ' cual ', " cuales ", " uno ", " su ", " nuestra ", " nuestro ",
            " una ", " uno ", " se ", " es ", " no ", " si", " es, ", " está ", " esta ",  " eso ", " esa ", " ser  ", " estar ", " tener ", " esta ", " ahi ", " ahí ",
            " ja", " je", " les "," buen ", " las ", " ser ", " sin ", " ya ", " los ", " son ", " pero ",  " poco ", " hace ", " toda ", " todo ","Esta ",
            " info ", " de ", " alto ",  " tambien ", " mas ", " para ", " del ", " este ", " sobre ", " vez ", " mi ", " podrá ", " quiero ", " quien ", " va ", " al",
            " todos ", " toda ", " todo ", " todas ", " personas ", " persona ", " con ", " gran ", " desde ", " hasta ", " cuando ", ' llego ', ' así ',
            " o ", " soy ", " noticias ", " desde ", ' aun ', ' aunque ', ' porque ', ' por ', ' que ', ' tus ',  " al ", " última ", " bueno ", " tu ", " con ", " o ",
            " se ", " le ", " tienen ", " para ", " buenas ", " tenia ", " atras ", " comenza ", " despues ", " pais ", " facil ", " queres ", " ultima ", " mejor ", " calidad ",
            " cualquier ", " quien ", " necesario ", " necesidades ", " pueda ", " sus ", " Nosotros ", " ideal ", " hacer ", " mejor ", " habia ", " bueno ", " buena ",
            " tenga ", " drama ", " dilema " , " dia ", " otra " ," puede ", " importa " , " importar ", " suerte ", " hacer ", " hacia " , " decia ",
            " tener " ," ademas ", " saber " , " solo ", " venir " , " encontrar ", " ahi " , " sino ", " aunque " ," buscar ", " entonces " , " da ", " pena ",
            " cierto ", " bien " , " vemos ", " pueda " ," podra ", " podria " , " estaba ", " sean ", " serán ", " eran " , " ser ", " segun " ," creer ",
            " fue " , " decir ", " habla ", " unir ", " tema ", " total ", " definir ", " menos ", " claro ", " acuerdo ", " perfecto ", " listo ", " segun ",
            " ya ", " tarde ", " importante ", " completo ", " sobre ", " destaco ", " lea ", ' Asesoria ', ' informacion ', ' comunicate ', " Creacion ",
            " Dias ", " diseno ", " desarrollo ", " campana ", "💪", "🎄", "🎁","✅", "🎅","✨", "👍", ' 𝗠a𝗦 '
        );
        // $result_replace = str_ireplace($array_replace_words," ", $getWords);
        $string=$string." ".$getWords;
    }
    //limpieza de la cadena de texto de saltos de linea
    $string = preg_replace("/[\r\n|\n|\r]+/", "", $string);
    // se convierte la cadena de texto a array cuando encuentra un espacio
    $words= explode(" ",$string);
    //limpieza de array elementos vacíos
    $words = array_diff($words, array("",null));

    //limpieza de array elementos vacíos
    while(array_search("",$words)){
        $index=array_search("",$words);
        unset($words[$index]);
    }
    /*cuenta las veces que se repite una palabra y crea un array
     nuevo donde el index es la palabra y el value las repeticiones
     */
    $count_words=array_count_values($words);
    $array_to_sort=[];
    /*transforma el array anterior a un array con dos keys
      word para las palabras y count para la cantidad de repeticiones
      el array nuevo es en formato $array[n]=>{word,key}
    */
    foreach ($count_words as $key=>$item){
        $array_to_sort[]=array('word'=>$key,'count'=>$item);
    }
    // se crea un array con los valores de count
    $keys = array_column($array_to_sort,'count');
    // se ordena el array de forma descendente
    array_multisort($keys, SORT_DESC, $array_to_sort);
    $range=0;
    $similar=[];
    $result=[];
    foreach ($array_to_sort as $word){
        $range=$word['count'];
        /* se filtran las palabras que tengan un count similar entre
         * el count actual x numero arriba y x numero abajo en este caso x=3
         * y que no sea la palabra actual
        */
        $similar=array_filter($array_to_sort, function ($a) use ($range, $word){
            return ($a['count']>=($range-3) && $a['count']<=($range+3))&& ($a['word']!=$word['word']);
        });
        // encuentra todos los posts que tienen la palabra principal abuscar
        $found=$posts->filter(function ($content) use ($word){
            if (isset($content['content']) && isset($content['title'])){
                return str_contains($content['content'],$word['word']) || str_contains($content['title'],$word['word']);
            }elseif (isset($content['content'])){
                return str_contains($content['content'],$word['word']);
            }elseif (isset($content['title'])){
                return str_contains($content['title'],$word['word']);
            }
        });
        /** Con las palabras similares se va a buscar si se encuentran en el array con publicaciones
         * que contienen la palabra principal, si se llegan a encontrar es porque son temas iguales
         * y se deben de manejar como el mismo tema
         */
        $found_first=$found->first();
        $posts_found=array_map(function ($a) use($found_first){
            if (isset($found_first['content']) && isset($found_first['title'])){
                if(str_contains($found_first['content'],$a['word']) || str_contains($found_first['title'],$a['word'])){
                    return $a;
                }
            }elseif (isset($found_first['content'])){
                if(str_contains($found_first['content'],$a['word'])){
                    return $a;
                }
            }elseif (isset($found_first['title'])){
                if(str_contains($found_first['title'],$a['word'])){
                    return $a;
                }
            }

        },$similar);
        if(count($similar)>1){
            $found_last=$found->last();
            $posts_found=array_map(function ($a) use($found_last){
                if (isset($found_last['content']) && isset($found_last['title'])){
                    if(str_contains($found_last['content'],$a['word']) || str_contains($found_last['title'],$a['word'])){
                        return $a;
                    }
                }elseif (isset($found_last['content'])){
                    if(str_contains($found_last['content'],$a['word'])){
                        return $a;
                    }
                }elseif (isset($found_last['title'])){
                    if(str_contains($found_last['title'],$a['word'])){
                        return $a;
                    }
                }

            },$similar);
        }
        /** En el array $posts_found se encuentran los temas que son similares al tema principal que se esta buscando
         * Se debe de agrupar los temas relacionados y ver como se van a almacenar para mostrar el reporte
         */
        $array_topic_similar=[];
        /* se limpia el array de valores null, el array_topic_similar contiene únicamente los temas
         * que son similares
         */
        foreach ($posts_found as $item){
            if($item){
                $array_topic_similar[]=$item;
            }
        }
        $end=0;
        $start=9999999;
        if(count($array_topic_similar)>0){
            if (isset($found_first['content'])){
                $text=$found_first['content'];
            }elseif (isset($found_first['title'])){
                $text=$found_first['title'];
            }
            $text = preg_replace("/[\r\n|\n|\r]+/", "", $text);

            $diff=[];
            array_push($array_topic_similar,$word);
            // quitar los temas similares de word
            foreach ($array_to_sort as $item){
                $flag=true;
                foreach ($array_topic_similar as $similar_item){
                    if(!in_array($item['word'], $similar_item)){
                        try {
                            $pos = strripos($text, $similar_item['word']);

                        }catch (\ErrorException $e){
                            continue;
                        }
                        if($pos>$end){
                            $end=$pos;
                            $end+=strlen($similar_item['word']);
                        }elseif ($pos<$start){
                            $start=$pos;
                        }
                        continue;
                    }
                    $flag=false;
                }
                ($flag)? $diff[] = $item:null;
            }
            $x=$end-$start;
            $result[]=array('topic'=>substr($text,$start,$x),'count'=>$array_topic_similar);
            $array_to_sort=$diff; // Muestra
        }
    }
    $result= array_slice($result,0,10);
    dd($array_to_sort);
    return  $result;
}

    public function eliminar_acentos($cadena){
        $cadena = str_replace(
            array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
            array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
            $cadena
        );

        //Reemplazamos la E y e
        $cadena = str_replace(
            array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
            array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
            $cadena );

        //Reemplazamos la I y i
        $cadena = str_replace(
            array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
            array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
            $cadena );

        //Reemplazamos la O y o
        $cadena = str_replace(
            array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
            array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
            $cadena );

        //Reemplazamos la U y u
        $cadena = str_replace(
            array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
            array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
            $cadena );

        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
            array('Ñ', 'ñ', 'Ç', 'ç'),
            array('N', 'n', 'C', 'c'),
            $cadena
        );
        return $cadena;
    }
}
