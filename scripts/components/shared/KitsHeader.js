import {cart} from '../../data/cart.js';
import {WindowUtils} from '../../utils/WindowUtils.js';
import {ComponentV2} from '../ComponentV2.js';

export class KitsHeader extends ComponentV2 {
  events = {
    'click .js-hamburger-menu-toggle':
      (event) => this.#toggleDropdownMenu(event),
    'keyup .js-search-bar':
      (event) => this.#handleSearchBarInput(event),
    'click .js-search-button':
      (event) => this.#handleSearchClick(event)
  };

  render() {
    const searchParams = new URLSearchParams(WindowUtils.getSearch());
    const searchText = searchParams.get('search') || '';
    const totalCartQuantity = cart.calculateTotalQuantity();

    this.element.innerHTML = `
      <section class="left-section">
        <a href="index.php" class="header-link">
          <img class="kits-logo"
            src="images/kits-logo-white.png">
          <img class="kits-mobile-logo"
            src="images/kits-mobile-logo-white.png">
        </a>
      </section>

      <section class="middle-section">
        <input class="js-search-bar search-bar" type="text"
          placeholder="Search" value="${searchText}"
          data-testid="search-input">

        <button class="js-search-button search-button"
          data-testid="search-button">
          <img class="search-icon" src="images/icons/search-icon.png">
        </button>
      </section>

      <section class="right-section">
        <a class="orders-link header-link" href="orders.php">
          <span class="returns-text">Returns</span>
          <span class="orders-text">& Orders</span>
        </a>

        <a class="cart-link header-link" href="checkout.php">
          <img class="cart-icon" src="images/icons/cart-icon.png">
          <div class="js-cart-quantity cart-quantity"
            data-testid="cart-quantity">
            ${totalCartQuantity}
          </div>
          <div class="cart-text">Cart</div>
        </a>
      </section>

      <section class="right-section-mobile">
        <img class="js-hamburger-menu-toggle hamburger-menu-toggle"
          src="images/icons/hamburger-menu.png"
          data-testid="hamburger-menu-toggle">
      </section>

      <div class="js-hamburger-menu-dropdown hamburger-menu-dropdown"
        data-testid="hamburger-menu-dropdown">
        <a class="hamburger-menu-link" href="orders.php">
          Returns & Orders
        </a>
        <a class="hamburger-menu-link" href="checkout.php">
          Cart (<span class="js-cart-quantity-mobile cart-quantity-mobile"
            data-testid="cart-quantity-mobile">${totalCartQuantity}</span>)
        </a>
      </div>
    `;
  }

  updateCartCount() {
    const totalCartQuantity = cart.calculateTotalQuantity();
    this.element.querySelector('.js-cart-quantity').textContent = totalCartQuantity;
    this.element.querySelector('.js-cart-quantity-mobile').textContent = totalCartQuantity;
  }

  #toggleDropdownMenu() {
    const dropdownMenu = this.element.querySelector('.js-hamburger-menu-dropdown');
    const isOpened = dropdownMenu.classList.contains('hamburger-menu-opened');

    if (!isOpened) {
      dropdownMenu.classList.add('hamburger-menu-opened');
    } else {
      dropdownMenu.classList.remove('hamburger-menu-opened');
    }
  }

  #handleSearchBarInput(event) {
    if (event.key === 'Enter') {
      this.#searchProducts(
        this.element.querySelector('.js-search-bar').value
      );
    }
  }

  #handleSearchClick() {
    this.#searchProducts(
      this.element.querySelector('.js-search-bar').value
    );
  }

  #searchProducts(searchText) {
    if (!searchText) {
      WindowUtils.setHref('./catalog.php');
      return;
    }

    WindowUtils.setHref(`./?search=${searchText}`);
  }
}
