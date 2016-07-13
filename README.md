# menulog box installation guide

## the structure
make sure you've checked out the vagrant box and all the projects in the following structure (don't rename them when u git clone - or if u already did so, just rename them back to this).

```
├── menulog
│   ├── frontend-admin
│   ├── frontend-desktop
│   └── frontend-mobile
└── scotch-box
    ├── README.md
    ├── README.scotchbox.md
    ├── Vagrantfile
    ├── bootstrap.sh
    ├── cnf
    ├── menulog
    └── public

```

## Prerequisites
### frontend-desktop:
Make sure you've run the scripts and generated the dbm mapping files under 
`apache_data_files`, if not, just run (copied from `install.sh`)

```
rm -f "/tmp/domains_using_menulog2.dbm.pag" "/tmp/domains_using_menulog2.dbm.dir"
httxt2dbm -f SDBM -i apache_data_files/domains_using_menulog2.txt -o /tmp/domains_using_menulog2.dbm
mv  "/tmp/domains_using_menulog2.dbm.pag" "/tmp/domains_using_menulog2.dbm.dir" apache_data_files/

rm -f "/tmp/preset_quicklinks.dbm.pag" "/tmp/preset_quicklinks.dbm.dir"
httxt2dbm -f SDBM -i apache_data_files/preset_quicklinks.txt -o /tmp/preset_quicklinks.dbm
mv  "/tmp/preset_quicklinks.dbm.pag" "/tmp/preset_quicklinks.dbm.dir" apache_data_files/
```

### Install vagrant
If you don't have vagrant installed already, follow the guideline here:
https://www.vagrantup.com/ for vagrant; and here: https://www.virtualbox.org/wiki/Downloads for virtualbox;

Once you have both installed, go to this project root, and simply run

```
vagrant up
```

### Install java and run combine js/css for mobile site
- install java environment from [http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html](http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html)
- go to frontend-mobile and run `./css_combine.sh && ./js_combine.sh`

### hosts files
While you are waiting for vagrant to finish building the image, go ahead and dump the stuff included in cnf/hosts into your local /etc/hosts (or the windows equivalent).

## Access the site
- Desktop: [http://menulog.local/](http://menulog.local/) 
- Mobile: [http://m.menulog.local/](http://m.menulog.local/) 
- Admin: [http://menulog-old.local/admin](http://menulog-old.local/admin) 

