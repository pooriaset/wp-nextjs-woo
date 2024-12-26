# wp-nextjs-woo

`wp-nextjs-woo` is a WordPress plugin designed to integrate seamlessly with [Next.js WooCommerce](https://github.com/pooriaset/nextjs-woo). This plugin acts as a bridge between your WordPress WooCommerce backend and your Next.js frontend, enabling a powerful and flexible eCommerce experience.

With `wp-nextjs-woo`, you can easily configure your WordPress installation to communicate with the Next.js application, streamlining the development process for modern WooCommerce projects.

## Installation

1. Upload the plugin to your WordPress site and activate it.

2. Add the following line to your `wp-config.php` file, located in the root of your WordPress installation:

   define('WP_NEXTJS_HOST', 'http://localhost:3000');

   Replace `http://localhost:3000` with the appropriate URL of your Next.js application.

3. Save the changes to `wp-config.php`.

4. You're all set!
