<?php

return [
    /*
     * An array of membership IDs which will not be automatically
     * removed if absent from a membership import, to allow staff
     * users access to the system.
     *
     * The default is to have none, with the STAFF_USERS environment
     * variable providing a comma-separated list.
     *
     * For safety, staff user membership IDs should not have a format
     * which could be used by an imported membership ID.
     *
     * There is currently no mechanism to add staff accounts other
     * than adding an extra line onto the membership file before it is
     * imported.
     */
    'staff' => explode(',', env('STAFF_USERS', '')),

    /*
     * The organisation type. Affects login advice and membership
     * import processing. Only one supported value for now. */
    'orgtype' => env('ORGANISATION_TYPE', 'UCUBranch'),
];
