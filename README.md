# Developing Applications on the CESMII SMIP - Advanced Topics

Over the years the list of topics to cover when it comes to developing on the SMIP has grown to that it could fill a full semester course. This post is a sequence of topics that is carefully compiled to guide the reader from taking first steps to mastering complex analyticical and vizualisation challenges. 

Among the many challenges in digital transformation are islands of knowledge and subject matter expertise. Folks with deep manufacturing knowledge might find certain key concepts of data capture and processing too technical. Specialists equiped with machine learning experience are likely to be opinionated about anything that doesn't come in very particular flavors such as Python or R. 

Working with the SMIP for many years, we understand that to fully leverage the raw power of the platform it takes a bit of all these: manufacturing curiosity, analytical skills, web development chops, basic understanding of PLC's, industry protocols and networking. It is expected to learn new tricks and master new techniques on a daily basis. Tools and skills to put this all together likely include most the following to a certain extend: PHP, Python, SQL, js, .NET, GraphQL, Vue.js, jwt, and others.  

## Part I: Browser Scripts with GraphQL and Vue.js

### Week 1: Introduction to the SMIP's GraphQL API, it's mini-IDE, and the Vue.js SPA Framework

Click around. Explore the API using GraphiQL. Build a Hello World page. Build a Unit Conversion Page. Tiny but useful PHP helpers: context, script loaders, URL parameters.

### Week 2: Model Automation and Attribute Manipulation

Take a closer look at types, inheritance, instances and how to avoid "death from dumb clicking". Display scripts for types and instances. Graph models and relationship crawling.

### Week 3: Introduction to Value Stream Use Cases

Time series data. Charting. Time Zones & Geo Location. SMIP components.

### Week 4: Introduction to the Material Ledger

Material Types. Accounts. Transactions. Laws of material movement and attribute propagation. Detection of material movement.

## Part II: Headless Scripts

### Week 5: Using PHP and Python to Access Model Data via SQL and GraphQL

Result object. Continuous computing. Batch vs. stream. Uptime, yield, mtbf, ...

### Week 6: Object-Oriented Project Organization

Reuse of code. Calling other scripts. API-like data access for browser scripts.

### Week 7: Smart Attributes, Event Detection, and Value Stream Aggregates

Using the objectValue field to configure smart attribute. Resolving FQN's and FQN-references. Moving window averaging. TS algos: removal of duplications, pulse counting, counter attributes, bucketing.

## Part III: Smart Manufacturing Apps

### Week 8: Distribution of SMIP Goodies using Libraries and Joomla Components

Bare bone joomla component. Import/Export of libraries. Spawning and clean-up of content.

### Week 9: Using Authenticators for Remote Access

JWT tokens. Roles. Keeping secrets out of repos. Desktop centric tasks.

### Week 10: Azure Functions and AWS Lambda

Stuff on timer, but from the outside. Protect/confuscate IP.

### Week 11: Serving Multiple SMIPs

Manage and store authenticators from multiple SMIPs. Self-serve concepts and workflows. Multi-tenancy B2B vs. B2C. Authentication vs. Authorization

### Week 12: SMIP-specific Machine Learning Concepts

Architecting a pipeline for ML tasks on the platform and off the platform.
