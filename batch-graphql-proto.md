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
