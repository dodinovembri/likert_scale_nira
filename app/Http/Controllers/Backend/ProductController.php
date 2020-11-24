<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductModel;
use App\Models\ProductCategoryModel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $table = "product";

    public $index = "admin/product/index";
    public $create = "admin/product/create";
    public $store = "admin/product/store";
    public $show = "admin/product/show";
    public $edit = "admin/product/edit";
    public $update = "admin/product/update";
    public $destroy = "admin/product/destroy";

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // for breadcrumb
        $data['breadcrumb'] = array(
            "home"=>array(
                "text"=>"Dashboard", 
                "link"=>"admin", 
                "is_active"=>"inactive"
            ),
            "product"=>array(
                "text"=>"Product", 
                "link"=>"", 
                "is_active"=>"active"
            )
        );
        $data['title'] = "Product";

        // for route link        
        $data['edit'] = $this->edit;
        $data['create'] = $this->create;
        $data['destroy'] = $this->destroy;
        

        $table = $this->table;
        $data['table_field'] = DB::select("DESCRIBE $table");
        $data['field_break'] = "created_at";
        $data['text_add'] = "Add New";
        $data['table_data'] = ProductModel::all();

        return view('admin.single_page.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // define product category
        $data['dropdown'] = ProductCategoryModel::where('status', 1)->get();
        $data['dropdown_option'] = "product_category_name";

        // for breadcrumb
        $data['breadcrumb'] = array(
            "home"=>array(
                "text"=>"Dashboard", 
                "link"=>"admin", 
                "is_active"=>"inactive"
            ),
            "product"=>array(
                "text"=>"Product", 
                "link"=>$this->index, 
                "is_active"=>"inactive"
            ),
            "create_product"=>array(
                "text"=>"Create Product", 
                "link"=>"#", 
                "is_active"=>"active"
            )
        );
        $data['title'] = "Create Product";

        $data['store'] = $this->store;
        $data['index'] = $this->index;
        $table = $this->table;
        $data['table_field'] = DB::select("DESCRIBE $table");
        $data['field_first'] = "id";
        $data['field_break'] = "created_at";        

        return view('admin.single_page.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $table = $this->table;
        $table_field = DB::select("DESCRIBE $table");
        $field_break = "created_at";
        $field_first = "id";
        foreach ($table_field as $key => $value) {
            if ($value->Field == $field_first){
                continue;
            }
            if ($value->Field == $field_break){
                break;
            }                                            
            $arr_field[] = $value->Field;
            $count = count($arr_field); 
        }

        $insert = new ProductModel();
        for ($i=0; $i < $count; $i++) { 
            $field_db = $arr_field[$i];            
            $insert->$field_db = $request->$field_db;            
        }        
        $insert->save();

        $result = preg_replace("/[^a-zA-Z]/", " ", $this->table); 
        return redirect(url($this->index))->with("message", "Success created $result !");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // define product category
        $data['dropdown'] = ProductCategoryModel::where('status', 1)->get();
        $data['dropdown_option'] = "product_category_name";
                
        // for breadcrumb
        $data['breadcrumb'] = array(
            "home"=>array(
                "text"=>"Dashboard", 
                "link"=>"admin", 
                "is_active"=>"inactive"
            ),
            "general_information"=>array(
                "text"=>"Product", 
                "link"=>$this->index, 
                "is_active"=>"inactive"
            ),
            "edit_general_information"=>array(
                "text"=>"Edit Product", 
                "link"=>"", 
                "is_active"=>"active"
            )            
        );
        $data['title'] = "Edit Product";
        $data['update'] = $this->update;
        $data['index'] = $this->index;

        $data['id'] = $id;
        $table = $this->table;
        $data['table_field'] = DB::select("DESCRIBE $table");
        $data['field_first'] = "id";
        $data['field_break'] = "created_at";
        $data['field_'] = "created_at";

        $data['table_content'] = ProductModel::find($id);

        return view('admin.single_page.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $table = $this->table;
        $table_field = DB::select("DESCRIBE $table");
        $field_break = "created_at";
        $field_first = "id";
        foreach ($table_field as $key => $value) {
            if ($value->Field == $field_first){
                continue;
            }
            if ($value->Field == $field_break){
                break;
            }                                            
            $arr_field[] = $value->Field;
            $count = count($arr_field); 
        }

        $update = ProductModel::find($id);
        for ($i=0; $i < $count; $i++) { 
            $field_db = $arr_field[$i];            
            $update->$field_db = $request->$field_db;            
        }        
        $update->update();

        $result = preg_replace("/[^a-zA-Z]/", " ", $this->table); 
        return redirect(url($this->index))->with("message", "Success updated $result !");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $findtodelete = ProductModel::find($id);
        $findtodelete->delete();

        $result = preg_replace("/[^a-zA-Z]/", " ", $this->table); 
        return redirect(url($this->index))->with("info", "Success deleted $result !");        
    }
}
