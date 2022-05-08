<?php
namespace App\Models;

interface Participatory
{
    /* Returns the short name/title of the participatory event */
    public function shortDesc();
    /* Returns yes/no/help/wait/- as a participation status */
    public function participation(Member $member);
}
