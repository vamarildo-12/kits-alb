import '../packages/uuid.js';
import {MoneyUtils} from '../utils/MoneyUtils.js';
import {deliveryOptions} from './deliveryOptions.js';
import {products} from './products.js';

export class Cart {
  #items;

  constructor() {
    this.loadFromStorage();
  }

  loadFromStorage() {
    this.#items = localStorage.getItem('cart')
      ? JSON.parse(localStorage.getItem('cart'))
      : [{
          id: 'b5f9b6c7-dcc7-4de4-8df9-128f2c9e24fa',
          productId: 'e43638ce-6aa0-4b85-b27f-e1d07eb678c6',
          quantity: 2,
          deliveryOptionId: 'f297d333-a5c4-452f-840b-15a662257b3f',
        }, {
          id: '7a8151b3-39d5-4ff6-8755-5abfa9be7102',
          productId: '15b6fc6f-327a-4ec4-896f-486349e85a3d',
          quantity: 1,
          deliveryOptionId: '6e2dd65a-6665-4f24-bcdc-f2ecdbc6e156'
        }];
  }

  get items() {
    return this.#items;
  }

  async addToCart(productId, quantity) {
    console.log('Adding to cart:', { productId, quantity });
  
    return fetch('/kits-alb/backend/add-to-cart.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        product_id: productId.toString(), // Ensure productId is a string
        quantity: parseInt(quantity),
      })
    })
    .then(response => response.json())
    .then(data => {
      console.log('Response:', data);
      return data.cart_count || 0;
    })
    .catch(error => {
      console.error('Error:', error);
      return 0;
    });
  }


  async calculateTotalQuantity() {
    try {
        const response = await fetch('/kits-alb/backend/get-cart-count.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({})  // No product_id needed, just calculate the total quantity
        });

        const data = await response.json();
        console.log('Cart count data:', data);
        
        // Return the total cart count or 0 if not found
        return data.cart_count || 0;
    } catch (error) {
        console.error('Cart count error:', error);
        return 0;
    }
}

  updateDeliveryOption(cartItemId, deliveryOptionId) {
    const cartItem = this.#items.find(cartItem => {
      return cartItem.id === cartItemId;
    });

    cartItem.deliveryOptionId = deliveryOptionId;
    this.#saveToStorage();
  }

  
  calculateCosts() {
    const productCostCents = this.#calculateProductCost();
    const shippingCostCents = this.#calculateShippingCosts();
    const taxCents = (productCostCents + shippingCostCents) * MoneyUtils.taxRate;
    const totalCents = Math.round(productCostCents + shippingCostCents + taxCents);

    return {
      productCostCents,
      shippingCostCents,
      taxCents,
      totalCents
    };
  }

  reset() {
    this.#items = [];
    this.#saveToStorage();
  }

  isEmpty() {
    return this.#items.length === 0;
  }

  #isSameVariation(variation1, variation2) {
    // Ensure both variations are objects before comparing
    if (!variation1) variation1 = {};
    if (!variation2) variation2 = {};
  
    console.log('Comparing variations:', variation1, variation2);
  
    // Compare the number of keys in both variations
    const variation1Keys = Object.keys(variation1);
    const variation2Keys = Object.keys(variation2);
  
    if (variation1Keys.length !== variation2Keys.length) {
      return false; // Different number of keys means they are not the same
    }
  
    // Compare each key-value pair in the variations
    for (const key of variation1Keys) {
      if (variation1[key] !== variation2[key]) {
        return false;
      }
    }
  
    return true;
  }
  #calculateProductCost() {
    let productCost = 0;

    this.#items.forEach(cartItem => {
      const product = products.findById(cartItem.productId);
      productCost += product.priceCents * cartItem.quantity;
    });

    return productCost;
  }

  #calculateShippingCosts() {
    let shippingCost = 0;

    this.#items.forEach(cartItem => {
      const deliveryOption = deliveryOptions.findById(cartItem.deliveryOptionId);
      shippingCost += deliveryOption.costCents;
    });

    return shippingCost;
  }

  #saveToStorage() {
    localStorage.setItem(
      'cart',
      JSON.stringify(this.#items)
    );
  }
}

export const cart = new Cart();
