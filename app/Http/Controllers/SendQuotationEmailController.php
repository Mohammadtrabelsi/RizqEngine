<?php

namespace App\Http\Controllers;

use App\Mail\QuotationMail;
use App\Models\Quotation;
use App\Services\QuotationService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendQuotationEmailController extends Controller
{
    public function __construct(private readonly QuotationService $quotations) {}

    public function __invoke(Quotation $quotation)
    {
        try {
            Mail::to($this->quotations->recipientEmail($quotation))->send(new QuotationMail($quotation));

            $this->quotations->markSent($quotation);

            session()->flash('success', trans('quotation.email-sent'));

        } catch (\Exception $exception) {
            Log::error($exception);
            session()->flash('error', trans('quotation.failed-to-send-email'));
        }

        return back();
    }
}
