<?php

namespace App\Http\Controllers;

use App\Http\Models\Category;
use App\Http\Models\Product;
use App\Http\Models\ProductImage;
use App\Http\Requests\ProductRequest;
use  Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

/**
 * Class ProductController.
 *
 * @category AppControllers
 *
 * @author acev <aasisvinayak@gmail.com>
 * @license https://github.com/aasisvinayak/flymyshop/blob/master/LICENSE  GPL-3.0
 *
 * @link https://github.com/aasisvinayak/flymyshop
 */
class ProductController extends Controller
{
    /**
     * Paginated listing of all products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::paginate(10);


        foreach ($products as $item) {
            switch ($item->status) {
                case 1:
                    $item->status = 'Published';
                    break;
                case 0:
                    $item->status = 'Un-published';
                    break;
                default:
                    $item->status = 'Status Unavailable';
            }
        }

        return view('admin/products', compact('products'));
    }

    public function updateProductStatus(Request $request)
    {
        $product = Product::findorFail($request->get('id'));
        $product->update(['status' => $request->get('status')]);

        if ($request->get('status') == '1') {
            $request->session()->flash('alert-success', 'Product has been published!');
        } else {
            $request->session()->flash('alert-success', 'Product has been un-published!');
        }

        return redirect('admin/products');
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories_list = Category::pluck('title', 'category_id');

        return view('admin/add-product', compact('categories_list'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param ProductRequest $request Product Request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $category_id = Category::select('id')
            ->where('category_id', '=', $request['category_id'])
            ->get()->toArray();
        $category_id = $category_id[0]['id'];
        $request['category_id'] = $category_id;
        $request['product_id'] = str_random(50);
        $request['status'] = 1;
        $request['sold_count'] = 0;
        $randomFileName = str_random(50);
        $extension = '';

        if (! is_null($request->file('image'))) {
            if ($request->file('image')->isValid()) {
                $destinationPath = 'uploads';
                $extension = $request->file('image')->getClientOriginalExtension();
                $fileName = $randomFileName.'.'.$extension;
                $request->file('image')->move(public_path($destinationPath), $fileName);
            }
        }

        $request ['image'] = $randomFileName;
        $request ['image_name'] = $randomFileName.'.'.$extension;
        $product_id = Product::create($request->all())->id;


        if (! is_null($request->file('image1'))) {
            if ($request->file('image1')->isValid()) {
                $randomFileName = str_random(50);
                $destinationPath = 'uploads';
                $extension = $request->file('image1')->getClientOriginalExtension();
                $fileName = $randomFileName.'.'.$extension;
                $request->file('image1')->move(public_path($destinationPath), $fileName);
                $additonalImage = new ProductImage();
                $additonalImage->create(
                    [
                        'image' => $randomFileName,
                        'image_name' => $randomFileName.'.'.$extension,
                        'product_id' => $product_id,
                    ]
                );
            }
        }
        if (! is_null($request->file('image2'))) {
            if ($request->file('image2')->isValid()) {
                $randomFileName = str_random(50);
                $destinationPath = 'uploads';
                $extension = $request->file('image2')->getClientOriginalExtension();
                $fileName = $randomFileName.'.'.$extension;
                $request->file('image2')->move(public_path($destinationPath), $fileName);
                $additonalImage = new ProductImage();
                $additonalImage->create(
                    [
                        'image' => $randomFileName,
                        'image_name' => $randomFileName.'.'.$extension,
                        'product_id' => $product_id,
                    ]
                );
            }
        }
        if (! is_null($request->file('image3'))) {
            if ($request->file('image3')->isValid()) {
                $randomFileName = str_random(50);
                $destinationPath = 'uploads';
                $extension = $request->file('image3')->getClientOriginalExtension();
                $fileName = $randomFileName.'.'.$extension;
                $request->file('image3')->move(public_path($destinationPath), $fileName);
                $additonalImage = new ProductImage();
                $additonalImage->create(
                    [
                        'image' => $randomFileName,
                        'image_name' => $randomFileName.'.'.$extension,
                        'product_id' => $product_id,
                    ]
                );
            }
        }

        $request->session()->flash('alert-success', 'Product has been added!');

        return redirect('/admin/products/');
    }

    /**
     * Display the specified product.
     *
     * @param int $id page id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findorFail($id);

        switch ($product->status) {
            case 1:
                $product->status = 'Published';
                break;
            case 0:
                $product->status = 'Un-published';
                break;
            default:
                $product->status = 'Status Unavailable';
        }

        $productAdditionalImages = $product->additionalImages()->get()->toArray();

        return view('admin.product', compact('product', 'productAdditionalImages'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param int $id product id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findorFail($id);
        $categories_list = Category::pluck('title', 'category_id');

        return view('admin.edit-product', compact('product', 'categories_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * //TODO: add support for replaces images
     *
     * @param \Illuminate\Http\Request $request product request
     * @param int $id product id
     *
     * @return Redirect
     */
    public function update(Request $request, $id)
    {
        unset($request['image']);
        unset($request['image1']);
        unset($request['image2']);
        unset($request['image3']);
        Product::findorFail($id)->update($request->all());
        $request->session()->flash('alert-success', 'Product has been updated!');

        return redirect('admin/products');
    }

    public function stocks()
    {
        $products = Product::paginate(10);

        return view('admin/stocks', compact('products'));
    }

    public function updateStock(Request $request)
    {
        $product = Product::findorFail($request->get('id'));
        $product->update([
            'stock' => $request->get('stock'),
        ]);

        return redirect('admin/stocks');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param int $id product id
     *
     * @param Request $request
     * @return Redirect
     */
    public function destroy($id, Request $request)
    {
        Product::findorFail($id)->delete();
        $request->session()->flash('alert-success', 'Product has been deleted!');

        return redirect('admin/products');
    }

    public static function getPublishedProducts($take, $skip)
    {
        return Product::PublishedProducts($take, $skip)->toArray();
    }
}
