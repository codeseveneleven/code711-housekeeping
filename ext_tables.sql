CREATE TABLE tx_code711housekeeping_domain_model_project
(
    title     varchar(255) DEFAULT '' NOT NULL,
    url       varchar(255) DEFAULT '' NOT NULL,
    version   varchar(255) DEFAULT '' NOT NULL,
    latest    varchar(255) DEFAULT '' NOT NULL,
    type      varchar(255) DEFAULT '' NOT NULL,
    elts      int(2) DEFAULT 0 NOT NULL,
    severity  varchar(255) DEFAULT '' NOT NULL,
    group     int(11) DEFAULT 0 NOT NULL,
    giturl    varchar(255) DEFAULT '' NOT NULL,
    gittoken  varchar(255) DEFAULT '' NOT NULL,
    gitbranch varchar(255) DEFAULT '' NOT NULL,
    php       varchar(255) DEFAULT '' NOT NULL,
    packages  int(11) DEFAULT 0 NOT NULL
);

CREATE TABLE tx_code711housekeeping_domain_model_group
(
    title    varchar(255) DEFAULT '' NOT NULL,
    code     varchar(255) DEFAULT '' NOT NULL,
    gittoken varchar(255) DEFAULT '' NOT NULL
);

CREATE TABLE tx_code711housekeeping_domain_model_package
(
    title       varchar(255) DEFAULT '' NOT NULL,
    version     varchar(255) DEFAULT '' NOT NULL,
    latest      varchar(255) DEFAULT '' NOT NULL,
    parentid    int(11) DEFAULT 0 NOT NULL,
    parenttable varchar(255) DEFAULT '' NOT NULL
);