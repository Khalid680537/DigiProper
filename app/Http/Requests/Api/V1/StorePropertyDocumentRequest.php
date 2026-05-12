<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\StorePropertyDocumentRequest as WebStorePropertyDocumentRequest;

class StorePropertyDocumentRequest extends WebStorePropertyDocumentRequest
{
    // Reuses the web request's rules and authorize().
}
