<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\LabelResource;
use App\Models\Category;
use App\Models\Label;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CategoryLabelController extends Controller
{
    public function categories()
    {
        if (\request('onlyByMe'))
            $categories = auth()->user()->categories;
        else
            $categories = auth()->user()->categories->merge(Category::whereNull('user_id')->get());

        return apiResponse('Categories Retrieved', CategoryResource::collection($categories));
    }

    public function labels()
    {
        if (\request('onlyByMe'))
            $labels = auth()->user()->labels;
        else
            $labels = auth()->user()->labels->merge(Label::whereNull('user_id')->get());

        return apiResponse('Labels Retrieved', LabelResource::collection($labels));
    }

    public function storeCategory(Request $request)
    {
        $category = auth()->user()->categories()->create([
            'name' => $request->name
        ]);
        return apiResponse('Category Created', new CategoryResource($category));
    }

    public function storeLabel(Request $request)
    {
        $label = auth()->user()->labels()->create([
            'name' => $request->name
        ]);
        return apiResponse('Label Created', new LabelResource($label));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $category->update([
            'name' => $request->name ?? $category->name
        ]);
        return apiResponse('Category Updated', new CategoryResource($category));
    }

    public function updateLabel(Request $request, Label $label)
    {
        $label->update([
            'name' => $request->name ?? $label->name
        ]);
        return apiResponse('Label Updated', new LabelResource($label));
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();
        return apiResponse('Category Deleted');
    }

    public function destroyLabel(Label $label)
    {
        $label->delete();
        return apiResponse('Label Deleted');
    }
}
