<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database Connection to use
    |--------------------------------------------------------------------------
    |
    | Set the default database connection to use for the repositories,
    | when set to default, it uses whatever connection you specified in your laravel db config.
    |
    */
    'database' => 'redis',

    /*
    |--------------------------------------------------------------------------
    | Output Token Type
    |--------------------------------------------------------------------------
    |
    | This will tell the authorization server the output format for the access token
    | and will tell the resource server how to parse the access token used.
    |
    | Default value is League\OAuth2\Server\TokenType\Bearer
    |
    */
    'token_type' => 'League\OAuth2\Server\TokenType\Bearer',

    /*
    |--------------------------------------------------------------------------
    | HTTP Header Only
    |--------------------------------------------------------------------------
    |
    | This will tell the resource server where to check for the access_token.
    | By default it checks both the query string and the http headers
    |
    */
    'http_headers_only' => false,
];
