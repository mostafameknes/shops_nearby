this application contains four main controller,

# the First one HomeController, is the main controller which manage the Shops list according to the user position and user preferences,
this controller contains 6 main methods;
--index() the home page shows the Shops list
--myPreferred() show the user preferred Shops
--like() get the liked Shop and add this shop to the user preferences
--dislike() get the disliked Shop and add this shop to the session with the current time

--remove() remove a Shop from user preferences
--storePosition() store in the session the latitude and longitude of the current visitor

--Distance() calculate the distance between a Shop and the current position of the user


# the second controller SecurityController to manage the sign in, and sign up of the user

# the third controller UserController : Manage the users (CRUD)

# the fourth controller ShopController : Manage the Shops (CRUD)
