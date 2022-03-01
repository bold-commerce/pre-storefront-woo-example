# PRE Sample Project
- This is a sample of pre-built Bold Price Rules storefront template for WooCommerce.
- This is an example of a Custom Woo Plugin that could be zipped and uploaded 
- This can be used with Bold Hosted Checkout
- This can be used with Bolds Woo Integrated Checkout

# Requirements
1. Woo Commerce Plugin to be active
2. Bold Plugin to be active
3. Bold Checkout App within account center
4. Bold pricing is enabled within the Checkout App -> Marketplace [ Bold Pricing ]

# Instructions for use
1. Fork the repo
2. customize for your store as needed
3. Run the following commands in the root:
    * ``composer i`` - adds vendor dependencies
    * ``chmod +x build.sh`` - makes bash script file executable
    * ``./build.sh`` - executes the bash script & generates a zip file
4. upload the zip file as a custom plugin. 
    *  From WP Admin Navigate to Plugins -> Add New 
    *  Select "Upload Plugin"
    *  Add the zip file.
