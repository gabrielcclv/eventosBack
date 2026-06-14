<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    use ApiResponseTrait;

    public function becomeOrganizer(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->update(['is_organizer' => true]);

        return $this->successResponse($user, 'Rol de organizador activado con éxito.');
    }

    public function myTickets(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        
        $tickets = $user->events()->get();

        return $this->successResponse($tickets);
    }
}