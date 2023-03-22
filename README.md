# Code711 Housekeeping

Keep track of your TYPO3 versions.

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
| url  | Needed for auto update                     | https://example.org | no          |
| version  | Current project version                    | 11.5.24             | yes         |
| latest | Current TYPO3 Version (for major of above) | 11.5.25             | yes         |
| type | type of last relase                        | security            | yes         |
| elts | last relase is elts                        | 1                   | yes         |
| severity | color class in dashboard                   | bg-green            | yes  |
| group | optional group                             | My group        | no |

Optionally you can also add one or more `Housekeeping: Group` to group your projects in different responsibilities or views.

| Field | Purpose                                 | Example  |
|-------|-----------------------------------------|----------|
| title | Displays in project record              | My group |
| code  | Configure available groups in dashboard | mygroup  |

### Auto update

With this feature it is possible to fetch the fields marked in the above table when saving a project record.

**Auto update is available when**:
* TYPO3 version of target project is higher then 11
* Target project has [code711_api](https://packagist.org/packages/code711/code711-api) installed and configured

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
