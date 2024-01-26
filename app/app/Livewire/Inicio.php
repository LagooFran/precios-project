<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use App\Livewire\client;

#[Layout("layouts.app")]
class Inicio extends Component
{
    public $product = "";
    public function render()
    {
        return view('livewire.inicio');
    }

    public function search(){
        $crawler = new HttpBrowser(HttpClient::create());
        $crawler->request('GET', 'https://www.cotodigital3.com.ar/sitios/cdigi/browse?_dyncharset=utf-8&Dy=1&Ntt='.$this->product.'&Nty=1&Ntk=&siteScope=ok&_D%3AsiteScope=+&atg_store_searchInput='.$this->product.'&idSucursal=200&_D%3AidSucursal=+&search=Ir&_D%3Asearch=+&_DARGS=%2Fsitios%2Fcartridges%2FSearchBox%2FSearchBox.jsp');
    }

}
