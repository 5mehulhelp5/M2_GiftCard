# Market Gift Card Module

A Magento 2 module that enables merchants to sell and manage gift cards as a dedicated product type. Customers can purchase gift cards, send them to recipients by email, and redeem them at checkout against any order.

## Features

- **Gift Card Product Type** - a first-class catalog product type (`giftcard`) that merchants create and manage like any other product
- **Custom Amounts** - optional `is_custom_allowed` attribute lets customers enter their own gift card value at purchase
- **Recipient Details** - each card stores recipient name and email for delivery
- **Balance Tracking** - initial and current value tracked per card; every redemption is recorded with the order reference and amount used
- **Service Contract API** - full repository layer with `SearchCriteria` support for integration and REST API consumption
- **Inventory Bypass** - gift cards skip standard qty/inventory checks since they are generated on demand

## Installation

1. Copy the module files to `app/code/Market/GiftCard/`
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

## Dependencies

- `Magento_Catalog`
- `Magento_Sales`

## License

This module is proprietary software. Unauthorized copying, modification, distribution, or use of this software, in whole or in part, is strictly prohibited without prior written permission from Market.

Copyright (c) 2026 Market. All rights reserved.
