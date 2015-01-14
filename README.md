Dicto CLI
---------

Prerequisits:
- Composer
- Php 5.3+
- Dicto

Installation
------------
```bash
composer install
chmod +x ./dicto.php
```

Usage
-----
Use 
```bash
./dicto.php list
```
To list all available commands. An example usage:
```bash
# Per default the DictoCLI runs on localhost:8010. If you use something compilabe eg. java specify --projectSource as a relative path to the source from the project dir.
./dicto.php createSuite DictoCLI /path/to/DictoCLI/

# Define the rules that are in a file
/dicto.php defineRules ./exampleRules.txt --suiteName="DictoCLI"

# Run the checks
./dicto.php generateResults --suiteName="DictoCLI"

# List the results and save it in json form into a file.
./dicto.php listResults --suiteName="DictoCLI" -s ./results/res

# Create html output
./dicto.php htmlOutput --suiteName="DictoCLI" results/index.html

#./Create html output relativly to a previous run:
./dicto.php htmlOutput --suiteName="DictoCLI" results/index.html -c ./results/res
```
