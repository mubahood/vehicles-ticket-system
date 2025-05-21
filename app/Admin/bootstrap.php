<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */


use App\Models\Utils;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Auth;
use App\Admin\Extensions\Nav\Shortcut;
use App\Admin\Extensions\Nav\Dropdown;
use App\Models\AdminRoleUser;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Carbon\Carbon;
use Encore\Admin\Form;
use Illuminate\Support\Facades\DB;

$x = 0;
$max = 300;
$user_ids = User::all()->pluck('id')->toArray();
$faker = Faker\Factory::create(); 
$vehicles_ids = Vehicle::all()->pluck('id')->toArray();
//ADD exit-records TO MENU

/* 
    "id" => 3
    "created_at" => "2025-03-24 10:24:19"
    "updated_at" => "2025-03-24 10:24:19"
    "registration_number" => "UBA - 329bA"
    "vehicle_type" => "Bus"
    "brand" => "Voluptatem maiores d"
    "model" => "Ut delectus magnam"
    "color" => "#000000"
    "year" => "2019"
    "status" => "Active"
    "rent_status" => "Available"
*/ 
/* DB::table('vehicle_requests')->truncate();  
for ($x = 0; $x < $max; $x++) {
    echo ' ' . $x . '  <br>';
    $req = new VehicleRequest();
    $now = Carbon::now();
    $bool = $faker->boolean;
    if ($bool) {
        $req->created_at = $now->subDays($faker->numberBetween(1, 60));
    }else{
        $req->created_at = $now->addDays($faker->numberBetween(1, 60));
    }

    $req->updated_at = $req->created_at;
    $req->vehicle_id = $faker->randomElement($vehicles_ids);
    $req->applicant_id = $faker->randomElement($user_ids);
    $req->requested_departure_time = $faker->dateTimeBetween('-1 years', 'now');
    $req->requested_return_time = $faker->dateTimeBetween('now', '+1 years');
    $req->destination = $faker->sentence(3);
    $req->justification = $faker->sentence(6);
    $req->status = $faker->randomElement(['Pending', 'Approved', 'Rejected']);
    $req->hod_status = $faker->randomElement(['Pending', 'Approved', 'Rejected']);
    $req->gm_status = $faker->randomElement(['Pending', 'Approved', 'Rejected']);
    $req->security_exit_status = $faker->randomElement(['Pending', 'Approved', 'Rejected']);
    $req->security_return_status = $faker->randomElement(['Pending', 'Approved', 'Rejected']);
    $req->over_stayed = $faker->randomElement(['Yes', 'No']);
    $req->exit_state = $faker->randomElement(['Pending', 'Approved', 'Rejected']);
    $req->exit_comment = $faker->sentence(6);
    $req->return_comment = $faker->sentence(6);
    $req->hod_comment = $faker->sentence(6);
    $req->gm_comment = $faker->sentence(6);
    $req->mail_sent_to_hod = $faker->randomElement(['Yes', 'No']);
    $req->mail_sent_to_gm = $faker->randomElement(['Yes', 'No']);
    $req->mail_sent_to_security_exit = $faker->randomElement(['Yes', 'No']);
    $req->mail_sent_to_security_return = $faker->randomElement(['Yes', 'No']);
    $req->mail_sent_to_applicant_on_hod_approval = $faker->randomElement(['Yes', 'No']);
    $req->mail_sent_to_applicant_on_gm_approval = $faker->randomElement(['Yes', 'No']);
    $req->mail_sent_to_applicant_on_security_exit_approval = $faker->randomElement(['Yes', 'No']);
    $req->type = $faker->randomElement(['Vehicle', 'Materials','Personnel']);
    $req->materials_requested = $faker->randomElement(['Special', 'Normal']);
    $req->company_id = $faker->randomElement([1, 2, 3]);
    $req->co_drivers = $faker->sentence(6);
    $req->is_closed = $faker->randomElement(['Yes', 'No']);
    $req->save();  
    echo '<pre>';
    print_r($req->toArray());
    echo '</pre>';
    echo '<hr>';
      
}
die();  */

/* 
  "id" => 7
 
    "" => "2025-03-24 10:38:13"
    "vehicle_id" => null
    "applicant_id" => 1
    "requested_departure_time" => "2025-03-25 00:00:00"
    "requested_return_time" => "2025-07-20 00:01:00"
    "actual_return_time" => null
    "actual_departure_time" => null
    "destination" => "Fuga Et ullam digni"
    "justification" => "jgvh as hj an b ahks"
    "status" => "Pending"
    "hod_status" => "Pending"
    "gm_status" => "Pending"
    "security_exit_status" => "Pending"
    "security_return_status" => "Pending"
    "return_state" => null
    "over_stayed" => "No"
    "exit_state" => null
    "exit_comment" => null
    "return_comment" => null
    "hod_comment" => null
    "gm_comment" => null
    "mail_sent_to_hod" => "No"
    "mail_sent_to_gm" => "No"
    "mail_sent_to_security_exit" => "No"
    "mail_sent_to_security_return" => "No"
    "mail_sent_to_applicant_on_hod_approval" => "No"
    "mail_sent_to_applicant_on_gm_approval" => "No"
    "mail_sent_to_applicant_on_security_exit_approval" => "No"
    "type" => "Personnel"
    "materials_requested" => "Special"
    "company_id" => null
    "co_drivers" => null
    "is_closed" => "No"
*/
Utils::start_session();

$u = Admin::user();
if ($u != null) {
    if ($u->is_mail_verified != 'Yes') {
        $USER = User::find($u->id);
        $USER->is_mail_verified  = 'Yes';
        $USER->save();

        /* scrip to rediect to verification-mail-send */
        // $url = url('verification-mail-send');
        // echo '<script>window.location = "' . $url . '";</script>';
    }
}

if (isset($_SESSION['my_success']) && $_SESSION['my_success'] != null && strlen($_SESSION['my_success']) > 2) {
    admin_success("Success", $_SESSION['my_success']);
    $_SESSION['my_success'] = null;
    unset($_SESSION['my_success']);
}

if (isset($_SESSION['my_error']) && $_SESSION['my_error'] != null && strlen($_SESSION['my_error']) > 2) {
    admin_error("Error", $_SESSION['my_error']);
    $_SESSION['my_error'] = null;
    unset($_SESSION['my_error']);
}



Admin::css('/assets/js/calender/main.css');
Admin::js('/assets/js/calender/main.js');

Admin::css('/css/jquery-confirm.min.css');
Admin::js('/assets/js/jquery-confirm.min.js');

$u = Admin::user();
if ($u != null) {
    if ($u->roles->count() < 1) {
        $role = new AdminRoleUser();
        $role->role_id = 4;
        $role->user_id = $u->id;
        $role->save();
    }
}

Utils::system_boot();


Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {

    $u = Auth::user();
/*     $navbar->left(view('admin.search-bar', [
        'u' => $u
    ]));
 */
    $navbar->left(Shortcut::make([
        'New Vehicle requests' => 'vehicle-requests/create',
        'New Material requests' => 'materials-requests/create',
        'New Leave requests' => 'leave-requests/create',
        /*  'Products or Services' => 'products/create',
        'Jobs and Opportunities' => 'jobs/create',
        'Event' => 'events/create', */
    ], 'fa-plus')->title('CREATE NEW'));
    /*     $navbar->left(Shortcut::make([
        'Candidate' => 'people/create', 
    ], 'fa-wpforms')->title('Register new')); */



    /*     $navbar->right(Shortcut::make([
        'How to register a new candidate' => '',
        'How to change  candidate\'s status' => '',
    ], 'fa-question')->title('HELP')); */
});


Form::init(function (Form $form) {
    //$form->disableEditingCheck();
    // $form->disableCreatingCheck();
    $form->disableViewCheck();
    $form->disableReset();
    //$form->disableCreatingCheck();

    $form->tools(function (Form\Tools $tools) {
        $tools->disableDelete();
        $tools->disableView();
    });
});


Encore\Admin\Form::forget(['map', 'editor']);
Admin::css(url('/assets/css/bootstrap.css'));
Admin::css('/assets/css/styles.css');
