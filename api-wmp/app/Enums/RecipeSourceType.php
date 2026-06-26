<?php

namespace App\Enums;

enum RecipeSourceType: string
{
    case Manual = 'manual';
    case Wikibooks = 'wikibooks';
    case PublicDomainBook = 'public_domain_book';
    case OtherOpen = 'other_open';
}
