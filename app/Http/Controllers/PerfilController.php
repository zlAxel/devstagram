<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Models\User;

class PerfilController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('perfil.index');
    }

    public function store(Request $request){
        $request->request->add([
            'username' => Str::slug($request->username),
        ]);

        $this->validate($request, [
            'username' => ['required', 'unique:users,username,'.auth()->user()->id, 'min:3', 'max:20', 'not_in:editar-perfil,imagen,register,login,logout,posts'],
        ]);

        if($request->imagen){
            $imagen = $request->file('imagen');

            $nombreImg = Str::uuid().".".$imagen->extension();
    
            $imgServer = Image::make($imagen);
            $imgServer->fit(1000, 1000);
    
            $imgPath = public_path('perfiles').'/'.$nombreImg;
    
            $imgServer->save($imgPath);
        }

        // Guardar cambios

        $usuario = User::find(auth()->user()->id);

        $usuario->username = $request->username;
        $usuario->imagen = $nombreImg ?? auth()->user()->imagen ?? NULL;

        $usuario->save();

        // Redireccionar

        return redirect()->route('posts.index', $usuario->username);
    }
}
