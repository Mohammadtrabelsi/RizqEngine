<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuotationMail;
use App\Models\Quotation;

class SendQuotationEmailController extends Controller
{
    public function __invoke(Quotation $quotation)
    {
        try {
            Mail::to($quotation->customer->customer_email)->send(new QuotationMail($quotation));

            $quotation->update([
                'status' => 'Sent',
            ]);

            session()->flash('success', trans('quotation.email-sent'));

        } catch (\Exception $exception) {
            Log::error($exception);
            session()->flash('error', trans('quotation.failed-to-send-email'));
        }

        return back();
    }
}
