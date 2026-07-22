<?php

return [
    /*
     * Comma-separated list of IPs or CIDR ranges (e.g. 10.127.127.0/24)
     * permitted to call the member-check API. An empty value blocks all
     * requests.
     */
    'ip_whitelist' => array_values(
        array_filter(
            array_map('trim', explode(',', env('API_IP_WHITELIST', '')))
        )
    ),
];
