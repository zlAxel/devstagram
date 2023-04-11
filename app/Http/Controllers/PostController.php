<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;

class PostController extends Controller{

    // 
    public function __construct(){
        $this->middleware('auth')->except(['show', 'index']);
    }

    // 
    public function index(User $user){
        // dd($user->id);
        // dd(auth()->user()); 

        $posts = Post::where('user_id', $user->id)->latest()->paginate(8);

        $context = [
            'user' => $user,
            'posts' => $posts,
        ];

        return view('dashboard', $context);
    }

    // 
    public function create(){        
        return view('posts.create');
    }

    // 
    public function store(Request $request){
        $this->validate($request, [
            'titulo' => 'required|max:255',
            'descripcion' => 'required',
            'imagen' => 'required',
        ]);

        // Post::create([
        //     'titulo' => $request->titulo,
        //     'descripcion' => $request->descripcion,
        //     'imagen' => $request->imagen,
        //     'user_id' => auth()->user()->id,
        // ]);

        // Segundo metodo para guardar datos

        // $post = new Post;
        // $post->titulo = $request->titulo;
        // $post->descripcion = $request->descripcion;
        // $post->imagen = $request->imagen;
        // $post->user_id = auth()->user()->id;

        // $post->save();

        // Tercer método para guardar registros

        $request->user()->posts()->create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'imagen' => $request->imagen,
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->route('posts.index', auth()->user()->username);
    }

    // 
    public function show(User $user, Post $post){
        $context = [
            'post' => $post,
            'user' => $user,
        ];

        return view('posts.show', $context);
    }

    // 
    public function destroy(Post $post){
        $this->authorize('delete', $post);

        $post->delete();

        // Borrar imagen del servidor
        $imgPath = public_path('uploads/'.$post->imagen);

        if(File::exists($imgPath)){
            unlink($imgPath);
        }

        return redirect()->route('posts.index', auth()->user()->username);
    }
}
