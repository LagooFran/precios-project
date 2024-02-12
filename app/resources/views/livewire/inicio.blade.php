<div >
    <form wire:submit="search">
        <p class="text-green-200 text-2xl">hola</p>
        <label class="text-white">Que producto quiere buscar?</label>
        <input class="" type="text" name="product" wire:model="product">
        <button class="text-green-200 hover:text-red-400" type="submit">Buscar</button>
    </form>
    <button class="text-green-200 hover:text-red-400" wire:click="back">Volver</button>

    @if($searchFinished)
    @if(!$searchSuccessCoto)
    <p class="text-red-200 text-2xl">Error durante la busqueda de productos en: Coto<p>
    @endif
    @if(!$searchSuccessMaxiconsumo)
    <p class="text-red-200 text-2xl">Error durante la busqueda de productos en: Maxiconsumo<p>
    @endif
    <div class="flex flex-col gap-3">
        @foreach(array_slice($productsComplete,0,$productsShown) as $product)
        <div wire:key="{{$product['name']}}" class="flex flex-col">
            <p class="text-green-200 text-2xl">{{$product['name']}}<p>
            <p class="text-green-200 text-2xl">{{$product['price']}}<p>
            <p class="text-green-200 text-2xl">{{$product['discountText']}}<p>
        </div>
        @endforeach
    </div>
    @endif
    
    @if($moreThan5)
        <button class="text-green-200 hover:text-red-400" wire:click="showMore">Mostrar mas</button>
    @endif
</div>
