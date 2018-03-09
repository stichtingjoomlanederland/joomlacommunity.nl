module.exports = {
    // compile less stylesheets to css -----------------------------------------
    options: {
        outputStyle: 'compact',
        sourceMap: true
    },
    build: {
        files: {
            '<%= paths.assets %>/css/easydiscuss.css': '<%= paths.assets %>/less/easydiscuss.less',
            '<%= paths.assets %>/css/template.css': '<%= paths.assets %>/less/template.less'
        }
    }
};
