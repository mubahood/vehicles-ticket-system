<?php

namespace App\Models;

use Encore\Admin\Form\Field\BelongsToMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as RelationsBelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;



class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;



    //sendEmailVerificationNotification
    public function sendEmailVerificationNotification()
    {
        return;
        $mail_verification_token = Utils::get_unique_text();
        $this->mail_verification_token = $mail_verification_token;
        $this->save();

        $url = url('verification-mail-verify?tok=' . $mail_verification_token);
        $from = env('APP_NAME') . " Team.";

        $mail_body =
            <<<EOD
        <p>Dear <b>$this->name</b>,</p>
        <p>Please click the link below to verify your email address.</p>
        <p><a href="{$url}">Verify Email Address</a></p>
        <p>Best regards,</p>
        <p>{$from}</p>
        EOD;

        // $full_mail = view('mails/mail-1', ['body' => $mail_body, 'title' => 'Email Verification']);

        try {
            $day = date('Y-m-d');
            $data['body'] = $mail_body;
            $data['data'] = $data['body'];
            $data['name'] = $this->name;
            $data['email'] = $this->email;
            $data['subject'] = 'Email Verification - ' . env('APP_NAME') . ' - ' . $day . ".";
            Utils::mail_sender($data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }


 

    //email getter
    public function getEmailAttribute($value)
    {
        if ($value == null || strlen($value) < 1) {
            $username = $this->username;
            if ($username != null && strlen($username) > 1) {
                //validate email
                if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                    $email = $username;
                    $this->email = $email;
                    $this->save();
                    $value = $email;
                }
            }
        }
        return $value;
    }

    //Belongs to company
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    //public send welcom message
    public function sendWelcomeMessage()
    {
        $user = $this;
        if ($user == null) {
            throw new \Exception("User not found.");
        }
        $newPassword = rand(100000, 999999); // Generate a random 6-digit number
        $user->password = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->save();
        //send email
        $APP_NAME = env('APP_NAME', 'Vehcle Management System');
        $subject = "Welcome to " . env('APP_NAME') . " - Your Account Details";
        $LOGIN_URL = admin_url();
        $message = <<<HTML
                    <h3>Hello {$user->name},</h3>
                    <p>Welcome to <strong> {$APP_NAME} </strong>!</p>
                    <p><strong>Your login details:</strong></p>
                    <ul>
                        <li><strong>Email:</strong> {$user->email}</li>
                        <li><strong>Temporary Password:</strong> {$newPassword}</li>
                    </ul>
                    <p>Log in here: <a href="{$LOGIN_URL}">Login</a></p>
                    <p>Please change your password after logging in.</p>
                    <p>Thank you.</p>
                    HTML;

        try {
            $data['body'] = $message;
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['subject'] = $subject;
            Utils::mail_sender($data);
        } catch (\Throwable $th) {
            // Handle email sending failure
            throw new \Exception("Failed to send email to {$user->email}. Error: " . $th->getMessage());
        }
    }
}
