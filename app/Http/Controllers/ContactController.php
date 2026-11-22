<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $user = Auth::user();

        $contact = $user->contacts()->create($request->validated());

        return new ContactResource($contact)->response()->setStatusCode(201);
    }

    public function get(Contact $contact): ContactResource
    {
        $user = Auth::user();

        $contact = $contact->where('user_id', $user->id)->first();

        if(!$contact) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 400));
        }

        return new ContactResource($contact);
    }

    public function update(Contact $contact, ContactUpdateRequest $request): JsonResponse
    {
        $user = Auth::user();

        $contact = $contact->where('user_id', $user->id)->first();

        if(!$contact) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ], 400));
        }

        $contact->update($request->validated());

        return new ContactResource($contact)->response()->setStatusCode(201);
        
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();
        
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
