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

class UploadImages extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'path', 'ext','module'
    ];


    public function procesImage($files, $module, $id, $name)
    {

        $image_types = Config::get('image_type.'. $module);
        // if not defined resolution take default
        if(!$image_types){
            $image_types = Config::get('image_type.default');
        }

        $module = str_slug(trim($module));
        $name = str_replace('_', '-', $name);
        $name = str_slug(str_limit($name,30,''));


        $filePath = public_path('/images/'.$module.'/'.$id);
        if(File::isDirectory($filePath) or File::makeDirectory($filePath, 0777, true, true));

        foreach ($files as $file) {

            if (!$file || !$file->isValid()) continue;

            $fileName =  $name . '-' . mt_rand();
            $fileName = strtolower($fileName);
            $fileExtension = '.' . strtolower($file->getClientOriginalExtension());

            $fileToDb = $fileName.$fileExtension;

            if($image_types){
                foreach ($image_types as $key => $image_type){

                    //creat file system for images
                    if(strlen($key) > 0){
                        if(File::isDirectory($filePath.'/'.$key) or File::makeDirectory($filePath.'/'.$key, 0777, true, true));
                    }

                    //na pracu s obrazkom
                    if (strcmp($image_type['greyscale'], 'yes')){
                        $img = Image::make($file);
                    }else{
                        $img = Image::make($file)->greyscale()->brightness($image_type['brightness']);
                    }

                    //ak je povoleny crop
                    if(strcmp($image_type['crop'], 'no') == 0){
                        // prevent possible upsizing
                        $img->resize($image_type['width'], $image_type['height'], function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }else{
                        // add callback functionality to retain maximal original image size
                        $img->fit($image_type['width'], $image_type['height'], function ($constraint) {
                            $constraint->upsize();
                        });
                    }

                    // save
                    $img->save($filePath. '/'. $key .'/'. $fileName  . $fileExtension);

                }
            }

        }

        $this->saveImageToDb($fileToDb, $module, $id);

        return true;

    }




    public function procesImageBlueImp($files, $module, $id, $name, $data)
    {

        $image_types = Config::get('image_type.'. $module);
        // if not defined resolution take default
        if(!$image_types){
            $image_types = Config::get('image_type.default');
        }

        $module = str_slug(trim($module));
        $name = str_replace('_', '-', $name);
        $name = str_slug(str_limit($name,60,''));


        $filePath = public_path('/images/'.$module.'/'.$id);
        if(File::isDirectory($filePath) or File::makeDirectory($filePath, 0777, true, true));

        foreach ($files as $File_key => $file) {

            if (!$file || !$file->isValid()) continue;

            $fileName =  $name . '-' . mt_rand();
            $fileName = strtolower($fileName);
            $fileExtension = '.' . strtolower($file->getClientOriginalExtension());

            $fileToDb = $fileName.$fileExtension;

            if($image_types){
                foreach ($image_types as $key => $image_type){

                    //creat file system for images
                    if(strlen($key) > 0){
                        if(File::isDirectory($filePath.'/'.$key) or File::makeDirectory($filePath.'/'.$key, 0777, true, true));
                    }

                    //na pracu s obrazkom
                    if (strcmp($image_type['greyscale'], 'yes')){
                        $img = Image::make($file);
                    }else{
                        $img = Image::make($file)->greyscale()->brightness($image_type['brightness']);
                    }

                    //ak je povoleny crop
                    if(strcmp($image_type['crop'], 'no') == 0){
                        // prevent possible upsizing
                        $img->resize($image_type['width'], $image_type['height'], function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }else{
                        // add callback functionality to retain maximal original image size
                        $img->fit($image_type['width'], $image_type['height'], function ($constraint) {
                            $constraint->upsize();
                        });
                    }

                    // save
                    $img->save($filePath. '/'. $key .'/'. $fileName  . $fileExtension);

                }


            }

            //id	event_id	image	ext	created_at	alt_name


            $data_db['image'] = $fileName  . $fileExtension;
            $data_db['event_id'] = $id;
            $data_db['mime'] = $file->getMimeType();
            $data_db['ext'] = $fileExtension;
            $data_db['alt_name'] = "";
            $data_db['created_at'] = Carbon::now();

            DB::table('events_images')->insert($data_db);

            $json[] = array(
                'name' => $fileName  . $fileExtension,
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
                'url' =>  asset('/images') .'/' . $module.'/'.$id .'/'. $fileToDb,
                'thumbnailUrl' => asset('/images') .'/' . $module.'/'.$id .'/sq/'. $fileName  . $fileExtension,
                //'deleteType' => 'DELETE',
                //'deleteUrl' => self::$route.'/deleteFile/'.$filename,
            );

        }

        //$this->saveImageToDb($fileToDb, $module, $id);

        return $json;

    }



    public function saveImageToDb($file, $module, $id)
    {

        if(strcmp($module,'user')==0){

           $user =  User::findOrFail($id);
           $user->image = $file;
           $user->save();

        }elseif(strcmp($module,'company')==0){

            $company =  Company::findOrFail($id);
            $company->image = $file;
            $company->save();

        }elseif(strcmp($module,'club')==0){

            $club =  Club::findOrFail($id);
            $club->image = $file;
            $club->save();

        }elseif(strcmp($module,'event')==0){

            $event =  Event::findOrFail($id);
            $event->image = $file;
            $event->save();

        }elseif(strcmp($module,'event-type')==0){

            DB::table('event_types')->where('id', $id)->update(['image' => $file]);

        }elseif(strcmp($module,'event-type-text')==0){

            DB::table('event_type_text')->where('id', $id)->update(['image' => $file]);

        }elseif(strcmp($module,'bug-report')==0){

            DB::table('bug_reports')->where('id', $id)->update(['image' => $file]);
        }

        return true;

    }

    public function deleteImage($module, $id, $id_module=null)
    {

        if(strcmp($module,'user')==0){

            $user =  User::findOrFail($id);
            $file = $user->image;


        }elseif(strcmp($module,'company')==0){

            $company =  Company::findOrFail($id);
            $file = $company->image;

        }elseif(strcmp($module,'club')==0){

            $club =  Club::findOrFail($id);
            $file = $club->image;

        }elseif(strcmp($module,'event')==0){

            $event =  Event::findOrFail($id);
            $file = $event->image;

        }elseif(strcmp($module,'event-type-text')==0){

            $file = DB::table('event_type_text')->where('id', $id)->value('image');

        }elseif(strcmp($module,'event-images')==0){

            $res = DB::table('events_images')->where('id', $id)->first();
            $file = $res->image;
            $id = $res->event_id;

        }elseif(strcmp($module,'event-type')==0){

            $file = DB::table('event_types')->where('id', $id)->value('image');

        }

        if ($file){

            $path_module = public_path('/images/'. $module .'/'. $id );

            File::delete($path_module . '/' . $file);

            if(File::isDirectory($path_module)){
                //ak existuju podadresare
                foreach (File::directories($path_module) as $dir){
                    File::delete($dir . '/' .$file);
                }
            }

            return $path_module . $file;

        }



    }


}
