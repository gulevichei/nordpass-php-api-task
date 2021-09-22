# Secure Information Storage REST API

### Project setup

* Add `secure-storage.localhost` to your `/etc/hosts`: `127.0.0.1 secure-storage.localhost`

* Run `make init` to initialize project

* Open in browser: http://secure-storage.localhost:8000/item Should get `Full authentication is required to access this resource.` error, because first you need to make `login` call (see `postman_collection.json` or `SecurityController` for more info).

### Run tests

make tests

### API credentials

* User: john
* Password: maxsecure

### Postman requests collection

You can import all available API calls to Postman using `postman_collection.json` file

### Project endpoints

* GET item - returns a list of all user items
* PUT item formdata:{id, data} - makes changes to item
* POST item formdata:{data} - creates a new item
* DELETE items/{id} - delete exist user item by id

### Updates 22-09-2021

* added new endpoint - PUT item
* accelerated return of the list items by skipping the conversion of data from the database into objects
* added item membership check to methods PUT and DELETE
* updated make init setting up test and prod environment
