<div >
    <form wire:submit="search">
        <p class="text-green-200 text-2xl">hola</p>
        <label class="text-white">Que producto quiere buscar?</label>
        <input class="" type="text" name="product" wire:model="product">
        <button class="text-green-200 hover:text-red-400" type="submit">Buscar</button>
    </form>
    <button class="text-green-200 hover:text-red-400" wire:click="back">Volver</button>

    @if($searched)
        @foreach($productsAnswer as $product)
            <p class="text-green-200 text-2xl" wire:key="{{$product['name']}}">{{$product['name']}}<p>
        @endforeach
    @endif
</div>
