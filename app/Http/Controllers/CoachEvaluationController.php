<?php

namespace App\Http\Controllers;

use App\Models\CoachEvaluation;
use App\Http\Requests\CoachEvaluationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Illuminate\Database\QueryException;

class CoachEvaluationController extends Controller
{
    public function storeFromEvent(CoachEvaluationRequest $request, Event $event, int $memberClubId)
    {
        $this->authorize('create', CoachEvaluation::class);

        $request->merge(['coach_member_club_id' => $memberClubId]);

        $data = $request->validated();

        try {
            CoachEvaluation::create([
                'coach_member_club_id' => $memberClubId,
                'evaluated_by_member_id' => Auth::user()->member?->member_id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);
            
            return redirect()->route('events.show', $event)->with('success', __('Evaluation saved successfully.'));
        } catch (QueryException $e) {
            $error = $this->mapEvaluationTriggerError($e);

            if ($error !== null) {
                return redirect()->route('events.show', $event)->with('error', $error);
            }

            throw $e;
        }
    }

    private function mapEvaluationTriggerError(QueryException $exception): ?string
    {
        $message = strtoupper($exception->getMessage());
        $driverErrorCode = (int) ($exception->errorInfo[1] ?? 0);

        if ($driverErrorCode !== 1644 && !str_contains($message, 'SQLSTATE[45000]')) {
            return null;
        }

        if (str_contains($message, 'COACH CANNOT EVALUATE THEMSELVES')) {
            return 'You cannot evaluate yourself.';
        }

        return 'Unable to save evaluation.';
    }
}
