<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Club;
use App\Models\Event;
use App\Models\MemberClub;
use Illuminate\Http\Request;
use App\Services\FileService;
use App\Models\FileCategory;

class AdminFileController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', File::class);

        $tab = $request->input('tab', 'events');

        $clubOptions = Club::whereHas('clubFiles')->orderBy('name')->pluck('name', 'club_id')->toArray();
        $eventOptions = Event::whereHas('eventFiles')->orderBy('title')->pluck('title', 'event_id')->toArray();
        $memberOptions = MemberClub::whereHas('memberClubFiles')
            ->with('member')
            ->whereNull('left_at')
            ->get()
            ->mapWithKeys(fn($mc) => [$mc->member_club_id => $mc->member?->full_name ?? '-'])
            ->toArray();

        $categoryOptions = FileCategory::orderBy('name')->pluck('name', 'file_category_id')->toArray();

        $files = match($tab) {
            'clubs' => File::whereHas('clubs')
                ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
                ->when($request->filled('category_id'), fn($q) => $q->whereHas('clubs', fn($q) => 
                    $q->where('club_files.file_category_id', $request->input('category_id'))
                ))
                ->when($request->filled('club_id'), fn($q) => $q->whereHas('clubs', fn($q) => 
                    $q->where('clubs.club_id', $request->input('club_id'))
                ))
                ->with(['clubs', 'uploadedByUser'])
                ->orderByDate()
                ->paginate(7)
                ->withQueryString(),

            'members' => File::whereHas('memberClubs')
                ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
                ->when($request->filled('category_id'), fn($q) => $q->whereHas('memberClubs', fn($q) => 
                    $q->where('member_club_files.file_category_id', $request->input('category_id'))
                ))
                ->when($request->filled('member_club_id'), fn($q) => $q->whereHas('memberClubs', fn($q) => 
                    $q->where('member_club.member_club_id', $request->input('member_club_id'))
                ))
                ->with(['memberClubs.member.user', 'memberClubs.club', 'uploadedByUser'])
                ->orderByDate()
                ->paginate(7)
                ->withQueryString(),

            default => File::whereHas('events')
                ->when($request->filled('search'), fn($q) => $q->search($request->input('search')))
                ->when($request->filled('category_id'), fn($q) => $q->whereHas('events', fn($q) => 
                    $q->where('event_files.file_category_id', $request->input('category_id'))
                ))
                ->when($request->filled('event_id'), fn($q) => $q->whereHas('events', fn($q) => 
                    $q->where('events.event_id', $request->input('event_id'))
                ))
                ->with(['events', 'uploadedByUser'])
                ->orderByDate()
                ->paginate(7)
                ->withQueryString(),
        };

        if ($request->ajax()) {
            return view('panel.admin.files._table', compact('files', 'tab'));
        }

        return view('panel.admin.files.index', compact(
            'files', 'tab', 'clubOptions', 'eventOptions', 'memberOptions', 'categoryOptions'
        ));
    }

    public function deleteFile(File $file)
    {
        $this->authorize('delete', $file);

        app(FileService::class)->deleteFile($file);

        return redirect()->back()->with('success', 'File deleted successfully.');
    }
}