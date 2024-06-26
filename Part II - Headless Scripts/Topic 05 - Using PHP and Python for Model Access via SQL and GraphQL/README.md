## 01 Accessing and Traversing Instances in the Model

The first script includes a simple model crawler that recursively iterates through the instances tree underneath a starting node. It reports the number of elements within a given search depth. The script uses the PHP utilitie's Node class, which all instance types are inherited from (Equipment, Organizations, Persons, Materials, ...)

We can use such a script as a simple template to work on a variety of use cases such as pattern finding, model automation, or reporting. 

To make a script a template that is available in the UI when new scripts are created, simply execute the following SQL statement:

``` SQL
update model.scripts set document='{"template": 1}' where id='1234';
update model.scripts set document='{"template": 1}' where display_name='Fancy Template';
```

## 02 Executing GraphQL queries from Headless Scripts

THe GraphQL API can be easily accessed from browser scripts and also in headless mode. This script shows how to use a headless script to make a GraphQL request.

## 03 Traversing along Relationships using Explicit Path Description

The third script contains a TiqTraverser class that allows to describe traversal from an origin in the model. The type of relationship and properties of the target can be described explicitely.
Script 03.1 includes a demo of how the TiqTraverser class is used.

## 04 Get all Types and Instances in  a Model

This script features the ::getNodeSet method that comes with most classes in the SMIP. You put your type of choice in front of it and you're ready to retrieve records.
The snippet below retrieves all libraries and all types:

``` PHP
$allLibraries = TiqUtilities\Model\Library::getNodeSet("libraries")["set"];
$allTypes = TiqUtilities\Model\Type::getNodeSet("types")["set"];
```
