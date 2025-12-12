<?php

namespace App\Http\Controllers;

use App\Models\SalesScript;
use App\Models\SalesScriptCategory;
use Illuminate\Http\Request;

class SuperAdminSalesScriptController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = SalesScriptCategory::ordered()
            ->withCount(['scripts'])
            ->paginate(20);

        return view('super-admin.sales-scripts.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function createCategory()
    {
        return view('super-admin.sales-scripts.create-category');
    }

    /**
     * Store a newly created category
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sales_script_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        $category = SalesScriptCategory::create($validated);

        return redirect()
            ->route('super-admin.sales-scripts.categories.show', $category)
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Show category and its scripts
     */
    public function showCategory(SalesScriptCategory $category)
    {
        $category->load(['scripts' => function ($query) {
            $query->ordered();
        }]);

        $scriptsByStage = [
            'introducao' => $category->scripts->where('stage', 'introducao')->values(),
            'qualificacao' => $category->scripts->where('stage', 'qualificacao')->values(),
            'levar_call' => $category->scripts->where('stage', 'levar_call')->values(),
            'quebra_objecao' => $category->scripts->where('stage', 'quebra_objecao')->values(),
            'fechamento' => $category->scripts->where('stage', 'fechamento')->values(),
        ];

        return view('super-admin.sales-scripts.show-category', compact('category', 'scriptsByStage'));
    }

    /**
     * Show the form for editing a category
     */
    public function editCategory(SalesScriptCategory $category)
    {
        return view('super-admin.sales-scripts.edit-category', compact('category'));
    }

    /**
     * Update category
     */
    public function updateCategory(Request $request, SalesScriptCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sales_script_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update($validated);

        return redirect()
            ->route('super-admin.sales-scripts.categories.show', $category)
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Show form to create a new script
     */
    public function createScript(SalesScriptCategory $category)
    {
        return view('super-admin.sales-scripts.create-script', compact('category'));
    }

    /**
     * Store a new script
     */
    public function storeScript(Request $request, SalesScriptCategory $category)
    {
        $validated = $request->validate([
            'stage' => 'required|in:introducao,qualificacao,levar_call,quebra_objecao,fechamento',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'tips' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['category_id'] = $category->id;
        
        SalesScript::create($validated);

        return redirect()
            ->route('super-admin.sales-scripts.categories.show', $category)
            ->with('success', 'Script criado com sucesso!');
    }

    /**
     * Show form to edit a script
     */
    public function editScript(SalesScript $script)
    {
        $script->load('category');
        return view('super-admin.sales-scripts.edit-script', compact('script'));
    }

    /**
     * Update a script
     */
    public function updateScript(Request $request, SalesScript $script)
    {
        $validated = $request->validate([
            'stage' => 'required|in:introducao,qualificacao,levar_call,quebra_objecao,fechamento',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'tips' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $script->update($validated);

        return redirect()
            ->route('super-admin.sales-scripts.categories.show', $script->category)
            ->with('success', 'Script atualizado com sucesso!');
    }

    /**
     * Delete a script
     */
    public function destroyScript(SalesScript $script)
    {
        $category = $script->category;
        $script->delete();

        return redirect()
            ->route('super-admin.sales-scripts.categories.show', $category)
            ->with('success', 'Script excluído com sucesso!');
    }

    /**
     * Toggle script active status
     */
    public function toggleScriptStatus(SalesScript $script)
    {
        $script->update(['is_active' => !$script->is_active]);

        return redirect()->back()->with('success', 
            'Script ' . ($script->is_active ? 'ativado' : 'desativado') . ' com sucesso!'
        );
    }
}

