<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Notifications\ResetPasswordCodeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Inscription
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Connexion
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Identifiants incorrects',
            ], 401);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    // Profil de l'utilisateur connecté
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    // Déconnexion (Révocation du jeton)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie',
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Génération d'un code à 6 chiffres
        $code = random_int(100000, 999999);

        // Suppression des anciens codes de cet email
        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        // Enregistrement du nouveau code
        DB::table('password_reset_codes')->insert([
            'email' => $request->email,
            'code' => $code,
            'created_at' => Carbon::now(),
        ]);

        // Notification / Envoi d'email
        $user = User::where('email', $request->email)->first();
        $user->notify(new ResetPasswordCodeNotification($code));

        return response()->json([
            'message' => 'Le code de réinitialisation a été envoyé sur votre email.',
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRecord = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        // Vérifier si le code existe et n'a pas expiré (ex: 15 minutes)
        if (! $resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(15)->isPast()) {
            return response()->json([
                'message' => 'Code invalide ou expiré.',
            ], 400);
        }

        // Mise à jour du mot de passe
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Révocation de tous les anciens tokens de l'utilisateur (Sécurité)
        $user->tokens()->delete();

        // Suppression du code utilisé
        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Mot de passe réinitialisé avec succès.',
        ], 200);
    }
}
