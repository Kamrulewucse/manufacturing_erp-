<?php

namespace App\Http\Controllers;

use App\Models\TaxSection;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaxSectionController extends Controller
{
    public function datatable() {
        $query = TaxSection::query();
        return DataTables::eloquent($query)
            ->addColumn('action', function(TaxSection $taxSection) {
                return '<a href="'.route('section_number_of_tds.edit',['taxSection'=>$taxSection->id]).'" class="btn btn-success btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }
    public function index() {
        return view('accounts.tax_section.all');
    }

    public function add() {
        $maxSection = TaxSection::max('sort');
        return view('accounts.tax_section.add',compact('maxSection'));
    }

    public function addPost(Request $request) {
        $rules = [
            'source' => 'required|string|max:255',
            'sort' => 'required|integer',
            'section' => 'nullable|string|max:255',
        ];
        $request->validate($rules);

        $taxSection = new TaxSection();
        $taxSection->source = $request->source;
        $taxSection->sort = $request->sort;
        $taxSection->section = $request->section;
        $taxSection->save();

        return redirect()->route('section_number_of_tds')->with('message', 'Section number of TDS add successfully.');
    }

    public function edit(TaxSection $taxSection) {
        return view('accounts.tax_section.edit', compact('taxSection'));
    }

    public function editPost(TaxSection $taxSection, Request $request) {
        $rules = [
            'source' => 'required|string|max:255',
            'sort' => 'required|integer',
            'section' => 'nullable|string|max:255',
        ];
        $request->validate($rules);


        $taxSection->source = $request->source;
        $taxSection->sort = $request->sort;
        $taxSection->section = $request->section;
        $taxSection->save();

        return redirect()->route('section_number_of_tds')->with('message', 'Section number of TDS  edit successfully.');
    }
}
