<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VectorSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteSearchController extends Controller
{
    protected VectorSearchService $searchService;

    public function __construct(VectorSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * AI-powered Semantic Search across notes.
     *
     * GET /api/notes/search?query=artificial+intelligence&limit=10
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->query('query', '');
        $limit = max(1, min(50, (int) $request->query('limit', 10)));

        if (empty(trim($query))) {
            return response()->json([
                'success' => false,
                'message' => 'Search query parameter "query" is required.',
                'data' => [],
            ], 400);
        }

        $results = $this->searchService->search($query, $limit);

        return response()->json([
            'success' => true,
            'query' => $query,
            'total_matches' => $results->count(),
            'data' => $results,
        ], 200);
    }
}
