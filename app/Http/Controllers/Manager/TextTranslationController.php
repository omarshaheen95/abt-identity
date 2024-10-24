<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

class TextTranslationController extends Controller
{
    private $file;
    private $path;
    private $arrayLang = array();

    public function __construct()
    {
        $this->middleware('permission:show translation')->only('index');
        $this->middleware('permission:edit translation')->only('updateTranslations');
    }

    public function index()
    {
        $folders = [];
        foreach (config('app.languages') as $local)
        {
            $folders[$local]['files'] = File::allFiles(base_path().'/resources/lang/'.$local);
        }

        return view('manager.translation_text.index', compact('folders'));
    }

    public function updateTranslations(Request $request, $lang, $file)
    {
        $this->arrayLang = $request->except(['_token']);
        $this->path = base_path("/resources/lang/$lang/$file");
        $this->save();
        return redirect()->back()->with('message', t('Successfully Updated'))->with('m-class', 'success');
    }

    //------------------------------------------------------------------------------
    // Delete from lang files
    //------------------------------------------------------------------------------

    private function deleteLangFileContent()
    {
        $this->read();
        unset($this->arrayLang[$this->key]);
        $this->save();
    }

    //------------------------------------------------------------------------------
    // Save lang file content
    //------------------------------------------------------------------------------

    private function save()
    {
        $content = "<?php\n\nreturn\n[\n";

        foreach ($this->arrayLang as $this->key => $this->value)
        {
            $content .= "\t'".str_replace('_', ' ', str_replace("'", "\'", $this->key))."' => '".str_replace("'", "\'", $this->value)."',\n";
        }

        $content .= "\n];";

        file_put_contents($this->path, $content);

    }
}
