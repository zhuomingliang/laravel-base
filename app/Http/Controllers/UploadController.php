<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller {
    public function postImage(Request $request) {
        set_time_limit(0);
        ini_set("memory_limit", "400M");
        $request->validate([
            'file' => 'required|image|max:2048',
        ]);


        $path = $request->file('file')->store('images');

        return ['url' => $path];
    }

    public function postVideo(Request $request) {
        set_time_limit(0);
        ini_set("memory_limit", "1024M");
        $request->validate([
            'file' => 'required|mimetypes:video/webm,video/mp4,video/ogg|max:208400',
        ]);


        $path = $request->file('file')->store('video');

        return ['url' => $path];
    }

    public function postFile(Request $request) {
        set_time_limit(0);
        ini_set("memory_limit", "400M");
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);


        $path = $request->file('file')->store('files');

        return  ['url' => $path];
    }
}
