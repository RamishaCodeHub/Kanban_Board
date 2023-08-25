<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\WebsiteLinkEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
{
    
    $email = $request->input('email');
    
    // Check if email already exists in the database
    $existingUser = User::where('email', $email)->first();
    if (!$existingUser) {
        $token = sha1(time());

        // Create a new user and save email and token in the user table
        $user = new User();
        $user->email = $email;
        $user->token = $token;
        $user->save();
       
    } else {
        $token = $existingUser->token; // Use existing token if email exists
    }

    Mail::to($email)->send(new WebsiteLinkEmail($email , $token));

    return response()->json(['message' => 'Email sent successfully']);
}
}
