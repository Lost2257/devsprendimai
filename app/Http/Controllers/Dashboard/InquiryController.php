<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Inertia\Inertia;

class InquiryController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Inquiries/Index', [
            'inquiries' => Inquiry::latest()->get()
        ]);
    }

    public function show(Inquiry $inquiry)
    {
        if (!$inquiry->is_read) {
            $inquiry->update([
                'is_read' => true
            ]);
        }

        return Inertia::render('Dashboard/Inquiries/Show', [
            'inquiry' => $inquiry
        ]);
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();

        return redirect('/dashboard/inquiries')
            ->with('success', 'Užklausa ištrinta.');
    }
}