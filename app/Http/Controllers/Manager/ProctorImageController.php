<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ProctorImage;
use Illuminate\Http\Request;

class ProctorImageController extends Controller
{
    const PER_PAGE = 60;

    public function __construct()
    {
        $this->middleware('permission:show proctor images')->only(['index', 'loadMore']);
        $this->middleware('permission:delete proctor images')->only(['destroy', 'bulkDestroy']);
    }

    public function index(Request $request)
    {
        $title = t('Proctor Image Browser');

        $firstDate = ProctorImage::selectRaw('MIN(DATE(created_at)) as d')->value('d');
        $lastDate  = ProctorImage::selectRaw('MAX(DATE(created_at)) as d')->value('d');

        $date = $request->get('date', $lastDate);
        if (!$date || !strtotime($date)) {
            $date = $lastDate;
        }

        $prevDate        = null;
        $nextDate        = null;
        $totalCount      = 0;
        $selfieCount     = 0;
        $screenshotCount = 0;
        $images          = collect();
        $hasMore         = false;
        $nextPage        = 2;

        if ($date) {
            $prevDate = ProctorImage::whereRaw('DATE(created_at) < ?', [$date])
                ->selectRaw('MAX(DATE(created_at)) as d')->value('d');
            $nextDate = ProctorImage::whereRaw('DATE(created_at) > ?', [$date])
                ->selectRaw('MIN(DATE(created_at)) as d')->value('d');

            $totalCount      = ProctorImage::whereDate('created_at', $date)->count();
            $selfieCount     = ProctorImage::whereDate('created_at', $date)->where('type', 'selfie')->count();
            $screenshotCount = ProctorImage::whereDate('created_at', $date)->where('type', 'screenshot')->count();

            $images = ProctorImage::whereDate('created_at', $date)
                ->with(['studentTerm.student'])
                ->orderBy('student_term_id')
                ->orderBy('capture_minute')
                ->orderBy('created_at')
                ->take(self::PER_PAGE)
                ->get();

            $hasMore = $totalCount > self::PER_PAGE;
        }

        return view('manager.proctor_images.index', compact(
            'title', 'images', 'date', 'prevDate', 'nextDate', 'firstDate', 'lastDate',
            'totalCount', 'selfieCount', 'screenshotCount', 'hasMore', 'nextPage'
        ));
    }

    public function loadMore(Request $request)
    {
        $date    = $request->get('date');
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = self::PER_PAGE;

        if (!$date || !strtotime($date)) {
            return response()->json(['images' => [], 'has_more' => false]);
        }

        $total = ProctorImage::whereDate('created_at', $date)->count();

        $images = ProctorImage::whereDate('created_at', $date)
            ->with(['studentTerm.student'])
            ->orderBy('student_term_id')
            ->orderBy('capture_minute')
            ->orderBy('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($img) {
                $student = optional(optional($img->studentTerm)->student);
                return [
                    'id'              => $img->id,
                    'file_path'       => asset($img->file_path),
                    'type'            => $img->type,
                    'capture_minute'  => $img->capture_minute,
                    'student_name'    => $student->name ?? null,
                    'student_term_id' => $img->student_term_id,
                ];
            });

        return response()->json([
            'images'    => $images,
            'has_more'  => ($page * $perPage) < $total,
            'next_page' => $page + 1,
            'total'     => $total,
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->get('ids', []);

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => t('No images selected')], 422);
        }

        $images  = ProctorImage::whereIn('id', $ids)->get();
        $deleted = 0;

        foreach ($images as $image) {
            $image->forceDelete();
            $deleted++;
        }

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
            'message' => t('Images deleted successfully'),
        ]);
    }

    public function destroy(ProctorImage $proctorImage)
    {
        $proctorImage->forceDelete();

        return response()->json([
            'success' => true,
            'message' => t('Image deleted successfully'),
        ]);
    }
}
