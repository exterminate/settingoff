settingoff
==========

A new app to communicate with your loved ones and friends

Using the app
=============

When you first open the site click the Go! button to create your ID. 

Now you're in. Bookmark this page.

You should click the set home link so we can work out how far you are from home.

When you are ready to set off click the Set off link. This will then show how many minutes it is since you set off.

Share this with friends or family by adding their ID and name into the firm provided. You can share with up to 4 people. You can see their leaving times and distance from their desination (when they are using the app).


Instructions for Matt
=====================

I'm hoping that most of the PHP is there for you to just do the JS.

I suppose the most important thing to do is the minutes since setting off. There is some JS that was working before. This, http://jsbin.com/utalate/9/edit, is the code it came from. 

Could you make this work for "self" and "friends". I feel like you could do this with some array. I guess it will be dependent on whether they have set off.

Next, if you can get it to work is the distance. So, when you "set home" you create coordinates that are save in the database. When you set off you need to get the location again and figure out the distance between the two. Check out the script.js for an earlier version. This should have all the relevant JS that is needed.

Friends are Assocs, i.e., assoc1, assoc2 etc.

There are some "people" already in the system.
