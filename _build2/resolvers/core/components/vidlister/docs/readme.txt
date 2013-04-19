#############################################################
VidLister

NewVersion: 1.1.0 beta1
Released: 2013-04-19
Author: Stepan Prishepenko {Setest} <itman116@gmail.com> http://community.modx-cms.ru/profile/setest/

Version: 1.0.0 alpha
Released: 2011-12-09

Author: Jeroen Kenters Web Development / www.kenters.com

License: GNU GENERAL PUBLIC LICENSE, Version 2
#############################################################

==========================================
 Description
==========================================
VidLister shows your (Youtube + Vimeo) videos on your site.
Thumbs are shown like a gallery and videos open inside a
lightbox.

==========================================
 Features
==========================================
* reads your Youtube/Vimeo feed and imports the video data
* shows all video thumbs (paginated) with link to video
* Languages (backend):
  - English
  - Russian

==========================================
 Requirements
==========================================
* MODX Revolution (tested using 2.1 and 2.2)
* getPage
* getResources
* WayFinder
* jQuery for the lightbox

==========================================
 Installation
==========================================
* Install through Package Management

==========================================
 Usage
==========================================

Import:
- if you need topic create "Topic" container in resource tree with children elements.
  All elements must be published and visible.
- open VidLister in system properties
- edit properies as you need, also add ID of your "Topic" resource
- fill in the details (for Vimeo visit http://vimeo.com/api/applications/new first)
- clear cache
- go to components -> VidLister and press 'Import'
- your videos should be listed after import

Snippet:
- you can use example chunk [[$vlYoutubeExampleTopic]] to explore how work with "Topic"
- or you can add [[!getPage? &element=`VidLister`]] [[+page.nav]] to your page
- make sure you already have jQuery in your website template: VidLister does not add it
- if you want to add the js/css yourself add &scripts=`0` to the snippet call