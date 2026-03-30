<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoachFileController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', File::class);

        $membership = Auth::user()?->activeMembership();
        abort_if(!$membership, 403);

        $categoryOptions = FileCategory::orderBy('name')
            ->pluck('name', 'file_category_id')
            ->toArray();

        $files = File::whereHas('clubs', fn($q) =>
                $q->where('clubs.club_id', $membership->club_id)
            )
            ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
            ->when($request->filled('category_id'), fn($q) => $q->whereHas('clubs', fn($q) =>
                $q->where('club_files.file_category_id', $request->input('category_id'))
            ))
            ->with(['clubs', 'uploadedByUser'])
            ->orderByDate()
            ->paginate(15)
            ->withQueryString();

        if ($request->ajax()) {
            return view('panel.coach.files._table', compact('files'));
        }

        return view('panel.coach.files.index', compact('files', 'categoryOptions'));
    }
}