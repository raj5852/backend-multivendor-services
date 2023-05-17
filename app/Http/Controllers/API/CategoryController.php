<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class CategoryController extends Controller
{

    public function CategoryIndex()
    {
        $category=Category::latest()->paginate(10);
        return response()->json([
            'status'=>200,
            'category'=>$category,
        ]);
    }
    public function CategoryStore(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name' => 'required|unique:categories|max:255',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=>400,
                'errors'=>$validator->messages(),
            ]);
        }
          else
          {
            $category =new Category;
            if($request->hasFile('image'))
            {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() .'.'.$extension;
                $file->move('uploads/category/', $filename);
                $category->image = 'uploads/category/'.$filename;
            }
            $category->name=$request->input('name');
            $category->slug =Str::slug($request->name);
            $category->description=$request->input('description');
            $category->meta_title=$request->input('meta_title');
            $category->meta_keywords=$request->input('meta_keywords');
            $category->meta_description=$request->input('meta_description');
            $category->status=$request->input('status');
            $category->tags=$request->input('tags');


            $category->save();
            return response()->json([
            'status'=>200,
             'message'=>'Category Added Sucessfully',
            ]);
          }
    }

    public function CategoryEdit($id)
    {
         $category = Category::find($id);
        if($category)
        {
            return response()->json([
                'status'=>200,
                'category'=>$category
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'No Category Id Found'
            ]);
        }
    }

    public function UpdateCategory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
                        'name'=>'required|max:191',
                    ]);

                    if($validator->fails())
                    {
                        return response()->json([
                            'status'=>422,
                            'errors'=>$validator->messages(),
                        ]);
                    }
                    else
                    {
                        $category = Category::find($id);
                        if($category)
                        {
                            $category->meta_title = $request->input('meta_title');
                            $category->meta_keywords = $request->input('meta_keywords');
                            $category->meta_description = $request->input('meta_description');
                            $category->name = $request->input('name');
                            $category->slug =Str::slug($request->name);
                            $category->description = $request->input('description');
                            $category->status = $request->input('status');

                            if($request->hasFile('image'))
                            {
                                $path = $category->image;
                                if(File::exists($path))
                                {
                                    File::delete($path);
                                }
                                $file = $request->file('image');
                                $extension = $file->getClientOriginalExtension();
                                $filename = time() .'.'.$extension;
                                $file->move('uploads/category/', $filename);
                                $category->image = 'uploads/category/'.$filename;
                            }


                            $category->save();
                            return response()->json([
                                'status'=>200,
                                'message'=>'Category Updated Successfully',
                            ]);
                        }
                        else
                        {
                            return response()->json([
                                'status'=>404,
                                'message'=>'No Category ID Found',
                            ]);
                        }

                    }
    }

     public function destroy($id)
    {


        // $productImage=Category::select('image')->where('id',$id)->first();
        // Get Product path
        // $large_image_path = 'uploads/category/';
        // if (file_exists($large_image_path.$productImage->image)) {
        //     unlink($large_image_path.$productImage->image);
        // }

        // Category::where('id',$id)->update(['image'=>'']);
        // return response()->json([
        //     'status'=>200,
        //     'message'=>'Category Deleted Successfully',

        // ]);



        $category = Category::find($id);

        //         if ($category->image) {
        //    unlink('uploads/category'.'/'.$category->image);
        // }

        if($category)
        {
            $category->delete();
            return response()->json([
                'status'=>200,
                'message'=>'Category Deleted Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'No Category ID Found',
            ]);
        }


    }




}
