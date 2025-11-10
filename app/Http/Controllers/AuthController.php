<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
        ], [
            'name.required' => 'Campo vuoto',
            'name.unique' => 'Nome già in uso',
            'email.required' => 'Campo vuoto',
            'email.email' => 'Dati errati',
            'email.unique' => 'Email già in uso',
            'password.required' => 'Campo vuoto',
            'password.confirmed' => 'Conferma la password'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('booksApi')->plainTextToken;

        return response()->json([
            'message' => 'Registrazione completata',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Campo vuoto',
            'password.required' => 'Campo vuoto',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenziali non valide'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('booksApi')->plainTextToken;

        return response()->json([
            'message' => 'Accesso eseguito!',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Disconnessione eseguita!',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
