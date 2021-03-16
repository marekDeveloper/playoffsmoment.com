# playoffsmoment.com
Drupal 8 repository for <a href="http://www.playoffsmoment.com" target="_blank">playoffsmoment.com</a> website.

# Steps to create website - Resulta info

## 1. Get Drupal 8 core

Download latest Drupal 8 version `8.9.13` into `src` folder using composer

`composer create-project drupal/recommended-project:8.9.13 src`


## 2. Install default D8 site

When going to website URL default installation script `/core/install.php` provides basic/standard D8 installation steps.


## 3. Tweaks for .gitignore

Drupal provides default `example.gitignore` file. Rename that file to `.gitignore` in `src` folder.


## 4. Feeds modules installation

I was thinking using D8 Migrations, that would require creation custom migration yml file. 
I'm wondering if I can do this exercise without writing single line of PHP code. We'll see.

I have decides to use Feeds module where all mapping will be done using Admin UI. 
Feeds module is around for a really long time and was "go to solution" for D7 sites.

Only disadvantage for D8 is "This project is not covered by Drupal’s security advisory policy."
And it does not have stable release, only alpha. But there are plenty if D8 modules in this state.
Plenty of sites are using feeds and there is even D9 work done. 

https://www.drupal.org/project/feeds

And we need another module for JSON parsing: https://www.drupal.org/project/feeds_ex

Download 2 modules using composer from within `src` directory:

composer require drupal/feeds
composer require drupal/feeds_ex

Go to Admin -> Extend (/admin/modules) and Enable 2 new modules.


## 5. Taxonomies

TO DO! TO CONTINUE! Create taxonomies for Conference & Division.




