<?php

namespace App\Http\Controllers;

use App\Domain\Interfaces\Services\UserSearchServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserSearchController extends Controller
{
    public function __construct(
        private UserSearchServiceInterface $userSearchService
    ) {}

    /**
     * API endpoint for dropdown search (AJAX)
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $users = $this->userSearchService->searchUsers($query, 5);

        return response()->json(['data' => $users]);
    }

    /**
     * Web endpoint for full search results page
     */
    public function searchPage(Request $request): Response
    {
        $query = $request->get('q', '');
        $users = [];

        if (strlen($query) >= 2) {
            $users = $this->userSearchService->searchUsersWithPagination($query, 20);
        }

        return Inertia::render('SearchUsers', [
            'query' => $query,
            'users' => $users
        ]);
    }
}
