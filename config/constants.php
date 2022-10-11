<?php

return [
    'allowedLocales' => ['en', 'ja'],
    'shopifyApi' => [
        'url' => 'https://pilate-wine.myshopify.com/api/2022-10/graphql.json',
        'token' => ['X-Shopify-Storefront-Access-Token' => 'f00502bffe1c0aeff048ab22cb87279a'],
        'payload' => [
            'collectionList' => '{ collections(first: 250) { nodes { handle, title, id, products(first: 250) { nodes { handle, id, title } } image { url } } } }'
        ]
    ]
];
