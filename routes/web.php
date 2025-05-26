<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\ImportUserData;
use App\Models\User;
use App\Models\Utils;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Sabberworm\CSS\Property\Import;

Route::get('send-new-password', function (Request $r) {
    //send new password to user
    $user = User::find($r->user_id);
    if ($user == null) {
        dd("User not found.");
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
        dd("Email sent successfully to " . $user->email);
    } catch (\Throwable $th) {
        // Handle email sending failure
        dd("Failed to send email: " . $th->getMessage());
    }
});
Route::get('import-user-data', function (Request $r) {
    $rec = ImportUserData::find($r->id);
    if ($rec == null) {
        dd("Record not found.");
    }
    $path = public_path('storage/' . $rec->title);

    //check 
    if (!file_exists($path)) {
        dd("File not found at path: " . $path);
    }

    //csv
    $csvData = file_get_contents($path);
    if ($csvData === false) {
        dd("Failed to read the file at path: " . $path);
    }
    $lines = explode("\n", $csvData);
    $header = str_getcsv(array_shift($lines));
    $data = [];
    foreach ($lines as $line) {
        $row = str_getcsv($line);
        if (count($row) === count($header)) {
            $data[] = array_combine($header, $row);
        }
    }
    // Process the data as needed
    /* 
    1 => array:3 [▼
    "name" => "Mohindo Jane"
    "gender" => "Female"
    "email" => "mail2@gmail.com"
    ]
     */

    $successCount = 0;
    $errorCount = 0;
    $count = 0;
    $totalCount = count($data);
    foreach ($data as $row) {
        echo "<hr>";
        $count++;
        if (!isset($row['name']) || !isset($row['email'])) {
            $errorCount++;
            echo "<span style='color: red;'>[$count/$totalCount] Invalid row data. Missing 'name' or 'email'.</span><br>";
            continue; // Skip to the next row if data is invalid
        }
        // Process each row
        // For example, you can print the name and email
        $existingUser = User::where('email', $row['email'])->first();
        if ($existingUser == null) {
            $existingUser = User::where('username', $row['email'])->first();
        }
        if ($existingUser != null) {
            $errorCount++;
            echo "<span style='color: red;'>
                [$count/$totalCount] User with email <strong>{$row['email']}</strong> already exists.
                Name: <strong>{$row['name']}</strong>, Gender: <strong>{$row['gender']}</strong>
            </span><br>";
            continue; // Skip to the next row if user already exists
        }


        $user = new User();
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->sex = $row['gender'] ?? null; // Use
        $user->username = $row['email'];
        $user->department_id = $rec->department_id;
        $user->company_id = 1;
        $user->password = password_hash($user->email, PASSWORD_BCRYPT);

        $name_parts = explode(' ', $user->name);
        if (count($name_parts) > 1) {
            $user->first_name = $name_parts[0];
            $user->last_name = implode(' ', array_slice($name_parts, 1));
        } else {
            $user->first_name = $user->name;
            $user->last_name = null;
        }

        try {
            $user->save();
            $successCount++;
            echo "<span style='color: green;'>[$count/$totalCount] User <strong>{$user->name}</strong> created successfully.</span><br>";
        } catch (\Exception $e) {
            $errorCount++;
            echo "<span style='color: red;'>[$count/$totalCount] Error creating user: {$e->getMessage()}</span><br>";
        }

        //assign role 2
        $sql = "INSERT INTO `admin_role_users` (`user_id`, `role_id`) VALUES ({$user->id}, 2)";
        try {
            DB::insert($sql);
        } catch (\Exception $e) {
            echo "<span style='color: red;'>[$count/$totalCount] Error assigning role to user: {$e->getMessage()}</span><br>";
        }
    }
    echo "<hr>";
    echo "<h3>Import Summary</h3>";
    echo "<p>Total Rows: $totalCount</p>";
    echo "<p>Successful Imports: $successCount</p>";
    echo "<p>Failed Imports: $errorCount</p>";
    echo "<p>Check the logs for more details.</p>";
    return;
});

Route::get('auth/login', function () {
    return view('auth/login');
});


Route::get('print-gatepass', function (Request $request) {
    $item = VehicleRequest::find($request->gatepass_id);
    if ($item == null) {
        die("Item not found.");
    }
    $pdf = App::make('dompdf.wrapper');
    $pdf->loadHTML(view('print/print-gatepass', [
        'item' => $item
    ]));
    return $pdf->stream();
});


//endpoint for migration
Route::get('migrate', function () {
    //artisan migrate
    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();
    return nl2br($output);
});
