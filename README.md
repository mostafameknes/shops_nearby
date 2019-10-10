# Folder src/controller : this folder contains four Controllers,

## the First one HomeController, is the main controller which manage the Shops list according to the user position and user preferences,
this controller contains 6 main methods;
--index() the home page shows the Shops list
--myPreferred() show the user preferred Shops
--like() get the liked Shop and add this shop to the user preferences
--dislike() get the disliked Shop and add this shop to the session with the current time

--remove() remove a Shop from user preferences
--storePosition() store in the session the latitude and longitude of the current visitor

--Distance() calculate the distance between a Shop and the current position of the user


## the second controller SecurityController to manage the sign in, and sign up of the user

## the third controller UserController : Manage the users (CRUD)

## the fourth controller ShopController : Manage the Shops (CRUD)

# Folder src/Entity 
this folder contains the models entities of the application; User class, Shop class, the relation between the two entity if manytomany, because a user can add many Shops to his preferred list, and a shop may be afftected to many user preferred list

# Folder src/Form
contains the form type of the models to add or update objects.

# Folder templates/admin/
this folder contains the views for the application CRUD

# Folder templates/home
this folder contains 2 pages;  index.html.twig: the homepage;
second preference.html.twig contains the user preferred shops

# Folder templates/security
this folder contains also two pages ; login.html.twig : Form login
register.html.twig Register Form of the users

# the file templates/admin.html.twig : 
base template for the backend
# the file templates/base.html.twig : 
base template for the frontend 
