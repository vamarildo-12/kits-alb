import {KitsHeader} from '../components/shared/KitsHeader.js';
import {OrderTracking} from '../components/tracking/OrderTracking.js';
import {products} from '../data/products.js';

products.loadFromBackend().then(() => {
  new KitsHeader('.js-kits-header').create();
  new OrderTracking('.js-order-tracking').create();
});
