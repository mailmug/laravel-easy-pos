<?php

namespace App\Livewire\Order;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On; 

class CartItem extends Component
{
    public $cartItem;

    public $currency_symbol;

    public $quantity;

    public $orderId;


    public function mount($cartItem, $orderId)
    {  
        $this->orderId = $orderId;
        $this->cartItem = $cartItem;
        $this->quantity = $cartItem->quantity;
    }

    #[On('cartUpdated')]
    public function cartUpdated(){
        $this->quantity = $this->cartItem->quantity;
    }


    public function removeFromCart()
    {   
        $product = Product::find( $this->cartItem->product_id );
        $product->quantity = $product->quantity + $this->quantity;
        $product->save();
        
        $this->quantity = 0;
        $this->cartItem->delete();
        $this->dispatch('cartUpdated');
    }


    public function updated(){
        if ($this->quantity > 0) {
            $product = Product::find( $this->cartItem->product_id );
            if( $product->quantity <  $this->quantity ){
                $this->quantity = $product->quantity;
            }
            $this->cartItem->quantity = $this->quantity;
            $product->quantity = $product->quantity + $this->cartItem->quantity;
            $product->save();  

            $this->cartItem->quantity = $this->quantity;
            $this->cartItem->save();

            $product->quantity = $product->quantity - $this->quantity;
            $product->save();
            $this->dispatch('cartUpdated');
        } 
    }

    public function render()
    {      
        return view('livewire.order.cart-item');
    }
}
