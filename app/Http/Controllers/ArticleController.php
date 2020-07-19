<?php
namespace App\Http\Controllers;

use App\Article;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index','detail']);
    }
    public function index()
    {
        $data = Article::latest()->paginate(5);
        return view('articles.index', [
            'articles' => $data,
        ]);
    }

    public function detail($id)
    {
        $data = Article::find($id);
        return view('articles.detail', [
            'article' => $data,
        ]);
    }
    public function add()
    {
        $data = [["id" => 1, "name" => "News"], ["id" => 2, "name" => "Tech"]];
        return view('articles.add', ['categories' => $data]);
    }
    public function create()
    {
        $validator = validator(request()->all(), ['title' => 'required', 'body' => 'required', 'category_id' => 'required']);

        if ($validator->fails())
        {
            return back()->withErrors($validator);
        }

        $article = new Article;
        $article->title = request()->title;
        $article->body = request()->body;
        $article->category_id = request()->category_id;
        $article->user_id = auth()->user()->id;
        $article->save();
        return redirect('/articles');
    }
    public function delete($id)
    {
        $article = Article::find($id);
        if($article->user_id == Auth::id()){
            $article->delete();
            return redirect('/articles')->with('info','Article Deleted');
        } else {
            return redirect('/articles')->with('info','Unauth');
        }  
    }
}
