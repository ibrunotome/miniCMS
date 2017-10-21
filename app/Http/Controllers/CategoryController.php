<?php

namespace App\Http\Controllers;

use App\Category;
use App\DataTables\CategoryDataTable;
use App\Http\Requests\CategoryCreateRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param CategoryDataTable $dataTable
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CategoryDataTable $dataTable)
    {
        return $dataTable->render('layouts.categories.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layouts.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryCreateRequest $request)
    {
        $data = $request->all();

        Category::create($data);

        return redirect(route('categories.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Category $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
