# joomlacommunity.nl

Het Nederlandstalige Joomla!-portal

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisities

https://docs.npmjs.com/getting-started/installing-node

```
Node.js: 
```

### Installing

A step by step series of examples that tell you have to get a development env running

The easiest way to install Less, is via npm, the node.js package manager, as so:
```
npm install -g less
```

Install gulp globally
```
npm install --global gulp
```

Install gulp and the devDependencies into your project (this may take a while)
```
cd *path\to\your\project* \templates\jc
npm install
```

Run gulp
```
gulp
```

#### Gulp does this for you

- watch less files for changes
- compile less files
- write source maps
- autoprefix css: Working with Autoprefixer is simple: just forget about vendor prefixes and write normal CSS according to the latest W3C specs.
- minify css