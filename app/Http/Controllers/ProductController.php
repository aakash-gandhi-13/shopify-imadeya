<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\SearchProduct;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Function to search products
     * @method searchProducts
     * @param SearchProduct $request
     * @method \Illuminate\Http\JsonResponse
     */
    public function searchProducts(SearchProduct $request)
    {
        $payload = $request->safe()->all();
        $fields = [
            'products.id as product_id', 'products.shopify_product_id', 'products.title as product_title',
            'products.handle as product_handle', 'categories.id as collection_id', 'categories.shopify_collection_id',
            'categories.title as collection_title', 'categories.handle as collection_handle',
            'categories.parent_id as collection_parent_id'
        ];
        $query = Category::select($fields)
            ->join('categories_products', 'categories.id', '=', 'categories_products.category_id')
            ->join('products', function ($join) use ($payload) {
                $join->on('categories_products.product_id', '=', 'products.id');
                if (isset($payload['title'])) {
                    $join->where('products.title', 'like', "%{$payload['title']}%");
                }
            })->when(isset($payload['handle']), function ($conditionQuery) use ($payload) {
                return $conditionQuery->where('categories.handle', 'like', "%{$payload['handle']}%");
            })->distinct($fields);
        $this->addPaginationToQuery($query, $request);
        return $this->sendSuccessResponse($query->get(), 200, 'request_successful');
    }
}
