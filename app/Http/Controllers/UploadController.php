<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller {
    public function postImage(Request $request) {
        $request->validate([
            'file' => 'required|image|max:2048',
        ]);


        $path = $request->file('file')->store('images');

        return ['url' => $path];
    }

    public function postVideo(Request $request) {
        $request->validate([
            'file' => 'required|mimetypes:video/webm,video/mp4,video/ogg|max:2048',
        ]);


        $path = $request->file('file')->store('video');

        return ['url' => $path];
    }

    public function postFile(Request $request) {
        $request->validate([
            'file' => 'required|file|max:2048',
        ]);


        $path = $request->file('file')->store('files');

        return  ['url' => $path];
    }
}
