@php
    $badges = [
        \App\Models\BonCommande::STATUS_DRAFT => 'badge-info',
        \App\Models\BonCommande::STATUS_CONFIRMED => 'badge-primary',
        \App\Models\BonCommande::STATUS_CONVERTED => 'badge-success',
        \App\Models\BonCommande::STATUS_CANCELLED => 'badge-danger',
    ];
@endphp
<span class="badge {{ $badges[$data->status] ?? 'badge-secondary' }}">
    {{ __('boncommande.status_'.$data->status) }}
</span>
