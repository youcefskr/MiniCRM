<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Affiche la liste des produits avec filtres
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filtrage
        $query->filter($request->all());

        $products = $query->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::all();
        
        // Statistiques
        $stats = [
            'total' => Product::count(),
            'active' => Product::active()->count(),
            'in_stock' => Product::inStock()->count(),
            'total_value' => Product::sum(\DB::raw('price * stock_quantity')),
        ];

        return view('products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * Créer un nouveau produit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'unique:products,code', 'max:50'],
            'brand' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'type' => ['required', 'in:product,service,subscription'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        try {
            // Gestion de l'image
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $validated['image'] = $path;
            }

            // Auto-générer le code si vide
            if (empty($validated['code'])) {
                $validated['code'] = 'PRD-' . strtoupper(uniqid());
            }

            $validated['is_active'] = $request->has('is_active') ? true : false;

            $product = Product::create($validated);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produit créé avec succès.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    /**
     * Afficher un produit spécifique
     */
    public function show(Product $product)
    {
        $product->load(['category', 'opportunities.contact']);
        
        // Statistiques du produit
        $stats = [
            'total_sold' => $product->opportunities()
                ->wherePivot('quantity', '>', 0)
                ->sum('opportunity_product.quantity'),
            'revenue' => $product->opportunities()
                ->sum('opportunity_product.total_price'),
            'opportunities_count' => $product->opportunities()->count(),
        ];

        return view('products.show', compact('product', 'stats'));
    }

    /**
     * Mettre à jour un produit
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'unique:products,code,' . $product->id, 'max:50'],
            'brand' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'type' => ['required', 'in:product,service,subscription'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        try {
            // Gestion de l'image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $path = $request->file('image')->store('products', 'public');
                $validated['image'] = $path;
            }

            $validated['is_active'] = $request->has('is_active') ? true : false;

            $product->update($validated);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produit mis à jour avec succès.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un produit
     */
    public function destroy(Product $product)
    {
        try {
            // Supprimer l'image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produit supprimé avec succès.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Exporter les produits en CSV
     */
    public function export(Request $request)
    {
        $query = Product::with('category');
        $query->filter($request->all());
        $products = $query->orderBy('name')->get();

        $filename = 'products_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($products) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            echo "\xEF\xBB\xBF";
            
            // En-têtes
            fputcsv($handle, [
                'Code',
                'Nom',
                'Marque',
                'Catégorie',
                'Type',
                'Prix (€)',
                'Stock',
                'Statut',
                'Créé le'
            ]);

            // Données
            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->code,
                    $product->name,
                    $product->brand,
                    $product->category->name ?? '',
                    $product->type,
                    $product->price,
                    $product->stock_quantity,
                    $product->is_active ? 'Actif' : 'Inactif',
                    $product->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Mettre à jour le stock d'un produit
     */
    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'operation' => ['required', 'in:set,add,subtract'],
        ]);

        $newStock = $product->stock_quantity;

        switch ($validated['operation']) {
            case 'set':
                $newStock = $validated['stock_quantity'];
                break;
            case 'add':
                $newStock += $validated['stock_quantity'];
                break;
            case 'subtract':
                $newStock -= $validated['stock_quantity'];
                if ($newStock < 0) $newStock = 0;
                break;
        }

        $product->update(['stock_quantity' => $newStock]);

        return response()->json([
            'success' => true,
            'message' => 'Stock mis à jour',
            'new_stock' => $newStock
        ]);
    }
}
