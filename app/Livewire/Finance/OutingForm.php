<?php

namespace App\Livewire\Finance;

use App\Models\Outing;
use App\Services\Finance\OutingService;
use Livewire\Component;

class OutingForm extends Component
{
    public ?int $outingId = null;

    public string $date;

    public ?string $location = null;

    public ?string $purpose = null;

    public string $participantsText = '';

    public string $food = '0';

    public string $gas = '0';

    public string $water = '0';

    public string $transport = '0';

    public string $misc = '0';

    public ?string $note = null;

    public function mount(?Outing $outing = null): void
    {
        if ($outing && $outing->exists) {
            $this->outingId = $outing->id;
            $this->date = $outing->date->format('Y-m-d');
            $this->location = $outing->location;
            $this->purpose = $outing->purpose;
            $this->participantsText = implode(', ', $outing->participants ?? []);
            $this->food = (string) $outing->food;
            $this->gas = (string) $outing->gas;
            $this->water = (string) $outing->water;
            $this->transport = (string) $outing->transport;
            $this->misc = (string) $outing->misc;
            $this->note = $outing->note;

            return;
        }

        $this->date = now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'participantsText' => 'nullable|string|max:2000',
            'food' => 'required|numeric|min:0',
            'gas' => 'required|numeric|min:0',
            'water' => 'required|numeric|min:0',
            'transport' => 'required|numeric|min:0',
            'misc' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:2000',
        ];
    }

    public function getTotalProperty(): float
    {
        return (float) $this->food + (float) $this->gas + (float) $this->water
            + (float) $this->transport + (float) $this->misc;
    }

    public function save(OutingService $outings)
    {
        $validated = $this->validate();

        $participants = collect(explode(',', $this->participantsText))
            ->map(fn ($p) => trim($p))
            ->filter()
            ->values()
            ->all();

        $data = [
            'date' => $validated['date'],
            'location' => $validated['location'],
            'purpose' => $validated['purpose'],
            'participants' => $participants,
            'food' => $validated['food'],
            'gas' => $validated['gas'],
            'water' => $validated['water'],
            'transport' => $validated['transport'],
            'misc' => $validated['misc'],
            'note' => $validated['note'] ?? null,
        ];

        if ($this->outingId) {
            $outings->update($this->outingId, $data);
            session()->flash('info', __('finance.outing_updated'));
        } else {
            $outings->create($data);
            session()->flash('success', __('finance.outing_created'));
        }

        return redirect()->route('outings.index');
    }

    public function render()
    {
        return view('livewire.finance.outing-form')
            ->layout('components.layouts.admin', ['title' => $this->outingId ? __('finance.edit_outing') : __('finance.add_outing')]);
    }
}
