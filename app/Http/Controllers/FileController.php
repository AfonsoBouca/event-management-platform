<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;   
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class FileController extends Controller
{   
    static $default = 'default.png';
    static $diskName = 'Tutorial02';

    static $systemTypes = [
        'profile' => ['png', 'jpg', 'jpeg', 'gif'],
        'post' => ['mp3', 'mp4', 'gif', 'png', 'jpg', 'jpeg'],
        'event' => ['png', 'jpg', 'jpeg']
    ];

    private static function isValidType(String $type){
        return array_key_exists($type, self::$systemTypes);
    }

    private static function defaultAsset(String $type){
        return asset($type . '/' . self::$default);
    }

    private static function getFileName (String $type, int $id){
        
        $fileName = null;
        switch($type) {
            case 'profile' :
                $fileName = User::find($id) ->profileimage;
                break;
        }
        return $fileName;
    }

    static function get(String $type, int $userId) {

        // Validation: upload type
        if (!self::isValidType($type)) {
            return self::defaultAsset($type);
        }
    
        // Validation: file exists
        $fileName = self::getFileName($type, $userId);
        if ($fileName) {
            return asset($type . '/' . $fileName);
        }
    
        // Not found: returns default asset
        return self::defaultAsset($type);
    }
 
    public function upload(Request $request)
    {
        // 1. Validar
        $validated = $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,jpg,png,gif', 'max:10240'],  // Aumentei o limite
            'id'   => ['required', 'integer', 'exists:user,userid'],
            'type' => ['required', 'in:profile'],
        ]);

        // 2. Obter dados
        $file   = $request->file('file');
        $type   = $validated['type'];     // "profile"
        $userId = (int) $validated['id'];

        // 3. Gerar nome único
        $fileName = $file->hashName();

        // 4. Gravar ficheiro em public/profile
        Storage::disk('Tutorial02')->putFileAs($type, $file, $fileName);

        // 5. Atualizar BD
        $user = User::findOrFail($userId);
        $user->profileimage = $fileName;
        $user->save();

        // 6. Voltar atrás com sucesso
        return redirect()
            ->route('user.profile', $user->username)
            ->with('success', 'Profile image updated successfully.');
    }


    }
    

