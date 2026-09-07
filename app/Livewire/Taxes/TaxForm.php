<?php

namespace App\Livewire\Taxes;

use App\Models\Tax;
use App\Services\TaxService;
use Livewire\Component;

class TaxForm extends Component
{
    public ?int $taxId = null;

    public string $name = '';

    public $rate = 0;

    public function mount(?Tax $tax = null): void
    {
        if ($tax && $tax->exists) {
            $this->taxId = $tax->id;
            $this->name = (string) $tax->name;
            $this->rate = $tax->rate;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
        ];
    }

    public function save(TaxService $taxes)
    {
        $data = $this->validate();

        if ($this->taxId) {
            $taxes->update($this->taxId, $data);
            session()->flash('info', trans('taxes.tax-updated'));
        } else {
            $taxes->create($data);
            session()->flash('success', trans('taxes.tax-created'));
        }

        return redirect()->route('taxes.index');
    }

    public function render()
    {
        return view('livewire.taxes.tax-form')
            ->layout('components.layouts.admin', ['title' => $this->taxId ? __('taxes.edit_tax') : __('taxes.add_tax')]);
    }
}
