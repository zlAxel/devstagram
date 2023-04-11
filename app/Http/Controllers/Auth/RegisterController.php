<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller{

    public function index(){ 
        return view('auth.register');
    }

    public function store(Request $request){ 
        // dd($request->get('email'));

        // Modificar request

        $request->request->add([
            'username' => Str::slug($request->username),
        ]);

        // =======================================================
        // Validaciones

        $this->validate($request, [
            'name' => ['required', 'max:30'],
            'username' => ['required', 'unique:users', 'min:3', 'max:20'],
            'email' => ['required', 'unique:users', 'email', 'max:60'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        // =======================================================
        // Guardar

        User::create([
            'name' => $request->name,
            'username' => Str::slug($request->username),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // =======================================================
        // Autenticar usuario

        // auth()->attempt([
        //     'email' => $request->email,
        //     'password' => $request->password,
        // ]);
        auth()->attempt($request->only('email', 'password'));

        // =======================================================
        // Redireccionar

        return redirect()->route('posts.index', ['user' => Str::slug($request->username)]);
        // $url = route('posts.index', ['user' => Str::slug($request->username)]);
        // return redirect($url);

    }
    
}
