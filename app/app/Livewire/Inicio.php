<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Exception;


#[Layout("layouts.app")]
class Inicio extends Component
{
    public $productsComplete = array();
    public $product = "";
    public $searchFinished = false;
    public $moreThan5 = false;
    public $productsShown = 5;




    public function render()
    {
        return view('livewire.inicio');
    }

    public function search()
    {
        //set everything to default values 
        $this->productsComplete = array();
        $this->searchFinished = false;
        $this->moreThan5 = false;
        $this->productsShown = 5;

        //search in every supported store
        // try{
        //     $this->searchCoto();
        // }
        // catch(Exception $e){

        // }

        $this->searchMaxiconsumo();

        //final step and show products
        $this->prepareAndShow();
    }

    public function searchCoto()
    {
        $prods = [];
        $storeName = 'Coto';
        $mayoristPrice = 'non mayorist';
        $browserNonHeadless = new HttpBrowser(HttpClient::create());
        //Coto website

        $crawler = $browserNonHeadless->request('GET', 'https://www.cotodigital3.com.ar/sitios/cdigi/browse?_dyncharset=utf-8&Dy=1&Ntt=' . $this->product . '&Nty=1&Ntk=&siteScope=ok&_D%3AsiteScope=+&atg_store_searchInput=' . $this->product . '&idSucursal=200&_D%3AidSucursal=+&search=Ir&_D%3Asearch=+&_DARGS=%2Fsitios%2Fcartridges%2FSearchBox%2FSearchBox.jsp');

        //load all products as dom elements from coto store 

        foreach ($crawler->filter("[class='clearfix']") as $prod) {
            array_push($prods, $prod);
        }
        foreach ($crawler->filter("[class='clearfix first']") as $prod) {
            array_push($prods, $prod);
        }
        foreach ($crawler->filter("[class='clearfix first_max']") as $prod) {
            array_push($prods, $prod);
        }

        //not all products container have the same class

        foreach ($prods as $prod) {
            $crawlerProd = new Crawler($prod);
            $name = $crawlerProd->filter("[class='descrip_full']")->text();
            //get name and then check if it relates to the user search if it relates look for all the other product atributtes
            if ($this->checkIfSpecific($name)) {

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
                $this->loadProd($name, $price, $storeName, $img, $discount, $discountText, $mayoristPrice);
            }

            
        }
    }

    public function searchMaxiconsumo()
    {
        $storeName = 'Maxiconsumo';
        //store does not support discounts
        $discount = 'no';
        $discountText = 'None';
        $browserNonHeadless = new HttpBrowser(HttpClient::create());

        $crawler = $browserNonHeadless->request('GET', 'https://maxiconsumo.com/sucursal_capital/catalogsearch/result/?q=' . $this->product . '');


        foreach ($crawler->filter("[class='list-item item']") as $prod) {
            $crawlerProd = new Crawler($prod);
            $name = $crawlerProd->filter("[class='product-item-link']")->text();
            
            if($this->checkIfSpecific($name)){
                $price = $crawlerProd->filter("[class='price-box price-final_price']")->text();
                $img = $crawlerProd->filter("[class='product-image-photo']")->attr('src');
                $mayoristPrice = $crawlerProd->filter("[class='price-box price-final_price highest']")->text();

                $this->loadProd($name, $price, $storeName, $img, $discount, $discountText, $mayoristPrice);
            }
        }
        dd($this->productsComplete);
    }

    public function checkIfSpecific($name)
    {
        //product MUST HAVE at least coincidence in all letters of the user search except 1 example: arroz -> product name must have 4 letters of coincidence
        if (similar_text(strtolower($name), strtolower(str_replace(' ', '', $this->product))) >= strlen(str_replace(' ', '', $this->product)) - 1) {
            return true;
        } else {
            return false;
        }
    }

    public function loadProd($name, $price, $storeName, $img, $discount, $discountText, $mayoristPrice)
    {
        //load all products with the same structure for better handling
        $newProd = ([
            "name" => $name,
            "price" => $price,
            "storeName" => $storeName,
            "img" => $img,
            "discount" => $discount,
            "discountText" => $discountText,
            "mayoristPrice" => $mayoristPrice
        ]);

        array_push($this->productsComplete, $newProd);
    }

    public function prepareAndShow()
    {
        //check if the search returns more than 5 elements to make the more button visible
        if (count($this->productsComplete,) > 5) {
            $this->moreThan5 = true;
        }

        //sort products by price
        usort($this->productsComplete, function ($a, $b) {
            if ($a['price'] == $b['price']) {
                return 0;
            }
            return ($a['price'] < $b['price']) ? -1 : 1;
        });

        //indicate that the search is done
        $this->searchFinished = true;
    }

    public function back()
    {
        //return everything to initial values
        $this->productsComplete = array();
        $this->searchFinished = false;
        $this->moreThan5 = false;
    }

    public function showMore()
    {
        //add five more to the variable that tells how many products to show
        $this->productsShown = $this->productsShown + 5;
        if (count($this->productsComplete,) < $this->productsShown) {
            $this->moreThan5 = false;
        }
    }
}
