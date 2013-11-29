What Hood Is This?
=======================

Introduction
------------
Whathood aims to settle the neighborhood border debate for all time by aggregating
neighborhood borders drawn by users into heatmaps showing the "identity" of a
location.

[http://whathood.in](http://whathood.in)

Implementation
--------------
Written with

* PHP

* Doctrine ORM

* Zend Framework 2

* LeafletJS

    * plugin [heatmap.js](http://www.patrick-wied.at/static/heatmapjs/)

* PostgreSQL 9.2

    * PostGIS 2.1

To Do
-----

* store heatmaps as geometries instead of text

* create neighborhood polygons from heatmap points using [PostGIS' Concave Hull function](http://www.bostongis.com/postgis_concavehull.snippet)

* load geoJson for all other possible metropolitan areas

* cleanup user experience

* Create a developer API to serve up neighborhood given an address

* Get Google, Foursquare, Yelp, and real estate sites to use published shapefiles

Credits
-------
originally developed by [Jim Smiley](http://jimsmiley.us) and Justin Crone