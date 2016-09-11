<?php

namespace App\Http\Controllers;

use App\Http\Models\Category;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceItem;
use App\Http\Models\Page;
use App\Http\Models\Product;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Input;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use Validator;
use View;

/**
 * Class ShopController
 * Default controller for FlyMyShop.
 *
 * @category Main
 *
 * @author acev <aasisvinayak@gmail.com>
 * @license https://github.com/aasisvinayak/flymyshop/blob/master/LICENSE  GPL-3.0
 *
 * @link https://github.com/aasisvinayak/flymyshop
 */
class ShopController extends Controller
{
    /**
     * Display list of all addresses for the user.
     *
     * @return View
     */
    public function address()
    {
        return view('account.address');
    }

    /**
     * Homepage of Shop.
     *
     * @return View
     */
    public function home()
    {
        $products = Product::featured();

        return view('pages/home', compact('products'));
    }

    /**
     * Add address using API provided by third party
     * TODO: Details of the third party service.
     *
     * @return View
     */
    public function addAddress()
    {
        $url = env('API_AUTH_URL').'authorize?client_id='.env('CLIENT_ID').'&redirect_uri='.env('CLIENT_RETURN_URL').'&response_type=code';

        return view('account.address-add')->with('url', $url);
    }

    /**
     * Update the address on record using data from third party service
     * TODO: Add third party info, update database.
     *
     * @return void
     */
    public function updateAddress()
    {
        $provider = new GenericProvider(
            [
                'clientId'                => env('CLIENT_ID'),
                'clientSecret'            => env('CLIENT_SECRET'),
                'redirectUri'             => 'http://localhost:8084/update_address',
                'urlAuthorize'            => env('API_AUTH_URL').'authorize',
                'urlAccessToken'          => env('API_AUTH_URL').'access_token',
                'urlResourceOwnerDetails' => env('API_URL').'api/merchant/data',
            ]
        );

        if (!isset($_GET['code'])) {
            $authorizationUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: '.$authorizationUrl);
            exit;

            // add csrf state check
        } else {
            try {
                $accessToken = $provider->getAccessToken(
                    'authorization_code', [
                        'code' => Input::get('code'),
                    ]
                );

                echo $accessToken->getToken()."\n";
                echo $accessToken->getExpires()."\n";

                // save to database

                $resourceOwner = $provider->getResourceOwner($accessToken);
                var_export($resourceOwner->toArray()); // test
            } catch (IdentityProviderException $e) {
                exit($e->getMessage());
            }
        }
    }

    /**
     * Retrieve new token using refresh token method
     * TODO: Replace with Laravel's passport.
     *
     * @return void
     */
    public function refreshToken()
    {
        $provider = new GenericProvider(
            [
            'clientId'                => env('CLIENT_ID'),
            'clientSecret'            => env('CLIENT_SECRET'),
            'redirectUri'             => 'http://localhost:8084/update_address',
            'urlAuthorize'            => env('API_AUTH_URL').'authorize',
            'urlAccessToken'          => env('API_AUTH_URL').'access_token',
            'urlResourceOwnerDetails' => env('API_URL').'api/merchant/data',

            ]
        );


        $currentAccessToken = ''; //get from database

        $newAccessToken = $provider->getAccessToken(
            'refresh_token', [
            'refresh_token' => $currentAccessToken->getRefreshToken(),
            ]
        );
    }

    /**
     * Fetch product from product_id.
     *
     * @param string $slug product_id
     *
     * @return View
     */
    public function productDetails($slug)
    {
        $product_id = Product::GetID($slug)->get()->toArray();
        $product = Product::findorFail($product_id[0]['id']);

        return view('shop.product', compact('product'));
    }

    /**
     * Get all products belonging to the category based on category_id.
     *
     * @param string $slug category_id
     *
     * @return View
     */
    public function category($slug)
    {
        $category_info = Category::GetInfo($slug)->get()->toArray();
        $category_name = '';
        $products = '';
        if (count($category_info) > 0) {
            $category_id = $category_info[0]['id'];
            $category_name = $category_info[0]['title'];
            $products = Product::ByCategory($category_id)->paginate(12);
        }

        return view('shop/products', compact('products', 'category_name'));
    }

    /**
     * View shopping cart (Session based).
     *
     * @return View
     */
    public function cart()
    {
        $cart_content = Cart::content();
        if (count($cart_content) < 1) {
            Session::flash('alert-warning', 'Empty Cart!');

            return redirect('/');
        } else {
            $total_price = 0.00;
            foreach ($cart_content as $item) {
                $total_price = (
                        filter_var(
                            $item->options->price, FILTER_SANITIZE_NUMBER_FLOAT,
                            FILTER_FLAG_ALLOW_FRACTION
                        ) * $item->qty
                    ) + $total_price;
            }

            return view('shop/cart', compact('cart_content', 'total_price'));
        }
    }

    /**
     * Add a product to Cart.
     *
     * @return mixed
     */
    public function addCart()
    {
        $item = Input::get('product_id');
        $product_summary = Product::ProductSummaryProductID($item)
            ->get()->toArray()[0];
        Cart::add(
            $item, $product_summary['title'], 1,
            filter_var(
                $product_summary['price'],
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION
            ),
            $product_summary
        );

        return redirect('/shop/cart');
    }

    /**
     * Remove product from cart.
     *
     * @return mixed
     */
    public function removeFromCart()
    {
        $rowId = Input::get('row_id');
        Cart::remove($rowId);

        return redirect('/shop/cart');
    }

    /**
     * Update quanity of product in cart.
     *
     * @return mixed
     */
    public function updateCart()
    {
        $rowId = Input::get('row_id');
        $qty = Input::get('qty');
        Cart::update($rowId, $qty);

        return redirect('/shop/cart');
    }

    /**
     * Add product to favourite (another Cart instance).
     *
     * @return mixed
     */
    public function addFavourite()
    {
        $item = Input::get('product_id');
        $product_summary = Product::ProductSummaryProductID($item)
            ->get()->toArray()[0];
        Cart::instance('favourites')->add(
            $item, $product_summary['title'], 1, filter_var(
                $product_summary['price'],
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION
            ),
            $product_summary
        );

        return redirect('/shop/favourites');
    }

    /**
     * Remove product from favourites.
     *
     * @return mixed
     */
    public function removeFavourite()
    {
        $rowId = Input::get('row_id');
        Cart::instance('favourites')->remove($rowId);

        return redirect('/shop/favourites');
    }

    /**
     * View favourites.
     *
     * @return View
     */
    public function favourites()
    {
        $favourites = Cart::instance('favourites')->content();
        if (count($favourites) < 1) {
            Session::flash('alert-warning', 'You have no saved items!');

            return redirect('/');
        } else {
            return view('shop/favourite', compact('favourites', 'total_price'));
        }
    }

    /**
     * Perform the checkout operation.
     *
     * @return mixed
     */
    public function checkOut()
    {
        $cart_content = Cart::content();
        $total_price = 0.00;

        foreach ($cart_content as $item) {
            $total_price = (
                    filter_var($item->options->price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) * $item->qty
                ) + $total_price;
        }

        $user = Auth::user();

        //TODO catch error and set which card to use
        $user->charge($total_price * 100);

        $invoice = new Invoice();
        $invoice->user_id = $user->id;
        $invoice->invoice_id = str_random(50);
        $invoice->order_no = rand(pow(10, 9) - 1, pow(10, 10) - 1);
        $invoice->sub_total = $total_price;
        $invoice->tax = env('TAX_RATE') * $total_price;
        $invoice->save();
        $invoice_id = $invoice->id;

        foreach ($cart_content as $item) {
            $invoice_item = new InvoiceItem();
            $invoice_item->invoice_id = $invoice_id;
            $invoice_item->product_id = $item->options->id;
            $invoice_item->qty = $item->qty;
            $invoice_item->save();
        }

        Cart::destroy();
        Session::flash('alert-success', 'Thank you for shopping with us!');

        return redirect('/account');
    }

    /**
     * Change shop currency.
     *
     * @param Request $request user request
     * @param string  $iso     currency code
     *
     * @return mixed
     */
    public function currency(Request $request, $iso)
    {
        $request->session()->flush();
        Session::put('shop_currency', $iso);

        return redirect(url()->previous());
    }

    /**
     * Empty cart and redirect to homepage.
     *
     * @return mixed
     */
    public function emptyCart()
    {
        Cart::destroy();

        return redirect('/');
    }

    /**
     * View page from reading page_id.
     *
     * @param string $title   Page Title
     * @param string $page_id page_id
     *
     * @return View
     */
    public function page($title, $page_id)
    {
        $page = Page::GetPage($page_id)[0];

        return view('shop/page', compact('page'));
    }

    /**
     * Return contact page.
     *
     * @return mixed
     */
    public function contact()
    {
        return View::make('pages.contact');
    }

    /**
     * Send email after user submits contact form.
     *
     * @param ContactFormRequest $request user request
     *
     * @return mixed
     */
    public function sendEmail(ContactFormRequest $request)
    {
        Mail::send(
            'emails.contact',
            [
                'name'    => $request->get('name'),
                'email'   => $request->get('email'),
                'message' => $request->get('message'),
            ], function ($message) {
                $message->from(env('MAIL_FROM'));
                $message->to(env('MAIL_TO'), env('MAIL_NAME'))->subject('Contact Form');
            }
        );

        return Redirect::to('contact')->with('message', 'Thanks for contacting us!');
    }

    /**
     * Search utility.
     *
     * @return View
     */
    public function search()
    {
        $rules = [
            'q' => 'required|alphaNum|min:2',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            Newsletter::subscribe(Input::get('q'));
            Session::flash('alert-danger', 'Invalid search');

            return redirect('/');
        } else {
            $products = Product::search(Input::get('q'));

            return view('shop/search', compact('products'));
        }
    }

    /**
     * Add email to newletter list.
     *
     * @return mixed
     */
    public function newsletter()
    {
        $rules = [
            'email' => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            Newsletter::subscribe(Input::get('email'));
            Session::flash('alert-danger', 'Invalid email');

            return redirect('/');
        } else {
            Newsletter::subscribe(Input::get('email'));
            Session::flash('alert-success', 'Thank you for subscribing to our newsletter');

            return redirect('/');
        }
    }
}
