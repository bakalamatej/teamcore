<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberFileController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', File::class);

        $membership = Auth::user()?->activeMembership();
        abort_if(!$membership, 403);

        $tab = $request->input('tab', 'my');

        $categoryOptions = FileCategory::orderBy('name')
            ->pluck('name', 'file_category_id')
            ->toArray();

        $files = match($tab) {
            'uploaded' => File::where('uploaded_by_user_id', Auth::user()->user_id)
                ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
                ->when($request->filled('category_id'), fn($q) => $q->whereHas('memberClubs', fn($q) =>
                    $q->where('member_club_files.file_category_id', $request->input('category_id'))
                ))
                ->with(['memberClubs.club', 'clubs', 'events', 'uploadedByUser'])
                ->orderByDate()
                ->paginate(15)
                ->withQueryString(),
            default => File::whereHas('memberClubs', fn($q) =>
                    $q->where('member_club.member_club_id', $membership->member_club_id)
                )
                ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
                ->when($request->filled('category_id'), fn($q) => $q->whereHas('memberClubs', fn($q) =>
                    $q->where('member_club_files.file_category_id', $request->input('category_id'))
                ))
                ->with(['memberClubs.club', 'clubs', 'uploadedByUser'])
                ->orderByDate()
                ->paginate(15)
                ->withQueryString(),
        };

        if ($request->ajax()) {
            return view('panel.files._table', compact('files', 'tab'));
        }

        return view('panel.files.index', compact('files', 'categoryOptions', 'tab'));
    }
}