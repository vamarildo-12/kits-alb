import { KitsHeader } from '../components/shared/KitsHeader.js';
import { products } from '../data/products.js';

products.loadFromBackend().then(() => {
  // Initialize the KitsHeader
  const kitsHeader = new KitsHeader('.js-kits-header').create();
  
  // Product page specific functionality
  setupProductPage();
});

function setupProductPage() {
  // Wait for the DOM to be fully loaded
  document.addEventListener("DOMContentLoaded", function() {
    const addToCartButton = document.querySelector(".add-to-cart");
    const returnButton = document.querySelector(".return-link");

    // Add event listener for the Add to Cart button
    if (addToCartButton) {
      addToCartButton.addEventListener("click", function() {
        const productId = addToCartButton.getAttribute("data-product-id");
        const size = document.getElementById("size").value;

        // Send data to cart (you can extend this part based on your cart system)
        console.log(`Product ID: ${productId}, Size: ${size}`);
        alert("Product added to cart!");
      });
    }

    // Return button functionality
    if (returnButton) {
      returnButton.addEventListener("click", function() {
        window.location.href = "catalog.php";
      });
    }

    // Render related products
    renderRelatedProducts();
  });
}

function renderRelatedProducts() {
  const relatedProductsList = document.getElementById("related-products-list");

  if (!relatedProductsList) {
    console.error("Related products container not found.");
    return;
  }

  // Fetch related products data from the backend
  const relatedProductsData = JSON.parse(relatedProductsList.dataset.relatedProducts || "[]");

  if (relatedProductsData.length === 0) {
    relatedProductsList.innerHTML = "<p>No related products available.</p>";
    return;
  }

  // Create HTML for related products
  const productHTML = relatedProductsData
    .map(product => `
      <div class="related-product">
        <a href="view-product.php?id=${product.id}">
          <img src="${product.image}" alt="${product.name}">
          <p class="product-name">${product.name}</p>
          <p class="product-price">$${(product.priceCents / 100).toFixed(2)}</p>
        </a>
      </div>
    `)
    .join("");

  // Inject the HTML into the related products container
  relatedProductsList.innerHTML = productHTML;
}
