<?php

namespace App\Livewire\WithholdingTaxes;

use App\Enums\WithholdingCalculationBase;
use App\Models\WithholdingTax;
use App\Services\WithholdingTaxService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class WithholdingTaxForm extends Component
{
    public ?int $withholdingTaxId = null;

    public string $name = '';

    public string $code = '';

    public string $description = '';

    public $rate = 0;

    public string $calculation_base = 'ttc';

    public bool $active = true;

    public bool $applicable_to_purchases = true;

    public bool $applicable_to_sales = false;

    public ?string $start_date = null;

    public ?string $end_date = null;

    public function mount(?WithholdingTax $withholding_tax = null): void
    {
        if ($withholding_tax && $withholding_tax->exists) {
            $this->withholdingTaxId = $withholding_tax->id;
            $this->name = (string) $withholding_tax->name;
            $this->code = (string) $withholding_tax->code;
            $this->description = (string) $withholding_tax->description;
            $this->rate = $withholding_tax->rate;
            $this->calculation_base = $withholding_tax->calculation_base->value;
            $this->active = $withholding_tax->active;
            $this->applicable_to_purchases = $withholding_tax->applicable_to_purchases;
            $this->applicable_to_sales = $withholding_tax->applicable_to_sales;
            $this->start_date = $withholding_tax->start_date?->format('Y-m-d');
            $this->end_date = $withholding_tax->end_date?->format('Y-m-d');
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => [
                'required', 'string', 'max:255',
                Rule::unique('withholding_taxes', 'code')->ignore($this->withholdingTaxId),
            ],
            'description' => 'nullable|string|max:1000',
            'rate' => 'required|numeric|min:0|max:100',
            'calculation_base' => ['required', Rule::in(array_column(WithholdingCalculationBase::cases(), 'value'))],
            'active' => 'boolean',
            'applicable_to_purchases' => 'boolean',
            'applicable_to_sales' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function save(WithholdingTaxService $withholdingTaxes)
    {
        $data = $this->validate();

        if (! $data['applicable_to_purchases'] && ! $data['applicable_to_sales']) {
            $this->addError('applicable_to_purchases', __('withholding.at_least_one_side'));

            return null;
        }

        if ($this->withholdingTaxId) {
            $withholdingTaxes->update($this->withholdingTaxId, $data);
            session()->flash('info', trans('withholding.withholding-updated'));
        } else {
            $withholdingTaxes->create($data);
            session()->flash('success', trans('withholding.withholding-created'));
        }

        return redirect()->route('withholding-taxes.index');
    }

    public function render()
    {
        return view('livewire.withholding-taxes.withholding-tax-form', [
            'bases' => WithholdingCalculationBase::options(),
        ])->layout('components.layouts.admin', [
            'title' => $this->withholdingTaxId ? __('withholding.edit_withholding') : __('withholding.add_withholding'),
        ]);
    }
}
