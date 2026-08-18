@php
    $badges = [
        \App\Models\Commande::STATUS_PENDING => 'badge-info',
        \App\Models\Commande::STATUS_CONFIRMED => 'badge-primary',
        \App\Models\Commande::STATUS_INVOICED => 'badge-success',
    ];
@endphp
<span class="badge {{ $badges[$data->status] ?? 'badge-secondary' }}">
    {{ __('commande.status_'.$data->status) }}
</span>
