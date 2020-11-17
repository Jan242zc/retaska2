# Retaška 2

## Technologies used
- Nette 3
- MySQL
- vanilla Javascript
- HTML 5
- vanilla CSS 3

The usage of Nette 3 also implies PHP 7 and object oriented programming.

The chosen technologies are almost the same as the ones I used for my previous project, Musiccollections [1]. I picked the Nette framework for the obvious reason that it's the most used PHP framework in the Czech republic [2]. The tools used on frontend are, on the contrary, slightly different. This time I decided not to use any libraries (jQuery, Bootstrap,...), but to write all the CSS and JS by myself. It was challenging, but worth it, as I utilized the knowledge gained thanks to the Udemy courses I was taking at the time [3] [4] (and, as of the date I'm writing these lines (17 Nov 2020), I still am).

I am aware of the fact that this application is by no means visually impressive, but that wasn't its purpose either. I wrote quite a lot of Javascript code to make the frontend interactive, but I wouldn't make it look stunning even if I wanted to, as I lack imagination to do so. And, as I aim at becoming a backend developer, I focused primarily on writing a correctly working, well designed backend.

## The purpose of this application

This application is a simple e-shop meant to be used by an entrepreneur that sells one type of goods (in the case, bags made from ecological materials). 

By developing this application I revisited an assignment (hence Retaška **2**) from an evening programming course that I took in autumn 2018. That application however used different technologies (Symfony 2, Doctrine ORM,...) and my code worked but wasn't great by any means, as I was an absolute beginner at the time. It can still be found on my Github.

## How does this application work

Nette is a standard MVC framework, so it should not be at all difficult for someone who is familiar with MVC frameworks to read to code.

The bussiness logic, aka the core af the app, aka the model part, is contained in the App\Services directory, along with other services.

Controllers (in Nette called 'presenters'), separately for Front and Admin modules, can be found in the App\Modules directory, along with their respective templates.

The App\Controls folder contains components (=reusable parts of code for frontend) that are either used on more that one place or are so complex that they deserve their own class. I tried to keep one-presenter components in the respective presenter classes.

The App\Entity directory contains entities, i. e. classes that are supposed to represent data. This means that each table in the database has one Entity class to which the table's rows are loaded and then manipulated with. Some Entities represent data that is saved in sessions only. I used classes & objects rather than associative arrays wherever possible (in contrast to Musiccollections).

## How to make the application run on your machine

To run this app, you (or your colleague developer) must have a server + MySQL + PHP installation stack on your computer (LAMP, WAMP, AMPPS etc.). If you currently think about hiring me, this should already be the case.

Next, follow these steps (it's nothing difficult, but I recommend reading them all first):

1. 	Use Git to (fork and) clone the repository
2.1.	Open the terminal and change directory to ...\retaska2\bin
2.2.	Run file 'database_backup_loader.php' (by typing 'php database_backup_loader.php' if your OS is Windows) OR
2.3.	(alternative to 2.1. + 2.2.) Open the field for SQL queries in your database administration tool (phpMyAdmin or other)
2.4.	(alternative to 2.1. + 2.2.) Copy & paste the contents of file backupA.txt (in root directory) and have them executed
3.1.	In the App\config directory, create a file and name it 'local.neon'
3.2.	Copy & paste the contents of file copy_this_to_local_neon.txt to local.neon and set the database connection according to your preferences

Congrats, you should now have a working application. The e-shop main page can be found at url {server name}/retaska2/www/, administration at url {server name}/retaska2/www/admin.homepage/

However, the database contains no data besides the one crucial for the app to function. If you want to add some extra data that will allow you to see how the app looks like non-empty (categories, products, etc.) and how it actually works, follow these steps:
4.1.	In the terminal and still in dir ...\retaska2\bin, run file 'database_population.php' with flag '--wdata' OR
4.2.	(alternative to 4.1.) Repeat step 2.4, but this time with contents of file backupB.txt.

If you messed something up in the database and wish to start over, run the database_backup_loader.php with flag '--reset' or execute the contents of file backupC.txt. This will drop and create again the database and the tables.

## Miscellaneous important not-so-obvious stuff 

- The database backup has two users ('admin' and 'oliver') both with passwords '1234'. Use their credentials when first accessing the administration.
- There are two user roles ('superadmin' and 'admin'). Roles and their permissions are hard-coded.
- Data of most entities is assigned a pseudo-random id upon insertion into database. The top bounds of these numbers are stipulated by the Entity table and can be changed by the administrator.
- Probably the most interesting but also the most difficult thing to understand about this application are 'delivery services'. 
	- A delivery service is a means of shipping ordered products to the customer. Each delivery service is defined by a means of delivery (post, picking up at the store, etc.), a payment method (bank transfer, cash etc.) and a country for which it is available. The customer picks their prefered delivery service in the PurchaseForm (retaska/www/front.finishpurchase/) by selecting a country of residence, a means of delivery and a payment method in this specific order. The options in the delivery select field are dynamically changed according to the currently selected country of residence / delivery country and the select field for payment is dynamically changed according to the currently selected means of delivery.
	- A delivery service does not need to have a country specified. The services without a specified country are considered to be of type 'customer comes for the goods', whereas the services with a specified country are considered to be 'goods come to the customer'. This should be considered when creating these services in the administration, as their logicality is entirely within admin's responsibility. In the PurchaseForm, the customer is offered delivery services without a country specified ('country independent services') and delivery services available in their country. If they check the 'ship to other than residential adress' box, they are offered only delivery services available in their country, if any. It makes sense not to offer them to pick up the goods at the store when they want them to be delivered to other than residential adress.
	- Why do it this way? I wanted the app to reflect the fact that different coutries have different shipping companies, different shipping companies have different shipment prices and accept different payment methods, while the fees for payment methods can also vary. It should go without saying that you will understand this better if you look it up in the app itself.

## Footnotes

[1]	I still keep this app in a private repository as I am not happy with the code.
[2]	www.zdrojak.cz/clanky/vysledky-technologie-na-ceskem-webu
[3]	https://www.udemy.com/course/the-complete-php-full-stack-web-developer-bootcamp/
[4]	https://www.udemy.com/course/the-complete-web-development-bootcamp/
