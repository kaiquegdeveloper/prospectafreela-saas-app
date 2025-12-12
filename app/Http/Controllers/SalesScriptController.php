<?php

namespace App\Http\Controllers;

use App\Models\SalesScript;
use App\Models\SalesScriptCategory;
use Illuminate\Http\Request;

class SalesScriptController extends Controller
{
    /**
     * Display a listing of all categories
     */
    public function index()
    {
        $categories = SalesScriptCategory::active()
            ->ordered()
            ->withCount(['scripts' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get();

        return view('sales-scripts.index', compact('categories'));
    }

    /**
     * Show scripts for a specific category
     */
    public function showCategory(SalesScriptCategory $category)
    {
        $category->load(['scripts' => function ($query) {
            $query->where('is_active', true)->ordered();
        }]);

        // Group scripts by stage
        $scriptsByStage = [
            'introducao' => $category->scripts->where('stage', 'introducao')->values(),
            'qualificacao' => $category->scripts->where('stage', 'qualificacao')->values(),
            'levar_call' => $category->scripts->where('stage', 'levar_call')->values(),
            'quebra_objecao' => $category->scripts->where('stage', 'quebra_objecao')->values(),
            'fechamento' => $category->scripts->where('stage', 'fechamento')->values(),
        ];

        $stageNames = [
            'introducao' => 'Introdução',
            'qualificacao' => 'Qualificação',
            'levar_call' => 'Levar para a Call',
            'quebra_objecao' => 'Quebra de Objeção',
            'fechamento' => 'Fechamento',
        ];

        return view('sales-scripts.category', compact('category', 'scriptsByStage', 'stageNames'));
    }

    /**
     * Show a specific script
     */
    public function show(SalesScript $script)
    {
        $script->load('category');
        
        // Get related scripts from same category and stage
        $relatedScripts = SalesScript::where('category_id', $script->category_id)
            ->where('stage', $script->stage)
            ->where('id', '!=', $script->id)
            ->active()
            ->ordered()
            ->limit(5)
            ->get();

        return view('sales-scripts.show', compact('script', 'relatedScripts'));
    }

    /**
     * Search scripts
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('sales-scripts.index');
        }

        $scripts = SalesScript::where('content', 'like', "%{$query}%")
            ->orWhere('title', 'like', "%{$query}%")
            ->active()
            ->with('category')
            ->ordered()
            ->paginate(20);

        return view('sales-scripts.search', compact('scripts', 'query'));
    }
}

