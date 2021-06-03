# CarPooling Hub - Aggregator
This application acts as an aggregator for car pooling providers. It makes several calls to the car pooling providers which are configured and returns aggregatred results

# Project Description
At the moment BlaBlaCar is integrated into the APP

# Configurations
configure/.env contains the configurations parameters. It can be created based on the .env.example contained in the same folder.


# Getting Started
. Clone the repository in your web server
	```
		git clone git@github.com:impronta48/opticities-aggregator.git
	```
. Run ``` composer update ``` into the main folder
. Create a database (opticities_aggregator) and add the DSN string into your configure/.env
. Point the browser to your project folder (eg: http://localhost/opticities-aggregator )

# Prerequisites (Optional)
You need a running LAMPP server with PHP 7.2+, MySQL or MariaDB, Apache

# Deployment (Optional)
Just repeat the installation procedure on the production server
Remember to set debug=false in your configure/.env


# Versioning (Mandatory)
2.0.1

# Authors (Optional)
Marina Dragoneri (Original Idea)
Massimo INFUNTI (Sw Architect)
Marco Toldo (Main Developer)
Antonino Segreto (Maintainer)

#Copyrights (Mandatory)
© Copyright CSI – 2010-current

# License (Mandatory)
SPDX-License-Identifier: EUPL-1.2
See the LICENSE.txt file for details

# Community site (Optional)
https://github.com/impronta48/opticities-aggregator