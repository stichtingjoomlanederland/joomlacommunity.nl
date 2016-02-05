# joomlacommunity.nl

Het Nederlandstalige Joomla!-portal

## Getting Started

How to install less and run gulp.

### Prerequisities

Node.js
https://docs.npmjs.com/getting-started/installing-node

### Installing


Install less globally
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