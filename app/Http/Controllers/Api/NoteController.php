<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use App\Services\AiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display a paginated listing of notes.
     *
     * GET /api/notes?page=1&limit=10
     */
    public function index(Request $request): JsonResponse
    {
        $limit = max(1, min(100, (int) $request->query('limit', 10)));
        
        $notes = Note::orderBy('created_at', 'desc')->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $notes->items(),
            'meta' => [
                'current_page' => $notes->currentPage(),
                'limit' => $notes->perPage(),
                'total' => $notes->total(),
                'last_page' => $notes->lastPage(),
                'has_more' => $notes->hasMorePages(),
            ],
        ], 200);
    }

    /**
     * Store a newly created note in storage and generate its AI embedding vector.
     *
     * POST /api/notes
     */
    public function store(StoreNoteRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Generate embedding vector for the title + content
        $textToEmbed = $validated['title'] . ' ' . $validated['content'];
        $validated['embedding'] = $this->aiService->generateEmbedding($textToEmbed);

        $note = Note::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Note created successfully.',
            'data' => $note,
        ], 201);
    }

    /**
     * Display the specified note.
     *
     * GET /api/notes/{id}
     */
    public function show($id): JsonResponse
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => "Note with ID {$id} not found.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $note,
        ], 200);
    }

    /**
     * Update the specified note in storage.
     *
     * PUT/PATCH /api/notes/{id}
     */
    public function update(UpdateNoteRequest $request, $id): JsonResponse
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => "Note with ID {$id} not found.",
            ], 404);
        }

        $validated = $request->validated();

        // If title or content changed, regenerate embedding vector
        if (isset($validated['title']) || isset($validated['content'])) {
            $newTitle = $validated['title'] ?? $note->title;
            $newContent = $validated['content'] ?? $note->content;
            $validated['embedding'] = $this->aiService->generateEmbedding($newTitle . ' ' . $newContent);
            // Clear cached summary on content change so it can be re-summarized
            $validated['summary'] = null;
        }

        $note->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Note updated successfully.',
            'data' => $note->fresh(),
        ], 200);
    }

    /**
     * Remove the specified note from storage.
     *
     * DELETE /api/notes/{id}
     */
    public function destroy($id): JsonResponse
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => "Note with ID {$id} not found.",
            ], 404);
        }

        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Note deleted successfully.',
        ], 200);
    }
}
