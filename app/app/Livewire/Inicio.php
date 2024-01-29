<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

#[Layout("layouts.app")]
class Inicio extends Component
{
    public $product = "";
    public function render()
    {
        return view('livewire.inicio');
    }

    public function search(){
        $names = [];
        $prices = [];
        $prods = [];
        $browser = new HttpBrowser(HttpClient::create()); 
        $crawler = $browser->request('GET', 'https://www.cotodigital3.com.ar/sitios/cdigi/browse?_dyncharset=utf-8&Dy=1&Ntt='.$this->product.'&Nty=1&Ntk=&siteScope=ok&_D%3AsiteScope=+&atg_store_searchInput='.$this->product.'&idSucursal=200&_D%3AidSucursal=+&search=Ir&_D%3Asearch=+&_DARGS=%2Fsitios%2Fcartridges%2FSearchBox%2FSearchBox.jsp');
        
        foreach($crawler->filter("[class='clearfix']") as $prod){ array_push($prods, $prod); }
        foreach($crawler->filter("[class='clearfix first']") as $prod){ array_push($prods, $prod); }
        foreach($crawler->filter("[class='clearfix first_max']") as $prod){ array_push($prods, $prod); }


        foreach($prods as $prod){
            $crawlerProd = new Crawler($prod);
            $name = $crawlerProd->filter("[class='descrip_full']")->text();
            array_push($names, $name);
            $price = $crawlerProd->filter("[class='atg_store_newPrice']")->text();
            array_push($prices, $price);
        }
        dd($names, $prices);
    }
}
