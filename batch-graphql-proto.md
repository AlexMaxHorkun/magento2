### Testing
To test the performance:
* create 4 products
* link them to each other as "Related products" and "Cross-Sell Products"
  (min 2 products per relation)
* Run the query:
  ```
  query ($filter: ProductAttributeFilterInput!) {
    products (filter: $filter) {
      items {
        __typename
        id
        sku
        related_products {
          sku
          related_products {
            sku
          }
          crosssell_products {
            sku
          }
        }
        crosssell_products {
          sku
          related_products {
            sku
          }
          crosssell_products {
            sku
          }
        }
      }
      total_count
    }
  }
  ```
  with variables:
  ```
  {"filter": {"category_id": {"eq": ,<your Category ID>}}}
  ```
 
### Test results:
* Performance difference __~200ms__ faster with batch processing in Developer mode
* __2__ _resolve()_ calls to _RelatedProducts/CrossSellProducts_ resolvers
  with batch processing vs __20__ calls with existing resolvers
 
 
### Remote ASYNC/SYNC Service Contract Test Results:
* 1000ms difference in favour of asynchronous HTTP requests (1700ms/2700ms)
* 4 HTTP requests to the service contract via REST API made
