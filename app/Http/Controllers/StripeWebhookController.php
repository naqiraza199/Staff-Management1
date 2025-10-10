<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Subscription;
use App\Models\Company;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        $data = $event['data']['object'];

        switch ($event['type']) {
            case 'customer.subscription.created':
                Subscription::updateOrCreate(
                    ['stripe_id' => $data['id']],
                    [
                        'status' => $data['status'],
                        'stripe_customer_id' => $data['customer'],
                        'stripe_price_id' => $data['items']['data'][0]['price']['id'] ?? null,
                        'current_period_end' => date('Y-m-d H:i:s', $data['current_period_end']),
                    ]
                );

                if ($company = Company::where('stripe_customer_id', $data['customer'])->first()) {
                    $company->update(['is_subscribed' => true]);
                }
                break;

            case 'customer.subscription.updated':
                Subscription::where('stripe_id', $data['id'])->update([
                    'status' => $data['status'],
                    'current_period_end' => date('Y-m-d H:i:s', $data['current_period_end']),
                ]);
                break;

            case 'customer.subscription.deleted':
                Subscription::where('stripe_id', $data['id'])->update([
                    'status' => 'canceled',
                ]);

                if ($company = Company::where('stripe_customer_id', $data['customer'])->first()) {
                    $company->update(['is_subscribed' => false]);
                }
                break;

            case 'invoice.payment_failed':
                Subscription::where('stripe_id', $data['subscription'])->update([
                    'status' => 'past_due',
                ]);
                break;
        }

        return response('Webhook received', 200);
    }
}
