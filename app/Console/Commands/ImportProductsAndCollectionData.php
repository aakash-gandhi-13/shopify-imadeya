<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportProductsAndCollectionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:shopifyData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to import products and collection data from shopify';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::withHeaders(config('constants.shopifyApi.token'))
            ->withBody(config('constants.shopifyApi.payload.collectionList'), 'application/graphql')
            ->post(config('constants.shopifyApi.url'));
        if ($response->status() != 200) {
            dd($response->json());
        } else {
            $response = $response->json()['data'];
            if (!empty($response['collections'])) {
                Category::truncate();
                Product::truncate();
                CategoryProduct::truncate();
                foreach ($response['collections']['nodes'] as $collectionNode) {
                    $collectionEntry = Category::create([
                        'shopify_collection_id' => str_replace('gid://shopify/Collection/', '', $collectionNode['id']),
                        'title' => $collectionNode['title'],
                        'handle' => $collectionNode['handle'],
                        'created_at' => now()
                    ]);
                    if (!empty($collectionNode['products']['nodes'])) {
                        foreach ($collectionNode['products']['nodes'] as $productNode) {
                            $shopifyProductId = str_replace('gid://shopify/Product/', '', $productNode['id']);
                            $productEntry = Product::where('shopify_product_id', $shopifyProductId)->first();
                            if (!$productEntry) {
                                $productEntry = Product::create([
                                    'shopify_product_id' => $shopifyProductId,
                                    'title' => $productNode['title'],
                                    'handle' => $productNode['handle'],
                                    'created_at' => now()
                                ]);
                            }
                            CategoryProduct::create([
                                'product_id' => $productEntry->id,
                                'category_id' => $collectionEntry->id,
                                'created_at' => now()
                            ]);
                        }
                    }
                }
                print('Operation complete.' . PHP_EOL);
            }
        }
    }
}
