<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Carbon;
use App\Models\Products;

class SyncWooProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $start;

    /**
     * Create a new job instance.
     * 
     * @return void
     */
    public function __construct(int $start = 0)
    {
        $this->start = $start;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $startTime = microtime(true);

        // WooCommerce API credentials
        $woocommerce = new Client(
            'https://woocommerce.kodeia.com',
            'ck_0ab72f4338b0c03c5c3f670b2f355ded8f737000',
            'cs_76e33f1efceac872c805aeb436cae67cbdd57515',
            [
                'wp_api' => true,
                'version' => 'wc/v3',
            ]
        );

        // Getting products according to the requirment
        $products = $woocommerce->get('products', ['per_page' => 10, 'offset' => $this->start]);

        // looping the products into the DB
        foreach ($products as $productData) {
            Products::updateOrCreate(
                ['id' => $productData->id],
                [
                    'name' => $productData->name,
                    // 'price' => $productData->regular_price,
                    'price' => !empty($productData->regular_price) ? $productData->regular_price : null,
                    'description' => $productData->description,
                ]
            );
        }

        $endTime = microtime(true);

        $responseTime = $endTime - $startTime;

        // Checking the delay and dispatching next job
        $delay = $this->responseDelay($responseTime);
        SyncWooProductJob::dispatch($this->start + 10)->delay(Carbon::now()->addMinutes($delay));
    }

    private function responseDelay($responseTime)
    {
        // calculate in minutes
        if ($responseTime <= 1) {
            return 1;
        } else {
            return 5;
        }
    }
}
