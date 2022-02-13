<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // basic bootstrapping
        $u = new \App\Models\User;
        $u->username = env("BOOT_USER");
        $u->password = Hash::make("boot");
        $u->save();

        $m = new \App\Models\Member;
        $m->membership = env("BOOT_USER");
        $m->firstname = "";
        $m->lastname = "";
        $m->email = "";
        $m->mobile = "";
        $m->department = "";
        $m->jobtype = "";
        $m->membertype = "Standard";
        $m->save();

        $r = new \App\Models\Role;
        $r->role = \App\Models\Role::ROLE_SUPERUSER;
        $r->member_id = $m->id;
        $r->save();
    }
}
