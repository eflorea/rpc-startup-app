# RPC STARTUP APP #
-------------------

## Quick Setup ##
1. Create a repository on bitbucket and clone it locally
1. Download the [latest stable version](https://bitbucket.org/Three29media/rpc-startup-app/downloads/?tab=tags) of rpc-startup-app and unzip it into your new project
1. Remove or edit README.md file (this file has instructions for rpc-startup-app - it should include information about your project)
1. Create .htaccess file in public folder (see .htaccess.example file)
1. Create .env file in config folder (see .env.example file) to setup db credentials etc.
1. Run composer install (this will install rpc packages and any dependencies)
1. Enjoy!

# RPC DOCUMENTATION #


* [Installation, Structure and Configuration](#markdown-header-installation-structure-and-configuration)
* [Routing](#markdown-header-routing)
* [Controllers](#markdown-header-controllers)
* [Models](#markdown-header-models)
* [Forms and Validations](#markdown-header-forms-and-validation)
* [Views](#markdown-header-views)
* [3rd Party Libraries](#markdown-3rd-party-libraries)


# Installation, Structure and Configuration #

This framework has a few system requirements:

* PHP >= 5.4.0
* PDO PHP Extension

## Structure ##

The structure of the app is pretty simple:
* APP
    * Controller
    * Home.php
  * Model
    * Row
      * User.php
    * Row.php
    * User.php
  * View
    * Home
      * index.php
    * layouts
  * Controller.php
  * Model.php
  * View.php
* config
    * .env
    * .env.example
    * routes.php
* public
    * .htaccess
    * .htaccess.example
    * index.php
    * skin
        * any css, images and javascript files
* tmp
  * cache
  * views
* autoloader.php

All of the configuration files are stored in the config directory in ***.env*** file.

## Configuration ##

You should configure **config/.env** and change database connection, app url, timezone and any other settings that your application needs.
Simple configuration files looks like this:

```console
#example of configuration file

#database
DB_HOSTNAME="localhost"
DB_USERNAME="user"
DB_PASSWORD="pass",
DB_NAME="db_name"
DB_PREFIX="t29_"
DB_PORT=3306,
DB_SOCKET=""
DB_ADAPTER="MySQL"

#debugging and logs
DEBUG_QUERIES=""
LOGS_ENABLED=false
LOG_TO_FILE=false
#LOG_PATH=""
#LOG_THRESHOLD=""
#LOG_DATE_FORMAT=""

```


# Routing #
You can setup special routing in the **config/routes.php**. These routes are a rewrite rule to where the urls actually point.
```php
<?php

$routes = array(
    array(
        'match' => '^admin$',
        'target' => 'admin/index/index'
    ),
    array(
        'match' => '^404$',
        'target' => 'index/notfound'
    ),
    array(
        'match' => '^login$',
        'target' => 'account/auth/login'
    ),
    array(
        'match' => '^logout',
        'target' => 'account/auth/logout'
    ),
);

?>
```
So in the above example

* **/admin** will point to **/admin/index/index**
* **/404** will point to **index/notfound**
etc...

The routing system automatically tries to find, load and execute the controller based on the URL. The naming/structure convention is simple:
DOMAIN/PART1/PART2/PART3/etc...

For example if you trying to access **/admin** the routing system will try to find **APP/Controller/Admin.php** and execute the method **detailsGET**


**Please note**


**/admin** and **/admin/index** are the same thing - the routing system automatically sets **index** for the second part of the url if it doesn't exists.

The URL can be nested like */admin/users/list/* - this will try to find **APP/Controller/Admin/Users.php** and execute the method **listGET**

## Get "Query String" ##

If you want to send data in the query string you the routing system has a built in, "user friendly" url that you can use to send parameters to the controllers.

**Example:**

Let's say we want to view a user's profile with ID 10. We can do this 2 ways.

**Classic way - "bad" way**

Make a call to **/user/view/?id=10**
```php
<?php

class User extends \APP\Controller
{

    public function viewGET( $request, $response )
    {
        ...

        //get user id from URL
        $id     = (int) $this->request->get['id'];

        //get row object
        $model  = new \APP\Model\User();
        $user   = $model->find( $id );

        if( $user )
        {
            //user was found
        }
        else
        {
            //user couldn't be found
        }
        ...
    }

    ...

}

?>
```


**Friendlier way - "good" way**

Make a call to **/user/view/params/id/10/**

```php
<?php

class User extends \APP\Controller
{

    public function viewGET( $request, $response )
    {
        ...

        //get user id from URL
        $id     = (int) $this->param( 'id', 0 );

        //get row object
        $model  = new \APP\Model\User();
        $user   = $model->find( $id );

        if( $user )
        {
            //user was found
        }
        else
        {
            //user couldn't be found
        }
        ...
    }

    ...

}   

?>
```

You should use the second method as much as possible. To get a param from the url you use the method **$this->param()** of the controller.

**Please note**

You can submit unlimited params in the URL. The params can be submitted after the "keyword" **params** in pairs "/params/PARAM1/PARAM1_VALUE/PARAM2/PARAM2_VALUE/PARAM3/PARAM3_VALUE/etc";

Example: **/user/view/params/id/10/tab/orders/

```php
<?php

class User extends \APP\Controller
{

    public function viewGET( $request, $response )
    {
        ...

        //get user id from URL
        $id     = (int) $this->param( 'id', 0 );
        $tab    = $this->param( 'tab' );

        //get row object
        $model  = new \APP\Model\User();
        $user   = $model->find( $id );

        if( $user )
        {
            //user was found
        }
        else
        {
            //user couldn't be found
        }
        ...

        switch( $tab )
        {
            case 'orders':
                //get user's orders

                break;

            default:
                //view user's profile
                break;
        }

        ...
    }

    ...

}   

?>
```



# Controllers #
All controllers files are located in **Controller** folder.
A controller is made of 2 parts:

* command
* method

**command** is the name of the file and the class, Eg: **controllers/User.php**
```php
<?php

class User extends \APP\Controller
{

    public function indexGET( $request, $response )
    {
        ...
    }

    ...

}

?>
```

**action** is the name of the method in the controller file. The above controller (command) has **index** action available (GET Request).

## Structure of a controller ##

Name of the controller is **always** first letter uppercase of the first part of the url concatenated with **Command**.

So **/users** will be **UsersCommand.php** for the controller file and **UserCommand** for the class.


**A controller always extends the main framework's controller or an extension of that.**

```
#!php
<?php

class UserCommand extends APP_Controller_Command
{

    ...

}

?>
```


**Methods of the controllers are called based on the URL and the request type**

The type name is appended to the method name:

* editGET

* editPOST


Also, if a method having "setup" appended to its name exists and/or one having "teardown" appended, it will be executed before, respectively after, either GET or POST methods:


* editSetup
* editTeardown


So sequence in calling these method is:
* editSetup
* editGET or editPOST (depends on the request type)
* editTeardown

**Please note**

"setup" and "teardown" methods are not required







# Models #

All models are located in **models** directory.
The framework requires that the name of the model has to be made by the name of the database table name (without the prefix - if any) concatenated with "Model".

Eg: You have a database table named **t29_users**

Your model name will be **UsersModel** and the file name will be **models/UsersModel.php**


### Creating a new row object ###
```
#!php
<?php
$model = new UserModel();
$user  = $model->create();

...

$user->save();

?>
```

### Loading a row object ###
```
#!php
<?php
$model = new UserModel();
$user  = $model->load( $id );

if( $user ) {
...
}

?>
```

### Updating a row object ###
```
#!php
<?php
$model = new UserModel();
$user  = $model->load( $id );

if( $user )
{
    ...
    $user['user_email'] = $new_email;
    $user->save();
}

?>
```


### Validate data for a row object ###

The framework can automatically validate all the columns of a table. To validate a row object you need to call **validate** method on the object.
To validate columns you need to have a method on the model named **validate_NAME_OF_COLUMN**

Example: Validate user's email

```
#!php
<?php
$model = new UserModel();
$user  = $model->create();

$user['user_email'] = $request->post['user_email'];

if( $user->validate() )
{
    $user->save();
}

$errors = $user->getErrors();

?>

<?php

class UserModel extends APP_Db_Table_Adapter_MySQL
{
    ...

    public function validate_email( $row, $op )
    {
        //validate email
        $v = new RPC_Validator_Email( 'Please fill in a valid email address.' );
        if( ! $v->validate( $row['user_email'] ) )
        {
            return $this->setError( 'user_email', $v->getError() );
        }

        //check for uniqueness
        ...
    }

    ...
}

?>
```


## Cleaning data before or after validation ##

If you want to clean data before or change data after validation you can use **preValidate** or **postValidate** methods on the row object. These methods are optionally but if they exist they will be called automatically.

```
#!php
<?php

class UserModel extends APP_Db_Table_Adapter_MySQL
{

    ...

    public function preValidate( $row, $op )
    {
        //remove whitespaces
        $row['user_phone'] = trim( $row['user_phone'] );

        ...

        return $row;
    }

    ...


    public function postValidate( $row, $op )
    {

        //format phone
        $row['user_phone'] = formatPhone( $row['user_phone'] );

        return $row;

    }

    ...
}

?>
```


## Custom Queries ##

Here's a list of all available methods on the model

```
#!php
<?php

    /**
     * Returns one row (the first in case there are more) which has the given
     * $field equal to $value. If no row is found, returns null
     *
     * @param string $field
     * @param string $value
     *
     * @return RPC_Db_Table_Row
     */
    abstract public function loadBy( $field, $value );

    /**
     * Returns all rows which has the given
     * $field equal to $value. If no row is found, returns null
     *
     * @param string $field
     * @param string $value
     *
     * @return RPC_Db_Table_Row
     */
    abstract public function loadAllBy( $field, $value );

    /**
     * Returns one row (the first in case there are more) which is returned by the query on the model's table. If no row is found, returns null
     *
     * @param string $condition_sql
     * @param array $condition_values
     *
     * @return RPC_Db_Table_Row
     */
    abstract public function loadBySql( $condition_sql, $condition_values );

    /**
     * Returns all rows returned by the query on the model's table. If no row is found, returns null
     *
     * @param string $condition_sql
     * @param array $condition_values
     *
     * @return RPC_Db_Table_Row
     */
    abstract public function loadAllBySql( $condition_sql, $condition_values );

    /**
     * Returns all rows returned by the custom query. If no row is found, returns null
     *
     * @param string $condition_sql
     * @param array $condition_values
     *
     * @return RPC_Db_Table_Row
     */
    abstract public function loadAllByCustomSql( $condition_sql, $condition_values );

    /**
     * Removes the rows which have the $field = $value
     *
     * @param int $field
     * @param mixed $value
     *
     * @return int Number of affected rows
     */
    abstract public function deleteBy( $field, $value );

?>
```

### Example of custom queries  that load row objects ###
```
#!php
<?php

$model = new UserModel();

//grab one/all users with first name John
$users = $model->loadBy( 'user_first_name', 'John' );
$users = $model->loadAllBy( 'user_first_name', 'John' );

//grab one/all users where first name John and status ACTIVE
$users = $model->loadBySql( 'user_first_name = ? and user_status = ?', array( 'John', 'ACTIVE' ) );
$users = $model->loadAllBySql( ' user_first_name = ? and user_status =? ', array( 'John', 'ACTIVE' ) );

//grab all users from a joined query
$users = $model->loadAllByCustomSql( 'select * from USERS_TABLE inner join ORDERS_TABLE on ( ORDERS_TABLE.user_id = USERS_TABLE.user_id ) where order_id = ? ', array( 15 ) );

//delete all users with name John
$model->deleteBy( 'user_first_name', 'John' )
?>
```

### Running custom queries ###
```
#!php
<?php

$model = new UserModel();

//grab all orders for today
$orders = $model->getDb()->prepare( "select * from ORDERS_TABLE where orders_date = ? " )->execute( array( date( 'Y-m-d' ) ) );

//running custom query inside of the model
$orders = $this->getDb()->prepare( $sql )->execute( $values ); //please note $this

?>
```




# Views #

* [Basic Usage](#markdown-header-basic-usage-of-views)
* [Including Template within Template](#markdown-header-including-templates-within-template)
* [Placeholders and Fillers](#markdown-header-placeholders-and-fillers)
* [Helpers](#markdown-header-helpers)
* [Error Tags](#markdown-header-error-tags)

## Basic usage of views ##

Views contain the HTML served by your application and separate your controller / application logic from your presentation logic. Views are stored in the views directory.
A simple view might look something like this:
```
#!html
<!-- View stored in views/account.php -->

<html>
    <body>
        <h1>Hello, <?= $name; ?></h1>
        <h2>Your IP is: <?php echo $_SERVER['REMOTE_ADDR']; ?></h2>
    </body>
</html>
```




Set template name and send data to view from the controller/application
```
#!php
<?php

class AccountCommand extends APP_Controller_Command
{
    public function indexGET( $request, $response )
    {
        //get the view object
        $view = $this->view();

        //send name to the view
        $view->name = 'Full Name';

        $view->display( 'account.php' );
    }
}

?>
```

Rendering view and get output to be used outside of the presentation logic (Eg: Email template)
```
#!php
<?php

class AccountCommand extends APP_Controller_Command
{
    public function indexGET( $request, $response )
    {
        //get the view object
        $view = $this->view();

        ....

        //send name to the view
        $view->name = 'Full Name';

        //get email content
        $email_content = $view->render( 'account.php' );

       //send email
        mail( $to, $subject, $email_content );
    }
}

?>

```


Of course, views may also be nested within sub-directories of the views directory. "/" notation may be used to reference nested views. For example, if your view is stored at views/admin/profile.php, you may reference it like so:
```
#!php
<?php

...
$view->display( 'admin/profile.php' );
...

?>
```

## Including template within template ##
This can be achieved by using <render>template_name</render> tag.
```
#!html
<html>
    <body>

        <div> some content here</div>
        <!-- let's include the footer -->
        <render>footer.php</render>

    </body>
</html>
```

## Placeholders and Fillers ##

Views can have placeholders tags that can be dynamically filled depending on the situation.

**layout.php**

```
#!html
<html>
    <body>

        <placeholder id="content">

    </body>
</html>
```

**users**

```
#!html
<render>layout.php</render>

<filler for="content">
    some users informations here
</filler>
```

You can have as many placeholders you want as long as the **id**s are unique.


## Helpers ##

There are a couple of template helpers available in the framework but you can add your own if you want. Available helpers:

### Datagrid ###

If you want a table with listing and sortable columns you can instantiate a datagrid from the controller and send it to the view:

```
#!php
<?php

class UsersCommand extends APP_Controller_Command
{

    public function listGET( $request, $response )
    {
        ...

        $view                   = $this->view();

        $users = new ModelDatagrid( 'user', false, new UserModel() );
        $users->allowSortBy( 'user_id', 'user_name' );
        $users->setInitialSortBy( 'user_id', 'desc' );

        $view->users = $users;

        ...

        $view->datagrid_users   = $datagrid_users;
        ...

        $view->display( 'list.php' );
    }

    ...

}

?>
```

```
#!html
<table datagrid="users">
    <thead>
        <th><sort field="user_id">id</sort></th>
        <th><sort field="user_name">name</sort></th>
    </thead>
    <tbody>
        <?php if( $users->getRows() ): ?>
            <?php foreach( $users->getRows() as $u ): ?>
                <tr>
                    <td><?= $u->id(); ?></td>
                    <td><?= $u->first_name(); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">No results</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">
                <pagination>
            </td>
        </tr>
    </tfoot>
</table>
```

**Please note**

On the table you neeed to set the attribute **datagrid** that will have the same name with what you set from the controller (Eg: **users**).

You can set sortable columns by using **sort** tag.

For pagination you need to use **pagination** tag.





## Error Tags ##

You can use error tags that can be used to show error messages.


Sending error messages from controller

```
#!php
<?php

...
$view = $this->view();

$view->plugin_error->set( array( 'user_email' => 'Email is required' ) );
...

?>
```

Error tag on view template

```
#!html
<html>
    <body>

        <input type="text" name="user">
        <error id="user_email">

    </body>
</html>
```

If the error is sent from controller then the error tag will be replace with: **<div class="has-error" id="error-{id}"><span class="help-block">{message}</span></div>**

In order example the **{id}** is **user_email** and the **{message}** is **Email is required**


# Forms and Validations #

Form can be built in normal html format and the view system will parse them and replace/fill in data

```
#!html
<form method="post" action="/users/add/">

    <label for="user_name">Name:</label>
    <input type="text" id="user_name" name="user_name" value="<?= $user->name(); ?>">
    <error id="user_name">

    <label for="user_email">Email:</label>
    <input type="text" id="user_email" name="user_email" value="<?= $user->email(); ?>">
    <error id="user_email">

    <label for="user_role">Role:</label>
    <select id="user_role" name="user_role" source="<?= $available_roles ?>" selected="<?= $user->role(); ?>"></select>

    <label for="user_bio">Bio:</label>
    <textarea id="user_bio" name="user_bio" value="<?= $user->bio(); ?>"></textarea>

    <label for="has_access">Has Access:</label>
    <input type="checkbox" id="has_access" name="has_access" checked="<?= $user->hasAccess() ?>">

    <label>Full Screen:</label>
    <label>
        <input type="radio" id="fullscreen_yes" name="fullscreen" checked="<?= ( $user->fullscreen() == 'yes' ) ?>"> Yes
    </label>
    <label>
        <input type="radio" id="fullscreen_no" name="fullscreen" checked="<?= ( $user->fullscreen() == 'no' ) ?>"> No
    </label>

    <button type="submit">Save</button>

</form>
```

Validation happens in the controller at the model level or controller level.


# 3rd Party Libraries #

All 3rd Party Libraries must be included in the lib folder. If they have a specific autoloader you can include them in the init.php or include that library directly in init.php