<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubscriptionPlan;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;

class SyncStripePlans extends Command
{
    protected $signature = 'stripe:sync-plans';
    protected $description = 'Sync local subscription plans to Stripe';

    public function handle()
    {
        // Set Stripe secret key from config/cashier.php
        Stripe::setApiKey(config('cashier.secret'));

        // Fetch all local subscription plans
        $plans = SubscriptionPlan::all();

        if ($plans->isEmpty()) {
            $this->warn('No subscription plans found in the database.');
            return Command::SUCCESS;
        }

        foreach ($plans as $plan) {
            // Convert your custom duration to Stripe’s interval format
            $interval = match (strtolower($plan->duration)) {
                'weekly' => 'week',
                'yearly' => 'year',
                default => 'month',
            };

            // Create a Stripe Product
            $product = Product::create([
                'name' => $plan->name,
                'description' => $plan->description ?? '',
            ]);

            // Create a Stripe Price for that product
            $price = Price::create([
                'unit_amount' => $plan->price * 100, // Stripe uses cents
                'currency' => strtolower($plan->currency),
                'recurring' => ['interval' => $interval],
                'product' => $product->id,
            ]);

            // Save Stripe product & price IDs locally
            $plan->update([
                'stripe_product_id' => $product->id,
                'stripe_price_id' => $price->id,
            ]);

            $this->info("✅ Synced plan: {$plan->name} ({$interval})");
        }

        $this->info('🎯 All subscription plans synced successfully with Stripe!');
        return Command::SUCCESS;
    }
}
