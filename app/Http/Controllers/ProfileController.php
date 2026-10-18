<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $profile = Auth::user()->profile;
        return response()->json($profile, 200);
    }

    public function store(StoreProfileRequest $request)
    {
        $user_id = Auth::user()->id;
        $validatedData = $request->validated();
        $validatedData['user_id'] = $user_id;
        if ($request->hasFile('image')) {
           $path = $request->file('image')->store('my photo', 'public');
           $validatedData['image'] = $path;
        }
        $profiles = Profile::create($validatedData);
        return response()->json(['message' => 'Profile created successfully', 'profile' => $profiles], 201);
    }
    public function update(UpdateProfileRequest $request)
    {
        $user_id = Auth::user()->id;
        $profile = Profile::where('user_id', $user_id)->firstOrFail();
        $profile->update($request->validated());
        return response()->json($profile, 200);
    }
}
