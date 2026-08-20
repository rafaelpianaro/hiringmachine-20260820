<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Used to sign the JWT. If the secret is not set, a random key will be
    | generated and used for the subsequent request.
    |
    */

    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication TTL
    |--------------------------------------------------------------------------
    |
    | The token lifetime in minutes. Set to null to allow indefinite tokens.
    |
    */

    'ttl' => env('JWT_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | JWT Refresh TTL
    |--------------------------------------------------------------------------
    |
    | The refresh token lifetime in minutes. Set to null to disable refresh
    | tokens.
    |
    */

    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),

    /*
    |--------------------------------------------------------------------------
    | JWT Algorithm
    |--------------------------------------------------------------------------
    |
    | The algorithm used to sign the JWT. Supported algorithms: HS256, HS384,
    | HS512, RS256, RS384, RS512, ES256, ES384, ES512.
    |
    */

    'algorithm' => env('JWT_ALGORITHM', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | JWT Header
    |--------------------------------------------------------------------------
    |
    | The JWT header type. This is typically "JWT" for JSON Web Tokens.
    |
    */

    'header' => 'Authorization',

    /*
    |--------------------------------------------------------------------------
    | JWT Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix for the JWT token. If set, the token will be prefixed with
    | this string.
    |
    */

    'prefix' => env('JWT_PREFIX', 'Bearer'),

    /*
    |--------------------------------------------------------------------------
    | JWT Claims Issuer
    |--------------------------------------------------------------------------
    |
    | The issuer claim for the JWT. This identifies the principal that issued
    | the JWT.
    |
    */

    'claims_claims' => [],

    /*
    |--------------------------------------------------------------------------
    | JWT Subject Claim
    |--------------------------------------------------------------------------
    |
    | The subject claim for the JWT. This identifies the principal that is the
    | subject of the JWT.
    |
    */

    'subject_claim' => 'sub',

    /*
    |--------------------------------------------------------------------------
    | JWT Blacklist Enabled
    |--------------------------------------------------------------------------
    |
    | When enabled, tokens will be blacklisted when the user logs out or when
    | the token is refreshed.
    |
    */

    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | JWT Blacklist Grace Period
    |--------------------------------------------------------------------------
    |
    | The grace period in seconds to allow after the token expires before
    | blacklisting the token.
    |
    */

    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    /*
    |--------------------------------------------------------------------------
    | JWT Providers
    |--------------------------------------------------------------------------
    |
    | The providers responsible for creating and parsing JWTs.
    |
    */

    'algo' => env('JWT_ALGO', env('JWT_ALGORITHM', 'HS256')),

    'providers' => [
        'jwt' => Tymon\JWTAuth\Providers\JWT\Lcobucci::class,
        'auth' => Tymon\JWTAuth\Providers\Auth\Illuminate::class,
        'storage' => Tymon\JWTAuth\Providers\Storage\Illuminate::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | JWT Factories
    |--------------------------------------------------------------------------
    |
    | The factories responsible for creating and parsing JWTs.
    |
    */

    'factories' => [
        'jwt' => Tymon\JWTAuth\Factories\JWTFactory::class,
    ],

];
