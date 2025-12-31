<?php
// app/Services/TaxService.php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaxService
{
    // Taux TVA en Côte d'Ivoire
    const TVA_RATE = 0.18; // 18%
    const TVA_REDUCED_RATE = 0.09; // 9% (certains produits)

    /**
     * Calcul TVA sur montant HT
     */
    public function calculateVAT(float $amountHT, string $vatType = 'standard'): float
    {
        $rate = $vatType === 'reduced' ? self::TVA_REDUCED_RATE : self::TVA_RATE;
        return round($amountHT * $rate, 0); // Arrondi à l'unité en CI
    }

    /**
     * Génération déclaration TVA mensuelle
     */
    public function generateVATDeclaration($tenantId, $month, $year)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // TVA collectée (ventes)
        $tvaCollected = DB::table('accounting_entry_lines')
            ->join('accounting_entries', 'accounting_entries.id', '=', 'accounting_entry_lines.entry_id')
            ->join('accounts', 'accounts.id', '=', 'accounting_entry_lines.account_id')
            ->where('accounting_entries.tenant_id', $tenantId)
            ->where('accounts.code', '4411') // TVA collectée
            ->whereBetween('accounting_entries.date', [$startDate, $endDate])
            ->sum('credit');

        // TVA récupérable (achats)
        $tvaDeductible = DB::table('accounting_entry_lines')
            ->join('accounting_entries', 'accounting_entries.id', '=', 'accounting_entry_lines.entry_id')
            ->join('accounts', 'accounts.id', '=', 'accounting_entry_lines.account_id')
            ->where('accounting_entries.tenant_id', $tenantId)
            ->where('accounts.code', '4451') // TVA récupérable
            ->whereBetween('accounting_entries.date', [$startDate, $endDate])
            ->sum('debit');

        $tvaDue = $tvaCollected - $tvaDeductible;

        return [
            'period' => "{$month}/{$year}",
            'sales_amount_ht' => $this->getSalesAmount($tenantId, $startDate, $endDate),
            'vat_collected' => $tvaCollected,
            'purchases_amount_ht' => $this->getPurchasesAmount($tenantId, $startDate, $endDate),
            'vat_deductible' => $tvaDeductible,
            'vat_due' => max(0, $tvaDue), // Si crédit TVA, = 0 (à reporter)
            'vat_credit' => $tvaDue < 0 ? abs($tvaDue) : 0,
            'payment_deadline' => $endDate->copy()->addDays(15), // 15 du mois suivant
        ];
    }

    /**
     * Export XML format DGI (Déclaration e-Impôts)
     */
    public function exportVATDeclarationXML(TaxDeclaration $declaration): string
    {
        // Format XML spécifique DGI Côte d'Ivoire
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><DeclarationTVA></DeclarationTVA>');

        $xml->addChild('NIF', $declaration->tenant->company->nif);
        $xml->addChild('RaisonSociale', $declaration->tenant->company->name);
        $xml->addChild('Periode', $declaration->period);
        $xml->addChild('ChiffreAffairesHT', $declaration->data['sales_amount_ht']);
        $xml->addChild('TVACollectee', $declaration->data['vat_collected']);
        $xml->addChild('TVADeductible', $declaration->data['vat_deductible']);
        $xml->addChild('TVANette', $declaration->data['vat_due']);

        return $xml->asXML();
    }
}
