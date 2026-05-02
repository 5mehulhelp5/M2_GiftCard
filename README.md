<div align="center">

# Market Gift Card for Magento 2

A Magento 2 module that introduces a dedicated **Gift Card** product type with recipient details, custom amounts, unique code generation, and GraphQL support.

[![Magento 2](https://img.shields.io/badge/Magento-2.4.x-orange.svg)](https://magento.com)
[![PHP](https://img.shields.io/badge/PHP-8.1%20%7C%208.2%20%7C%208.3-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-Proprietary-red.svg)](#license)
[![Status](https://img.shields.io/badge/status-in%20development-yellow.svg)](#description)

</div>

---

> [!WARNING]
> **Under active development.** APIs, schema, and behavior may change without notice. Not recommended for production use.

## Description

Market Gift Card adds a first-class `giftcard` product type to Magento 2. Merchants sell gift cards with fixed or customer-entered amounts, recipient details are captured at cart, and a unique secure code is generated per gift card on invoice. The module also ships with GraphQL `addProductsToCart` support so headless storefronts work out of the box.

## Installation

1. Copy the module into your Magento project at `app/code/Market/GiftCard/`:

```bash
mkdir -p app/code/Market/GiftCard
cp -r /path/to/module-gift-card/* app/code/Market/GiftCard/
```

1. Enable the module and run setup:

```bash
bin/magento module:enable Market_GiftCard
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:flush
```

1. Verify:

```bash
bin/magento module:status Market_GiftCard
```

## Dependencies

- `Magento_Catalog`
- `Magento_Sales`

## License

Proprietary. Copyright © 2026 Market. All rights reserved.
