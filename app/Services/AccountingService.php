<?php
// app/Services/AccountingService.php
namespace App\Services;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\AccountingEntry;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Enregistrement automatique d'une facture client
     */
    public function recordInvoice(Invoice $invoice): void
    {
        $tenant = $invoice->tenant;

        // Compte client (411)
        $clientAccount = Account::where('code', '411')->first();

        // Compte vente (701 ou 706 selon type)
        $salesAccount = Account::where('code', '701')->first();

        // Compte TVA collectée (4411)
        $tvaAccount = Account::where('code', '4411')->first();

        // Créer l'écriture comptable
        $entry = AccountingEntry::create([
            'tenant_id' => $tenant->id,
            'date' => $invoice->issue_date,
            'reference' => "FAC-{$invoice->invoice_number}",
            'description' => "Facture client {$invoice->customer->name}",
            'document_type' => 'invoice',
            'document_id' => $invoice->id,
        ]);

        // Ligne Débit : Client (411)
        $entry->lines()->create([
            'account_id' => $clientAccount->id,
            'debit' => $invoice->total_ttc,
            'credit' => 0,
            'description' => "Client {$invoice->customer->name}",
        ]);

        // Ligne Crédit : Vente (701)
        $entry->lines()->create([
            'account_id' => $salesAccount->id,
            'debit' => 0,
            'credit' => $invoice->total_ht,
            'description' => "Vente de marchandises",
        ]);

        // Ligne Crédit : TVA collectée (4411)
        $entry->lines()->create([
            'account_id' => $tvaAccount->id,
            'debit' => 0,
            'credit' => $invoice->total_tva,
            'description' => "TVA collectée 18%",
        ]);
    }

    /**
     * Enregistrement d'un règlement client
     */
    public function recordPayment(Payment $payment): void
    {
        $invoice = $payment->invoice;

        // Compte banque ou caisse selon mode paiement
        $bankAccount = $this->getBankAccount($payment->payment_method);

        // Compte client (411)
        $clientAccount = Account::where('code', '411')->first();

        $entry = AccountingEntry::create([
            'tenant_id' => $invoice->tenant_id,
            'date' => $payment->payment_date,
            'reference' => "REG-{$payment->reference}",
            'description' => "Règlement facture {$invoice->invoice_number}",
        ]);

        // Débit : Banque/Caisse
        $entry->lines()->create([
            'account_id' => $bankAccount->id,
            'debit' => $payment->amount,
            'credit' => 0,
        ]);

        // Crédit : Client
        $entry->lines()->create([
            'account_id' => $clientAccount->id,
            'debit' => 0,
            'credit' => $payment->amount,
        ]);
    }

    private function getBankAccount(string $paymentMethod): Account
    {
        return match($paymentMethod) {
            'orange_money' => Account::where('code', '5213')->first(),
            'mtn_money' => Account::where('code', '5214')->first(),
            'bank_transfer' => Account::where('code', '521')->first(),
            'cash' => Account::where('code', '57')->first(),
            default => Account::where('code', '521')->first(),
        };
    }

    /**
     * Génération balance de vérification
     */
    public function generateTrialBalance($tenantId, $startDate, $endDate)
    {
        // Récupérer toutes les lignes d'écritures sur la période
        $entries = DB::table('accounting_entry_lines')
            ->join('accounting_entries', 'accounting_entries.id', '=', 'accounting_entry_lines.entry_id')
            ->join('accounts', 'accounts.id', '=', 'accounting_entry_lines.account_id')
            ->where('accounting_entries.tenant_id', $tenantId)
            ->whereBetween('accounting_entries.date', [$startDate, $endDate])
            ->select([
                'accounts.code',
                'accounts.label',
                DB::raw('SUM(accounting_entry_lines.debit) as total_debit'),
                DB::raw('SUM(accounting_entry_lines.credit) as total_credit'),
            ])
            ->groupBy('accounts.code', 'accounts.label')
            ->orderBy('accounts.code')
            ->get();

        return $entries->map(function($entry) {
            $entry->balance = $entry->total_debit - $entry->total_credit;
            return $entry;
        });
    }
}
