<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Services\AiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteSummaryController extends Controller
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Generate or fetch AI summary for a note.
     *
     * POST /api/notes/{id}/summary
     */
    public function summary(Request $request, $id): JsonResponse
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => "Note with ID {$id} not found.",
            ], 404);
        }

        $force = $request->boolean('force', false);

        // Use cached summary if available and force is false
        if (!empty($note->summary) && !$force) {
            return response()->json([
                'success' => true,
                'cached' => true,
                'summary' => $note->summary,
                'note_id' => $note->id,
                'title' => $note->title,
            ], 200);
        }

        // Generate summary using AI Service
        $summaryText = $this->aiService->generateSummary($note->content);

        // Persist summary in note record
        $note->summary = $summaryText;
        $note->save();

        return response()->json([
            'success' => true,
            'cached' => false,
            'summary' => $summaryText,
            'note_id' => $note->id,
            'title' => $note->title,
        ], 200);
    }
}
