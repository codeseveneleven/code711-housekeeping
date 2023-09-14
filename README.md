# Code711 Housekeeping

[![Latest Stable Version](https://poser.pugx.org/code711/code711-housekeeping/v/stable.svg)](https://extensions.typo3.org/extension/code711_housekeeping/)
[![TYPO3 12](https://img.shields.io/badge/TYPO3-12-orange.svg)](https://get.typo3.org/version/12)
[![Total Downloads](https://poser.pugx.org/code711/code711-housekeeping/d/total.svg)](https://packagist.org/packages/code711/code711-housekeeping)
[![Monthly Downloads](https://poser.pugx.org/code711/code711-housekeeping/d/monthly)](https://packagist.org/packages/code711/code711-housekeeping)

Keep track of your TYPO3 versions by checking your git-repositories (gitlab for now). You can then see the results on your TYPO3 Dashboard. It is of course checked against the currently available latest TYPO3 version, keeping you updated on what needs to be updated.

**Note that this version no longer supports API calls with the code711_api extension. Use the 2.x version instead which 
is also available for TYPO3 12.x.**

You need for this:

* Install extension code711_housekeeping
* Add your groups and projects as records
* Configure and add your project widget to the dashboard

## Installation

Install it via `composer req code711/code711-housekeeping`.

Activate the extension.

## Groups and projects

Add a new record `Housekeeping: Project` in list module.

|  Field | Purpose                                    | Example             | Auto update |
|---|--------------------------------------------|---------------------|-------------|
|  title | Displays in dashboard                      | My project          | no          |
| url  | Only needed as preview                     | https://example.org | no          |
| giturl | To check against your git | https://[githost]/[group]/[project] | no |
| gittoken | Personal access token | xxx | no |
| version  | Current project version                    | 12.4.4             | yes         |
| latest | Current TYPO3 Version (for major of above) | 12.4.4             | yes         |
| type | type of last release                       | security            | yes         |
| elts | last release is elts                       | 1                   | yes         |
| severity | color class in dashboard                   | bg-green            | yes  |
| group | optional group                             | My group            | no |

Optionally you can also add one or more `Housekeeping: Group` to group your projects in different responsibilities or views.

| Field | Purpose                                 | Example  |
|-------|-----------------------------------------|----------|
| title | Displays in project record              | My group |
| code  | Configure available groups in dashboard | mygroup  |

### Auto update

With this feature it is possible to fetch the fields marked in the above table when saving a project record.

**Auto update is available when**:
* You have a GIT repository with personal access token available

## Configure dashboard

The widget is available right away but you might like to override it with some custom values.
Or even copy the hole widget configuration to add another one with different options.

An example to override just some values could look like this:

````yml
services:
    dashboard.widget.projects:
        class: 'Code711\Code711Housekeeping\Widgets\ProjectsWidget'
        arguments:
            $view: '@dashboard.views.widget'
            $options:
                groups:
                    - mygroupx
                    - mygroupy
                sorting:
                    - 'severity desc'
                    - 'version asc'
                    - 'title asc'
        tags:
            - name: dashboard.widget
              identifier: 'projects'
              groupNames: 'general'
              title: 'My own widget title'
              description: 'LLL:EXT:code711_housekeeping/Resources/Private/Language/locallang.xlf:widgets.projects.description'
              iconIdentifier: 'content-widget-text'
              height: 'large'
              width: 'large'
````
Remember to clear the cache in the install tool when changing `Service.yaml`.

### Groups
Using option groups you are able to pick projects of a special group.

### Sorting
All project fields are **sortable** for dashboard view. Has to be `fieldname order` each.
