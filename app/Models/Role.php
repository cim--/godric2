<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /* Superuser: set up roles, set up campaigns, do member imports, run reports */
    public const ROLE_SUPERUSER = "superuser";
    /* Rep: run reports and charts */
    public const ROLE_REP = "rep";
    /* Comms: run reports */
    public const ROLE_COMMS = "comms";
    
    
    public function member()
    {
        return $this->belongsTo(Member::class);
    }


    public static function roleTypes()
    {
        return [
            self::ROLE_COMMS => "Communications",
            self::ROLE_REP => "Representative",
            self::ROLE_SUPERUSER => "Super-user",
        ];
    }

    public static function roleFields()
    {
        return [
            "" => "Unrestricted",
            "department" => "Department",
            "jobtype" => "Job Type",
            "membertype" => "Member Type"
        ];
    }

}
