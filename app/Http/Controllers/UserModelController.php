<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserModelController extends Controller
{
    /**
     * Update the user's preferred model.
     */
    public function update(Request $request)
    {
        $request->validate([
            'last_model' => 'required|string',
        ]);

        auth()->user()->update([
            'last_model' => $request->last_model,
        ]);

        return back();
    }
}
