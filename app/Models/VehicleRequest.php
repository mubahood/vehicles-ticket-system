<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRequest extends Model
{
    use HasFactory;

    //belongs to applicant
    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    //belongs to vehicle
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    //boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model = self::do_prepare($model);
            return $model;
        });

        static::updating(function ($model) {
            $model = self::do_prepare($model);
            return $model;
        });
    }

    public static function do_prepare($model)
    {
        //IF type is Vehicle Request
        if ($model->type == 'Vehicle') {
            $vehicle = Vehicle::find($model->vehicle_id);
            if ($vehicle == null) {
                throw new \Exception("Vehicle not found");
            }
        }
        $applicant = User::find($model->applicant_id);
        if ($applicant == null) {
            throw new \Exception("Applicant not found");
        }

        if ($model->hod_status != 'Approved') {
            $model->gm_status = 'Pending';
            $model->security_exit_status = 'Pending';
            $model->security_return_status = 'Pending';
        }

        if ($model->gm_status != 'Approved') {
            $model->security_exit_status = 'Pending';
            $model->security_return_status = 'Pending';
        }

        if ($model->security_exit_status != 'Approved') {
            $model->security_return_status = 'Pending';
        }

        //status

        //actual_return_time NOT PENDDING
        if ($model->actual_return_time != null) {
            $model->status = 'Completed';
        }



        return $model;
    }

    //has many material items
    public function materialItems()
    {
        return $this->hasMany(MaterialItem::class);
    }

    //get title
    public function getTitle()
    {
        if ($this->type == 'Vehicle') {
            if ($this->vehicle) {
                return $this->vehicle->registration_number . ' - ' . $this->vehicle->brand . ' - ' . $this->vehicle->model . ' - ' . $this->vehicle->vehicle_type;
            } else {
                return 'N/A';
            }
        } else if ($this->type == 'Materials') {
            $materials = "";
            foreach ($this->materialItems as $index => $materialItem) {
                $materials .= $materialItem->type . ' - ' . $materialItem->quantity . ' ' . $materialItem->unit;
                if ($index < $this->materialItems->count() - 1) {
                    $materials .= ', ';
                }
            }
            return $materials;
        } else if ($this->type == 'Fuel') {
            return 'Fuel Request';
        } else {
            return 'N/A';
        }
    }

    //has many RequestHasDriver 
    public function drivers()
    {
        return $this->hasMany(RequestHasDriver::class, 'vehicle_request_id');
    } 
}
