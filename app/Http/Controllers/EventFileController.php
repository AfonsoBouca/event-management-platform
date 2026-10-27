<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class EventFileController extends Controller
{
    // Disk name from your tutorial
    private static $diskName = 'Tutorial02';

    /**
     * LISTAR fotos do evento (usado pelo AJAX para carregar a sidebar)
     */
    public function list($eventId)
    {
        $photos = DB::table('event_platform.eventphoto AS ep')
            ->join('event_platform.user AS u', 'ep.userid', '=', 'u.userid')
            ->select('ep.*', 'u.username')
            ->where('ep.eventid', $eventId)
            ->orderBy('ep.createdat', 'desc')
            ->get();

        return view('events.partials.photos', [
            'eventId' => $eventId,
            'photos'  => $photos
        ]);
    }

    /**
     * UPLOAD de uma foto
     */
    public function upload(Request $request, $eventId)
    {
        // Apenas users autenticados podem enviar
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,gif|max:10240'
        ]);

        $file = $request->file('photo');
        $userId = Auth::id();

        // Gerar nome único
        $fileName = $file->hashName();

        // Guardar dentro de "event/"
        Storage::disk(self::$diskName)->putFileAs('event', $file, $fileName);

        // Inserir na tabela
        DB::table('eventphoto')->from('event_platform.eventphoto')->insert([
            'eventid'  => $eventId,
            'userid'   => $userId,
            'filepath' => $fileName,
            'createdat'=> now()
        ]);

        return response()->json(['success' => true]);
    }


    
}



