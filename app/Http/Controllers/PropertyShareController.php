<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateShareVisibilityRequest;
use App\Models\Property;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PropertyShareController extends Controller
{
    /**
     * Common QR sizes we cache per token. Used to clear cache on rotate/disable.
     *
     * @var int[]
     */
    protected const CACHED_SIZES = [36, 44, 96, 140, 180, 512];

    public function rotate(Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $previousToken = $property->share_token;

        $property->share_token = Str::random(32);
        $property->save();

        $this->forgetQrCache($previousToken);

        return Redirect::route('properties.show', $property)->with('status', 'share-rotated');
    }

    public function disable(Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $previousToken = $property->share_token;

        $property->share_token = null;
        $property->save();

        $this->forgetQrCache($previousToken);

        return Redirect::route('properties.show', $property)->with('status', 'share-disabled');
    }

    public function enable(Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        if ($property->share_token === null) {
            $property->share_token = Str::random(32);
            $property->save();
        }

        return Redirect::route('properties.show', $property)->with('status', 'share-enabled');
    }

    public function updateVisibility(UpdateShareVisibilityRequest $request, Property $property): RedirectResponse
    {
        $property->update($request->validated());

        return Redirect::route('properties.show', $property)->with('status', 'share-visibility-updated');
    }

    public function show(string $token): View
    {
        $property = $this->resolveSharedProperty($token);

        return view('properties.share.show', [
            'property' => $property,
        ]);
    }

    public function qrSvg(string $token): Response
    {
        $property = $this->resolveSharedProperty($token);

        return response($property->qrSvg(512), 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
            'Content-Disposition' => 'inline; filename="property-'.$property->id.'-qr.svg"',
        ]);
    }

    public function qrPng(string $token): Response
    {
        $property = $this->resolveSharedProperty($token);

        $result = (new Builder(
            writer: new PngWriter,
            data: route('properties.share.show', $property->share_token),
            size: 512,
            margin: 16,
        ))->build();

        return response($result->getString(), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600',
            'Content-Disposition' => 'attachment; filename="property-'.$property->id.'-qr.png"',
        ]);
    }

    /**
     * Look up a shared property by raw token, bypassing the owner global scope
     * but excluding soft-deleted rows. 404s on any miss.
     */
    protected function resolveSharedProperty(string $token): Property
    {
        return Property::withoutGlobalScopes(['owner'])
            ->where('share_token', $token)
            ->with(['primaryPhoto', 'photos', 'contacts'])
            ->firstOrFail();
    }

    protected function forgetQrCache(?string $token): void
    {
        if ($token === null) {
            return;
        }

        foreach (self::CACHED_SIZES as $size) {
            Cache::forget(Property::qrCacheKey($token, $size));
        }
    }
}
