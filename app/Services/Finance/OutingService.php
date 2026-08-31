<?php

namespace App\Services\Finance;

use App\Models\Outing;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Owns persistence for outings ("bons de sortie"), including reference
 * generation and the automatic voucher document that is (re)generated and
 * stored on the public disk whenever an outing is created or updated.
 */
class OutingService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Outing::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('location', 'like', $term)
                    ->orWhere('purpose', 'like', $term);
            })
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): Outing
    {
        $data['reference'] = $this->nextReference(Carbon::parse($data['date']));
        $outing = Outing::create($data);
        $this->generateVoucher($outing);

        return $outing;
    }

    /** @param array<string, mixed> $data */
    public function update(int $id, array $data): Outing
    {
        $outing = Outing::findOrFail($id);
        $outing->update($data);
        $this->generateVoucher($outing);

        return $outing;
    }

    public function delete(int $id): void
    {
        $outing = Outing::findOrFail($id);

        if ($outing->voucher_path) {
            Storage::disk('public')->delete($outing->voucher_path);
        }

        $outing->delete();
    }

    /**
     * Build the next sequential reference for the outing's year:
     * BS-YYYY-00001, BS-YYYY-00002, …
     */
    public function nextReference(Carbon $date): string
    {
        $year = $date->year;

        $count = Outing::query()
            ->whereYear('date', $year)
            ->where('reference', 'like', "BS-{$year}-%")
            ->count();

        return sprintf('BS-%d-%05d', $year, $count + 1);
    }

    /**
     * Render the outing voucher to a PDF and persist it on the public disk,
     * replacing any previously generated file.
     */
    public function generateVoucher(Outing $outing): string
    {
        if ($outing->voucher_path) {
            Storage::disk('public')->delete($outing->voucher_path);
        }

        $pdf = PDF::loadView('finance.voucher', ['outing' => $outing->fresh()]);

        $path = "vouchers/{$outing->reference}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $outing->forceFill(['voucher_path' => $path])->saveQuietly();

        return $path;
    }
}
