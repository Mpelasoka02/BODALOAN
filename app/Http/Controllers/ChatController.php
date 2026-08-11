<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Message;
use App\Models\User;
use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $conversations = collect();

        if ($user->isAdmin()) {
            $loans = Loan::with(['owner', 'driver', 'motorcycle'])
                ->whereIn('status', ['pending', 'active', 'overdue'])
                ->latest()
                ->get();
        } elseif ($user->isOwner()) {
            $loans = Loan::where('owner_id', $user->id)
                ->with(['driver', 'motorcycle'])
                ->latest()
                ->get();
        } else {
            $loans = Loan::where('driver_id', $user->id)
                ->with(['owner', 'motorcycle'])
                ->latest()
                ->get();
        }

        foreach ($loans as $loan) {
            $lastMessage = Message::where('loan_id', $loan->id)
                ->with('sender')
                ->latest()
                ->first();

            $unreadCount = Message::where('loan_id', $loan->id)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();

            $otherParticipants = collect();
            if ($loan->owner && $loan->owner_id !== $user->id) $otherParticipants->push($loan->owner);
            if ($loan->driver && $loan->driver_id !== $user->id) $otherParticipants->push($loan->driver);

            $conversations->push([
                'type' => 'loan',
                'conversation_id' => 'loan-' . $loan->id,
                'title' => $loan->motorcycle->make . ' ' . $loan->motorcycle->model,
                'subtitle' => $loan->motorcycle->plate_number,
                'loan' => $loan,
                'other_participants' => $otherParticipants,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
            ]);
        }

        $directUserIds = Message::where(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->whereNotNull('receiver_id');
        })->orWhere(function ($q) use ($user) {
            $q->where('receiver_id', $user->id)->whereNotNull('receiver_id');
        })
        ->whereNull('loan_id')
        ->pluck('sender_id')
        ->merge(Message::where('receiver_id', $user->id)->whereNull('loan_id')->pluck('sender_id'))
        ->unique()
        ->values();

        foreach ($directUserIds as $otherId) {
            if ($otherId == $user->id) continue;

            $otherUser = User::find($otherId);
            if (!$otherUser) continue;

            $lastMessage = Message::whereNull('loan_id')
                ->where(function ($q) use ($user, $otherId) {
                    $q->where(fn($q2) => $q2->where('sender_id', $user->id)->where('receiver_id', $otherId));
                    $q->orWhere(fn($q2) => $q2->where('sender_id', $otherId)->where('receiver_id', $user->id));
                })
                ->with('sender')
                ->latest()
                ->first();

            $unreadCount = Message::whereNull('loan_id')
                ->where('sender_id', $otherId)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count();

            $conversations->push([
                'type' => 'direct',
                'conversation_id' => 'direct-' . $otherId,
                'title' => $otherUser->name,
                'subtitle' => ucfirst($otherUser->role),
                'other_user' => $otherUser,
                'other_participants' => collect([$otherUser]),
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
            ]);
        }

        $conversations = $conversations->sortByDesc(function ($c) {
            return $c['last_message'] ? $c['last_message']->created_at->timestamp : 0;
        })->values();

        $availableContacts = $this->getAvailableContacts($user);

        return view('chat.index', compact('conversations', 'availableContacts'));
    }

    public function showConversation(string $conversationId)
    {
        $user = Auth::user();

        if (str_starts_with($conversationId, 'loan-')) {
            return $this->showLoanConversation($user, $conversationId);
        } elseif (str_starts_with($conversationId, 'direct-')) {
            return $this->showDirectConversation($user, $conversationId);
        }

        abort(404);
    }

    private function showLoanConversation($user, $conversationId)
    {
        $loanId = (int) str_replace('loan-', '', $conversationId);
        $loan = Loan::with(['owner', 'driver', 'motorcycle'])->findOrFail($loanId);

        if (!$user->isAdmin() && $loan->owner_id !== $user->id && $loan->driver_id !== $user->id) {
            abort(403);
        }

        Message::where('loan_id', $loan->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where('loan_id', $loan->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $participants = collect();
        if ($loan->owner) $participants->push($loan->owner);
        if ($loan->driver) $participants->push($loan->driver);
        $admin = User::where('role', 'admin')->first();
        if ($admin) $participants->push($admin);

        return view('chat.show', [
            'conversationId' => $conversationId,
            'chatType' => 'loan',
            'loan' => $loan,
            'otherUser' => null,
            'participants' => $participants,
            'messages' => $messages,
        ]);
    }

    private function showDirectConversation($user, $conversationId)
    {
        $otherId = (int) str_replace('direct-', '', $conversationId);
        $otherUser = User::findOrFail($otherId);

        Message::whereNull('loan_id')
            ->where('sender_id', $otherId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::whereNull('loan_id')
            ->where(function ($q) use ($user, $otherId) {
                $q->where(fn($q2) => $q2->where('sender_id', $user->id)->where('receiver_id', $otherId));
                $q->orWhere(fn($q2) => $q2->where('sender_id', $otherId)->where('receiver_id', $user->id));
            })
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $participants = collect([$user, $otherUser]);

        return view('chat.show', [
            'conversationId' => $conversationId,
            'chatType' => 'direct',
            'loan' => null,
            'otherUser' => $otherUser,
            'participants' => $participants,
            'messages' => $messages,
        ]);
    }

    public function sendToConversation(Request $request, string $conversationId)
    {
        $user = Auth::user();

        if (str_starts_with($conversationId, 'loan-')) {
            return $this->sendLoanMessage($request, $user, $conversationId);
        } elseif (str_starts_with($conversationId, 'direct-')) {
            return $this->sendDirectMessage($request, $user, $conversationId);
        }

        abort(404);
    }

    private function sendLoanMessage(Request $request, $user, $conversationId)
    {
        $loanId = (int) str_replace('loan-', '', $conversationId);
        $loan = Loan::findOrFail($loanId);

        if (!$user->isAdmin() && $loan->owner_id !== $user->id && $loan->driver_id !== $user->id) {
            abort(403);
        }

        $request->validate(['body' => 'required|string|max:2000']);

        $message = Message::create([
            'sender_id' => $user->id,
            'loan_id' => $loan->id,
            'body' => $request->body,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $message->id,
                'body' => $message->body,
                'sender_name' => $user->name,
                'sender_id' => $user->id,
                'is_mine' => true,
                'created_at' => $message->created_at->format('g:i A'),
            ]);
        }

        return redirect()->route('chat.show.conversation', $conversationId);
    }

    private function sendDirectMessage(Request $request, $user, $conversationId)
    {
        $otherId = (int) str_replace('direct-', '', $conversationId);

        if ($otherId == $user->id) abort(400);

        if (!$user->isAdmin() && !$this->canDirectMessage($user, $otherId)) {
            abort(403);
        }

        $request->validate(['body' => 'required|string|max:2000']);

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $otherId,
            'body' => $request->body,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $message->id,
                'body' => $message->body,
                'sender_name' => $user->name,
                'sender_id' => $user->id,
                'is_mine' => true,
                'created_at' => $message->created_at->format('g:i A'),
            ]);
        }

        return redirect()->route('chat.show.conversation', $conversationId);
    }

    public function fetchConversation(Request $request, string $conversationId)
    {
        $user = Auth::user();

        if (str_starts_with($conversationId, 'loan-')) {
            return $this->fetchLoanConversation($request, $user, $conversationId);
        } elseif (str_starts_with($conversationId, 'direct-')) {
            return $this->fetchDirectConversation($request, $user, $conversationId);
        }

        abort(404);
    }

    private function fetchLoanConversation(Request $request, $user, $conversationId)
    {
        $loanId = (int) str_replace('loan-', '', $conversationId);

        Message::where('loan_id', $loanId)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $lastId = $request->get('last_id', 0);

        $messages = Message::where('loan_id', $loanId)
            ->where('id', '>', $lastId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages->map(fn($m) => [
            'id' => $m->id,
            'body' => $m->body,
            'sender_id' => $m->sender_id,
            'sender_name' => $m->sender->name,
            'is_mine' => $m->sender_id === $user->id,
            'created_at' => $m->created_at->format('g:i A'),
        ]));
    }

    private function fetchDirectConversation(Request $request, $user, $conversationId)
    {
        $otherId = (int) str_replace('direct-', '', $conversationId);

        Message::whereNull('loan_id')
            ->where('sender_id', $otherId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $lastId = $request->get('last_id', 0);

        $messages = Message::whereNull('loan_id')
            ->where('id', '>', $lastId)
            ->where(function ($q) use ($user, $otherId) {
                $q->where(fn($q2) => $q2->where('sender_id', $user->id)->where('receiver_id', $otherId));
                $q->orWhere(fn($q2) => $q2->where('sender_id', $otherId)->where('receiver_id', $user->id));
            })
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages->map(fn($m) => [
            'id' => $m->id,
            'body' => $m->body,
            'sender_id' => $m->sender_id,
            'sender_name' => $m->sender->name,
            'is_mine' => $m->sender_id === $user->id,
            'created_at' => $m->created_at->format('g:i A'),
        ]));
    }

    public function startDirect(int $userId)
    {
        $user = Auth::user();

        if ($userId == $user->id) abort(400);

        if (!$user->isAdmin() && !$this->canDirectMessage($user, $userId)) {
            abort(403);
        }

        return redirect()->route('chat.show.conversation', 'direct-' . $userId);
    }

    private function canDirectMessage($user, $otherId): bool
    {
        $other = User::find($otherId);
        if (!$other) return false;

        if ($user->isDriver()) {
            if ($other->isAdmin()) return true;
            if ($other->isOwner()) {
                return Loan::where('driver_id', $user->id)->where('owner_id', $otherId)->exists()
                    || \App\Models\Application::where('driver_id', $user->id)
                        ->whereHas('motorcycle', fn($q) => $q->where('owner_id', $otherId))
                        ->exists()
                    || Motorcycle::where('owner_id', $otherId)->where('verification_status', 'verified')->exists();
            }
        }

        if ($user->isOwner()) {
            if ($other->isAdmin()) return true;
            if ($other->isDriver()) {
                return Loan::where('owner_id', $user->id)->where('driver_id', $otherId)->exists()
                    || \App\Models\Application::where('driver_id', $otherId)
                        ->whereHas('motorcycle', fn($q) => $q->where('owner_id', $user->id))
                        ->exists();
            }
        }

        return false;
    }

    private function getAvailableContacts($user): array
    {
        $contacts = ['admin' => collect(), 'owners' => collect(), 'drivers' => collect()];

        if ($user->isAdmin()) {
            $contacts['owners'] = User::where('role', 'owner')->latest()->get();
            $contacts['drivers'] = User::where('role', 'driver')->latest()->get();
        } elseif ($user->isOwner()) {
            $contacts['admin'] = User::where('role', 'admin')->get();
            $contacts['drivers'] = User::where('role', 'driver')
                ->where(function ($q) use ($user) {
                    $q->whereHas('loans', fn($lq) => $lq->where('owner_id', $user->id))
                      ->orWhereHas('sentMessages') // fallback: anyone they've chatted with
                      ->orWhereHas('receivedMessages');
                })
                ->latest()->get();
        } elseif ($user->isDriver()) {
            $contacts['admin'] = User::where('role', 'admin')->get();
            $contacts['owners'] = User::where('role', 'owner')
                ->where(function ($q) use ($user) {
                    $q->whereHas('motorcycles', function ($mq) use ($user) {
                        $mq->where('verification_status', 'verified');
                    })
                    ->orWhereHas('ownerLoans', fn($lq) => $lq->where('driver_id', $user->id))
                    ->orWhereHas('motorcycles', function ($mq) use ($user) {
                        $mq->whereHas('applications', fn($aq) => $aq->where('driver_id', $user->id));
                    });
                })
                ->latest()->get();
        }

        return $contacts;
    }
}
