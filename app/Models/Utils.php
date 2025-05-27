<?php

namespace App\Models;

use Carbon\Carbon;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use SplFileObject;
use Illuminate\Support\Str;

class Utils extends Model
{
    use HasFactory;


    public static function get_test_mails()
    {
        return [
            'gm@mail.com',
            'gm@test.com',
            'employee@gmail.com',
            'admin@gmail.com',
        ];
    }

    public static function get_general_managers()
    {
        $gm_roles = 'SELECT user_id FROM admin_role_users WHERE role_id = (SELECT id FROM admin_roles WHERE slug = "gm")';
        $gm_user_ids = \DB::select($gm_roles);

        $gm_user_ids = array_map(function ($item) {
            return $item->user_id;
        }, $gm_user_ids);
        $gm_user = User::whereIn('id', $gm_user_ids)
            ->orderBy('id', 'desc')
            ->get()
            ->first();

        if ($gm_user == null) {
            return null;
        }
        return $gm_user;
    }
    public static function get_dropdown($model, $name)
    {
        $data = $model::where([
        ])->get();
        $arr = [];


        foreach ($data as $key => $value) {
            if (is_array($name)) {
                $n = '';
                foreach ($name as $k => $v) {
                    $n .= $value->$v;
                    //if not last, add ' - '
                    if ($k < count($name) - 1) {
                        $n .= ' - ';
                    }
                }
                $arr[$value->id] = $n;
            } else {
                $arr[$value->id] = $value->$name;
            }
        }
        //sort
        asort($arr);
        return $arr;
    }
    public static function get_unique_text()
    {
        //get uniqte text
        $section_0 = uniqid();
        $section_1 = time();
        $section_2 = rand(1000000, 99999999);
        $section_3 = rand(1000000, 99999999);
        $unique_text = $section_0 . '-' . $section_1 . '-' . $section_2 . '-' . $section_3;
        return $unique_text;
    }


    public static function mail_sender($data)
    {
        try {
            Mail::send(
                'mails/mail-1',
                [
                    'body' => $data['body'],
                    'title' => $data['subject']
                ],
                function ($m) use ($data) {
                    $m->to($data['email'], $data['name'])
                        ->subject($data['subject']);
                    $m->from(env('MAIL_FROM_ADDRESS'), $data['subject']);
                }
            );
        } catch (\Throwable $th) {
            $msg = 'failed';
            throw $th;
        }
    }


    public static function getCurrentSegmentTitle()
    {
        $segs = Utils::getSegments();

        $seg = '';
        if (isset($segs[1])) {
            $seg = $segs[1];
        }

        if ($seg == 'my-applications') {
            return 'My Applications';
        } else if ($seg == 'cases-pending') {
            return 'Pending Applications';
        } else if ($seg == 'cases-hearing') {
            return 'Hearing';
        } else if ($seg == 'cases-mediation') {
            return 'Mediation';
        } else if ($seg == 'cases-court') {
            return 'Court';
        } else if ($seg == 'cases-closed') {
            return 'Closed Cases';
        } else if ($seg == 'cases') {
            return 'Cases';
        } else if ($seg == 'applications') {
            return 'Applications';
        } else if ($seg == 'attarchments') {
            return 'Attachments';
        } else if ($seg == 'districts') {
            return 'Districts';
        } else if ($seg == 'sub-counties') {
            return 'Sub-Counties';
        } else if ($seg == 'offences') {
            return 'Offences';
        } else if ($seg == 'applications-filing') {
            return 'Applications Filing';
        } else if ($seg == 'applications-defense') {
            return 'Applications Defense';
        } else if ($seg == 'applications-scheduled') {
            return 'Applications Scheduled';
        } else if ($seg == 'applications-mediation') {
            return 'Applications Mediation';
        } else if ($seg == 'applications-hearing') {
            return 'Applications Hearing';
        } else if ($seg == 'applications-closed') {
            return 'Applications Closed';
        } else if ($seg == 'applications-defense') {
            return 'Applications under Defence';
        } else {
            return 'Applications';
        }
    }
    public static function getCurrentSegment()
    {
        $segs  = Utils::getSegments();
        if (in_array('my-applications', $segs)) {
            return 'my-applications';
        } else if (in_array('cases-pending', $segs)) {
            return 'cases-pending';
        } else if (in_array('cases-hearing', $segs)) {
            return 'cases-hearing';
        } else if (in_array('cases-mediation', $segs)) {
            return 'cases-mediation';
        } else if (in_array('cases-court', $segs)) {
            return 'cases-court';
        } else if (in_array('cases-closed', $segs)) {
            return 'cases-closed';
        } else if (in_array('cases', $segs)) {
            return 'cases';
        } else if (in_array('applications', $segs)) {
            return 'applications';
        } else if (in_array('attarchments', $segs)) {
            return 'attarchments';
        } else if (in_array('districts', $segs)) {
            return 'districts';
        } else if (in_array('sub-counties', $segs)) {
            return 'sub-counties';
        } else if (in_array('offences', $segs)) {
            return 'offences';
        } else if (in_array('applications-filing', $segs)) {
            return 'applications-filing';
        } else if (in_array('applications-defense', $segs)) {
            return 'applications-defense';
        } else if (in_array('applications-scheduled', $segs)) {
            return 'applications-scheduled';
        } else if (in_array('applications-mediation', $segs)) {
            return 'applications-mediation';
        } else if (in_array('applications-hearing', $segs)) {
            return 'applications-hearing';
        } else if (in_array('applications-closed', $segs)) {
            return 'applications-closed';
        }
        return '';
    }

    public static function hasSegment($seg)
    {
        $segs = Utils::getSegments();
        return in_array($seg, $segs);
    }
    public static function getSegments()
    {
        // Get the current URL
        $current_url = $_SERVER['REQUEST_URI'];

        // Remove leading and trailing slashes
        $current_url = trim($current_url, '/');

        // Split the URL into segments
        $url_segments = explode('/', $current_url);

        // Filter out any empty segments
        $url_segments = array_filter($url_segments);

        $final_segs = [];
        foreach ($url_segments as $key => $value) {
            $my_segs = explode('?', $value);
            if (isset($my_segs[0])) {
                $value = $my_segs[0];
            }
            $final_segs[] = $value;
        }

        return $final_segs;
    }


    public static function importPwdsProfiles($path)
    {
        $csv = new SplFileObject($path);
        $csv->setFlags(SplFileObject::READ_CSV);
        //$csv->setCsvControl(';');  //separator change if you need
        set_time_limit(-1); // Time in seconds
        $disability_description = [];
        $cats = [];
        $isFirst  = true;
        foreach ($csv as $line) {
            if ($isFirst) {
                $isFirst = false;
                continue;
            }

            $name = $line[0];
            $user = Person::where(['name' => $name])->first();
            if ($user == null) {
                continue;
            }
            $user->district_id = 88;
            $user->parish .= 1;
            $user->save();
            continue;



            /* if ((Person::count('id') >= 3963)) {
                die("done");
            } */

            $p = new Person();
            $p->name = 'N/A';



            $p->subcounty_description = null;
            if (
                isset($line[10]) &&
                $line[10] != null &&
                strlen($line[10]) > 2
            ) {
                $dis = $line[10];
                $_dis = Location::where(
                    'name',
                    'LIKE',
                    '%' . $dis . '%'
                )->first();
                if ($_dis != null) {
                    $p->district_id = $_dis->id;
                } else {
                    $p->district_id = 1002006;
                }
            }


            $p->subcounty_description = null;
            if (
                isset($line[8]) &&
                $line[8] != null &&
                strlen($line[8]) > 1
            ) {
                $p->dob = $line[8];
            }

            $p->subcounty_description = null;
            if (
                isset($line[7]) &&
                $line[7] != null &&
                strlen($line[7]) > 3
            ) {
                $p->caregiver_name = $line[7];
                $p->has_caregiver = 'Yes';
            } else {
                $p->has_caregiver = 'No';
            }

            $p->subcounty_description = null;
            if (
                isset($line[4]) &&
                $line[4] != null &&
                strlen($line[4]) > 3
            ) {
                $p->disability_description = $line[4];
            }

            $p->education_level = null;
            if (
                isset($line[5]) &&
                $line[5] != null &&
                strlen($line[5]) > 1
            ) {
                //$p->education_level = $line[5];
            }

            $p->job = null;
            if (
                isset($line[6]) &&
                $line[6] != null &&
                strlen($line[6]) > 1
            ) {
                $p->employment_status = 'Yes';
                $p->job = $line[6];
            } else {
                $p->employment_status = 'No';
            }

            if (
                isset($line[0]) &&
                $line[0] != null &&
                strlen($line[0]) > 2
            ) {
                $p->name = trim($line[0]);
            }

            $p->sex = 'N/A';
            if (
                isset($line[1]) &&
                $line[1] != null &&
                strlen($line[1]) > 0
            ) {
                if (strtolower(substr($line[0], 0, 1)) == 'm') {
                    $p->sex = 'Male';
                } else {
                    $p->sex = 'Female';
                }
            }

            $p->phone_number = null;
            if (
                isset($line[2]) &&
                $line[2] != null &&
                strlen($line[2]) > 5
            ) {
                $p->phone_number = Utils::prepare_phone_number($line[2]);
            }

            if (
                isset($line[3]) &&
                $line[3] != null &&
                strlen($line[3]) > 2
            ) {
                $cat =  trim(strtolower($line[3]));

                if (in_array($cat, [
                    'epilepsy'
                ])) {
                    $p->disability_id = 1;
                    $p->disability_description = $line[3];
                } elseif (in_array($cat, [
                    'visual',
                    'visual impairment',
                    'deaf-blind',
                    'visual disability',
                    'visual impairmrnt',
                    'blind',
                ])) {
                    $p->disability_id = 2;
                    $p->disability_description = $line[3];
                } elseif (in_array($cat, [
                    'deaf',
                    'epileosy/hard of speach',
                    'hard of hearing',
                    'hearing impairment',
                    'deaf blindness',
                    'hearing impairment',
                    'deaf-blind',
                    'youth rep (deaf )',
                    'deaf rep',
                    'deaf rep.',
                    'deaf',
                    'deafblind',
                ])) {
                    $p->disability_id = 3;
                    $p->disability_description = $line[3];
                } elseif (in_array($cat, [
                    'visual disabilty',
                    "low vision",
                    "visual",
                    "visual impairment",
                ])) {
                    $p->disability_id = 4;
                    $p->disability_description = $line[3];
                } elseif (in_array($cat, [
                    'intellectual disability',
                    'mental disabilty',
                    'mental disability',
                    'intellectual',
                    'interlectual',
                    'parent with interlectual',
                    'interlectual rep.',
                    'cerebral pulse',
                    'mental',
                    'mental retardation',
                    'mental health',
                    'mental illness',
                ])) {

                    $p->disability_id = 5;
                    $p->disability_description = $line[3];
                } elseif (in_array($cat, [
                    'epileptic',
                    'parent with children with intellectual disability',
                    'brain injury',
                    'spine damage',
                    'epilipsy',
                    'person with epilepsy',
                    'epilepsy',
                    'hydrosphlus',
                    'epilpesy',
                    'celebral palsy',
                    'women rep .celebral palsy',
                ])) {

                    $p->disability_id = 6;
                    $p->disability_description = $line[3];
                } elseif (in_array($cat, [
                    'physical',
                    'parent',
                    'physical  disability',
                    'physical disability',
                    'physical disabbility',
                    'physical disabilty',
                    'pyhsical disability',
                    'physical didability',
                    'physical diability',
                    'physical impairment',
                    'male',
                    'amputee',
                    'sickler',
                    'physical',
                    'physical impairment',
                    'parent rep',
                    'women rep.',
                    'youth rep',
                    'parent rep.',
                    'parent  rep.',
                    'parent',
                    'youth rep,',
                    'women rep',
                    'youth rep.',
                ])) {
                    $p->disability_id = 7;
                    $p->disability_description = $line[3];
                } elseif (in_array($cat, [
                    'albino',
                    'albinism',
                    'person with albinism',
                    'albism',
                    'albino',
                    'albinsim',
                    'albinism',
                ])) {
                    $p->disability_id = 8;
                    $p->disability_description = $line[3];
                } elseif (in_array($cat, [
                    'little person',
                    'littleperson',
                    'liitleperson',
                    'liittleperson',
                    'little person',
                    'dwarfism',
                    'persons of short stature (little persons)',
                ])) {
                    $p->disability_id = 9;
                    $p->disability_description = $line[3];
                } else {
                    $p->disability_id = 7;
                    $p->disability_description = $line[3];
                }
            } else {
                $p->disability_id = 6;
                $p->disability_description = 'Other';
            }

            $p->subcounty_description = null;
            if (
                isset($line[2]) &&
                $line[2] != null &&
                strlen($line[2]) > 5
            ) {
                $p->phone_number = Utils::prepare_phone_number($line[2]);
            }

            $_p = Person::where(['name' => $p->name, 'district_id' => $p->district_id])->first();
            if ($_p != null) {
                echo "FOUND => $_p->name<=========<hr>";
                continue;
            }

            try {
                $p->save();
                echo $p->id . ". " . $p->name . "<hr>";
            } catch (\Throwable $th) {
                echo $th;
                echo "failed <br>";
            }
        }

        dd($disability_description);
        echo "done! with $p->id <pre>";
        die('');

        dd($path);
    }





    public static function phone_number_is_valid($phone_number)
    {
        $phone_number = Utils::prepare_phone_number($phone_number);
        if (substr($phone_number, 0, 4) != "+256") {
            return false;
        }

        if (strlen($phone_number) != 13) {
            return false;
        }

        return true;
    }
    public static function prepare_phone_number($phone_number)
    {
        $original = $phone_number;
        //$phone_number = '+256783204665';
        //0783204665
        if (strlen($phone_number) > 10) {
            $phone_number = str_replace("+", "", $phone_number);
            $phone_number = substr($phone_number, 3, strlen($phone_number));
        } else {
            if (substr($phone_number, 0, 1) == "0") {
                $phone_number = substr($phone_number, 1, strlen($phone_number));
            }
        }
        if (strlen($phone_number) != 9) {
            return $original;
        }
        return "+256" . $phone_number;
    }



    public static function docs_root()
    {
        $r = $_SERVER['DOCUMENT_ROOT'] . "";

        if (!str_contains($r, 'home/')) {
            $r = str_replace('/public', "", $r);
            $r = str_replace('\public', "", $r);
        }

        if (!(str_contains($r, 'public'))) {
            $r = $r . "/public";
        }


        /* 
         "/home/ulitscom_html/public/storage/images/956000011639246-(m).JPG
        
        public_html/public/storage/images
        */
        return $r;
    }

    public static function upload_images_2($files, $is_single_file = false)
    {

        ini_set('memory_limit', '-1');
        if ($files == null || empty($files)) {
            return $is_single_file ? "" : [];
        }
        $uploaded_images = array();
        foreach ($files as $file) {

            if (
                isset($file['name']) &&
                isset($file['type']) &&
                isset($file['tmp_name']) &&
                isset($file['error']) &&
                isset($file['size'])
            ) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $file_name = time() . "-" . rand(100000, 1000000) . "." . $ext;
                $destination = Utils::docs_root() . '/storage/images/' . $file_name;

                $res = move_uploaded_file($file['tmp_name'], $destination);
                if (!$res) {
                    continue;
                }
                //$uploaded_images[] = $destination;
                $uploaded_images[] = $file_name;
            }
        }

        $single_file = "";
        if (isset($uploaded_images[0])) {
            $single_file = $uploaded_images[0];
        }


        return $is_single_file ? $single_file : $uploaded_images;
    }






    public static function checkEventRegustration()
    {
        return true;
        $u = Admin::user();
        if ($u == null) {
            return;
        }

        if (!$u->complete_profile) {
            return;
        }

        $ev = EventBooking::where(['administrator_id' => $u->id, 'event_id' => 1])->first();
        if ($ev != null) {
            return;
        }


        $btn = '<a class="btn btn-lg btn-primary" href="' . admin_url('event-bookings/create?event=1') . '" >BOOK A SEAT</a>';
        admin_info(
            'NOTICE: IUIU-ALUMNI GRAND DINNER - 2023',
            "Dear {$u->name}, there is an upcoming IUIUAA Grand dinner that will take place on 10th FEB, 2023.
        Please this form to apply for your ticket now! {$btn}"
        );
    }


    public static function process_things()
    {
        foreach (
            Renting::where(
                'invoice_as_been_billed',
                '!=',
                'Yes'
            )->get() as $key => $inv
        ) {
            $inv->process_bill();
            echo $inv->id . ". INVOICE - " . $inv->name . ", BALANCE {$inv->balance} <br>";
        }
        foreach (
            Renting::where(
                'invoice_status',
                'Active'
            )->get() as $key => $m
        ) {
            $m->is_overstay = 'No';
            if ($m->invoice_status == 'Active') {
                $lastDate = Carbon::parse($m->end_date);
                $now = Carbon::now();
                if ($now->gt($lastDate)) {
                    $m->is_overstay = 'Yes';
                } else {
                    $m->is_overstay = 'No';
                }
            }
            echo $m->id . ". INVOICE OVERSTAY - " . $m->name . ", is_overstay: {$m->is_overstay} <br>";
            $m->save();
        }

        foreach (Landload::all() as $key => $landload) {
            $landload->update_balance();
            echo $landload->id . ". LANDLORD - " . $landload->name . ", BALANCE {$landload->balance} <br>";
        }
        foreach (Tenant::all() as $key => $t) {
            $t->update_balance();
            echo $t->id . ". TENANT - " . $t->name . ", BALANCE {$t->balance} <br>";
        }
    }

    public static function system_boot()
    {
        //send mails to admins for pending applications
        self::send_mails_for_pending_applications();
        //send mail to ura for defence applications
        self::notify_ura_to_submit_defence();
        //notify_registrar_how_ura_has_submitted_defence
        self::notify_registrar_how_ura_has_submitted_defence();
        //send_schedule_email
        self::send_schedule_email();
    }

    //send_schedule_email
    public static function send_schedule_email() {}

    /**
     * Sends email notifications to URAs for applications that have reached the
     * Defence stage but have not had an email notification sent yet.
     *
     * @return void
     */
    public static function notify_registrar_how_ura_has_submitted_defence() {}
    public static function notify_ura_to_submit_defence() {}

    public static function get_emails_for_role($role_slug) {}

    public static function get_tat_members() {}

    public static function send_mails_for_pending_applications() {}

    public static function start_session()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }



    public static function month($t)
    {
        $c = Carbon::parse($t);
        if ($t == null) {
            return $t;
        }
        return $c->format('M - Y');
    }
    public static function my_day($t)
    {
        $c = Carbon::parse($t);
        if ($t == null) {
            return $t;
        }
        return $c->format('d M');
    }


    public static function my_date_1($t)
    {
        $c = Carbon::parse($t);
        if ($t == null) {
            return $t;
        }
        return $c->format('D - d M');
    }

    public static function my_date($t)
    {
        $c = Carbon::parse($t);
        if ($t == null) {
            return '-';
        }
        return $c->format('d M, Y');
    }
    public static function my_date_4($t)
    {
        $c = Carbon::parse($t);
        if ($t == null) {
            return $t;
        }
        return $c->format('Y-m-d');
    }

    public static function my_date_time($t)
    {
        $c = Carbon::parse($t);
        if ($t == null) {
            return $t;
        }
        return $c->format('d M, Y - h:m a');
    }

    public static function to_date_time($raw)
    {
        $t = Carbon::parse($raw);
        if ($t == null) {
            return  "-";
        }
        $my_t = $t->toDateString();

        return $my_t . " " . $t->toTimeString();
    }
    public static function number_format($num, $unit)
    {
        $num = (int)($num);
        $resp = number_format($num);
        if ($num < 2) {
            $resp .= " " . $unit;
        } else {
            $resp .= " " . Str::plural($unit);
        }
        return $resp;
    }





    public static function COUNTRIES()
    {
        $data = [];
        foreach (
            [
                '',
                "Uganda",
                "Somalia",
                "Nigeria",
                "Tanzania",
                "Kenya",
                "Sudan",
                "Rwanda",
                "Congo",
                "Afghanistan",
                "Albania",
                "Algeria",
                "American Samoa",
                "Andorra",
                "Angola",
                "Anguilla",
                "Antarctica",
                "Antigua and Barbuda",
                "Argentina",
                "Armenia",
                "Aruba",
                "Australia",
                "Austria",
                "Azerbaijan",
                "Bahamas",
                "Bahrain",
                "Bangladesh",
                "Barbados",
                "Belarus",
                "Belgium",
                "Belize",
                "Benin",
                "Bermuda",
                "Bhutan",
                "Bolivia",
                "Bosnia and Herzegovina",
                "Botswana",
                "Bouvet Island",
                "Brazil",
                "British Indian Ocean Territory",
                "Brunei Darussalam",
                "Bulgaria",
                "Burkina Faso",
                "Burundi",
                "Cambodia",
                "Cameroon",
                "Canada",
                "Cape Verde",
                "Cayman Islands",
                "Central African Republic",
                "Chad",
                "Chile",
                "China",
                "Christmas Island",
                "Cocos (Keeling Islands)",
                "Colombia",
                "Comoros",
                "Cook Islands",
                "Costa Rica",
                "Cote D'Ivoire (Ivory Coast)",
                "Croatia (Hrvatska",
                "Cuba",
                "Cyprus",
                "Czech Republic",
                "Denmark",
                "Djibouti",
                "Dominica",
                "Dominican Republic",
                "East Timor",
                "Ecuador",
                "Egypt",
                "El Salvador",
                "Equatorial Guinea",
                "Eritrea",
                "Estonia",
                "Ethiopia",
                "Falkland Islands (Malvinas)",
                "Faroe Islands",
                "Fiji",
                "Finland",
                "France",
                "France",
                "Metropolitan",
                "French Guiana",
                "French Polynesia",
                "French Southern Territories",
                "Gabon",
                "Gambia",
                "Georgia",
                "Germany",
                "Ghana",
                "Gibraltar",
                "Greece",
                "Greenland",
                "Grenada",
                "Guadeloupe",
                "Guam",
                "Guatemala",
                "Guinea",
                "Guinea-Bissau",
                "Guyana",
                "Haiti",
                "Heard and McDonald Islands",
                "Honduras",
                "Hong Kong",
                "Hungary",
                "Iceland",
                "India",
                "Indonesia",
                "Iran",
                "Iraq",
                "Ireland",
                "Israel",
                "Italy",
                "Jamaica",
                "Japan",
                "Jordan",
                "Kazakhstan",

                "Kiribati",
                "Korea (North)",
                "Korea (South)",
                "Kuwait",
                "Kyrgyzstan",
                "Laos",
                "Latvia",
                "Lebanon",
                "Lesotho",
                "Liberia",
                "Libya",
                "Liechtenstein",
                "Lithuania",
                "Luxembourg",
                "Macau",
                "Macedonia",
                "Madagascar",
                "Malawi",
                "Malaysia",
                "Maldives",
                "Mali",
                "Malta",
                "Marshall Islands",
                "Martinique",
                "Mauritania",
                "Mauritius",
                "Mayotte",
                "Mexico",
                "Micronesia",
                "Moldova",
                "Monaco",
                "Mongolia",
                "Montserrat",
                "Morocco",
                "Mozambique",
                "Myanmar",
                "Namibia",
                "Nauru",
                "Nepal",
                "Netherlands",
                "Netherlands Antilles",
                "New Caledonia",
                "New Zealand",
                "Nicaragua",
                "Niger",
                "Niue",
                "Norfolk Island",
                "Northern Mariana Islands",
                "Norway",
                "Oman",
                "Pakistan",
                "Palau",
                "Panama",
                "Papua New Guinea",
                "Paraguay",
                "Peru",
                "Philippines",
                "Pitcairn",
                "Poland",
                "Portugal",
                "Puerto Rico",
                "Qatar",
                "Reunion",
                "Romania",
                "Russian Federation",
                "Saint Kitts and Nevis",
                "Saint Lucia",
                "Saint Vincent and The Grenadines",
                "Samoa",
                "San Marino",
                "Sao Tome and Principe",
                "Saudi Arabia",
                "Senegal",
                "Seychelles",
                "Sierra Leone",
                "Singapore",
                "Slovak Republic",
                "Slovenia",
                "Solomon Islands",

                "South Africa",
                "S. Georgia and S. Sandwich Isls.",
                "Spain",
                "Sri Lanka",
                "St. Helena",
                "St. Pierre and Miquelon",
                "Suriname",
                "Svalbard and Jan Mayen Islands",
                "Swaziland",
                "Sweden",
                "Switzerland",
                "Syria",
                "Taiwan",
                "Tajikistan",
                "Thailand",
                "Togo",
                "Tokelau",
                "Tonga",
                "Trinidad and Tobago",
                "Tunisia",
                "Turkey",
                "Turkmenistan",
                "Turks and Caicos Islands",
                "Tuvalu",
                "Ukraine",
                "United Arab Emirates",
                "United Kingdom (Britain / UK)",
                "United States of America (USA)",
                "US Minor Outlying Islands",
                "Uruguay",
                "Uzbekistan",
                "Vanuatu",
                "Vatican City State (Holy See)",
                "Venezuela",
                "Viet Nam",
                "Virgin Islands (British)",
                "Virgin Islands (US)",
                "Wallis and Futuna Islands",
                "Western Sahara",
                "Yemen",
                "Yugoslavia",
                "Zaire",
                "Zambia",
                "Zimbabwe"
            ] as $key => $v
        ) {
            $data[$v] = $v;
        };
        return $data;
    }


    public static function convert_number_to_words($number)
    {

        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . Self::convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number <= 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number <= 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . Self::convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = Self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder <= 100 ? $conjunction : $separator;
                    $string .= Self::convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }


    public static function prepare_calendar_events($u)
    {

        $conditions = [
            'reminder_state' => 'On'
        ];
        if (!$u->isRole('admin')) {
            $conditions['administrator_id'] = $u->id;
        }

        $eves = Event::where($conditions)->get();
        $events = [];
        foreach ($eves as $key => $event) {
            continue;
            $ev['title'] = substr($event->description, 0, 20) . '...';
            $ev['start'] = Carbon::parse($event->reminder_date)->format('Y-m-d');
            $ev['Reminder Date'] = Carbon::parse($event->event_date)->format('Y-m-d');
            $details = "<b>Description:</b> " . $event->description . '<br>';
            $details .= "<b>Application:</b> #" . $event->application->id . '<br>';
            $details .= "<b>Due to:</b> " . $ev['start'] . '<br>';

            $ev['type'] = "Application #" . $event->application->id . '<br>';
            $ev['classNames'] = ['bg-success', 'border-success', 'text-white'];
            $details .= "<b>Pririty:</b> {$event->priority}<br>";
            $ev['administrator_id'] = $u->id;
            $ev['details'] = $details;
            $events[] = $ev;
        }
        return $events;

        die();

        foreach (Application::all() as $key => $app) {
            $ev = new Event();
            $ev->created_at = $app->created_at;
            $ev->administrator_id = $app->user_id;
            $ev->event_date = Carbon::now()->addDays(rand(-100, 500));
            $ev->remind_beofre_days = rand(1, 10);
            $ev->application_id = $app->id;
            $ev->reminder_state = 'On';
            $ev->outcome = Null;
            $ev->users_to_notify = 'No';
            $ev->reminders_sent = 'No';
            $ev->description = "#" . $app->application_number . " - " . $app->applicant_name;
            $ev->priority = [
                '0' => 'Low',
                '1' => 'Medium',
                '2' => 'High',
            ][rand(0, 2)];
            $ev->save();
        }


        for ($i = 0; $i < 200; $i++) {
            $ev['title'] = 'Event ' . $i;
            $ev['start'] = Carbon::now()->addDays(rand(-100, 100))->format('Y-m-d');
            //$ev['start'] = Carbon::parse($act->due_date)->format('Y-m-d');
            $details = "<b>Description:</b> details: " . $i . '<br>';
            $details .= "<b>Enterprise:</b> act name " . $i . '<br>';
            $details .= "<b>Due to:</b> " . $ev['start'] . '<br>';

            $ev['classNames'] = ['bg-success', 'border-success', 'text-white'];
            $details .= "<b>Activity status:</b> Not Done (Missed)<br>";

            $details .= "<b>Status remarks:</b> Done details: " . $i . '<br>';
            $details .= "<b>Person responsible:</b> assigned to" . $i . '<br>';

            $ev['details'] = $details;
            $ev['administrator_id'] = $u->id;
            $ev['done_status'] =  $i % 2 == 0 ? 1 : 0;
            $ev['done_details'] = 'Done details: ' . $i;
            $ev['garden_id'] =  $i;
            $ev['activity_id'] = $i;
            $ev['id'] = count($events);
            $ev['person_responsible'] = $i;
            $ev['type'] = 'Scheduled activity';

            //$ev['textColor'] = 'red';

            $events[] = $ev;
        }
        return $events;

        $activities = GardenActivity::where(['administrator_id' => $u->id])
            ->orWhere(['person_responsible' => $u->id])
            ->get();


        foreach ($activities as $act) {
            //$ev['display'] = 'list-item';
            $ev['title'] = $act->name;
            $ev['start'] = Carbon::parse($act->due_date)->format('Y-m-d');
            $details = "<b>Description:</b> " . $act->details . '<br>';
            $details .= "<b>Enterprise:</b> " . $act->enterprise->name . '<br>';
            $details .= "<b>Due to:</b> " . $ev['start'] . '<br>';


            $ev['is_done'] = $act->is_done;
            if ($act->is_done == 1 || $act->is_done == true) {
                $ev['is_done'] = 1;
                $ev['classNames'] = ['bg-success', 'border-success', 'text-white'];

                if ($act->done_status == 1 || $act->done_status == true) {
                    $details .= "<b>Activity status:</b> Done<br>";
                } else {
                    $details .= "<b>Activity status:</b> Not Done (Missed)<br>";
                }
            } else {
                $ev['is_done'] = 0;
                $details .= "<b>Activity status:</b>Pending<br>";
                $ev['classNames'] = ['bg-danger', 'border-danger', 'text-white'];
            }



            $details .= "<b>Status remarks:</b> " . $act->done_details . '<br>';
            $details .= "<b>Person responsible:</b> " . $act->assigned_to->name . '<br>';

            $ev['details'] = $details;
            $ev['administrator_id'] = $act->administrator_id;
            $ev['done_status'] = $act->done_status;
            $ev['done_details'] = $act->done_details;
            $ev['garden_id'] = $act->garden_id;
            $ev['activity_id'] = $act->id;
            $ev['id'] = count($events);
            $ev['person_responsible'] = $act->person_responsible;
            $ev['type'] = 'Scheduled activity';

            //$ev['textColor'] = 'red';

            $events[] = $ev;
        }


        return $events;
    }
}
