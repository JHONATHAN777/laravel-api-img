<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;



class AuthController extends Controller
{

    public function register_user(Request $request){
        $request->validate([
            'name'=> 'required',
            'last_name' => 'required',
            'phone_number'  => 'required',
            'email' => 'required|unique:users',
            'profile_picture' => 'required|image|max:1024', 
            'password' => 'required|confirmed',
        ]);
    

        
        $user = new User();
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->phone_number = $request->phone_number;
        $user->email = $request->email;
        
        // Manejo de la imagen
        if ($request->hasFile('profile_picture')) {
            // Eliminar imagen de perfil existente si existe
            if(File::exists(public_path($user->profile_picture))) { 
                File::delete(public_path($user->profile_picture));
            }
            $image = $request->file('profile_picture');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/img', $imageName);
            $user->profile_picture = 'http://localhost:8000/storage/img/' . $imageName; 
        }
    
        $user->password = bcrypt($request->password); // Hashear la contraseña antes de guardarla
        $user->save();
    
        return response($user, Response::HTTP_CREATED);
    }
    
    
    public function edit($id)
    {
        // Buscar el usuario por su ID
        $user = User::find($id);
    
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
    
        // Retornar los datos del usuario para ser editados
        return response()->json(['user' => $user]);
    }
    

    public function login(Request $request){

        $credentials = $request->validate([
            'email'=> ['required','email'],
            'password'=> 'required',

        ]);

        
        if (Auth::attempt($credentials)){
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('cookie_token', $token, 60 * 24);
            return response(["token"=>$token], Response::HTTP_OK)->withoutCookie($cookie);

        }else{
            return response(["message" => "Estimado usuario las credenciales no son validas"],Response::HTTP_UNAUTHORIZED);
        }
        return response()->json([
            "message" => "Todo OK con el login",
        ]);
    }
    public function logout(Request $request){

        return response()->json([
            "message" => "Ya estas verificado :) ",
        ]);

    }


    

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            


            
            if ($request->hasFile('profile_picture')) {
                
                if(File::exists(public_path($user->profile_picture))) { 
                    File::delete(public_path($user->profile_picture));
                }
                $image = $request->file('profile_picture');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/img', $imageName);
                $user->profile_picture = 'http://localhost:8000/storage/img/' . $imageName; 
            }
    

            $user->name = $request->input('name', $user->name);
            $user->last_name = $request->input('last_name', $user->last_name);
            $user->phone_number = $request->input('phone_number', $user->phone_number);
            $user->email = $request->input('email', $user->email);
            

            $user->save();
    
            return response()->json(['success' => true, $user]);
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    

    

    
    
    
    

    
    




    public function user_profile(Request $request){

        return response()->json([
            "message" => "Usuario verificado OK",
            "userData" => auth()->user()
        ], Response::HTTP_OK);

    }

    public function all_users(Request $request){

    }

        public function delete(Request $request, $id){
        $user = User::find($id);
        if(!$user) {
            return response()->json(["message" => "Usuario no encontrado"], Response::HTTP_NOT_FOUND);
        }
        $user->delete();
        return response()->json(["message" => "Usuario eliminado exitosamente"], Response::HTTP_OK);
    }



    public function show($id){
        $user = User::find($id);
        if(!$user) {
            return response()->json(["message" => "Usuario no encontrado"], Response::HTTP_NOT_FOUND);
        }
        return response()->json($user, Response::HTTP_OK);
    }

    public function index(Request $request) {
        $users = User::paginate(10);
        return response()->json($users, 200);
    }
}
