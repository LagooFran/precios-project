<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use App\Models\Product;
use Exception;

use function Livewire\Volt\mount;

#[Layout("layouts.app")]
class Inicio extends Component
{
    public $productsAnswer = array();
    public $product = "";
    public $searched = false;
    public $pos = 5;
    public $moreThan5 = false;



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



        $browser = new HttpBrowser(HttpClient::create());

        $prods = [];

        //Coto

        $crawler = $browser->request('GET', 'https://www.cotodigital3.com.ar/sitios/cdigi/browse?_dyncharset=utf-8&Dy=1&Ntt=' . $this->product . '&Nty=1&Ntk=&siteScope=ok&_D%3AsiteScope=+&atg_store_searchInput=' . $this->product . '&idSucursal=200&_D%3AidSucursal=+&search=Ir&_D%3Asearch=+&_DARGS=%2Fsitios%2Fcartridges%2FSearchBox%2FSearchBox.jsp');

        foreach ($crawler->filter("[class='clearfix']") as $prod) {
            array_push($prods, $prod);
        }
        foreach ($crawler->filter("[class='clearfix first']") as $prod) {
            array_push($prods, $prod);
        }
        foreach ($crawler->filter("[class='clearfix first_max']") as $prod) {
            array_push($prods, $prod);
        }


        foreach ($prods as $prod) {
            try {
                $crawlerProd = new Crawler($prod);
                $name = $crawlerProd->filter("[class='descrip_full']")->text();

                if (similar_text(strtolower($name), strtolower(str_replace(' ', '', $this->product))) >= strlen(str_replace(' ', '', $this->product)) - 1) {
                    $price = $crawlerProd->filter("[class='atg_store_newPrice']")->text();
                    $img = $crawlerProd->filter("[class='atg_store_productImage'] > img")->attr('src');
                    $discountText = $crawlerProd->filter("[class='info_discount']")->children()->text();
                    if ($discountText == $price) {
                        $discount = 'No';
                        $discountText = 'None';
                    } else {
                        $discount = 'Yes';
                        $discountText = strstr($discountText, 'OFERTA');
                    }

                    $newProd = ([
                        "name" => $name,
                        "price" => $price,
                        "storeId" => 'coto',
                        "img" => $img,
                        "discount" => $discount,
                        "discountText" => $discountText
                    ]);

                    array_push($this->productsAnswer, $newProd);
                }
            } catch (Exception $e) {
            }
        }

        $prods = [];

        // carrefour


   


        if (count($this->productsAnswer) > 5) {
            $this->moreThan5 = true;
        }


        usort($this->productsAnswer, function ($a, $b) {
            if ($a['price'] == $b['price']) {
                return 0;
            }
            return ($a['price'] < $b['price']) ? -1 : 1;
        });

        $this->searched = true;
    }

    public function back()
    {
        $this->productsAnswer = array();
        $this->searched = false;
        $this->moreThan5 = false;
    }

    public function showMore()
    {
        $this->pos = $this->pos + 5;
        if (count($this->productsAnswer) < $this->pos) {
            $this->moreThan5 = false;
        }
    }
}
