<?php

namespace App\Enums;

enum PackNormSource: string
{
    case Manual = 'manual';
    case OpenFoodFacts = 'openfoodfacts';
    case Scraped = 'scraped';
    case Receipts = 'receipts';
}
