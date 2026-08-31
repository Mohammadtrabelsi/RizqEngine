<?php

namespace App\Http\Controllers;

use App\Exceptions\ConversionException;
use App\Models\Commande;
use App\Services\CommandeService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class CommandeController extends Controller
{
    public function __construct(private readonly CommandeService $commandes) {}

    public function index()
    {
        abort_if(Gate::denies('access_commandes'), 403);

        return view('commande.index');
    }

    public function show(Commande $commande)
    {
        abort_if(Gate::denies('show_commandes'), 403);

        [$commande, $customer] = $this->commandes->showData($commande);

        return view('commande.show', compact('commande', 'customer'));
    }

    public function confirm(Commande $commande)
    {
        abort_if(Gate::denies('confirm_commandes'), 403);

        try {
            $this->commandes->confirm($commande);
        } catch (ConversionException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        session()->flash('success', trans('commande.confirmed'));

        return redirect()->route('commandes.show', $commande);
    }

    public function destroy(Commande $commande)
    {
        abort_if(Gate::denies('delete_commandes'), 403);

        $this->commandes->delete($commande);

        session()->flash('warning', trans('commande.deleted'));

        return redirect()->route('commandes.index');
    }
}
