<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ImagenController extends Controller{
    public function store(Request $request){
        
        $imagen = $request->file('file');

        $nombreImg = Str::uuid().".".$imagen->extension();

        $imgServer = Image::make($imagen);
        $imgServer->fit(1000, 1000);

        $imgPath = public_path('uploads').'/'.$nombreImg;

        $imgServer->save($imgPath);

        return response()->json(['imagen' => $nombreImg]);
    }
}
