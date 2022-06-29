# Create Laravel Custom Package

Packages are a great way to make a bunch of code reusable and easily shareable. You may have already come across many Laravel packages, both official and community-maintained—some of them simple and some very complex. But have you ever wondered how you can put “your” code into a package and share it with others?

# Project Plan and Structure

Executing mkdir -p packages/rohith/inspire/src this command on your project root should structure the directories correctly.


# Initializing a New Package
To initialize a new Laravel custom package development, cd into the packages/rohith/inspire directory and execute the composer init command. This will start the package initialization process. Make sure to write the package name, description, and author information properly. After that, you can press enter and accept the default for all the options, except when it asks whether you’d like to add your dependencies and dev-depdendencies interactively or not. For those two prompts, write n and hit enter to answer in negative.

Once the package has been initialized, you’ll find a new vendor directory and composer.json file inside the inspire directory. Open the composer.json file (keep in mind, this composer.json file is separate from your project composer.json file) in your code editor of choice and look for the require {} section. This is where you’ll have to enlist any other package that your package depends on. Well in this one, you’ll need the guzzlehttp/guzzle package to make HTTP requests. So, update the require {} section as follows:

    {
        "name": "rohith/inspire",
        "autoload": {
            "psr-4": {
                "Rohith\\Inspire\\": "src/"
            }
        },
        "authors": [
            {
                "name": "rohith",
                "email": rohith@gmail.com"
            }
        ],
        "require": {
            "guzzlehttp/guzzle": "^7.0.1"
        }
    }

Make sure that the required dependency has been enlisted and the autolaod section has been populated. "Rohith\\Inspire\\": "src/" will instruct Composer to treat the src directory as the Rohith\Inspire namespace.

#Creating the Inspire Class
Create a new file Inspire.php inside the src directory and put the following code in it:

    <?php

    namespace Rohith\Inspire;

    use Illuminate\Support\Facades\Http;

    class Inspire {
        public function justDoIt() {
            $response = Http::get('https://inspiration.goprogram.ai/');

            return $response['quote'] . ' -' . $response['author'];
        }
    }

# Testing the Inspire Class

Before going any further, let's test out the Inspire class. To do so, first, open the composer.json file on your project root and scroll down to the autoload section. There should be three directories from the beginning. Append a new line after them as follows:

    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/",
            "Rohith\\Inspire\\": "packages/rohith/inspire/src/"
        }
    },

This line will instruct Composer to load the packages/rohith/inspire/src/ directory as the Rohith\Inspire namespace.

Now to generate a updated autoload.php file, execute the following command on the project root:

    composer dump-autoload

Now, open the routes/web.php file and register the following GET route in there:
    
    Route::get('inspire', function(Rohith\Inspire\Inspire $inspire) {
        return $inspire->justDoIt();
    });

# Adding a Service Provider to Your Package  

Now that the class is working fine, it’s time to add a service provider to the package. This service provider will work as sort of the entry point to your package. Create a new file src/Providers/InspirationProvider.php and put the following code in it:

    <?php

    namespace Rohith\Inspire\Providers;

    use Illuminate\Support\ServiceProvider;

    class InspirationProvider extends ServiceProvider
    {
        /**
        * Bootstrap services.
        *
        * @return void
        */
        public function boot()
        {
            //
        }
    }

Keep the boot method empty for now, you’ll soon add code here to load package routes and views. Open the config/app.php file and scroll down to the providers array. In that array, there should be a section for the package service providers. Add the following line of code in that section:

    /*
    * Package Service Providers...
    */
    Rohith\Inspire\Providers\InspirationProvider::class,

This will register the InspirationProvider class as one of the service providers for this project.

# Adding Controllers to Your Package

In a previous section, you registered the /inspire route as part of your project code. But this should be a part of the package code, so anyone using this package will get this route from the get-go.

So you may want to move the routing functionality to the package along with a new controller class. Adding a controller for such a simple task may be overkill, but I want to show you how you may include controllers in your packages.

Create a new file src/Controllers/InspirationController.php with the following code:

    <?php
    namespace Rohith\Inspire\Controllers;

    use Illuminate\Http\Request;
    use Rohith\Inspire\Inspire;

    class InspirationController
    {
        public function __invoke(Inspire $inspire) {
            $quote = $inspire->justDoIt();

            return $quote';
        }
    }

# Adding Routes to Your Package

Now that the controller is in place, time to create the GET route. To do so, create a new file src/routes/web.php and put the following code in it:

    <?php

    use Rohith\Inspire\Controllers;
    use Illuminate\Support\Facades\Route;

    Route::get('inspire', InspirationController::class);

As you can see, there is a single route in here that refers to the controller you created in the previous section. At this moment, you can open the routes/web.php file in your project root and remove the previously added route from there. Because, if the same route exists on a package and the project routes/web.php file, the project code will take precedence.

After adding the route, open your src/Providers/InspirationProvider.php file and update the boot method as follows:

    <?php
    namespace Rohith\Inspire\Providers;

    use Illuminate\Support\ServiceProvider;

    class InspirationProvider extends ServiceProvider
    {
        /**
        * Bootstrap services.
        *
        * @return void
        */
        public function boot()
        {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }
    }

# Adding Views to Your Package
The final part is adding some views to the package. Again, this may be overkill but I wanted to show you how you may add views to your package. Create a new file src/views/index.blade.php and put the following content in it:

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Inspire</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>

    <body>
        <div class="container">
            <h1>{{ $quote }}</h1>
        </div>
    </body>
    </html>

A very simple view that shows the quote within a Bootstrap container. Adding the view is not enough though. You’ll have to load these views. To do so, open the src/Providers/InspirationProvider.php file and update its code as follows:

    <?php
    namespace Rohith\Inspire\Providers;

    use Illuminate\Support\ServiceProvider;

    class InspirationProvider extends ServiceProvider
    {
        /**
        * Bootstrap services.
        *
        * @return void
        */
        public function boot()
        {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            $this->loadViewsFrom(__DIR__.'/../views', 'inspire');
        }
    }

The loadViewsFrom method takes two parameters. The first one is the directory where you’ve kept your views and the second one is the namespace. The namespace should be your package name.

Finally, open the src/Controllers/InspirationController.php file and update it’s code as follows:

    <?php
    namespace Rohith\Inspire\Controllers;

    use Illuminate\Http\Request;
    use Rohith\Inspire\Inspire;

    class InspirationController
    {
        public function __invoke(Inspire $inspire) {
            $quote = $inspire->justDoIt();

            return view('inspire::index', compact('quote'));
        }
    }

# Sharing Your Package With Others

Now that your package is ready, you may want to share it with others. You can use Packagist for that but for a package this dumb, I would not like to litter the platform. Let’s use GitHub for sharing our package for now.

cd into the packages/rohith/inspire directory and execute the following set of commands:

    // packages/rohith/inspire

    echo "/vendor/" > .gitignore
    git init
    git checkout -b master
    git add .
    git commit -m "Initial commit"
    git tag 1.0.0

This will turn the package directory into a git repository, add all the files, create an initial commit and tag the source code as version 1.0.0. Now head over to GitHub and create a new repository.

    // packages/rohith/inspire

    git remote add origin git@github.com:rohith/inspire.git
    git push -u origin --all
    git push -u origin --tags

# Installing Your Project Into a New Project

To test out the package installation, you’ll need a new Laravel project. Create a new project somewhere on your computer with the name needs-inspiration.

    laravel new needs-inspiration

By default, Composer pulls in packages from Packagist so you’ll have to make a slight adjustment to your new project composer.json file. Open the file and update include the following array somewhere in the object:
   
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/rohith/inspire"
        }
    ]

The updated composer.json file should look as follows:
    
    {
        "name": "laravel/laravel",
        "type": "project",
        "description": "The Laravel Framework.",
        "keywords": ["framework", "laravel"],
        "license": "MIT",
        // here you go
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/rohith/inspire"
            }
        ],
        "require": {
            "php": "^7.3|^8.0",
            "fruitcake/laravel-cors": "^2.0",
            "guzzlehttp/guzzle": "^7.0.1",
            "laravel/framework": "^8.75",
            "laravel/sanctum": "^2.11",
            "laravel/tinker": "^2.5"
        },
        "require-dev": {
            "facade/ignition": "^2.5",
            "fakerphp/faker": "^1.9.1",
            "laravel/sail": "^1.0.1",
            "mockery/mockery": "^1.4.4",
            "nunomaduro/collision": "^5.10",
            "phpunit/phpunit": "^9.5.10"
        },
        // ... so on
    }

Now composer will also look into this repository for any installable package. Execute the following command to install the package:

    composer require rohith/inspire

As you can see, the package has been installed successfully. Now, open the config/app.php file and scroll down to the providers array. In that array, there should be a section for the package service providers. Add the following line of code in that section:

    /*
    * Package Service Providers...
    */
    Rohith\Inspire\Providers\InspirationProvider::class,

This will register the InspirationProvider class as one of the service providers for this project. Start the application using php artisan serve and visit the /inspire route to get inspired. Also, since we’ve put the logic for getting the inspiration quote in a separate class instead of directly in the controller, you can use the Rohith\Inspire\Inspire.php class anywhere in the project. I could’ve turned it into a facade but I don’t like to complicate things unnecessarily. So that’s that.
