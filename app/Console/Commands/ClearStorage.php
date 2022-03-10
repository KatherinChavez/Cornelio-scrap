<?php

namespace App\Console\Commands;

use App\Models\BitacoraPDF;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ClearStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cornelio:ClearStorage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina los pdf ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $extension = '';
        $take=0;

        $start = Carbon::now()->subMonth(1);
        $end = Carbon::now();
        $all_api = BitacoraPDF::whereBetween('created_at', [$start, $end])->get();

        foreach($all_api as $api){
            $file_db = $api->file;

            //BUSCA LOS PDF QUE SE HAN GENERADO DEL TOP 10 REACCIONES
            $filesTop = glob("../public/whatsapp_files/*");
            foreach ($filesTop as $element) {
                $find = stripos($element, $file_db);
                if($find == True){
                    $path = pathinfo(public_path($element));

                    try {
                        //Se toma la extension desde la ubicacion y nombre del archivo.
                        $extension = $path['extension'];
                    } catch (\Exception $e) {/** No hacer nada y continuar con el proceso. **/}

                    if ($extension == ('pdf')) {
                        //Se elimina el archivo.
                        File::delete($element);
                        BitacoraPDF::where('id', $api->id)->delete();
                    }
                    else{
                        if($take>=10000){
                            break;
                        }
                        $take++;
                        //Se convoca el path en el que se encuetra el archivo.
                        $path = pathinfo(public_path($element));
                        //Esta excepcion se utiliza para que cuando un archivo no tiene extension, el comando no caiga.
                        try {
                            //Se toma la extension desde la ubicacion y nombre del archivo.
                            $extension = $path['extension'];
                        } catch (\Exception $e) {/** No hacer nada y continuar con el proceso. **/}

                        // Se valida el archivo: escalabe para varias extensiones.
                        if ($extension == ('pdf')) {
                            //Se elimina el archivo.
                            File::delete($element);
                        }
                    }
                }
            }

            //BUSCA POR LOS PDF CON LAS PUBLICACIONES CON MAS INTERACCIONES
            $fileInteraction = glob("../public/interaction_files/*");
            foreach ($fileInteraction as $element) {
                $find = stripos($element, $file_db);
                if($find == True){
                    $path = pathinfo(public_path($element));
                    try {
                        //Se toma la extension desde la ubicacion y nombre del archivo.
                        $extension = $path['extension'];
                    } catch (\Exception $e) {/** No hacer nada y continuar con el proceso. **/}

                    if ($extension == ('pdf')) {
                        //Se elimina el archivo.
                        File::delete($element);
                        BitacoraPDF::where('id', $api->id)->delete();
                    }
                    else{
                        if($take>=10000){
                            break;
                        }
                        $take++;
                        //Se convoca el path en el que se encuetra el archivo.
                        $path = pathinfo(public_path($element));
                        //Esta excepcion se utiliza para que cuando un archivo no tiene extension, el comando no caiga.
                        try {
                            //Se toma la extension desde la ubicacion y nombre del archivo.
                            $extension = $path['extension'];
                        } catch (\Exception $e) {/** No hacer nada y continuar con el proceso. **/}

                        // Se valida el archivo: escalabe para varias extensiones.
                        if ($extension == ('pdf')) {
                            //Se elimina el archivo.
                            File::delete($element);
                        }
                    }
                }
            }
        }
    }

    public function deleteFile()
    {
        /** Obtenemos todos los nombres de los archivos en x folder **/
        //dd(Storage::deleteDirectory('whatsapp_files'));

        $extension = '';
        $take=0;
        //Se debe definir manualmente la ruta del directorio que se quiere limpiar
        //$files = glob("../public_html/whatsapp_files/*");
        $files = glob("../public/whatsapp_files/*");
        foreach ($files as $element) {
            if($take>=1){
                break;
            }
            $take++;
            //Se convoca el path en el que se encuetra el archivo.

            $path = pathinfo(public_path($element));

            //Esta excepcion se utiliza para que cuando un archivo no tiene extension, el comando no caiga.

            try {

                //Se toma la extension desde la ubicacion y nombre del archivo.

                $extension = $path['extension'];

            } catch (\Exception $e) {

                /** No hacer nada y continuar con el proceso. **/

            }

            // Se valida el archivo: escalabe para varias extensiones.

            if ($extension == ('pdf')) {

                //Se elimina el archivo.

                File::delete($element);
            }
        }

        /******************** Metodo para limpiar el directorio sin filtro *****************************/

        /** Se instancia FileSystem para poder acceder al directorio. **/

        //$file = new Filesystem;

        /** Se limpia el directorio asignado. **/

        //$file->cleanDirectory('storage/app/files');
    }
}
