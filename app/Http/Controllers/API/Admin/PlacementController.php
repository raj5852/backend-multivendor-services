<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Placement;
use Illuminate\Http\Request;

class PlacementController extends Controller
{

public function index($colum) {
        $placements = Placement::select('id', $colum)->where($colum, $colum)->latest()->paginate(10);

        return $this->response($placements);
    }


    public function store(Request $request, $colum) {
        $request->validate([
            'colum_name' => 'required'
        ]);
        if (!$colum) {
            return responsejson('Not found !', 'fail');
        }
        Placement::create([
            'colum_name' => $colum,
            $colum => request($colum)
        ]);

        return $this->response('Added successfully.');
    }

    public function show($id, $colum) {

        $placement = Placement::select('id', $colum)->find($id);

        return $this->response($placement);
    }

    public function update($id, $colum) {
        if (!$id) {
            return responsejson('Not found !', 'fail');
        }
        Placement::find($id)->update([
            $colum => request($colum)
        ]);
        return $this->response('Update successfull');
    }


    public function delete($id) {
        Placement::find($id)->delete();
        return $this->response('Deleted successfully.');
    }




}
