<?php

namespace App\Http\Requests;

class UpdatePropertyRequest extends StorePropertyRequest
{
    // Identical validation rules as StorePropertyRequest. Extracted via inheritance
    // so a future divergence (e.g. name unique on create, optional on edit) is one
    // method override away.
}
