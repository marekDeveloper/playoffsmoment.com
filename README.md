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

I've decided to use `Feeds module` where all mapping will be done using Admin UI. 
Feeds module is around for a really long time and was "go to solution" for D7 sites.

Only disadvantage for D8 is "This project is not covered by Drupal’s security advisory policy."
And it does not have stable release, only alpha. But there are plenty if D8 modules in this state.
Plenty of sites are using feeds and there is even D9 work done. 

[https://www.drupal.org/project/feeds]

And we need another module for JSON parsing: [https://www.drupal.org/project/feeds_ex]

Download 2 modules using composer from within `src` directory:

```
composer require drupal/feeds
composer require drupal/feeds_ex
```

Go to Admin -> Extend (/admin/modules) and enable 2 new modules.


## 5. Taxonomies

Need to create taxonomies for Conference & Division.

I think I just need to create taxonomy vocabularies and Feeds can create items into lists, we'll see.

Creating 2 vocabularies "NFL Conference" & "NFL Division" - /admin/structure/taxonomy. 
Created both and empty list of terms are there for now.


## 6. Team content type

Creating new `Team` content type with multiple fields.

Create Team: "Admin -> Structure -> Content types -> Add content type" (/admin/structure/types/add)

Type name: Team.

Tweaks for default form: Remove check for "Promoted to front page" & "Display author and date information" + Disable for Menu

Continue to add new fields in Manage fields section for Team content type + match API fields

name = Title (text plain field) - required
nickname = Nickname (text plain field) - required
display_name = Display Name (text plain field)
conference = Conference (taxonomy reference: NFL Conference)
division = Division (taxonomy reference: NFL Division)
id = API ID (text plain field - integer) - required

For taxonomies reference ... check "Create referenced entities if they don't already exist"

I did some small tweaks in "Manage form display" and "Manage display" tabs.


## 7. Create Feed Type

Need to create new `Feed Type`, go to "Admin -> Structure -> Feed types -> Add feed type".

Type Name: NFL JSON
Description: 3rd party API for NFL team import from JSON
Fetcher: Download from URL
Parser: JsonPath
Processor: Node
Content Type: Team
Import period: 15 minutes
TO DO! Requires cron to be configured?!

Processor settings: Update existing content items & un-publish content item when no longer in the feed + Owner: Feed author

### Feeds Mapping Tab

Context: $.results.data.team.*

id: 			API ID (field_api_id) - unique
name: 			Title (title)
nickname: 		Nickname (field_nickname)
display_name: 	Display Name (field_display_name)
conference: 	Conference (field_conference) - Reference by: Name + Autocreate terms: Yes
division: 		Division (field_division) - Reference by: Name + Autocreate terms: Yes


## 8. Create Feed Node

Go to "Admin -> Content -> Feeds -> Add feed"

Creating new feed 

Title: NFL Teams Import - Resulta Test
Feed URL: 3rd party JSON API provided by Resulta team

Save and run import ... 

And all seems to be working fine ... nice green message "NFL Teams Import - Resulta Test: Created 32 Team items."

Now need to check actual data in D8 CMS.

Information imported into `Team` content type seems all good, even taxonomies are imported properly.


## 9. Display Suite module installation

I want 2 column layout for my content, so installing DS module [https://www.drupal.org/project/ds]

composer require drupal/ds

Go to Admin -> Extend (/admin/modules) and enable new module.


## 10. Create View

Creating new `Block` view `NFL Teams`. Go to "Admin -> Structure -> Views -> Add view"

Created 2 view blocks `NFC Teams` & `AFC Teams`. Display content fields as table and multiple view configuration done.


## 11. Views Reference Field module installation

To be able to assign views to fields I need Views Reference Field module [https://www.drupal.org/project/viewsreference]

composer require drupal/viewsreference

Go to Admin -> Extend (/admin/modules) and enable new module.


## 12. Teams Page content type

Creating new `Teams Page` content type which will show all imported teams on one page.

I would like to display 2 tables, one for each Conference. I was not able to do this just in Views.
I'm going to use Display Suite module.

Create Team Page: "Admin -> Structure -> Content types -> Add content type" (/admin/structure/types/add)

Type name: Teams Page.

Tweaks for default form: Remove check for "Promoted to front page" & "Display author and date information"

Continue to add new fields in Manage fields section for Teams Page content type

Adding 2 fields `Left Column` & `Right Column` for left and right column blocks as `Views reference`.

I did some small tweaks in "Manage form display" and "Manage display" tabs.

In "Manage display" I've configured `Two column stacked layout` for this content type.


## 13. Create Teams Page node

Go to "Admin -> Content -> Add content -> Teams Page" (node/add/teams_page) and created new node.

Making this new page to be homepage ... go to "Configuration -> Basic site settings -> Front Page" (/admin/config/system/site-information)

And added views reference to Left and Right. Now I can see all information I want to see.

Well, it is kind of ugly with this default theme.
And not even responsive ... even testing "Fluid two column stacked layout" no luck with responsive, need another theme.


## 14. Try new theme

Going to try `Basic` theme: [https://www.drupal.org/project/basic]

Basic boasts a clean HTML5 structure with extensible CSS classes for unlimited theming possibilities as well as a top-down load order for improved SEO. It is fully responsive out-of-the-box.

New theme installation using Admin -> Appearance interface -> Install new theme.

Oh well, this is disappointment ... I've tried few themes and I did not like any of them.

TO DO! Continue to try some other themes ... showcase_lite seems little better.

I did little bit more research and decided to go with Adaptive theme [https://www.drupal.org/project/adaptivetheme]

Adaptive theme needs AT Tools module [https://www.drupal.org/project/at_tools] so I installed it using composer.

And created `marek_test` theme from adaptive theme usin AT UI interface where I configured new theme and needed to write few lines of CS to make my NFL team tables better for responsive.





