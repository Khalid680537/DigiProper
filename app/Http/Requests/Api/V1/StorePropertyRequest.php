<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\StorePropertyRequest as WebStorePropertyRequest;

class StorePropertyRequest extends WebStorePropertyRequest
{
    // Reuses the web request's rules, prepareForValidation, and authorize().
    // Validation and authorization are identical between web and API.
}
