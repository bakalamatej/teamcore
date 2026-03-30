<?php

namespace App\Http\Controllers;

use App\Models\CoachEvaluation;
use App\Http\Requests\CoachEvaluationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Club;
use Illuminate\Database\QueryException;

class CoachEvaluationController extends Controller
{
    /**
     * Store evaluation from event page
     */
    public function storeFromEvent(CoachEvaluationRequest $request, Event $event)
    {
        $this->authorize('create', CoachEvaluation::class);

        $coachMemberId = Auth::user()?->member?->member_id;
        
        if (!$coachMemberId) {
            return redirect()->route('events.show', $event)
                ->with('error', __('You must be a member to create evaluations.'));
        }

        $evaluatedByMemberId = $request->input('evaluated_by_member_id');

        try {
            CoachEvaluation::create([
                'coach_member_id' => $coachMemberId,
                'evaluated_by_member_id' => $evaluatedByMemberId,
                'rating' => $request->input('rating'),
                'comment' => $request->input('comment') ?? null,
            ]);

            return redirect()->route('events.show', $event)
                ->with('success', __('Evaluation saved successfully.'));
        } catch (QueryException $e) {
            $error = $this->mapEvaluationTriggerError($e);
            
            if ($error !== null) {
                return redirect()->route('events.show', $event)->with('error', $error);
            }
            
            throw $e;
        }
    }

    public function storeFromClub(CoachEvaluationRequest $request, Club $club)
    {
        $this->authorize('create', CoachEvaluation::class);

        $evaluatedByMemberId = Auth::user()?->member?->member_id;
        if (!$evaluatedByMemberId) {
            return redirect()->route('clubs.show', $club)
                ->with('error', __('You must be a member to create evaluations.'));
        }

        try {
            CoachEvaluation::create([
                'coach_member_id' => $request->input('coach_member_id'),
                'evaluated_by_member_id' => $evaluatedByMemberId,
                'rating' => $request->input('rating'),
                'comment' => $request->input('comment') ?? null,
            ]);

            return redirect()->route('clubs.show', $club)
                ->with('success', __('Evaluation saved successfully.'));
        } catch (QueryException $e) {
            $error = $this->mapEvaluationTriggerError($e);
            if ($error !== null) {
                return redirect()->route('clubs.show', $club)->with('error', $error);
            }
            throw $e;
        }
    }

    /**
     * Map database trigger errors to user-friendly messages
     */
    private function mapEvaluationTriggerError(QueryException $exception): ?string
    {
        $message = strtoupper($exception->getMessage());
        $driverErrorCode = (int) ($exception->errorInfo[1] ?? 0);

        // Check if it's a trigger error (code 1644 or SQLSTATE[45000])
        if ($driverErrorCode !== 1644 && !str_contains($message, 'SQLSTATE[45000]')) {
            return null;
        }

        // Map specific trigger errors
        if (str_contains($message, 'NOT A COACH')) {
            return 'Only coaches can create evaluations.';
        }

        if (str_contains($message, 'COACH CANNOT EVALUATE THEMSELVES')) {
            return 'You cannot evaluate yourself.';
        }

        return 'Unable to save evaluation.';
    }
}