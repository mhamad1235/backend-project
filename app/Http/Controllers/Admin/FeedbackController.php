<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Feedback;
class FeedbackController extends Controller
{
     public function index(Request $request)
    {
        if ($request->ajax()) {
            $feedbacks = Feedback::with(['user', 'feedbackable'])
                ->when($request->search, function ($query, $search) {
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->where('comment', 'like', "%$search%");
                    })
                    ->orWhere('comment', 'like', "%$search%");
                }) ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            });

            return DataTables::of($feedbacks)
                ->addIndexColumn()
                ->addColumn('user', function ($feedback) {
                    return $feedback->user ? $feedback->user->name : 'Deleted User';
                })
                ->addColumn('feedbackable', function ($feedback) {
                    return $feedback->feedbackable ? $feedback->feedbackable->name : 'Deleted Item';
                })
                ->addColumn('rating', function ($feedback) {
                    return $this->renderRatingStars($feedback->rating);
                })
                ->addColumn('status', function ($feedback) {
                    return $this->renderStatusBadge($feedback->status);
                })
                ->addColumn('action', function ($feedback) {
                    return $this->renderActionButtons($feedback);
                })
                ->rawColumns(['rating', 'status', 'action'])
                ->toJson();
        }

        return view('admin.feedbacks.index');
    }

    public function updateStatus(Request $request, Feedback $feedback)
    {
        $request->validate([
            'status' => 'required|in:hide,visible'
        ]);

        $feedback->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    private function renderRatingStars($rating)
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $starClass = $i <= $rating ? 'text-warning' : 'text-muted';
            $stars .= "<i class='ri-star-fill $starClass'></i>";
        }
        return $stars;
    }

    private function renderStatusBadge($status)
    {
        $badgeClass = $status === 'visible' ? 'badge bg-success' : 'badge bg-secondary';
        $statusText = ucfirst($status);
        return "<span class='$badgeClass'>$statusText</span>";
    }

    private function renderActionButtons($feedback)
    {
        $updateUrl = route('admin.feedbacks.update-status', $feedback->id);
        $visibleClass = $feedback->status === 'visible' ? 'd-none' : '';
        $hiddenClass = $feedback->status === 'hide' ? 'd-none' : '';
        
        return "
        <div class='d-flex gap-2'>
            <button class='btn btn-sm btn-success make-visible $visibleClass' 
                    data-url='$updateUrl' data-status='visible'>
                <i class='ri-eye-line'></i>
            </button>
            <button class='btn btn-sm btn-secondary make-hidden $hiddenClass' 
                    data-url='$updateUrl' data-status='hide'>
                <i class='ri-eye-off-line'></i>
            </button>
            <button class='btn btn-sm btn-danger delete-feedback' 
                    data-id='{$feedback->id}'>
                <i class='ri-delete-bin-line'></i>
            </button>
        </div>";
    }
    public function destroy(Feedback $feedback)
{
    $feedback->delete();
    return response()->json(['success' => true, 'message' => 'Feedback deleted successfully']);
}
}
