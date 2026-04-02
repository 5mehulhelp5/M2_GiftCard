# Market Gift Card Module

A Magento 2 module that enables merchants to sell and manage gift cards as a dedicated product type. Customers can purchase gift cards, send them to recipients by email, and redeem them at checkout against any order.

## Features

- **Gift Card Product Type** — a first-class catalog product type (`giftcard`) that merchants create and manage like any other product
- **Custom Amounts** — optional `is_custom_allowed` attribute lets customers enter their own gift card value at purchase
- **Recipient Details** — each card stores recipient name and email for delivery
- **Balance Tracking** — initial and current value tracked per card; every redemption is recorded with the order reference and amount used
- **Service Contract API** — full repository layer with `SearchCriteria` support for integration and REST API consumption
- **Inventory Bypass** — gift cards skip standard qty/inventory checks since they are generated on demand

## Installation

```bash
composer require market/module-gift-card
bin/magento module:enable Market_GiftCard
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:flush
```

## How It Works

### Creating a Gift Card Product

1. Go to **Catalog > Products > Add Product**
2. Select **Gift Card Product** as the product type
3. Set a price (fixed) or enable **Allow Custom Amount** for open-value cards
4. Set the recipient name and email fields — these are stored on the generated gift card record

### Purchasing & Generating

When a customer purchases a gift card product, a `market_gift_card` record is created with:

- A unique code
- The purchased value as both `initial_value` and `current_value`
- The recipient's name and email
- Status set to active

### Redeeming at Checkout

Customers enter the gift card code at checkout. The module:

1. Looks up the card by code via `GiftCardRepositoryInterface::getByCode()`
2. Validates the card is active and has sufficient balance
3. Deducts the used amount from `current_value`
4. Writes a `market_gift_card_usage` record linking the card to the order

### Balance History

Every redemption is stored in `market_gift_card_usage` with the order ID, amount used, and an optional note — giving merchants a full audit trail per card.

## API

The module exposes its data through Magento service contracts, accessible via REST API or direct injection.

### GiftCardRepositoryInterface

| Method | Description |
| --- | --- |
| `getById(int $id)` | Load a gift card by its primary key |
| `getByCode(string $code)` | Load a gift card by its unique code |
| `save(GiftCardInterface $card)` | Create or update a gift card |
| `delete(GiftCardInterface $card)` | Delete a gift card |
| `deleteById(int $id)` | Delete a gift card by ID |
| `getList(SearchCriteriaInterface)` | Search/filter/paginate gift cards |

### GiftCardUsageRepositoryInterface

| Method | Description |
| --- | --- |
| `getById(int $id)` | Load a usage record by ID |
| `save(GiftCardUsageInterface $usage)` | Create or update a usage record |
| `delete(GiftCardUsageInterface $usage)` | Delete a usage record |
| `deleteById(int $id)` | Delete a usage record by ID |
| `getList(SearchCriteriaInterface)` | Search/filter/paginate usage history |

## Dependencies

- `Magento_Catalog`
- `Magento_Sales`
