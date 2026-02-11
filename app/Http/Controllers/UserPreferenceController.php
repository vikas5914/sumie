<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateImageProxyPreferenceRequest;
use Illuminate\Http\RedirectResponse;

class UserPreferenceController extends Controller
{
    public function updateImageProxy(UpdateImageProxyPreferenceRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'use_image_proxy' => $request->boolean('use_image_proxy'),
        ]);

        return back();
    }
}
