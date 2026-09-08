<?php

namespace App\Livewire\Taxes;

use App\Models\Tax;
use App\Services\TaxService;
use Livewire\Component;

class TaxForm extends Component
{
    public ?int $taxId = null;

    public string $name = '';

    public string $type = Tax::TYPE_PERCENTAGE;

    public $rate = 0;

    public string $apply_to = Tax::APPLY_TO_ORDER;

    public $order = 0;

    public function mount(?Tax $tax = null): void
    {
        if ($tax && $tax->exists) {
            $this->taxId = $tax->id;
            $this->name = (string) $tax->name;
            $this->type = $tax->type;
            $this->rate = $tax->rate;
            $this->apply_to = $tax->apply_to;
            $this->order = $tax->order;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:'.Tax::TYPE_PERCENTAGE.','.Tax::TYPE_FIXED,
            'rate' => $this->type === Tax::TYPE_PERCENTAGE
                ? 'required|numeric|min:0|max:100'
                : 'required|numeric|min:0',
            'apply_to' => 'required|in:'.implode(',', [
                Tax::APPLY_TO_PRODUCT,
                Tax::APPLY_TO_PURCHASE,
                Tax::APPLY_TO_SALE,
                Tax::APPLY_TO_ORDER,
            ]),
            'order' => 'required|integer|min:0',
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
