# Google News Search Plugin

## Description

The Google News Search plugin allows you to easily integrate a Google News search functionality into your WordPress website. It provides a simple search form that allows users to search for news articles using Google News RSS feed, with paginated results.

## Installation

1. Upload the `google-news-search` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the shortcode `[google_news_search]` in your posts or pages to display the search form

## Usage

Simply add the shortcode `[google_news_search]` to any post or page where you want the search form to appear. Users can then enter their search terms and view the results directly on your website.

## Features

- Easy to use shortcode
- AJAX-powered search for a smooth user experience
- Paginated results (10 items per page)
- Responsive design
- Error handling for offline status and failed searches

## Files

### google-search.php

This is the main plugin file that:

- Registers the shortcode
- Handles the AJAX search request
- Parses the RSS feed
- Generates pagination

### google-news-search.js

This JavaScript file:

- Handles form submission
- Manages AJAX requests
- Updates the DOM with search results and pagination
- Provides offline status handling

### style.css

This CSS file provides styling for:

- Search form
- Search results
- Pagination
