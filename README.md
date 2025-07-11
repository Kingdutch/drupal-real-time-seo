# Drupal Real-Time SEO

Monorepo for the [Real-Time SEO Drupal Project](https://www.drupal.org/project/yoast_seo) which 
includes both the module itself as well as the JavaScript library that can not be hosted on 
Drupal.org. This repository also contains a development set-up in the form of Docker images to 
help with automated testing and the CI pipeline.

## Subtree Management
The `html/libraries/rtseo.js` and `html/modules/yoast_seo` are git subtrees that split out to 
their own repositories (https://github.com/Kingdutch/RTSEO.js/ and 
https://git.drupalcode.org/project/yoast_seo/ respectively) from which they are published.

### Splitting
To push changes to the downstream repository use the following:
```bash
# Add the remote and fetch its history
git remote add split <remote>
git fetch split

# Split to a separate branch so that we have a branch we can tag on later.
git subtree split --prefix="<project-path>" -b split-main

git push split split-main:<target-branch>
```

### Pulling 
Ideally all changes happen in this repository and flow downstream, but it could happen that 
changes are merged in Drupal.org by accident.

To pull in any changes that happened in a Drupal.org git branch (8.x-2.x in this example) run:
```bash
git subtree pull --prefix html/modules/yoast_seo git@git.drupal.org:project/yoast_seo.git 8.x-2.x
```

For the RTSEO.js library run:
```bash
git subtree pull --prefix=html/libraries/rtseo.js git@github.com:Kingdutch/RTSEO.js.git master
```
