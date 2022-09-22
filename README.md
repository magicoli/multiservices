# MultiPass (dev)

- Contributors: magicoli69
- Donate link: https://magiiic.com/support/MultiPass+Plugin
- Tags: hotel, booking, multi-prestations, multi-resources, woocommerce, ota, pms, booking engine
- Requires at least: 5.9.0
- Tested up to: 6.0.2
- Stable tag: 0.1.1
- License: AGPLv3 or later
- License URI: http://www.gnu.org/licenses/agpl-3.0.txt

Group resources from different sources (WooCommerce, OTA, booking engines...) and manage them as a whole.

## Description

Bring orders from several sources together and see them as a single provision of resources.

Particularly useful in lodging facilities, if your business offers other kinds of resources (meals, vehicule rentals, merchandising, local products...) that are not or poorly handled by the main booking engine.

This should also fit well for other kind of resources, needing a more fluid approach than usual e-commerce solutions.

DISCLAIMER: this is an early-stage development version, updates might include breaking changes until 1.0 release.

WARNING: **Make a full backup of your website and databases** before installing this plugin. I MEAN IT (see disclaimer).

### Use cases

- An order is created and paid upon reservation, but **additional resources are usually added later** (e.g. once the resource begins).
- Your booking engine **can not handle the other kinds of resource you provide**.
- When customer book several rooms, your booking engine create **separated orders**, one for each room, and you want to manage the full project.
- You use an external booking engine, but you prefer to **collect payments on your website** (e.g. with WooCommerce)
- You need to handle **partial payments**.
- You need to **validate booking** when deposit threshold is reached.

## Features

- [x] Multi-calendar view
- [x] Centralized view of prestations (sets of resources, ordered as parts of a common project).
- [x] Prestations admin list, showing resource dates and payment status.
- [x] Deposits, on prestation or resource level, by percentage or fixed amount.
- [x] Discounts, on prestation or resource level, by percentage or fixed amount.
- [x] Payments, on prestation or resource level.
- [x] Sources association: link same product/resource present on several sources
- [x] Summarized payment status, centralizing amounts paid locally or via external providers.
- [x] Custom payments via WooCommerce product
- [x] Class-based modular design, allowing developpers to create addons for other plugins or other resource providers.

### Integrations (work in progress)

- [x] WooCommerce, including
  - [x] WooCommerce Bookings
  - [x] WooCommerce Accommodation Bookings
  - [x] Custom payments made via WooCommerce
- Lodgify
  - [x] Sync properties
- HBook by Maestrel
  - [x] Sync properties
- HotelDruid (probably import only)
- Email processing (access your mailbox and link messages to related prestations)

