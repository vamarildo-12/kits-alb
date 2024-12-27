import { cart } from '../../data/cart.js';
import { deliveryOptions } from '../../data/deliveryOptions.js';
import { products } from '../../data/products.js';
import { MoneyUtils } from '../../utils/MoneyUtils.js';
import { DomUtils } from '../../utils/DomUtils.js';
import { DateUtils } from '../../utils/DateUtils.js';
import { ComponentV2 } from '../ComponentV2.js';
import { VariationUtils } from '../../utils/VariationUtils.js';

export class CartSummary extends ComponentV2 {
  events = {
    'click .js-delivery-option':(event) => this.#selectDeliveryOption(event),
    'keyup .js-new-quantity-input': (event) => this.#handleQuantityInput(event),
    'click .js-cancel-quantity-update': (event) => this.#cancelUpdateQuantity(event),
    'click .js-delete-quantity-link': (event) => this.#handleDeleteLinkClick(event),
  };

  #paymentSummary;
  #checkoutHeader;

  setPaymentSummary(paymentSummary) {
    this.#paymentSummary = paymentSummary;
  }

  setCheckoutHeader(checkoutHeader) {
    this.#checkoutHeader = checkoutHeader;
  }

  /**
   * Render the cart, including fetching data and rendering cart items.
   */
  async render() {
    try {
      console.log('Rendering CartSummary component...');
      await this.fetchCartData();
      this.#attachEventListeners();  // Ensure event listeners are attached after render
    } catch (error) {
      console.error('Error rendering cart:', error);
      this.#renderErrorMessage('Unable to load cart. Please try again later.');
    }
  }

  #attachEventListeners() {
    // Delegate events for dynamically added cart items.
    this.element.addEventListener('click', (event) => {
      if (event.target.matches('.js-delivery-option-input')) {
        this.#selectDeliveryOption(event);
      } else if (event.target.matches('.js-delete-quantity-link')) {
        this.#handleDeleteLinkClick(event);
      }
    });

    this.element.addEventListener('keyup', (event) => {
      if (event.target.matches('.js-new-quantity-input')) {
        this.#handleQuantityInput(event);
      }
    });
  }
  /**
   * Fetch cart data from the backend.
   */
  async fetchCartData() {
    try {
      console.log('Fetching cart data from backend...');
      const response = await fetch('/kits-alb/backend/get-cart-products.php');

      if (!response.ok) {
        throw new Error(`Failed to fetch cart data. Status: ${response.status}`);
      }

      const cartData = await response.json();
      console.log('Cart data:', cartData);  // Log the entire response to ensure it's correct

      // Render cart items
      this.renderCartItems(cartData);
    } catch (error) {
      console.error('Error fetching cart data:', error);
      this.#renderErrorMessage('Unable to load cart items. Please try again later.');
    }
  }

  /**
   * Render the cart items.
   * @param {Array} cartData - The cart data fetched from the backend.
   */
  renderCartItems(cartData) {
    if (cartData.length === 0) {
      this.#renderEmptyCartMessage();
      return;
    }

    let cartSummaryHTML = '';
    cartData.forEach(cartItem => {
      
      cartSummaryHTML += `
  <div class="js-cart-item cart-item-container" data-cart-item-id="${cartItem.productId}">
    <div class="delivery-date">
      <span class="js-delivery-date"></span>
    </div>
    <img class="product-image" src="${cartItem.image}" alt="${cartItem.name}">
    <div class="product-details">
      <div class="product-name">${cartItem.name}</div>
      <div class="product-price">${MoneyUtils.formatMoney(cartItem.priceCents * cartItem.quantity)}</div>
    </div>
    <div class="quantity-container js-quantity-container">
      Quantity: 
      <span class="js-quantity-label">${cartItem.quantity}</span>
      <input 
        class="js-quantity-input js-new-quantity-input" 
        type="number" 
        value="${cartItem.quantity}" 
        min="1" 
        data-cart-item-id="${cartItem.productId}" />
      <span class="js-delete-quantity-link link-primary">Delete</span>
    </div>
    <div class="delivery-options">
      <div class="delivery-options-title">
        Choose a delivery option:
      </div>
      ${this.#createDeliveryOptionsHTML(cartItem)}
    </div>
  </div>`;
    });

    this.element.innerHTML = cartSummaryHTML;
  }

  #createDeliveryOptionsHTML(cartItem) {
    let deliverOptionsHTML = '';

    deliveryOptions.all.forEach(deliveryOption => {
      const id = deliveryOption.id;
      const costCents = deliveryOption.costCents;
      const deliveryDate = deliveryOption.calculateDeliveryDate();

      const shippingText = costCents === 0
        ? 'FREE Shipping'
        : `${MoneyUtils.formatMoney(costCents)} - Shipping`;

      deliverOptionsHTML += `
        <div class="js-delivery-option delivery-option"
          data-delivery-option-id="${id}" data-testid="delivery-option-${id}">
          <input
            class="js-delivery-option-input delivery-option-input"
            ${cartItem.deliveryOptionId === id ? 'checked' : ''}
            name="${cartItem.id}-delivery-option"
            type="radio"
            data-testid="delivery-option-input"
          >
          <div>
            <div class="delivery-option-date">
              ${DateUtils.formatDateWeekday(deliveryDate)}
            </div>
            <div class="delivery-option-price">
              ${shippingText}
            </div>
          </div>
        </div>
      `;
    });

    return deliverOptionsHTML;
  }

  /**
   * Handles delivery option selection.
   * @param {Event} event - The click event on the delivery option.
   */
  #selectDeliveryOption(event) {
    // Ensure the event is triggered by a delivery option
    const radioInput = event.target;

    // Check if this is the radio input (should be the event target)
    if (!radioInput || !radioInput.classList.contains('js-delivery-option-input')) {
      console.log('Clicked target is not a delivery option radio input.');
      return;
    }

    // Find the parent cart item element
    const cartItemElem = radioInput.closest('.js-cart-item');
    const cartItemId = cartItemElem.getAttribute('data-cart-item-id');
    const deliveryOptionId = radioInput.closest('.js-delivery-option').getAttribute('data-delivery-option-id');

    // Find the corresponding delivery option from the deliveryOptions list
    const deliveryOption = deliveryOptions.findById(deliveryOptionId);
    if (!deliveryOption) {
      console.error("Delivery option not found!");
      return;
    }

    // Calculate the delivery date using the deliveryOption's method
    const newDeliveryDate = deliveryOption.calculateDeliveryDate();
    const formattedDeliveryDate = DateUtils.formatDateWeekday(newDeliveryDate);

    // Update the UI for the selected cart item (the delivery date)
    cartItemElem.querySelector('.js-delivery-date').textContent = `Delivery date: ${formattedDeliveryDate}`;
    this.#updateHeaderWithDeliveryOption(deliveryOption);
  }


/**
 * Update the header with the selected delivery option details.
 * @param {Object} deliveryOption - The selected delivery option.
 */
#updateHeaderWithDeliveryOption(deliveryOption) {
    const shippingCost = MoneyUtils.formatMoney(deliveryOption.costCents);
    const deliveryDate = DateUtils.formatDateWeekday(deliveryOption.calculateDeliveryDate());

    // Assuming you have a method on #checkoutHeader to update the delivery info:
    this.#checkoutHeader.updateDeliveryInfo(shippingCost, deliveryDate);
}


  #renderEmptyCartMessage() {
    this.element.innerHTML = `
      <div data-testid="empty-cart-message">
        Your cart is empty.
      </div>
      <a class="button-primary view-products-link" href="catalog.php" data-testid="view-products-link">
        View products
      </a>
    `;
  }

  #renderErrorMessage(message) {
    this.element.innerHTML = `<div class="error-message">${message}</div>`;
  }
  

  #handleQuantityInput(event) {
    const inputElement = event.target; // Directly get the input element that triggered the event
    
    if (!inputElement.classList.contains('js-new-quantity-input')) {
      return; // Exit early if the event wasn't triggered by the correct input element
    }
  
    if (event.key === 'Enter') {
      this.#updateQuantity(inputElement);  // Pass the input element instead of the container
    } else if (event.key === 'Escape') {
      // Get the current quantity from the label (this is passed as an argument)
      const currentQuantity = inputElement.closest('.js-quantity-container')
                                          ?.querySelector('.js-quantity-label')
                                          ?.textContent;
  
      if (!currentQuantity) {
        console.error("Current quantity not found. Can't cancel.");
        return; // Exit early if current quantity is not found
      }
  
      // Extract digits using regex
      const digitsOnly = currentQuantity.replace(/\D/g, ''); // Removes all non-digit characters
      this.#cancelUpdateQuantity(inputElement, digitsOnly);  // Pass the input element and current quantity
    }
  }
  
  #cancelUpdateQuantity(inputElement, currentQuantity) {
    // Ensure the container and input element are valid
    const quantityContainer = inputElement.closest('.js-quantity-container');
    
    if (!quantityContainer) {
      console.error('Quantity container not found!');
      return;
    }
  
    // Hide the input element and show the label
    quantityContainer.classList.remove('is-updating-quantity');
    
    // Set the input value to the passed current quantity
    inputElement.value = currentQuantity;
  }

  

  // Handle adding products to the cart (send the difference to backend)
  async #addProductsToCart(productId, quantityToAdd) {
    console.log(`Adding ${quantityToAdd} products to cart with ID: ${productId}`);
    const userId = await this.#getUserId();

    if (!userId) {
        window.location.href = 'login.php';  // Redirect to login if no user ID
        return;
    }

    const addToCartPromises = [];
    for (let i = 0; i < quantityToAdd; i++) {
        addToCartPromises.push(this.#sendAddToCartRequest(productId));  // Push promises to the array
    }

    await Promise.all(addToCartPromises);
    this.#checkoutHeader.updateCartCount();
}

async #sendAddToCartRequest(productId) {
    const userId = await this.#getUserId();

    if (!userId) {
        console.error("User is not logged in");
        window.location.href = 'login.php';  // Redirect to login if no user ID
        return;
    }

    const basePath = window.location.origin + '/kits-alb/backend/';
    const response = await fetch(`${basePath}/add-to-cart.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId, product_id: productId }),
    });

    const data = await response.json();
    console.log('Add to Cart Response:', data);
    if (data.status === 'Product added to cart' || data.status === 'Product quantity updated in cart') {
        console.log("Product added to cart successfully");
    } else {
        console.error(data.status);
    }
}

// Handle removing products from the cart (send the difference to backend)
#removeSomeProductsFromCart(productId, quantityToRemove) {
  fetch('/kits-alb/backend/remove-some-from-cart.php', {
      method: 'POST',
      body: JSON.stringify({
          product_id: productId,
          quantity: quantityToRemove  // Remove the excess quantity (N)
      }),
      headers: {
          'Content-Type': 'application/json',
      },
  })
  .then(response => response.json())
  .then(data => {
      if (data.status === 'Product removed from cart') {
          console.log(`Successfully removed ${quantityToRemove} items from the cart.`);
          this.#checkoutHeader.updateCartCount();
      } else {
          console.error('Failed to remove products from cart:', data.message);
      }
  })
  .catch(error => {
      console.error('Error removing products from cart:', error);
  });
}
  
#updateQuantity(inputElement) {
  const newQuantity = parseInt(inputElement.value, 10); // Get the new quantity from the input element

  if (newQuantity < 1) {
    alert('Quantity must be at least 1.');
    return; // Exit if the quantity is invalid
  }

  const cartItemContainer = inputElement.closest('.js-cart-item');
  const cartItemId = cartItemContainer.getAttribute('data-cart-item-id');

  const currentQuantityLabel = cartItemContainer.querySelector('.js-quantity-label');
  const currentQuantity = parseInt(currentQuantityLabel.textContent, 10);

  if (newQuantity === currentQuantity) {
    console.log("No change in quantity. Exiting...");
    return; // If the quantity hasn't changed, exit
  }

  const quantityDifference = newQuantity - currentQuantity;

  // Handle adding/removing products based on the quantity change
  if (quantityDifference > 0) {
    console.log(`Adding ${quantityDifference} item(s) to cart for product ID: ${cartItemId}`);
    this.#addProductsToCart(cartItemId, quantityDifference);
  } else if (quantityDifference < 0) {
    console.log(`Removing ${Math.abs(quantityDifference)} item(s) from cart for product ID: ${cartItemId}`);
    this.#removeSomeProductsFromCart(cartItemId, Math.abs(quantityDifference));
  }

  // Update the current quantity label with the new quantity
  currentQuantityLabel.textContent = newQuantity;

  // Optionally, log or handle further UI updates here
  console.log(`Updated quantity for product ID: ${cartItemId} to ${newQuantity}`);
}



  /**
   * Handle the deletion of an item when the "Delete" button is clicked.
   * @param {Event} event - The event from the "Delete" button click.
   */
  #handleDeleteLinkClick(event) {
    console.log("Boton clicked");
    // Ensure the event target is a delete link
    const deleteLink = event.target.closest('.js-delete-quantity-link');
    
    if (!deleteLink) {
      console.error('Delete link not found');
      return;  // Prevent further processing if the target is not the delete link
    }
  
    const cartItemContainer = deleteLink.closest('.js-cart-item');
    
    if (!cartItemContainer) {
      console.error('Cart item container not found');
      return;  // Prevent further processing if the cart item container is not found
    }
  
    const cartItemId = cartItemContainer.getAttribute('data-cart-item-id');
    
    if (!cartItemId) {
      console.error('Cart item ID not found');
      return;  // Prevent further processing if no cart item ID is found
    }
  
    // Proceed with backend removal and UI update
    this.#removeFromCart(cartItemId);
  
    // Remove the item from the UI
    this.#removeFromCartSummary(cartItemContainer);
  }
  /**
   * Remove an item from the cart on the backend.
   * @param {String} cartItemId - The ID of the item to remove from the cart.
   */
  #removeFromCart(cartItemId) {
    fetch('/kits-alb/backend/remove-from-cart.php', {
      method: 'POST',
      body: JSON.stringify({ product_id: cartItemId }),
      headers: {
        'Content-Type': 'application/json',
      },
    })
    .then(response => {
      if (!response.ok) {
        throw new Error(`Request failed with status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        this.#checkoutHeader.updateCartCount();
        console.log('Item removed from cart');
        
      } else {
        console.error('Error removing item:', data.error || 'Unknown error');
      }
    })
    .catch(error => {
      console.error('Error with request or response:', error);
    });
}
  
  #removeFromCartSummary(cartItemElement) {
    DomUtils.removeElement(cartItemElement);

    if (this.element.querySelectorAll('.js-cart-item').length === 0) {
      this.#renderEmptyCartMessage();
    }
  }

   async #getUserId() {
    const basePath = window.location.origin + '/kits-alb/backend/';
    const response = await fetch(`${basePath}/get-user-id.php`);
    const data = await response.json();
    return data.userId || null;
  }
}

