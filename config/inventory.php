<?php

return [
    'low_stock_threshold' => (int) env('LOW_STOCK_THRESHOLD', 10),

    'default_currency' => 'INR',

    'currencies' => [
        'INR' => ['symbol' => '₹', 'name' => 'Indian Rupee'],
        'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
        'EUR' => ['symbol' => '€', 'name' => 'Euro'],
        'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
    ],
];