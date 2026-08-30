<?php

namespace App\Livewire\Currencies;

use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class CurrencyForm extends Component
{
    public ?int $currencyId = null;

    public string $currency_name = '';

    public string $code = '';

    public string $symbol = '';

    public string $thousand_separator = '';

    public string $decimal_separator = '';

    public function mount(?Currency $currency = null): void
    {
        if ($currency && $currency->exists) {
            abort_if(Gate::denies('edit_currencies'), 403);

            $this->currencyId = $currency->id;
            $this->currency_name = $currency->currency_name;
            $this->code = $currency->code;
            $this->symbol = $currency->symbol;
            $this->thousand_separator = $currency->thousand_separator;
            $this->decimal_separator = $currency->decimal_separator;
        } else {
            abort_if(Gate::denies('create_currencies'), 403);
        }
    }

    protected function rules(): array
    {
        return [
            'currency_name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'symbol' => 'required|string|max:255',
            'thousand_separator' => 'required|string|max:255',
            'decimal_separator' => 'required|string|max:255',
        ];
    }

    public function save(CurrencyService $currencies)
    {
        $data = $this->validate();

        if ($this->currencyId) {
            abort_if(Gate::denies('edit_currencies'), 403);

            $currencies->update($this->currencyId, $data);

            session()->flash('info', trans('currency.currency-updated'));
        } else {
            abort_if(Gate::denies('create_currencies'), 403);

            $currencies->create($data);

            session()->flash('success', trans('currency.currency-created'));
        }

        return redirect()->route('currencies.index');
    }

    public function render()
    {
        return view('livewire.currencies.currency-form')
            ->layout('components.layouts.admin', ['title' => $this->currencyId ? __('currency.edit_currency') : __('currency.add_currency')]);
    }
}
