# Reusable Block Count

 Display a "Reusable blocks" listing page, and a link to view all posts containing a given block.

 Only displays the number of posts that contain the block, not the number of times the block is used (does /not/ count multiple instances within a single post)

# Props

 Loosely based on https://github.com/yeswework/fabrica-reusable-block-instances/ -- simplified the code for maintainability and readability.  Swapped to WordPress Core methods when available.

 Icon comes from https://material.io/resources/icons/?icon=view_list&style=outline

# Deploying to WordPress.org SVN

This repository uses https://github.com/10up/action-wordpress-plugin-deploy to deploy to WordPress.org -- all you should need to do is update the version numbers for the release in `readme.txt` and `reusable-block-count.php`,  and then publish a release in GitHub, and it should automatically deploy.

You can confirm deploys here:

https://plugins.trac.wordpress.org/browser/reusable-block-count
