# Base (abstract) module for different targeting systems
Base (abstract) module for different targeting systems

Most of the Retargeting systems provide similar interface with difference in details.

## Step 1
First off all you must mark your module as "retargeting"
You need to add next config to your config.xml
 ```
 <config>
    <retargeting>
        <modules>
            <Popov_SalesDoubler>
                <utm_uid_name>aff_id</utm_uid_name><!-- your module name responsible for implement retargetting logic -->
            </Popov_SalesDoubler>
        </modules>
    </retargeting>     
 </config>
 ```

`utm_uid_name` - the param name of unique identifier in targeting system passed through GET method  

## Step 2
For adding cookies you should implement `Data` helper with two methods: `setCookies` and `clearCookies`.
 
## Step 
If targeting system require add their javascript, continue reading this part.
Next step will be adding the script on all pages which contain products.
Create `Script` class under `Block` directory. This class will be created automatically with param `action`.
Depend on this param you can implement custom business logic. Call this as `$this->getData('action')` 
and retrieve something like this `catalog_product_view`.

`Script` must implement protected method `_toHtml` and return retargeting javascript.
 
Second, you should in your `Data` helper implement method `setCoockies` relative to requirements targeting system.

And third, create `PostBack` helper with method `send` which will be called when customer will finish order successfully.