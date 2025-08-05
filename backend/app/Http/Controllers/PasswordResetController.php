<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Controllers\Controller;

class PasswordResetController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required|digits:10']);
        $phone = $request->phone;

        $user = User::where('Phone', $phone)->first();
        if (!$user) {
            return response()->json(['message' => 'Téléphone non trouvé'], 404);
        }

        $otp = rand(100000, 999999);

        DB::table('password_resets_sms')->where('phone', $phone)->delete();

        DB::table('password_resets_sms')->insert([
            'phone' => $phone,
            'otp_code' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $twilio_number = config('services.twilio.from');

            $client = new Client($sid, $token);

            $cleanedPhone = preg_replace('/^0/', '', $phone);
            $twilioPhone = "+261" . $cleanedPhone;

            $client->messages->create($twilioPhone, [
                'from' => $twilio_number,
                'body' => "Votre code de vérification est : $otp"
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur Twilio: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du SMS',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json(['message' => 'Code OTP envoyé par SMS']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'code' => 'required|string|size:6',
        ]);

        $record = DB::table('password_resets_sms')
            ->where('phone', $request->phone)
            ->where('otp_code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Code OTP invalide ou expiré'], 400);
        }
        return response()->json(['message' => 'Code OTP valide', 'token' => base64_encode($request->code)]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'token' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $otp_code = base64_decode($request->token);

        $record = DB::table('password_resets_sms')
            ->where('phone', $request->phone)
            ->where('otp_code', $otp_code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Code OTP invalide ou expiré'], 400);
        }

        $user = User::where('Phone', $request->phone)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        $user->mot_de_passe = Hash::make($request->new_password);
        $user->doit_changer_mdp = false;
        $user->save();

        DB::table('password_resets_sms')->where('phone', $request->phone)->delete();

        return response()->json(['message' => 'Mot de passe modifié avec succès']);
    }
}
