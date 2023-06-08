<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use App\Models\Utility;
use Auth;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function changeLanquage($lang)
    {
        $user       = Auth::user();
        $user->lang = $lang;
        $user->save();

        return redirect()->back()->with('success', __('Language change successfully.'));
    }

    public function manageLanguage($currantLang)
    {
        if(\Auth::user()->type == 'super admin')
        {
            $languages = Utility::languages();

            $dir = base_path() . '/resources/lang/' . $currantLang;
            if(!is_dir($dir))
            {
                $dir = base_path() . '/resources/lang/en';
            }
            $arrLabel   = json_decode(file_get_contents($dir . '.json'));
            $arrFiles   = array_diff(
                scandir($dir), array(
                                 '..',
                                 '.',
                             )
            );
            $arrMessage = [];

            foreach($arrFiles as $file)
            {
                $fileName = basename($file, ".php");
                $fileData = $myArray = include $dir . "/" . $file;
                if(is_array($fileData))
                {
                    $arrMessage[$fileName] = $fileData;
                }
            }



            return view('lang.index', compact('languages', 'currantLang', 'arrLabel', 'arrMessage'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function storeLanguageData(Request $request, $currantLang)
    {
        
        if(\Auth::user()->type == 'super admin')
        {
            $Filesystem = new Filesystem();
            $dir        = base_path() . '/resources/lang/';
            if(!is_dir($dir))
            {
                mkdir($dir);
                chmod($dir, 0777);
            }
            $jsonFile = $dir . "/" . $currantLang . ".json";

            if(isset($request->label) && !empty($request->label))
            {
                file_put_contents($jsonFile, json_encode($request->label));
            }

            $langFolder = $dir . "/" . $currantLang;

            if(!is_dir($langFolder))
            {
                mkdir($langFolder);
                chmod($langFolder, 0777);
            }
            if(isset($request->message) && !empty($request->message))
            {
                foreach($request->message as $fileName => $fileData)
                {
                    $content = "<?php return [";
                    $content .= $this->buildArray($fileData);
                    $content .= "];";
                    file_put_contents($langFolder . "/" . $fileName . '.php', $content);
                }
            }

            return redirect()->route('manage.language', [$currantLang])->with('success', __('Language save successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function buildArray($fileData)
    {
        $content = "";
        foreach($fileData as $lable => $data)
        {
            if(is_array($data))
            {
                $content .= "'$lable'=>[" . $this->buildArray($data) . "],";
            }
            else
            {
                $content .= "'$lable'=>'" . addslashes($data) . "',";
            }
        }

        return $content;
    }

    public function createLanguage()
    {
        return view('lang.create');
    }

    public function storeLanguage(Request $request)
    {
        if(\Auth::user()->type == 'super admin')
        {
            $Filesystem = new Filesystem();
            $langCode   = strtolower($request->code);
            $langDir    = base_path() . '/resources/lang/';
            $dir        = $langDir;
            if(!is_dir($dir))
            {
                mkdir($dir);
                chmod($dir, 0777);
            }
            $dir      = $dir . '/' . $langCode;
            $jsonFile = $dir . ".json";
            \File::copy($langDir . 'en.json', $jsonFile);

            if(!is_dir($dir))
            {
                mkdir($dir);
                chmod($dir, 0777);
            }
            $Filesystem->copyDirectory($langDir . "en", $dir . "/");

            return redirect()->route('manage.language', [$langCode])->with('success', __('Language successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function destroyLang($lang)
    {

        if(\Auth::user()->type == 'super admin')
        {
            $default_lang = env('default_language') ?? 'en';
            $langDir      = base_path() . '/resources/lang/';
            if(is_dir($langDir))
            {
                // remove directory and file
                Utility::delete_directory($langDir . $lang);
                unlink($langDir . $lang . '.json');
                // update user that has assign deleted language.
                User::where('lang', 'LIKE', $lang)->update(['lang' => $default_lang]);
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        return redirect()->route('manage.language', $default_lang)->with('success', __('Language Deleted Successfully.'));
    }

    public function changeLanquageStore($slug,$lang)
    {
        
        session(['lang' => $lang]);

       
        return redirect()->back()->with('success', __('Language change successfully.'));
    }
}
