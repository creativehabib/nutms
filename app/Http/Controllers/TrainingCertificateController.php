<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class TrainingCertificateController extends Controller
{
    public function __invoke(Training $training): Response
    {
        $participant = $training->participants()
            ->whereKey(auth()->id())
            ->wherePivot('status', 'Completed')
            ->wherePivotNotNull('certificate_number')
            ->firstOrFail();

        abort_unless($training->has_certificate, 404);

        return response()
            ->view('training.certificate', [
                'training' => $training,
                'participant' => $participant,
                'certificateNumber' => $participant->pivot->certificate_number,
                'completedAt' => Carbon::parse($participant->pivot->completed_at)->format('d F Y'),
            ])
            ->header('Content-Type', 'image/svg+xml; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="training-certificate-'.$training->id.'.svg"');
    }
}
