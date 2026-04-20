<div align="center">

# Market_GiftCard

</div>

> [!WARNING]
> **This module is currently under active development and is not yet complete.**
> It is not recommended for use in production environments. APIs, database schema,
> and behaviour may change without notice until a stable release is published.

Magento 2 module that adds a dedicated **Gift Card** product type. Merchants can create gift card products with fixed or custom amounts. Customers purchase gift cards, enter recipient details, and the system tracks balances and usage history across orders.

## Overview

The `giftcard` product type extends `Virtual` and introduces three buy-request options: **recipient name**, **recipient email**, and **amount**. When custom amounts are enabled via the `is_custom_allowed` attribute, the customer enters their own value at purchase time and the observer overrides the product price before totals are collected.

The module also:

- Registers `giftcard` as a first-class product type via `product_types.xml`.
- Provides a custom price template (`final_price.phtml`) that shows "Custom Amount" when the attribute is enabled.
- Persists gift card options from quote to order via `MoveQuoteItemOptionsToOrderItem` plugin.
- Bypasses inventory and qty validation for gift card items since they are generated on demand.
- Exposes a full repository layer with `SearchCriteria` support for REST/GraphQL API consumption.
- Supports GraphQL `addProductsToCart` via a custom `BuyRequestDataProvider` using `entered_options` with a `giftcard/` UID prefix.

## Architecture

```text
Product Type (giftcard) extends Virtual
    |
    +-- Model\Type\GiftCard                   <- prepareForCartAdvanced, assigns buy request options
    |
    +-- Observer\SetGiftCardCustomPrice       <- sets custom price on sales_quote_collect_totals_before
    |
    +-- Model\Cart\BuyRequest\GiftCardDataProvider  <- GraphQL entered_options -> buy request
    |
    +-- Plugin
    |       +-- MoveQuoteItemOptionsToOrderItem     <- persists options quote -> order
    |       +-- PreventInventoryForGiftCard          <- skips inventory salability check
    |       +-- PreventQtyLookupForGiftCard          <- skips qty validation
    |
    +-- Frontend
    |       +-- templates/product/price/final_price.phtml   <- price display
    |       +-- templates/product/view/type/giftcard.phtml  <- product options form
    |       +-- templates/cart/item/giftcard.phtml           <- cart item renderer
    |
    +-- API
            +-- GiftCardRepositoryInterface          <- CRUD for gift cards
            +-- GiftCardUsageRepositoryInterface      <- usage/redemption history
```

## Installation

### Manual

1. Copy the module to `app/code/Market/GiftCard/`
2. Run the following commands:

```bash
bin/magento module:enable Market_GiftCard
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:flush
```

### Verify Installation

```bash
bin/magento module:status Market_GiftCard
```

## Attribute Details

| Property                | Value                |
|-------------------------|----------------------|
| Attribute code          | `is_custom_allowed`  |
| Type                    | `int`                |
| Input                   | `select` (Yes/No)    |
| Scope                   | Global               |
| Group                   | General              |
| Required                | Yes                  |
| Used in product listing | Yes                  |
| Apply to                | `giftcard` only      |

## Gift Card Options

Each gift card purchase captures three options via the buy request:

| Option             | Constant                        | Description                     |
|--------------------|---------------------------------|---------------------------------|
| `recipient_name`   | `Attributes::OPTION_RECIPIENT_NAME`  | Name of the gift card recipient |
| `recipient_email`  | `Attributes::OPTION_RECIPIENT_EMAIL` | Email of the gift card recipient|
| `amount`           | `Attributes::OPTION_AMOUNT`          | Custom amount (when allowed)    |

## Custom Price Handling

When `is_custom_allowed` is enabled, the `SetGiftCardCustomPrice` observer listens to `sales_quote_collect_totals_before` and:

1. Iterates all quote address items of type `giftcard`
2. Reads the `amount` custom option value
3. Sets `custom_price` and `original_custom_price` on the item
4. Enables super mode on the product to bypass price validation

## GraphQL Support

Gift cards can be added to cart via the standard `addProductsToCart` mutation using `entered_options` with a `giftcard/` UID prefix:

```graphql
mutation {
  addProductsToCart(
    cartId: "CART_ID"
    cartItems: [
      {
        sku: "gift-card-sku"
        quantity: 1
        entered_options: [
          {
            uid: "Z2lmdGNhcmQvcmVjaXBpZW50X25hbWU="
            value: "John Doe"
          }
          {
            uid: "Z2lmdGNhcmQvcmVjaXBpZW50X2VtYWls"
            value: "john@example.com"
          }
          {
            uid: "Z2lmdGNhcmQvYW1vdW50"
            value: "50.00"
          }
        ]
      }
    ]
  ) {
    cart {
      items { id quantity product { sku name } }
    }
    user_errors { code message }
  }
}
```

### UID Encoding

| Field             | Raw String                  | Base64 UID                           |
|-------------------|-----------------------------|--------------------------------------|
| Recipient Name    | `giftcard/recipient_name`   | `Z2lmdGNhcmQvcmVjaXBpZW50X25hbWU=`  |
| Recipient Email   | `giftcard/recipient_email`  | `Z2lmdGNhcmQvcmVjaXBpZW50X2VtYWls`  |
| Amount            | `giftcard/amount`           | `Z2lmdGNhcmQvYW1vdW50`              |

## Database Changes

### `market_gift_card`

| Column                 | Type           | Description              |
|------------------------|----------------|--------------------------|
| `id`                   | `int`          | Primary key              |
| `assigned_customer_id` | `int`          | FK to `customer_entity`  |
| `code`                 | `varchar(255)` | Unique gift card code    |
| `status`               | `int`          | Card status              |
| `initial_value`        | `decimal(12,4)`| Original card value      |
| `current_value`        | `decimal(12,4)`| Remaining balance        |
| `recipient_email`      | `varchar(255)` | Recipient email          |
| `recipient_name`       | `varchar(255)` | Recipient name           |
| `created_at`           | `timestamp`    | Creation timestamp       |
| `updated_at`           | `timestamp`    | Last update timestamp    |

### `market_gift_card_usage`

| Column          | Type           | Description              |
|-----------------|----------------|--------------------------|
| `id`            | `int`          | Primary key              |
| `gift_card_id`  | `int`          | FK to `market_gift_card` |
| `order_id`      | `int`          | FK to `sales_order`      |
| `value_change`  | `decimal(20,6)`| Amount used              |
| `notes`         | `text`         | Usage notes              |
| `created_at`    | `timestamp`    | Creation timestamp       |

## Uninstalling

```bash
bin/magento module:uninstall Market_GiftCard
bin/magento setup:upgrade
bin/magento cache:flush
```

This will trigger the `revert()` method on data patches, removing the `is_custom_allowed` product attribute. Database tables added via `db_schema.xml` will also be removed automatically.

## Dependencies

- `Magento_Catalog`
- `Magento_Sales`

## License

This module is proprietary software. Unauthorized copying, modification, distribution, or use of this software, in whole or in part, is strictly prohibited without prior written permission from Market.

Copyright (c) 2026 Market. All rights reserved.
