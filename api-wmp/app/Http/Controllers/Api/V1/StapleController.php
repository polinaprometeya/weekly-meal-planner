<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StapleResource;
use App\Models\Staple;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StapleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $staples = Staple::query()
            ->orderBy('name_da')
            ->get();

        return StapleResource::collection($staples);
    }

    public function show(Staple $staple): StapleResource
    {
        return new StapleResource($staple);
    }
}
