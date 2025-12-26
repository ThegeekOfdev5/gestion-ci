<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->nullOnDelete();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('quote_id')
                ->references('id')
                ->on('quotes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['quote_id']);
        });
    }
};
