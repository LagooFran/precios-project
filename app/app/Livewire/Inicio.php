<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;
use App\Models\Product;
use Exception;

use function Livewire\Volt\mount;

#[Layout("layouts.app")]
class Inicio extends Component
{
    public $product = "";
    public $productsAnswer = array();
    public $searched = false;
    public $moreThan5 = false;
    public $pos = 5;




    public function render()
    {
        return view('livewire.inicio');
    }



    public function search()
    {
        $this->productsAnswer = array();
        $this->searched = false;
        $this->moreThan5 = false;
        $this->pos = 5;



        // $browser = new HttpBrowser(HttpClient::create());

        // $prods = [];

        // //Coto

        // $crawler = $browser->request('GET', 'https://www.cotodigital3.com.ar/sitios/cdigi/browse?_dyncharset=utf-8&Dy=1&Ntt=' . $this->product . '&Nty=1&Ntk=&siteScope=ok&_D%3AsiteScope=+&atg_store_searchInput=' . $this->product . '&idSucursal=200&_D%3AidSucursal=+&search=Ir&_D%3Asearch=+&_DARGS=%2Fsitios%2Fcartridges%2FSearchBox%2FSearchBox.jsp');

        // foreach ($crawler->filter("[class='clearfix']") as $prod) {
        //     array_push($prods, $prod);
        // }
        // foreach ($crawler->filter("[class='clearfix first']") as $prod) {
        //     array_push($prods, $prod);
        // }
        // foreach ($crawler->filter("[class='clearfix first_max']") as $prod) {
        //     array_push($prods, $prod);
        // }


        // foreach ($prods as $prod) {
        //     try {
        //         $crawlerProd = new Crawler($prod);
        //         $name = $crawlerProd->filter("[class='descrip_full']")->text();

        //         if (similar_text(strtolower($name), strtolower(str_replace(' ', '', $this->product))) >= strlen(str_replace(' ', '', $this->product)) - 1) {
        //             $price = $crawlerProd->filter("[class='atg_store_newPrice']")->text();
        //             $img = $crawlerProd->filter("[class='atg_store_productImage'] > img")->attr('src');
        //             $discountText = $crawlerProd->filter("[class='info_discount']")->children()->text();
        //             if ($discountText == $price) {
        //                 $discount = 'No';
        //                 $discountText = 'None';
        //             } else {
        //                 $discount = 'Yes';
        //                 $discountText = strstr($discountText, 'OFERTA');
        //             }

        //             $newProd = ([
        //                 "name" => $name,
        //                 "price" => $price,
        //                 "storeId" => 'coto',
        //                 "img" => $img,
        //                 "discount" => $discount,
        //                 "discountText" => $discountText
        //             ]);

        //             array_push($this->productsAnswer, $newProd);
        //         }
        //     } catch (Exception $e) {
        //     }
        // }

        $prods = [];

        // carrefour
        $browserFactory = new BrowserFactory('./chrome-linux/chrome');
        $browser = $browserFactory->createBrowser();
        $page = $browser->createPage();
        $page->setViewport(1920, 1080)->await();
        $page->navigate('https://www.carrefour.com.ar/' . $this->product . '?_q=' . $this->product . '&map=ft')->waitForNavigation(Page::FIRST_MEANINGFUL_PAINT, 30000);
        $prods = $page->dom()->querySelectorAll('.valtech-carrefourar-product-summary-status-0-x-container.valtech-carrefourar-product-summary-status-0-x-productNotAdded.flex.flex-column.h-100');
        foreach ($prods as $prod) {
            $name = $prod->querySelector('.vtex-product-summary-2-x-productBrand.vtex-product-summary-2-x-brandName.t-body')->getText();
            $price = str_replace('$&nbsp;','',$prod->querySelector('.valtech-carrefourar-product-price-0-x-currencyContainer')->getText());
            $img = $prod->querySelector('.vtex-product-summary-2-x-imageNormal.vtex-product-summary-2-x-image')->getAttribute('src');

            $newProd = ([
                "name" => $name,
                "price" => $price,
                "storeId" => 'Carrefour',
                "img" => $img,
                "discount" => '',
                "discountText" => ''
            ]);

            array_push($this->productsAnswer, $newProd);
        }

        dd($this->productsAnswer);


        //     if (count($this->productsAnswer) > 5) {
        //         $this->moreThan5 = true;
        //     }


        //     usort($this->productsAnswer, function ($a, $b) {
        //         if ($a['price'] == $b['price']) {
        //             return 0;
        //         }
        //         return ($a['price'] < $b['price']) ? -1 : 1;
        //     });

        //     $this->searched = true;
        // }

        // public function back()
        // {
        //     $this->productsAnswer = array();
        //     $this->searched = false;
        //     $this->moreThan5 = false;
        // }

        // public function showMore()
        // {
        //     $this->pos = $this->pos + 5;
        //     if (count($this->productsAnswer) < $this->pos) {
        //         $this->moreThan5 = false;
        //     }
    }
}
