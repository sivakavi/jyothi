<?php

namespace App\Http\Controllers\Student;

use App\Models\Auth\User\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use App\Group;
use App\Category;
use App\SubCategoryFile;
use Auth;
use App\Question;
use App\ViewReport;

class DashboardController extends Controller
{

    private $user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subCategories = $this->user->group->sub_categories()->get();
        $subCategoriesCount = $this->user->group->sub_categories()->count();
        $totalCount = 0;
        $viewedCount = 0;
        $student = $this->user;
        $viewCount = ViewReport::where("user_id", $this->user->id)->count();
        $lastViewed = ViewReport::where("user_id", $student->id)->orderBy('created_at', 'desc')->first();
        $categories = $subCategoriesGroups = array();
        foreach ($subCategories as $subCategory) {
            $categories[$subCategory->category_id] = $subCategory->parent_name;
            $lessons = SubCategoryFile::where('sub_category_id', $subCategory->id)->count();
            $totalCount += $lessons;
            $viewed = ViewReport::where('sub_category_id', $subCategory->id)->count();
            $viewedCount += $viewed;
            $subCategoriesGroups[$subCategory->id]['name']=$subCategory->name;
            $subCategoriesGroups[$subCategory->id]['progress']=$lessons? (int)round($viewed/$lessons*100): 0;
        }
        // dd($subCategoriesGroups);
        return view('student.dashboard', compact('categories', 'subCategoriesGroups', 'totalCount', 'viewedCount', 'student', 'subCategoriesCount', 'lastViewed'));
    }

    public function category($id)
    {
        $subCategories = $this->getSubCategory($id);
        return view('student.subcategorylist', compact('subCategories'));  
    }

    public function subCategories()
    {
        $subCategories = $this->getSubCategory();
        return view('student.subcategorylist', compact('subCategories'));
    }

    public function subCategory($id)
    {
        $subCategory = $this->user->group->sub_categories()->where('id',$id)->first();
        if(!is_null($subCategory)){
            $subCategoryFiles = array();
            $data = SubCategoryFile::orderBy('id', 'desc')->where('sub_category_id', $id)->get();
            foreach($data as $subCategoryFile){
                $str = $subCategoryFile->file;
                //$str = explode('_', $str);
                $subCategoryFiles[$subCategoryFile->id] = $str;
            }
            $subCategory = $subCategory->toArray();
            $tests = Question::select('test_id', 'sub_category_id')->where('sub_category_id', $id)->get()->toArray();
            $test = array();
            foreach($tests as $testValue){
                $test[$testValue['test_id']] = $testValue['parent_name'];
            }
            return view('student.subcategory', compact('subCategory', 'test', 'subCategoryFiles'));
        }
        abort(404, 'Yor are not authorized to page access');
    }

    public function subCategoryPDF($id)
    {
        $subCategoryFile = SubCategoryFile::find($id);
        if(!is_null($subCategoryFile)){
            ViewReport::updateOrCreate(['user_id' => $this->user->id, 'sub_category_file_id' => $subCategoryFile->id, 'sub_category_id' => $subCategoryFile->sub_category_id]);
            return redirect("uploads/".$subCategoryFile->file);
        }
    }

    public function test(Request $request, $id)
    {
        
        $questions = Question::where('test_id', $id)->where('sub_category_id', $request->input('subCatId'))->get()->toArray();
        return view('student.test', compact('questions'));
    }

    private function getSubCategory($category= null)
    {
        $subCategories =array();
        if($category){
            $sub_categories = $this->user->group->sub_categories()->select('category_id')->get()->toArray();
            $userCategories = array_column($sub_categories, 'category_id');
            if(in_array($category, $userCategories)){
                $subCategories =  $this->user->group->sub_categories()->where('category_id',$category)->get()->toArray();
                return $this->transformSubCategory($subCategories);
            }
            abort(404, 'Yor are not authorized to page access');
        }
        $subCategories = $this->user->group->sub_categories()->get()->toArray();
        return $this->transformSubCategory($subCategories);
    }

    private function transformSubCategory($subCategories=array())
    {
        $subCategory = array();
        foreach ($subCategories as $key => $value) {
            $subCategory[$value['id']]['id'] = $value['id'];
            $subCategory[$value['id']]['name'] = $value['name'];
            $subCategory[$value['id']]['category_name'] = $value['parent_name'];
            $lessons = SubCategoryFile::where('sub_category_id', $value['id'])->count();
            $viewed = ViewReport::where('sub_category_id', $value['id'])->count();
            $progress = $lessons? (int)round($viewed/$lessons*100): 0;
            $subCategory[$value['id']]['progress'] = $progress;
        }
        return $subCategory;
    }
}
