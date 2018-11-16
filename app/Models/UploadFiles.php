<?php

namespace App\Models;

use App\Models\Event\Event;
use App\User;
use Carbon\Carbon;
use Config;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadFiles extends Model
{

    public function procesUploadFiles($files, $module, $id=0, $name=""){

        $module = str_slug(trim($module));

        $res =  null;

        // nastavenia rozlisenia pre obrazky
        $image_types = Config::get('image_type.'. $module);

        // if not defined resolution take default
        if(!$image_types){
            $image_types = Config::get('image_type.default');
        }


        //zistime ci existuje adresar ak nie vytvorime
        $file_path = public_path('/files/'.$module.'/'.$id);
        //$file_path = storage_path('app/files/'.$module.'/'.$id);

        if(File::isDirectory($file_path) or File::makeDirectory($file_path, 0777, true, true));

        if($files->all()['files']){
            foreach ($files->all()['files'] as $file) {

                if (!$file || !$file->isValid()) continue;

                $file_extension = strtolower($file->getClientOriginalExtension());
                $file_original_name = strtolower($file->getClientOriginalName());
                $file_mime_type = strtolower($file->getClientMimeType());

                $file_name = "";
                if (strlen($name) > 3){

                    $file_name = str_slug($name . "-" . str_random(6))  . '.' . $file_extension;

                }else{

                    $file_name = str_slug(str_replace('.' . $file_extension, "", $file_original_name));
                    $file_name = $file_name ."-" . str_random(6) . '.' . $file_extension;

                }


                // zsitme ci su subory obrazky ak ano urobime aj kopie obrazkov pre nahlady
                if (in_array($file_extension,['png', 'jpg', 'jpeg'])){

                    // vyroime thumbnail a ulozime files/images
                    $img = Image::make($file);


                    // add callback functionality to retain maximal original image size
                    $img->fit(500, 333, function ($constraint) {
                        $constraint->upsize();
                    });

                    //ak neexistuju directory pre obrazok  vytvorime
                    if(File::isDirectory($file_path . '/image/') or File::makeDirectory($file_path .'/image/', 0777, true, true));

                    // save image
                    $img->save($file_path. '/image/'. $file_name );

                    // ulozime original suboru
                    if (!$file->move($file_path, $file_name)) {
                        return 'Error saving the image file.';
                    }

                } else {

                    if (!$file->move($file_path, $file_name)) {
                        return 'Error saving the file.';
                    }

                }

                $file_data = null;
                $file_data['name'] = $file_name;
                $file_data['mime'] = $file_mime_type;
                $file_data['path'] = $file_path;
                $file_data['ext'] = $file_extension;

                $id_file = $this->saveImageToDb($file_data, $module, $id);

                if ($id_file){

                    $res[] = $id_file;

                }

            }
        }

        return $res;

    }



    public function saveImageToDb($file, $module, $id)
    {

        $data['module_id'] = $id;
        $data['module'] = $module;
        $data['path'] = 'files/' . $module . '/' . $id . '/';
        $data['file'] = $file['name'];
        $data['mime'] = $file['mime'];
        $data['ext'] = $file['ext'];
        $data['created_at'] = Carbon::now();

        $id_new = DB::table('files')->insertGetId($data);

        if ($id_new > 0){
            return $id_new;
        } else {
            return false;
        }

    }




    public function deleteFile($id)
    {

        $res = null;

        $file = DB::table('files')->find($id);

        if ($file){

            $path_file = public_path($file->path . $file->file);
            //$path_file = storage_path($file->path . $file->file);

            if(File::isFile($path_file)){
                //zmazeme subor
                File::delete($path_file);

                if (in_array($file->ext,['png', 'jpg', 'jpeg'])){

                    $path_image = public_path($file->path . 'image/' . $file->file);
                    //$path_image = storagge_path($file->path . 'image/' . $file->file);

                    if(File::isFile($path_image)) {
                        //zmazeme subor
                        File::delete($path_image);
                    }
                }
            }

            $res = DB::table('files')->delete($id);

        }

        return $res;

    }




    public function getIconMimeText($ext){

        $mimet = array(
            'txt' => 'fa-file',
            'htm' => 'fa-file',
            'html' => 'fa-file',
            'php' => 'fa-file',
            'css' => 'fa-file',
            'js' => 'fa-file',
            'json' => 'fa-file',
            'xml' => 'fa-file',
            'swf' => 'fa-file',
            'flv' => 'fa-file',

            // images
            'png' => 'fa-image',
            'jpe' => 'fa-image',
            'jpeg' => 'fa-image',
            'jpg' => 'fa-image',
            'gif' => 'fa-file',
            'bmp' => 'fa-file',
            'ico' => 'fa-file',
            'tiff' => 'fa-file',
            'tif' => 'fa-file',
            'svg' => 'fa-file',
            'svgz' => 'fa-file',

            // archives
            'zip' => 'fa-archive',
            'rar' => 'fa-archive',
            'exe' => 'fa-archive',
            'msi' => 'fa-archive',
            'cab' => 'fa-archive',

            // audio/video
            'mp3' => 'fa-music',
            'qt' => 'fa-film',
            'mov' => 'fa-film',

            // adobe
            'pdf' => 'fa-file',
            'psd' => 'fa-file',
            'ai' => 'fa-filet',
            'eps' => 'fa-file',
            'ps' => 'fa-file',

            // ms office
            'doc' => 'fa-file',
            'rtf' => 'fa-file',
            'xls' => 'fa-bar-chart-o',
            'ppt' => 'fa-bar-chart-o',
            'docx' => 'fa-file',
            'xlsx' => 'fa-bar-chart-o',
            'pptx' => 'fa-bar-chart-o',


            // open office
            'odt' => 'afa-file',
            'ods' => 'fa-bar-chart-o',
        );


        if(in_array($ext, $mimet)){
            return $mimet[$ext];
        } else {
            return 'fa-file';
        }


    }





}
